<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

if ((int) ($_SESSION['nivelAcesso'] ?? 1) !== 0) {
    http_response_code(403);
    echo 'Acesso negado.';
    exit;
}

require_once __DIR__.'/../model/ProblemaModel.php';

$problemaModel = new ProblemaModel();
$stats = $problemaModel->estatisticasGerais();
$categorias = $problemaModel->problemasPorCategoria();
$status = $problemaModel->problemasPorStatus();
$prioridade = $problemaModel->problemasPorPrioridade();
$recentesPorMes = $problemaModel->problemasRecentesPorMes(6);
$recentes = $problemaModel->problemasRecentes(5);

// Preparar dados para gráficos
$categoriasLabels = array_map(function($c) { return htmlspecialchars($c['nome']); }, $categorias);
$categoriasData = array_map(function($c) { return (int)$c['total']; }, $categorias);

$statusLabels = array_map(function($s) { 
    $labels = [
        'aberta' => 'Aberta',
        'em_analise' => 'Em análise',
        'resolvida' => 'Resolvida',
        'cancelada' => 'Cancelada'
    ];
    return $labels[$s['status']] ?? ucfirst($s['status']);
}, $status);
$statusData = array_map(function($s) { return (int)$s['total']; }, $status);

$prioridadeLabels = array_map(function($p) { 
    $labels = ['baixa' => 'Baixa', 'media' => 'Média', 'alta' => 'Alta', 'critica' => 'Crítica'];
    return $labels[$p['prioridade']] ?? ucfirst($p['prioridade']);
}, $prioridade);
$prioridadeData = array_map(function($p) { return (int)$p['total']; }, $prioridade);

$mesesLabels = array_map(function($m) { return $m['mes']; }, $recentesPorMes);
$mesesData = array_map(function($m) { return (int)$m['total']; }, $recentesPorMes);

$pageTitle = 'Painel Administrativo - Meu Bairro Melhor';
include __DIR__ . '/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .stat-card.total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-card.aberta { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-card.resolvida { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .stat-card.em-analise { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .stat-card.cancelada { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
    .stat-card.pendente { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
    .stat-card h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        opacity: 0.9;
    }
    .stat-card .number {
        font-size: 32px;
        font-weight: bold;
        margin: 10px 0;
    }
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .chart-container {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: relative;
        height: 350px;
    }
    .chart-container h3 {
        margin-top: 0;
        color: #333;
        font-size: 16px;
    }
    .recentes-container {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    .recentes-container h3 {
        margin-top: 0;
        color: #333;
    }
    .recentes-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    .recentes-table th {
        background: #f5f5f5;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #ddd;
    }
    .recentes-table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
    }
    .recentes-table tbody tr:hover {
        background: #f9f9f9;
    }
</style>

