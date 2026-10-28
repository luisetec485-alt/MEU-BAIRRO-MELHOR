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
        $bairro = null,
        $rua = null
    ) {
        $conexao = Conexao::getConexao();
        $conexao->beginTransaction();

        try {
            // Insere primeiro para obter o ID interno
            $stmt = $conexao->prepare(
                "INSERT INTO problemas
                (titulo, descricao, imagem, categoriaid, subcategoriaid, usuarioid, latitude, longitude, prioridade, bairro, rua)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $anexos = is_array($imagem) ? $imagem : ($imagem ? [$imagem] : []);
            $stmt->execute([
                $titulo, $descricao, $anexos ? json_encode($anexos, JSON_UNESCAPED_SLASHES) : null, $categoriaid, $subcategoriaid,
                $usuarioId, $latitude, $longitude, $prioridade, $bairro, $rua
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

    public function listarTodos($usuarioId = null, $orderBy = 'data', $orderDir = 'DESC', $bairro = null) {
        $filtros = [];
        if ($bairro !== null && $bairro !== '') {
            $filtros['bairro'] = $bairro;
        }

        return $this->filtrar($filtros, $usuarioId, $orderBy, $orderDir);
    }

    /*
     * 1.6 - Consulta dinâmica: monta o WHERE de acordo com os filtros
     * escolhidos na tela de denúncias e consulta direto no banco.
     * Todos os valores vão como parâmetros (prepared statement); a ordenação
     * só aceita colunas de uma lista fixa.
     *
     * $filtros aceita: texto, categoriaid, subcategoriaid, status,
     * prioridade e bairro. Os únicos status são aguardando encaminhamento
     * e enviado.
     */
    public function filtrar(array $filtros = [], $usuarioId = null, $ordem = 'data', $direcao = 'DESC') {

        $conexao = Conexao::getConexao();

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
                ON p.usuarioid = l.idUsuario";

        $condicoes = [];
        $parametros = [];

        if ($usuarioId !== null) {
            $condicoes[] = 'p.usuarioid = ?';
            $parametros[] = (int) $usuarioId;
        }

        $texto = trim((string) ($filtros['texto'] ?? ''));
        if ($texto !== '') {
            $busca = '%' . $texto . '%';
            $condicoes[] = '(p.titulo LIKE ? OR p.descricao LIKE ? OR p.codigoDenuncia LIKE ?'
                . ' OR p.bairro LIKE ? OR c.nome LIKE ? OR s.nome LIKE ?)';
            array_push($parametros, $busca, $busca, $busca, $busca, $busca, $busca);
        }

        if (!empty($filtros['categoriaid'])) {
            $condicoes[] = 'p.categoriaid = ?';
            $parametros[] = (int) $filtros['categoriaid'];
        }

        if (!empty($filtros['subcategoriaid'])) {
            $condicoes[] = 'p.subcategoriaid = ?';
            $parametros[] = (int) $filtros['subcategoriaid'];
        }

        if (!empty($filtros['status'])
            && in_array($filtros['status'], [
                'aguardando_encaminhamento',
                'em_analise',
                'recusada',
                'enviado'
            ], true)
        ) {
            $condicoes[] = 'p.status = ?';
            $parametros[] = $filtros['status'];
        }

        if (!empty($filtros['prioridade'])
            && in_array($filtros['prioridade'], ['baixa', 'media', 'alta', 'critica'], true)
        ) {
            $condicoes[] = 'p.prioridade = ?';
            $parametros[] = $filtros['prioridade'];
        }

        $bairro = trim((string) ($filtros['bairro'] ?? ''));
        if ($bairro !== '') {
            $condicoes[] = 'p.bairro LIKE ?';
            $parametros[] = '%' . $bairro . '%';
        }

        if ($condicoes) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }

        $dir = strtoupper((string) $direcao) === 'ASC' ? 'ASC' : 'DESC';
        $ordenacoes = [
            'data' => "p.dataproblema {$dir}, p.id {$dir}",
            'titulo' => "p.titulo {$dir}",
            // Crítica > Alta > Média > Baixa (DESC mostra as mais urgentes primeiro)
            'prioridade' => "CASE p.prioridade
                    WHEN 'critica' THEN 4
                    WHEN 'alta' THEN 3
                    WHEN 'media' THEN 2
                    ELSE 1
                END {$dir}, p.dataproblema DESC"
        ];
        $sql .= ' ORDER BY ' . ($ordenacoes[$ordem] ?? $ordenacoes['data']);

        $stmt = $conexao->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Bairros já informados nas denúncias, para sugerir no filtro.
    public function listarBairros($usuarioId = null) {
        $conexao = Conexao::getConexao();

        $sql = "SELECT DISTINCT bairro
                FROM problemas
                WHERE bairro IS NOT NULL AND bairro <> ''";
        $parametros = [];

        if ($usuarioId !== null) {
            $sql .= ' AND usuarioid = ?';
            $parametros[] = (int) $usuarioId;
        }

        $sql .= ' ORDER BY bairro ASC';

        $stmt = $conexao->prepare($sql);
        $stmt->execute($parametros);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
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

    public function atualizarStatus($id, $status, $usuarioId) {
        $statusValidos = [
            'aguardando_encaminhamento',
            'em_analise',
            'recusada',
            'enviado'
        ];

        if (!in_array($status, $statusValidos, true)) {
            return false;
        }

        return $this->atualizarCampo($id, $usuarioId, 'status', $status);
    }

    public function atualizarPrioridade($id, $prioridade, $usuarioId) {
        $prioridadesValidas = ['baixa', 'media', 'alta', 'critica'];
        if (!in_array($prioridade, $prioridadesValidas)) {
            return false;
        }

        return $this->atualizarCampo($id, $usuarioId, 'prioridade', $prioridade);
    }

    public function buscar($texto, $orderBy = 'data', $orderDir = 'DESC') {
        return $this->filtrar(['texto' => $texto], null, $orderBy, $orderDir);
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
            'aguardando_encaminhamento' => 0,
            'em_analise' => 0,
            'recusada' => 0,
            'enviado' => 0,
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
        if (!$problema) {
            return false;
        }

        if ($parametros === []) {
            return true;
        }

        return (int) $problema['usuarioid'] === (int) ($parametros[0] ?? 0)
            && $problema['status'] === 'aguardando_encaminhamento';
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