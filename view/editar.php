<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário - Meu Bairro Melhor</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estilos específicos para a página de edição */
        .box {
            background: rgba(242, 232, 213, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            width: 100%;
            max-width: 500px;
            margin: 20px;
            animation: fadeIn 0.5s ease;
        }

        .box h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #1F4D36;
        }

        .box label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            font-weight: 600;
            color: #1F4D36;
        }

        .box input[type="text"],
        .box input[type="email"],
        .box input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
        }

        .box input:focus {
            border-color: #E48C2A;
            outline: none;
        }

        .btn-salvar {
            width: 100%;
            padding: 14px;
            margin-top: 20px;
            background-color: #3E7C47;
            color: white;
            border: none;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-salvar:hover {
            transform: scale(1.02);
            filter: brightness(1.05);
        }

        .btn-excluir {
            width: 100%;
            padding: 14px;
            margin-top: 15px;
            background-color: #D64545;
            color: white;
            border: none;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-excluir:hover {
            transform: scale(1.02);
            filter: brightness(1.05);
        }

        .btn-voltar {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #5A2E1C;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-voltar:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="fixed-logo">
    <img src="logo.png" alt="Logo Meu Bairro Melhor">
</div>

<section class="screen">
    <div class="box">
        <h2>Editar Dados</h2>

        <form method="POST" action="../controller/routes.php?acao=atualizar">
            <input type="hidden" name="id" value="<?php echo $usuario['idUsuario']; ?>">

            <label>Nome:</label>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nomeUsuario']); ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['Email']); ?>" required>

            <label>Nova Senha:</label>
            <input type="password" name="senha" placeholder="Deixe em branco para manter">

            <button type="submit" class="btn-salvar">Salvar Alterações</button>
        </form>

        <form method="POST" action="../controller/routes.php?acao=excluir" 
              onsubmit="return confirm('Tem certeza que deseja excluir sua conta?');">
            <input type="hidden" name="id" value="<?php echo $usuario['idUsuario']; ?>">
            <button type="submit" class="btn-excluir">Excluir Conta</button>
        </form>

        <a href="../view/dashboard.php" class="btn-voltar">Voltar ao Dashboard</a>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>