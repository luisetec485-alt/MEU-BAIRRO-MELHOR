<?php

require_once 'Conexao.php';

class UsuarioModel
{

    public function cadastrar($nome, $email, $senha)
    {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare("SELECT * FROM login WHERE Email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
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
    public function uploadFoto()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['id']) && isset($_FILES['foto'])) {

            $idUsuario = $_SESSION['id'];

            $extensao = strtolower(
                pathinfo(
                    $_FILES['foto']['name'],
                    PATHINFO_EXTENSION
                )
            );

            $extensoesPermitidas = ['jpg', 'jpeg', 'png'];

            if (in_array($extensao, $extensoesPermitidas)) {

                $nomeFoto = uniqid() . "." . $extensao;

                $diretorioDestino = __DIR__ . "/../view/img/" . $nomeFoto;

                if (
                    move_uploaded_file(
                        $_FILES['foto']['tmp_name'],
                        $diretorioDestino
                    )
                ) {

                    $caminhoBanco = "img/" . $nomeFoto;

                    $conexao = Conexao::getConexao();

                    $stmt = $conexao->prepare(
                        "UPDATE login
                         SET foto = ?
                         WHERE idUsuario = ?"
                    );

                    $resultado = $stmt->execute([
                        $caminhoBanco,
                        $idUsuario
                    ]);

                    if ($resultado) {

                        $_SESSION['foto'] = $caminhoBanco;

                        echo "
                        <script>
                            alert('Foto de perfil atualizada com sucesso!');
                            window.location.href = '../view/dashboard.php';
                        </script>
                        ";
                        exit;
                    } else {
                        echo "
                        <script>
                            alert('Erro ao salvar no banco.');
                            history.back();
                        </script>
                        ";
                    }

                } else {
                    echo "
                    <script>
                        alert('Erro ao mover o arquivo.');
                        history.back();
                    </script>
                    ";
                }

            } else {
                echo "
                <script>
                    alert('Extensão inválida! Use JPG, JPEG ou PNG.');
                    history.back();
                </script>
                ";
            }

        } else {
            echo "
            <script>
                alert('Nenhum arquivo enviado.');
                history.back();
            </script>
            ";
        }
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

    public function salvarFotoPerfil($usuarioId, $endpasta)
    {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT id FROM perfis WHERE usuarioId = ?"
        );

        $stmt->execute([$usuarioId]);

        if ($stmt->rowCount() > 0) {

            $stmt = $conexao->prepare(
                "UPDATE perfis
                 SET endpasta = ?
                 WHERE usuarioId = ?"
            );

            return $stmt->execute([
                $endpasta,
                $usuarioId
            ]);

        } else {

            $stmt = $conexao->prepare(
                "INSERT INTO perfis (endpasta, usuarioId)
                 VALUES (?, ?)"
            );

            return $stmt->execute([
                $endpasta,
                $usuarioId
            ]);
        }
    }

    public function buscarFotoPerfil($usuarioId)
    {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT endpasta
             FROM perfis
             WHERE usuarioId = ?"
        );

        $stmt->execute([$usuarioId]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado
            ? $resultado['endpasta']
            : 'img/default-avatar.png';
    }

    public function excluir($id)
    {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "DELETE FROM login
             WHERE idUsuario = ?"
        );

        $stmt->execute([$id]);

        return "ok";
    }
}

?>