<section class="screen">
    <div class="dashboard-container max-width-large">
        <div class="welcome-box">
            <h2>Painel Administrativo - Dashboard</h2>
            <p>Visualize estatísticas e relatórios do sistema.</p>

            <!-- KPIs -->
            <div class="stats-grid">
                <div class="stat-card total">
                    <h3>Total de Denúncias</h3>
                    <div class="number"><?= $stats['total'] ?></div>
                </div>
                <div class="stat-card aberta">
                    <h3>Denúncias Abertas</h3>
                    <div class="number"><?= $stats['aberta'] ?></div>
                </div>
                <div class="stat-card em-analise">
                    <h3>Em Análise</h3>
                    <div class="number"><?= $stats['em_analise'] ?></div>
                </div>
                <div class="stat-card resolvida">
                    <h3>Resolvidas</h3>
                    <div class="number"><?= $stats['resolvida'] ?></div>
                </div>
                <div class="stat-card pendente">
                    <h3>Pendentes</h3>
                    <div class="number"><?= $stats['pendente'] ?></div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="charts-grid">
                <!-- Gráfico de Status -->
                <div class="chart-container">
                    <h3>Denúncias por Status</h3>
                    <canvas id="statusChart"></canvas>
                </div>

                <!-- Gráfico de Categoria -->
                <div class="chart-container">
                    <h3>Denúncias por Categoria</h3>
                    <canvas id="categoriasChart"></canvas>
                </div>

                <!-- Gráfico de Prioridade -->
                <div class="chart-container">
                    <h3>Denúncias por Prioridade</h3>
                    <canvas id="prioridadeChart"></canvas>
                </div>

                <!-- Gráfico de Tendência -->
                <div class="chart-container">
                    <h3>Tendência nos Últimos 6 Meses</h3>
                    <canvas id="tendenciaChart"></canvas>
                </div>
            </div>

            <!-- Denúncias Recentes -->
            <?php if (!empty($recentes)): ?>
            <div class="recentes-container">
                <h3>Denúncias Mais Recentes</h3>
                <table class="recentes-table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Título</th>
                            <th>Categoria</th>
                            <th>Status</th>
                            <th>Prioridade</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentes as $problema): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($problema['codigoDenuncia']) ?></strong></td>
                            <td><?= htmlspecialchars(substr($problema['titulo'], 0, 30)) ?>...</td>
                            <td><?= htmlspecialchars($problema['categoriaNome']) ?></td>
                            <td><?= htmlspecialchars(ucwords(str_replace('_', ' ', $problema['status']))) ?></td>
                            <td><?= htmlspecialchars(ucfirst($problema['prioridade'])) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($problema['dataproblema'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <!-- Menu de Acesso Rápido -->
            <div class="cards">
                <div class="card">
                    <h3>Denúncias</h3>
                    <p>Consulte denúncias e atualize seus status.</p>
                    <a href="../controller/routes.php?acao=listarProblemas" class="btn-orange">Gerenciar denúncias</a>
                </div>

                <div class="card">
                    <h3>Usuários</h3>
                    <p>Consulte os usuários cadastrados no sistema.</p>
                    <a href="../controller/routes.php?acao=relatorioUsuarios" class="btn-blue">Ver usuários</a>
                </div>

                <div class="card">
                    <h3>Categorias</h3>
                    <p>Organize as categorias dos problemas urbanos.</p>
                    <a href="../controller/routes.php?acao=listarCategorias" class="btn-green">Gerenciar categorias</a>
                </div>

                <div class="card">
                    <h3>Subcategorias</h3>
                    <p>Organize as subcategorias da taxonomia.</p>
                    <a href="../controller/routes.php?acao=listarSubcategorias" class="btn-green">Gerenciar subcategorias</a>
                </div>
            </div>

            <a href="../controller/routes.php?acao=dashboard" class="btn-secondary btn-block">Voltar ao dashboard</a>
        </div>
    </div>
</section>

<script>
    // Configuração de cores
    const colors = {
        primary: '#667eea',
        success: '#00f2fe',
        warning: '#fee140',
        danger: '#f5576c',
        info: '#30cfd0'
    };

    // Gráfico de Status
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($statusLabels) ?>,
            datasets: [{
                data: <?= json_encode($statusData) ?>,
                backgroundColor: ['#f5576c', '#fa709a', '#4facfe', '#30cfd0'],
                borderColor: 'white',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico de Categorias
    new Chart(document.getElementById('categoriasChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($categoriasLabels) ?>,
            datasets: [{
                label: 'Quantidade',
                data: <?= json_encode($categoriasData) ?>,
                backgroundColor: '#667eea',
                borderColor: '#667eea',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de Prioridade
    new Chart(document.getElementById('prioridadeChart'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($prioridadeLabels) ?>,
            datasets: [{
                data: <?= json_encode($prioridadeData) ?>,
                backgroundColor: ['#4facfe', '#00f2fe', '#fa709a', '#f5576c'],
                borderColor: 'white',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico de Tendência
    new Chart(document.getElementById('tendenciaChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($mesesLabels) ?>,
            datasets: [{
                label: 'Denúncias por Mês',
                data: <?= json_encode($mesesData) ?>,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>