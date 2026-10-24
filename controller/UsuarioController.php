<?php
require_once '../model/UsuarioModel.php';
require_once '../model/Conexao.php';
require_once '../model/MensagemFlash.php';

class UsuarioController {
    
    public function cadastrar() {
        $nome = $_POST['nome'] ?? '';
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';

        if (empty($nome) || empty($email) || empty($senha)) {
            MensagemFlash::redirecionar('../view/cadastro.php', 'Preencha todos os campos.', 'erro');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            MensagemFlash::redirecionar('../view/cadastro.php', 'Informe um endereço de e-mail válido.', 'erro');
        }

        if (strlen($nome) > 50) {
            MensagemFlash::redirecionar('../view/cadastro.php', 'O nome deve ter no máximo 50 caracteres.', 'erro');
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
            $_SESSION['foto'] = $resultado['foto'];
            $_SESSION['nivelAcesso'] = (int) $resultado['nivelAcesso'];

            MensagemFlash::redirecionar('../view/dashboard.php', 'Login realizado com sucesso.');
        }

        MensagemFlash::redirecionar('../view/login.php', 'E-mail ou senha incorretos', 'erro');
    }

    public function uploadFoto() {
        if (!isset($_SESSION['id'])) {
            MensagemFlash::redirecionar('../view/login.php', 'Sua sessão expirou. Entre novamente.', 'erro');
        }

        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            MensagemFlash::redirecionar('routes.php?acao=editar', 'Selecione uma foto válida para enviar.', 'erro');
        }

        $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $extensoesPermitidas = ['jpg', 'jpeg', 'png'];
        $tiposPermitidos = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipoMime = $finfo ? finfo_file($finfo, $_FILES['foto']['tmp_name']) : false;
        if ($finfo) {
            finfo_close($finfo);
        }

        if (!in_array($extensao, $extensoesPermitidas, true)
            || $tipoMime !== $tiposPermitidos[$extensao]
            || $_FILES['foto']['size'] > 5 * 1024 * 1024
        ) {
            MensagemFlash::redirecionar(
                'routes.php?acao=editar',
                'A foto deve ser PNG, JPG ou JPEG e ter no máximo 5 MB.',
                'erro'
            );
        }

        $nomeFoto = bin2hex(random_bytes(16)) . '.' . $extensao;
        $diretorioDestino = __DIR__ . '/../view/img/' . $nomeFoto;
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $diretorioDestino)) {
            MensagemFlash::redirecionar(
                'routes.php?acao=editar',
                'Não foi possível enviar a foto. Tente novamente.',
                'erro'
            );
        }

        $caminhoBanco = 'img/' . $nomeFoto;
        $conexao = Conexao::getConexao();
        $stmt = $conexao->prepare('UPDATE login SET foto = ? WHERE idUsuario = ?');
        $resultado = $stmt->execute([$caminhoBanco, $_SESSION['id']]);

        if (!$resultado) {
            if (is_file($diretorioDestino) && !unlink($diretorioDestino)) {
                error_log('Não foi possível remover o upload de perfil órfão: ' . $diretorioDestino);
            }
            MensagemFlash::redirecionar(
                'routes.php?acao=editar',
                'Não foi possível salvar a foto no perfil. Tente novamente.',
                'erro'
            );
        }

        $_SESSION['foto'] = $caminhoBanco;
        MensagemFlash::redirecionar('routes.php?acao=editar', 'Foto de perfil atualizada com sucesso.');
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
        $nome = $_POST['nome'] ?? '';
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';
        
        if (empty($nome) || empty($email)) {
            MensagemFlash::redirecionar('routes.php?acao=editar', 'Nome e e-mail são obrigatórios.', 'erro');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            MensagemFlash::redirecionar('routes.php?acao=editar', 'Informe um endereço de e-mail válido.', 'erro');
        }

        if (strlen($nome) > 50) {
            MensagemFlash::redirecionar('routes.php?acao=editar', 'O nome deve ter no máximo 50 caracteres.', 'erro');
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
        $id = $_SESSION['id'];
        $model = new UsuarioModel();
        $model->excluir($id);
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