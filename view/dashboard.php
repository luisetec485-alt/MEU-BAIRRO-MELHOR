<?php
session_start();

if(!isset($_SESSION['id'])){
    header("Location: ../view/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Meu Bairro Melhor</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="fixed-logo">
    <img src="../view/img/logo.png" alt="Logo Meu Bairro Melhor">
</div>

<br><br>

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

            <div class="cards">

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

</body>
</html>