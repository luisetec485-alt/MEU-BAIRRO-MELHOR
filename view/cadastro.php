<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Meu Bairro Melhor</title>
    <link rel="stylesheet" href="../view/css/style.css">
</head>
<body>

<div class="fixed-logo">
    <img src="img/logo.png" alt="Logo Meu Bairro Melhor">
</div>

<section class="screen">
    <div class="dashboard-container max-width-small">
        <div class="welcome-box">
            <h2>Cadastro de Usuário</h2>
            <p>Crie sua conta para começar a ajudar seu bairro.</p>

            <form method="POST" action="../controller/routes.php?acao=cadastrar" class="form-moderno">
                
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" required
                           oninvalid="this.setCustomValidity('Dado Requerido')"
                           oninput="this.setCustomValidity('')">
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required
                           oninvalid="this.setCustomValidity('Dado Requerido')"
                           oninput="this.setCustomValidity('')">
                </div>

                <div class="form-group">
                    <label for="senha">Senha:</label>
                    <input type="password" id="senha" name="senha" required
                           oninvalid="this.setCustomValidity('Dado Requerido')"
                           oninput="this.setCustomValidity('')">
                </div>

                <button type="submit" class="btn-green btn-block">Cadastrar</button>
            </form>

            <a href="login.php" class="btn-secondary btn-block">Já tem conta? Faça login</a>
            
        </div>
    </div>
</section>

</body>
</html>