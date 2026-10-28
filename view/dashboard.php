<?php
require_once __DIR__ . '/../model/Seguranca.php';
Seguranca::exigirLogin();

require_once __DIR__.'/../model/ProblemaModel.php';
require_once __DIR__.'/../model/NotificacaoModel.php';
$problemaModel = new ProblemaModel();
$notifModel = new NotificacaoModel();
$userId = $_SESSION['id'];
$notificacoes = $notifModel->buscarNaoLidas($userId);
$stats = $problemaModel->estatisticasGerais();
$categorias = $problemaModel->problemasPorCategoria();

$categoriasLabels = array_map(function($c) { return htmlspecialchars($c['nome']); }, $categorias);
$categoriasData = array_map(function($c) { return (int)$c['total']; }, $categorias);

$pageTitle = 'Dashboard - Meu Bairro Melhor';
include __DIR__ . '/header.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<style>
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin: 20px 0 30px 0;
    }
    .stat-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .stat-box.total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .stat-box.aberta { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
    .stat-box.resolvida { background: linear-gradient(135deg, #4facfe 0%, #00b894 100%); }
    .stat-box.pendente { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
    .stat-box h4 {
        margin: 0;
        font-size: 12px;
        text-transform: uppercase;
        opacity: 0.9;
        font-weight: 600;
    }
    .stat-box .number {
        font-size: 28px;
        font-weight: bold;
        margin: 8px 0 0 0;
    }
    .chart-box {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .chart-box h3 {
        margin-top: 0;
        color: #333;
        font-size: 16px;
    }
    .chart-small {
        height: 250px;
        position: relative;
    }
</style>

<section class="screen">

    <div class="dashboard-container">

        <div class="welcome-box">

            <h2>Olá, <?php echo htmlspecialchars($_SESSION['nomeUsuario'] ?? $_SESSION['nome']); ?>!</h2>
            <p>Bem-vindo ao painel do Meu Bairro Melhor.</p>

            <p>Gerencie seu perfil e registre problemas urbanos do seu bairro.</p>

            <!-- Estatísticas Gerais -->
            <h3 style="margin-top: 30px; color: #333;">Estatísticas Gerais do Sistema</h3>
            <div class="stats-container">
                <div class="stat-box total">
                    <h4>Total de Denúncias</h4>
                    <div class="number"><?= $stats['total'] ?></div>
                </div>
                <div class="stat-box pendente">
                    <h4>Aguardando encaminhamento</h4>
                    <div class="number"><?= $stats['aguardando_encaminhamento'] ?></div>
                </div>
                <div class="stat-box resolvida">
                    <h4>Enviadas</h4>
                    <div class="number"><?= $stats['enviado'] ?></div>
                </div>
            </div>

            <!-- Gráficos Estatísticos -->
            <div class="chart-box">
                <h3>Denúncias por Categoria</h3>
                <div class="chart-small">
                    <canvas id="categoriasChart"></canvas>
                </div>
            </div>

            <div class="cards">

                <?php if ((int) ($_SESSION['nivelAcesso'] ?? 1) === 0): ?>
                    <div class="card">
                        <h3>Painel Administrativo</h3>
                        <p>Consulte denúncias e registre os encaminhamentos manuais.</p>
                        <a href="../controller/routes.php?acao=painelAdmin" class="btn-red">
                            Acessar painel
                        </a>
                    </div>
                <?php endif; ?>

                <div class="card">

                    <h3>Meu Perfil</h3>

                    <p>
                        Atualize suas informações pessoais e senha.
                    </p>

                    <a href="../controller/routes.php?acao=editar"
                       class="btn-green">
                        Editar Perfil
                    </a>

                </div>

                <?php if ((int) ($_SESSION['nivelAcesso'] ?? 1) !== 0): ?>
                    <div class="card">
                        <h3>Registrar Problema</h3>
                        <p>Cadastre um problema urbano encontrado no bairro.</p>
                        <a href="../controller/routes.php?acao=formProblema" class="btn-blue">
                            Novo Problema
                        </a>
                    </div>
                <?php endif; ?>

                <div class="card">

                    <?php if ((int) ($_SESSION['nivelAcesso'] ?? 1) === 0): ?>
                        <h3>Denúncias Registradas</h3>
                        <p>
                            Pesquise, filtre e gerencie todas as denúncias cadastradas.
                        </p>
                    <?php else: ?>
                        <h3>Minhas Denúncias</h3>
                        <p>
                            Pesquise e acompanhe as denúncias que você registrou.
                        </p>
                    <?php endif; ?>

                    <a href="../controller/routes.php?acao=listarProblemas"
                       class="btn-orange">
                        Ver Denúncias
                    </a>

                </div>

                <?php if ((int) ($_SESSION['nivelAcesso'] ?? 1) === 0): ?>

                <div class="card">

                    <h3>Nova Categoria</h3>

                    <p>
                        Cadastre uma nova categoria para os problemas.
                    </p>

                    <a href="../controller/routes.php?acao=formCategoria"
                       class="btn-blue">
                        Cadastrar Categoria
                    </a>

                </div>

                <div class="card">

                    <h3>Listar Categorias</h3>

                    <p>
                        Visualize todas as categorias cadastradas.
                    </p>

                    <a href="../controller/routes.php?acao=listarCategorias"
                       class="btn-orange">
                        Ver Categorias
                    </a>

                </div>

                <div class="card">

                    <h3>Nova Subcategoria</h3>

                    <p>
                        Cadastre subcategorias vinculadas às categorias.
                    </p>

                    <a href="../controller/routes.php?acao=formSubcategoria"
                       class="btn-blue">
                        Cadastrar Subcategoria
                    </a>

                </div>

                <div class="card">

                    <h3>Listar Subcategorias</h3>

                    <p>
                        Visualize todas as subcategorias cadastradas.
                    </p>

                    <a href="../controller/routes.php?acao=listarSubcategorias"
                       class="btn-orange">
                        Ver Subcategorias
                    </a>

                </div>

                <?php endif; ?>

                <div class="card">

                    <h3>Sair</h3>

                    <p>
                        Encerrar sessão atual com segurança.
                    </p>

                    <a href="../controller/routes.php?acao=logout"
                       class="btn-red">
                        Logout
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>

<script>
    // Gráfico de Categorias
    new Chart(document.getElementById('categoriasChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($categoriasLabels) ?>,
            datasets: [{
                label: 'Quantidade de Denúncias',
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
</script>

<script>
    // Notificações de Status
    const notifs = <?= json_encode($notificacoes) ?>;
    if (notifs && notifs.length > 0) {
        notifs.forEach(function(n) {
            alert(n.mensagem);

            // Marca como lida via API
            fetch('../api/marcarNotificacaoLida.php?id=' + n.id)
                .then(res => res.json())
                .then(data => {
                    if(!data.ok) console.error('Erro ao marcar notificação ' + n.id + ' como lida');
                });
        });
    }
</script>

<?php include __DIR__ . '/footer.php'; ?>