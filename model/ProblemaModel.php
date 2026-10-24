<?php

require_once __DIR__.'/Conexao.php';

    class ProblemaModel {

        public function inserir(
        $titulo,
        $descricao,
        $imagem,
        $categoriaid,
        $subcategoriaid,
        $usuarioId,
        $latitude = null,
        $longitude = null,
        $prioridade = 'media',
        $bairro = null
    ) {
        $conexao = Conexao::getConexao();
        $conexao->beginTransaction();

        try {
            // Insere primeiro para obter o ID interno
            $stmt = $conexao->prepare(
                "INSERT INTO problemas
                (titulo, descricao, imagem, categoriaid, subcategoriaid, usuarioid, latitude, longitude, prioridade, bairro)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $titulo, $descricao, $imagem, $categoriaid, $subcategoriaid,
                $usuarioId, $latitude, $longitude, $prioridade, $bairro
            ]);

            // ID interno gerado pelo banco
            $id = $conexao->lastInsertId();

            // Código público da denúncia
            $codigoDenuncia = 'MBM-' . str_pad($id, 6, '0', STR_PAD_LEFT);

            // Atualiza o código da denúncia
            $stmt = $conexao->prepare(
                "UPDATE problemas SET codigoDenuncia = ? WHERE id = ?"
            );

            $stmt->execute([$codigoDenuncia, $id]);
            $this->registrarHistorico($id, $codigoDenuncia, $usuarioId, 'criacao', null, 'Denúncia criada', $conexao);
            $conexao->commit();

            return $codigoDenuncia;
        } catch (Throwable $erro) {
            $conexao->rollBack();
            throw $erro;
        }
    }

    public function listarTodos($usuarioId = null, $orderBy = 'p.dataproblema', $orderDir = 'DESC', $bairro = null) {

        $conexao = Conexao::getConexao();

        $allowedOrder = [
            'data' => 'p.dataproblema',
            'prioridade' => 'p.prioridade',
            'titulo' => 'p.titulo'
        ];
        $column = $allowedOrder[$orderBy] ?? 'p.dataproblema';
        $dir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        $sql = "SELECT
                p.*,
                c.nome AS categoriaNome,
                s.nome AS subcategoriaNome,
                l.nomeUsuario
            FROM problemas p
            INNER JOIN categorias c
                ON p.categoriaid = c.id
            INNER JOIN subcategorias s
                ON p.subcategoriaid = s.id
            INNER JOIN login l
                ON p.usuarioId = l.idUsuario
            ";

        $parametros = [];

        if ($usuarioId !== null) {
            $sql .= " WHERE p.usuarioid = ?";
            $parametros[] = $usuarioId;
        }

        $sql .= " ORDER BY $column $dir";

        $stmt = $conexao->prepare($sql);

        $stmt->execute($parametros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {

        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT *
             FROM problemas
             WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function excluir($id, $usuarioId) {

        $conexao = Conexao::getConexao();
        $conexao->beginTransaction();

        try {
            $problema = $this->buscarPorId($id);
            if (!$problema) {
                $conexao->rollBack();
                return false;
            }

            $this->registrarHistorico($id, $problema['codigoDenuncia'], $usuarioId, 'exclusao', 'Denúncia existente', 'Denúncia excluída', $conexao);
            $stmt = $conexao->prepare("DELETE FROM problemas WHERE id = ?");
            $stmt->execute([$id]);
            $conexao->commit();

            return $stmt->rowCount() > 0;
        } catch (Throwable $erro) {
            $conexao->rollBack();
            throw $erro;
        }
    }

    public function cancelar($id, $usuarioId) {
        $conexao = Conexao::getConexao();
        return $this->atualizarCampo($id, $usuarioId, 'status', 'cancelada', "usuarioid = ? AND status = 'aberta'", [$usuarioId]);
    }

    public function atualizarStatus($id, $status, $usuarioId) {
        return $this->atualizarCampo($id, $usuarioId, 'status', $status);
    }

    public function atualizarPrioridade($id, $prioridade, $usuarioId) {
        $prioridadesValidas = ['baixa', 'media', 'alta', 'critica'];
        if (!in_array($prioridade, $prioridadesValidas)) {
            return false;
        }

        return $this->atualizarCampo($id, $usuarioId, 'prioridade', $prioridade);
    }

    public function buscar($texto, $orderBy = 'p.dataproblema', $orderDir = 'DESC') {

        $conexao = Conexao::getConexao();

        $allowedOrder = [
            'data' => 'p.dataproblema',
            'prioridade' => 'p.prioridade',
            'titulo' => 'p.titulo'
        ];
        $column = $allowedOrder[$orderBy] ?? 'p.dataproblema';
        $dir = strtoupper($orderDir) === 'ASC' ? 'ASC' : 'DESC';

        $busca = "%" . $texto . "%";

        $stmt = $conexao->prepare(
            "SELECT
                p.*,
                c.nome AS categoriaNome,
                s.nome AS subcategoriaNome,
                l.nomeUsuario
            FROM problemas p
            INNER JOIN categorias c
                ON p.categoriaid = c.id
            INNER JOIN subcategorias s
                ON p.subcategoriaid = s.id
            INNER JOIN login l
                ON p.usuarioId = l.idUsuario
            WHERE p.titulo LIKE ?
                OR p.descricao LIKE ?
                OR c.nome LIKE ?
                OR s.nome LIKE ?
            ORDER BY $column $dir"
        );

        $stmt->execute([
            $busca,
            $busca,
            $busca,
            $busca
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function totalProblemas() {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare("SELECT COUNT(*) as total FROM problemas");
        $stmt->execute();
        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function problemasPorCategoria() {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare(
            "SELECT 
                c.id,
                c.nome,
                COUNT(p.id) as total
            FROM categorias c
            LEFT JOIN problemas p ON c.id = p.categoriaid
            GROUP BY c.id, c.nome
            ORDER BY total DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function problemasPorStatus() {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare(
            "SELECT 
                status,
                COUNT(*) as total
            FROM problemas
            GROUP BY status
            ORDER BY total DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function problemasPorPrioridade() {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare(
            "SELECT 
                prioridade,
                COUNT(*) as total
            FROM problemas
            GROUP BY prioridade
            ORDER BY CASE 
                WHEN prioridade = 'critica' THEN 1
                WHEN prioridade = 'alta' THEN 2
                WHEN prioridade = 'media' THEN 3
                WHEN prioridade = 'baixa' THEN 4
                ELSE 5
            END"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function problemasRecentesPorMes($meses = 6) {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare(
            "SELECT 
                DATE_FORMAT(dataproblema, '%Y-%m') as mes,
                COUNT(*) as total
            FROM problemas
            WHERE dataproblema >= DATE_SUB(NOW(), INTERVAL ? MONTH)
            GROUP BY DATE_FORMAT(dataproblema, '%Y-%m')
            ORDER BY mes ASC"
        );
        $stmt->execute([$meses]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function problemasRecentes($limite = 5) {
        $conexao = Conexao::getConexao();
        $limite = max(1, min(100, (int) $limite));
        $stmt = $conexao->prepare(
            "SELECT
                p.*,
                c.nome AS categoriaNome,
                s.nome AS subcategoriaNome,
                l.nomeUsuario
            FROM problemas p
            INNER JOIN categorias c ON p.categoriaid = c.id
            INNER JOIN subcategorias s ON p.subcategoriaid = s.id
            INNER JOIN login l ON p.usuarioId = l.idUsuario
            ORDER BY p.dataproblema DESC
            LIMIT {$limite}"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function estatisticasGerais() {
        $conexao = Conexao::getConexao();
        
        $stats = [
            'total' => $this->totalProblemas(),
            'aberta' => 0,
            'em_analise' => 0,
            'resolvida' => 0,
            'cancelada' => 0,
            'pendente' => 0,
            'critica' => 0,
            'alta' => 0,
            'media' => 0,
            'baixa' => 0
        ];

        $statusCounts = $this->problemasPorStatus();
        foreach ($statusCounts as $item) {
            $stats[$item['status']] = (int) $item['total'];
        }

        $prioridadeCounts = $this->problemasPorPrioridade();
        foreach ($prioridadeCounts as $item) {
            $stats[$item['prioridade']] = (int) $item['total'];
        }

        // RF-38: "pendente" = ainda aguardando solução (aberta ou em análise).
        // Não entram aqui as resolvidas nem as canceladas.
        $stats['pendente'] = $stats['aberta'] + $stats['em_analise'];

        return $stats;
    }

    public function adicionarObservacao($id, $observacoes, $usuarioId) {
        return $this->atualizarCampo($id, $usuarioId, 'observacoes', $observacoes, null, [], true);
    }

    public function listarHistorico($id) {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare(
            "SELECT h.*, l.nomeUsuario
             FROM historico_problemas h
             LEFT JOIN login l ON h.usuarioid = l.idUsuario
             WHERE h.problemaid = ?
             ORDER BY h.data_evento DESC, h.id DESC"
        );
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function atualizarCampo($id, $usuarioId, $campo, $novoValor, $condicao = null, $condicaoParametros = [], $observacao = false) {
        $conexao = Conexao::getConexao();
        $conexao->beginTransaction();

        try {
            $problema = $this->buscarPorId($id);
            if (!$problema || ($condicao && !$this->condicaoValida($id, $condicaoParametros))) {
                $conexao->rollBack();
                return false;
            }

            $valorAnterior = $problema[$campo] ?? null;
            if ((string) $valorAnterior === (string) $novoValor) {
                $conexao->rollBack();
                return false;
            }

            $sql = "UPDATE problemas SET {$campo} = ?" . ($observacao ? ', data_observacao = NOW()' : '') . " WHERE id = ?";
            if ($condicao) {
                $sql .= " AND {$condicao}";
            }
            $stmt = $conexao->prepare($sql);
            $stmt->execute(array_merge([$novoValor, $id], $condicaoParametros));
            if ($stmt->rowCount() === 0) {
                $conexao->rollBack();
                return false;
            }

            $this->registrarHistorico($id, $problema['codigoDenuncia'], $usuarioId, $campo, $valorAnterior, $novoValor, $conexao);
            $conexao->commit();
            return true;
        } catch (Throwable $erro) {
            $conexao->rollBack();
            throw $erro;
        }
    }

    private function condicaoValida($id, $parametros) {
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare("SELECT usuarioid, status FROM problemas WHERE id = ?");
        $stmt->execute([$id]);
        $problema = $stmt->fetch(PDO::FETCH_ASSOC);
        return $problema && (int) $problema['usuarioid'] === (int) ($parametros[0] ?? 0) && $problema['status'] === 'aberta';
    }

    private function registrarHistorico($problemaId, $codigoDenuncia, $usuarioId, $campo, $valorAnterior, $valorNovo, $conexao) {
        $stmt = $conexao->prepare(
            "INSERT INTO historico_problemas
             (problemaid, codigo_denuncia, usuarioid, campo, valor_anterior, valor_novo)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$problemaId, $codigoDenuncia, $usuarioId, $campo, $valorAnterior, $valorNovo]);
    }

    public function obterObservacoes($id) {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT observacoes, data_observacao
             FROM problemas
             WHERE id = ?"
        );

        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarComObservacoes($id) {
        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare(
            "SELECT 
                p.*,
                c.nome AS categoriaNome,
                s.nome AS subcategoriaNome,
                l.nomeUsuario
            FROM problemas p
            INNER JOIN categorias c ON p.categoriaid = c.id
            INNER JOIN subcategorias s ON p.subcategoriaid = s.id
            INNER JOIN login l ON p.usuarioid = l.idUsuario
            WHERE p.id = ?"
        );

        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>