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

    <link rel="stylesheet" href="style.css">

    <style>

        .dashboard-container{
            width: 100%;
            max-width: 1100px;
            margin: auto;
        }

        .welcome-box{
            background: rgba(242, 232, 213, 0.92);
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        .welcome-box h2{
            margin-bottom: 10px;
            color: #1F4D36;
        }

        .welcome-box p{
            color: #555;
            margin-bottom: 30px;
        }

        .cards{
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .card{
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: 0.3s;
            text-align: center;
        }

        .card:hover{
            transform: translateY(-5px);
        }

        .card h3{
            color: #1F4D36;
            margin-bottom: 10px;
        }

        .card p{
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .card a{
            display: inline-block;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-green{
            background: #3E7C47;
            color: white;
        }

        .btn-orange{
            background: #E48C2A;
            color: white;
        }

        .btn-blue{
            background: #2F6FED;
            color: white;
        }

        .btn-red{
            background: #D64545;
            color: white;
        }

        .card a:hover{
            transform: scale(1.03);
            filter: brightness(1.05);
        }

    </style>
</head>

<body>

<div class="fixed-logo">
    <img src="logo.png" alt="Logo Meu Bairro Melhor">
</div>

<section class="screen">

    <div class="dashboard-container">

        <div class="welcome-box">

            <h2>
                Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?>!
            </h2>

            <p>
                Gerencie seu perfil e registre problemas urbanos do seu bairro.
            </p>

            <div class="cards">

                <!-- CARD PERFIL -->
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

                <!-- CARD PROBLEMAS -->
                <div class="card">

                    <h3>Registrar Problema</h3>

                    <p>
                        Cadastre um problema urbano encontrado no bairro.
                    </p>

                    <a href="../view/problemaForm.php"
                       class="btn-blue">
                        Novo Problema
                    </a>

                </div>

                <!-- CARD LISTA -->
                <div class="card">

                    <h3>Problemas Registrados</h3>

                    <p>
                        Visualize os problemas cadastrados no sistema.
                    </p>

                    <a href="../controller/routes.php?acao=listarProblemas"
                       class="btn-orange">
                        Ver Problemas
                    </a>

                </div>

                <!-- CARD SAIR -->
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