<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$historico = $historico ?? [];
$problema = $problema ?? [];

function rotuloCampoHistorico($campo) {
    return [
        'criacao' => 'Criação',
        'exclusao' => 'Exclusão',
        'status' => 'Status',
        'prioridade' => 'Prioridade',
        'observacoes' => 'Observação'
    ][$campo] ?? ucfirst($campo);
}

function valorHistorico($valor, $campo) {
    if ($valor === null || $valor === '') {
        return 'Não informado';
    }

    if (in_array($campo, ['status', 'prioridade'], true)) {
        return ucfirst(str_replace('_', ' ', $valor));
    }

    return $valor;
}
?>

<?php include 'header.php'; ?>

<section class="screen">
    <div class="dashboard-container max-width-large">
        <div class="welcome-box">
            <h2>Histórico da denúncia</h2>
            <p><?= htmlspecialchars($problema['codigoDenuncia'] ?? '') ?> - <?= htmlspecialchars($problema['titulo'] ?? '') ?></p>

            <div class="table-responsive">
                <table class="tabela-dados">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Usuário</th>
                            <th>Alteração</th>
                            <th>Anterior</th>
                            <th>Novo valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($historico): ?>
                            <?php foreach ($historico as $evento): ?>
                                <tr>
                                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($evento['data_evento']))) ?></td>
                                    <td><?= htmlspecialchars($evento['nomeUsuario'] ?? 'Usuário removido') ?></td>
                                    <td><?= htmlspecialchars(rotuloCampoHistorico($evento['campo'])) ?></td>
                                    <td><?= nl2br(htmlspecialchars(valorHistorico($evento['valor_anterior'], $evento['campo']))) ?></td>
                                    <td><?= nl2br(htmlspecialchars(valorHistorico($evento['valor_novo'], $evento['campo']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Nenhum evento registrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <a href="../controller/routes.php?acao=listarProblemas" class="btn-secondary btn-block">Voltar às denúncias</a>
        </div>
    </div>
</section>
