<?php

require_once 'Conexao.php';

class UsuarioModel
{
    private static $tabelaTentativasLoginCriada = false;

    public function cadastrar($nome, $email, $senha)
    {
        $conexao = Conexao::getConexao();

        if ($this->emailEmUso($email)) {
            return "emailExiste";
        }

        $stmt = $conexao->prepare(
            "INSERT INTO login (nomeUsuario, Senha, Email)
             VALUES (?, ?, ?)"
        );

        $stmt->execute([$nome, $senha, $email]);

        return "ok";
    }

    public function login($email, $senha)
    {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT * FROM login WHERE Email = ?"
        );

        $stmt->execute([$email]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['Senha'])) {
            return [$usuario];
        }

        return [];
    }

    public function obterBloqueioLogin($email, $ip)
    {
        $conexao = Conexao::getConexao();
        $chave = $this->chaveTentativaLogin($email, $ip);
        $this->garantirTabelaTentativasLogin($conexao);

        $stmt = $conexao->prepare(
            "SELECT bloqueado_ate
             FROM login_tentativas
             WHERE chave = ? AND bloqueado_ate > NOW()"
        );
        $stmt->execute([$chave]);
        $bloqueadoAte = $stmt->fetchColumn();

        if ($bloqueadoAte !== false) {
            return $bloqueadoAte;
        }

        $stmt = $conexao->prepare(
            "UPDATE login_tentativas
             SET tentativas = 0, bloqueado_ate = NULL
             WHERE chave = ? AND bloqueado_ate IS NOT NULL AND bloqueado_ate <= NOW()"
        );
        $stmt->execute([$chave]);

        return null;
    }

    public function registrarFalhaLogin($email, $ip)
    {
        $conexao = Conexao::getConexao();
        $chave = $this->chaveTentativaLogin($email, $ip);
        $this->garantirTabelaTentativasLogin($conexao);
        $conexao->beginTransaction();

        try {
            $stmt = $conexao->prepare(
                "INSERT IGNORE INTO login_tentativas (chave, tentativas)
                 VALUES (?, 0)"
            );
            $stmt->execute([$chave]);

            $stmt = $conexao->prepare(
                "SELECT tentativas, bloqueado_ate
                 FROM login_tentativas
                 WHERE chave = ?
                 FOR UPDATE"
            );
            $stmt->execute([$chave]);
            $tentativa = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($tentativa['bloqueado_ate'] !== null
                && strtotime($tentativa['bloqueado_ate']) > time()
            ) {
                $conexao->commit();
                return $tentativa['bloqueado_ate'];
            }

            $quantidade = (int) $tentativa['tentativas'] + 1;
            if ($quantidade >= 5) {
                $stmt = $conexao->prepare(
                    "UPDATE login_tentativas
                     SET tentativas = 0, bloqueado_ate = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
                     WHERE chave = ?"
                );
                $stmt->execute([$chave]);

                $stmt = $conexao->prepare(
                    "SELECT bloqueado_ate FROM login_tentativas WHERE chave = ?"
                );
                $stmt->execute([$chave]);
                $bloqueadoAte = $stmt->fetchColumn();
            } else {
                $stmt = $conexao->prepare(
                    "UPDATE login_tentativas
                     SET tentativas = ?, bloqueado_ate = NULL
                     WHERE chave = ?"
                );
                $stmt->execute([$quantidade, $chave]);
                $bloqueadoAte = null;
            }

            $conexao->commit();
            return $bloqueadoAte;
        } catch (Throwable $erro) {
            if ($conexao->inTransaction()) {
                $conexao->rollBack();
            }
            throw $erro;
        }
    }

    public function limparTentativasLogin($email, $ip)
    {
        $conexao = Conexao::getConexao();
        $chave = $this->chaveTentativaLogin($email, $ip);
        $this->garantirTabelaTentativasLogin($conexao);

        $stmt = $conexao->prepare(
            "DELETE FROM login_tentativas WHERE chave = ?"
        );
        $stmt->execute([$chave]);
    }

    private function chaveTentativaLogin($email, $ip)
    {
        return hash('sha256', strtolower(trim((string) $email)) . "\0" . (string) $ip);
    }

    private function garantirTabelaTentativasLogin($conexao)
    {
        if (self::$tabelaTentativasLoginCriada) {
            return;
        }

        $conexao->exec(
            "CREATE TABLE IF NOT EXISTS login_tentativas (
                chave CHAR(64) NOT NULL PRIMARY KEY,
                tentativas TINYINT UNSIGNED NOT NULL DEFAULT 0,
                bloqueado_ate DATETIME DEFAULT NULL
            ) ENGINE=InnoDB"
        );
        self::$tabelaTentativasLoginCriada = true;
    }

    public function buscarPorId($id)
    {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT * FROM login WHERE idUsuario = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar($id, $nome, $email, $senha = null)
    {
        $conexao = Conexao::getConexao();

        if (!empty($senha)) {

            $stmt = $conexao->prepare(
                "UPDATE login
                 SET nomeUsuario = ?, Email = ?, Senha = ?
                 WHERE idUsuario = ?"
            );

            $stmt->execute([
                $nome,
                $email,
                $senha,
                $id
            ]);

        } else {

            $stmt = $conexao->prepare(
                "UPDATE login
                 SET nomeUsuario = ?, Email = ?
                 WHERE idUsuario = ?"
            );

            $stmt->execute([
                $nome,
                $email,
                $id
            ]);
        }

        return "ok";
    }

    // true se o e-mail já pertence a alguma conta (ignorando a conta $ignorarId).
    public function emailEmUso($email, $ignorarId = null)
    {
        $conexao = Conexao::getConexao();

        $sql = "SELECT COUNT(*) FROM login WHERE Email = ?";
        $parametros = [$email];

        if ($ignorarId !== null) {
            $sql .= " AND idUsuario <> ?";
            $parametros[] = (int) $ignorarId;
        }

        $stmt = $conexao->prepare($sql);
        $stmt->execute($parametros);

        return (int) $stmt->fetchColumn() > 0;
    }

    /*
     * Exclusão definitiva da conta (RF-5 / LGPD).
     * As denúncias do usuário precisam sair antes, porque a tabela problemas
     * tem chave estrangeira para login sem ON DELETE CASCADE; sem isso o banco
     * recusava a exclusão de quem já tinha registrado algum problema.
     * Mensagens, notificações e tokens saem pelo CASCADE do banco.
     */
    public function excluir($id)
    {
        $conexao = Conexao::getConexao();
        $arquivos = [];

        try {
            $conexao->beginTransaction();

            $stmt = $conexao->prepare("SELECT imagem FROM problemas WHERE usuarioid = ? AND imagem IS NOT NULL AND imagem <> ''");
            $stmt->execute([$id]);
            foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $imagem) {
                $anexos = json_decode($imagem, true);
                if (is_array($anexos)) {
                    foreach ($anexos as $anexo) {
                        $arquivos[] = $anexo;
                    }
                } else {
                    $arquivos[] = $imagem;
                }
            }

            $stmt = $conexao->prepare("DELETE FROM problemas WHERE usuarioid = ?");
            $stmt->execute([$id]);

            $stmt = $conexao->prepare("DELETE FROM login WHERE idUsuario = ?");
            $stmt->execute([$id]);

            $conexao->commit();
        } catch (Throwable $erro) {
            if ($conexao->inTransaction()) {
                $conexao->rollBack();
            }
            error_log('Falha ao excluir a conta ' . (int) $id . ': ' . $erro->getMessage());
            return false;
        }

        // Remove os anexos de denúncias que eram só desse usuário.
        foreach ($arquivos as $relativo) {
            if (!preg_match('~^(img|video)/(problemas/)?[A-Za-z0-9._-]+$~', $relativo)) {
                continue;
            }
            $caminho = __DIR__ . '/../view/' . $relativo;
            if (is_file($caminho) && !unlink($caminho)) {
                error_log('Não foi possível remover o arquivo ' . $caminho);
            }
        }

        return true;
    }
}

?>