<?php
require_once '../model/UsuarioModel.php';
require_once '../model/Conexao.php';
require_once '../model/MensagemFlash.php';

class UsuarioController {
    
    public function cadastrar() {
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $senha = (string) ($_POST['senha'] ?? '');

        // 4.1 - campos obrigatórios conferidos também no servidor, um de cada vez,
        // para a mensagem dizer exatamente o que falta.
        if ($nome === '') {
            MensagemFlash::redirecionar('../view/cadastro.php', 'Preencha o seu nome.', 'erro');
        }

        if ($email === '') {
            MensagemFlash::redirecionar('../view/cadastro.php', 'Preencha o seu e-mail.', 'erro');
        }

        if ($senha === '') {
            MensagemFlash::redirecionar('../view/cadastro.php', 'Crie uma senha para a sua conta.', 'erro');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            MensagemFlash::redirecionar('../view/cadastro.php', 'Informe um endereço de e-mail válido.', 'erro');
        }

        if (mb_strlen($email) > 100) {
            MensagemFlash::redirecionar('../view/cadastro.php', 'O e-mail deve ter no máximo 100 caracteres.', 'erro');
        }

        if (mb_strlen($nome) > 50) {
            MensagemFlash::redirecionar('../view/cadastro.php', 'O nome deve ter no máximo 50 caracteres.', 'erro');
        }

        // 4.3 - nome só com letras (acentos permitidos), espaços, apóstrofo, ponto e hífen. Sem números.
        if (!preg_match("/^[\\p{L}][\\p{L} '.-]*$/u", $nome)) {
            MensagemFlash::redirecionar('../view/cadastro.php', 'O nome deve conter apenas letras, sem números ou símbolos.', 'erro');
        }

        if (strlen($senha) < 8 || !preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $senha)) {
            MensagemFlash::redirecionar(
                '../view/cadastro.php',
                'A senha deve conter letras e números e ter no mínimo 8 caracteres.',
                'erro'
            );
        }

        if (strlen($senha) > 255) {
            MensagemFlash::redirecionar('../view/cadastro.php', 'A senha informada é muito longa.', 'erro');
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $model = new UsuarioModel();
        $resultado = $model->cadastrar($nome, $email, $senhaHash);

        if ($resultado === 'emailExiste') {
            MensagemFlash::redirecionar('../view/cadastro.php', 'Este e-mail já está cadastrado.', 'erro');
        }

        MensagemFlash::redirecionar('../view/login.php', 'Cadastro realizado com sucesso. Faça login para continuar.');
    }

    public function login() {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            MensagemFlash::redirecionar('../view/login.php', 'Informe seu e-mail e sua senha.', 'erro');
        }

        $conexao = Conexao::getConexao();

        $stmt = $conexao->prepare("SELECT * FROM login WHERE Email = ?");
        $stmt->execute([$email]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado && password_verify($senha, $resultado['Senha'])) {
            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            session_regenerate_id(true);

            $_SESSION['id'] = $resultado['idUsuario'];
            $_SESSION['nomeUsuario'] = $resultado['nomeUsuario'];
            $_SESSION['nivelAcesso'] = (int) $resultado['nivelAcesso'];

            MensagemFlash::redirecionar('../view/dashboard.php', 'Login realizado com sucesso.');
        }

        MensagemFlash::redirecionar('../view/login.php', 'E-mail ou senha incorretos', 'erro');
    }

    public function mostrarEditar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if(!isset($_SESSION['id'])){
            header('Location: ../view/login.php');
            exit;
        }
        
        $model = new UsuarioModel();

        $id = $_SESSION['id'];
        $usuario = $model->buscarPorId($id);
        
        require_once '../view/editar.php';
    }

    public function atualizar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $id = $_SESSION['id'];
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $senha = (string) ($_POST['senha'] ?? '');

        if ($nome === '') {
            MensagemFlash::redirecionar('routes.php?acao=editar', 'Preencha o seu nome.', 'erro');
        }

        if ($email === '') {
            MensagemFlash::redirecionar('routes.php?acao=editar', 'Preencha o seu e-mail.', 'erro');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            MensagemFlash::redirecionar('routes.php?acao=editar', 'Informe um endereço de e-mail válido.', 'erro');
        }

        if (mb_strlen($email) > 100) {
            MensagemFlash::redirecionar('routes.php?acao=editar', 'O e-mail deve ter no máximo 100 caracteres.', 'erro');
        }

        if (mb_strlen($nome) > 50) {
            MensagemFlash::redirecionar('routes.php?acao=editar', 'O nome deve ter no máximo 50 caracteres.', 'erro');
        }

        // 4.3 - nome só com letras (acentos permitidos), espaços, apóstrofo, ponto e hífen. Sem números.
        if (!preg_match("/^[\\p{L}][\\p{L} '.-]*$/u", $nome)) {
            MensagemFlash::redirecionar('routes.php?acao=editar', 'O nome deve conter apenas letras, sem números ou símbolos.', 'erro');
        }

        if (strlen($senha) > 255) {
            MensagemFlash::redirecionar('routes.php?acao=editar', 'A nova senha informada é muito longa.', 'erro');
        }

        if (!empty($senha) && (
            strlen($senha) < 8
            || !preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $senha)
        )) {
            MensagemFlash::redirecionar(
                'routes.php?acao=editar',
                'A nova senha deve conter letras e números e ter no mínimo 8 caracteres.',
                'erro'
            );
        }
        
        $model = new UsuarioModel();

        // Não deixa trocar para um e-mail que já pertence a outra conta.
        if ($model->emailEmUso($email, $id)) {
            MensagemFlash::redirecionar('routes.php?acao=editar', 'Este e-mail já está sendo usado por outra conta.', 'erro');
        }

        if(!empty($senha)){
            $senha = password_hash($senha, PASSWORD_DEFAULT);
            $model->atualizar($id, $nome, $email, $senha);
        } else {
            $model->atualizar($id, $nome, $email);
        }
        
        $_SESSION['nomeUsuario'] = $nome;
        MensagemFlash::redirecionar('../view/dashboard.php', 'Dados do perfil atualizados com sucesso.');
    }

    public function excluir() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Só pelo botão do formulário (POST com token). Um link GET não pode apagar a conta.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            MensagemFlash::redirecionar('routes.php?acao=editar', 'Use o botão "Excluir Conta" para remover a sua conta.', 'erro');
        }

        $id = $_SESSION['id'];
        $model = new UsuarioModel();

        if (!$model->excluir($id)) {
            MensagemFlash::redirecionar('routes.php?acao=editar', 'Não foi possível excluir a conta agora. Tente novamente.', 'erro');
        }

        $_SESSION = [];
        session_regenerate_id(true);
        MensagemFlash::redirecionar('../view/cadastro.php', 'Sua conta foi excluída com sucesso.');
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        session_regenerate_id(true);
        MensagemFlash::redirecionar('../view/login.php', 'Você saiu da sua conta com segurança.');
    }
}
?>