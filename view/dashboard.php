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
    .stat-box.aberta { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
    .stat-box.resolvida { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
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

        <div class="perfil-foto-container" style="margin-bottom: 20px;">
                <?php if (isset($_SESSION['foto']) && !empty($_SESSION['foto'])): ?>
                    <img src="<?php echo $_SESSION['foto']; ?>" alt="Foto de Perfil" 
                         style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #1F4D36; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                <?php else: ?>
                    <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="Sem Foto" 
                         style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #5A2E1C; opacity: 0.7;">
                <?php endif; ?>
            </div>
            <h2>Olá, <?php echo htmlspecialchars($_SESSION['nomeUsuario'] ?? $_SESSION['nome']); ?>!</h2>
            <p>Bem-vindo ao painel do Meu Bairro Melhor.</p>

            <p>
                Gerencie seu perfil e registre problemas urbanos do seu bairro.
            </p>

            <!-- Estatísticas Gerais -->
            <h3 style="margin-top: 30px; color: #333;">Estatísticas Gerais do Sistema</h3>
            <div class="stats-container">
                <div class="stat-box total">
                    <h4>Total de Denúncias</h4>
                    <div class="number"><?= $stats['total'] ?></div>
                </div>
                <div class="stat-box aberta">
                    <h4>Abertas</h4>
                    <div class="number"><?= $stats['aberta'] ?></div>
                </div>
                <div class="stat-box" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <h4>Em Análise</h4>
                    <div class="number"><?= $stats['em_analise'] ?></div>
                </div>
                <div class="stat-box resolvida">
                    <h4>Resolvidas</h4>
                    <div class="number"><?= $stats['resolvida'] ?></div>
                </div>
                <div class="stat-box pendente">
                    <h4>Pendentes</h4>
                    <div class="number"><?= $stats['pendente'] ?></div>
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
                        <p>Gerencie denúncias, usuários e status do sistema.</p>
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

                <div class="card">

                    <h3>Registrar Problema</h3>

                    <p>
                        Cadastre um problema urbano encontrado no bairro.
                    </p>

                    <a href="../controller/routes.php?acao=formProblema"
                       class="btn-blue">
                        Novo Problema
                    </a>

                </div>

                <div class="card">

                    <h3>Problemas Registrados</h3>

                    <p>
                        Visualize todos os problemas cadastrados.
                    </p>

                    <a href="../controller/routes.php?acao=listarProblemas"
                       class="btn-orange">
                        Ver Problemas
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