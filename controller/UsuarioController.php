<?php
require_once '../model/UsuarioModel.php';

class UsuarioController {
    
    // função de cadastro
    public function cadastrar() {   
        // pega os dados
        $nome = $_POST['nome'] ?? '';
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';

        // validação para email com o formato certo (luis@gmail.com)
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            echo '
                <!DOCTYPE html>
                <html>
                <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </head>
                <body>
                <script>
                Swal.fire({
                    title: "Erro!",
                    text: "Formato de email errado!",
                    icon: "error",
                    confirmButtonText: "Tentar Novamente"
                }).then(() => {
                    window.history.back();
                });
                </script>
                </body>
                </html>
            ';
            return;
        }

        // validação para nome com até 50 caracteres
        if(strlen($nome) > 50){
            echo '
                <!DOCTYPE html>
                <html>
                <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </head>
                <body>
                <script>
                Swal.fire({
                    title: "Erro!",
                    text: "Nome muito longo!",
                    icon: "error",
                    confirmButtonText: "Tentar Novamente"
                }).then(() => {
                    window.history.back();
                });
                </script>
                </body>
                </html>
            ';
            return;
        }

        // validação para valor vazio
        if(empty($nome) || empty($email) || empty($senha)) {
            echo '
                <!DOCTYPE html>
                <html>
                <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </head>
                <body>
                <script>
                Swal.fire({
                    title: "Erro!",
                    text: "Todos os campos são obrigatórios!",
                    icon: "error",
                    confirmButtonText: "Voltar"
                }).then(() => {
                    window.history.back();
                });
                </script>
                </body>
                </html>
            ';
            return;
        }

        // validação para senha com no minimo 8 caracteres
        if(strlen($senha) < 8){
            echo '
                <!DOCTYPE html>
                <html>
                <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </head>
                <body>
                <script>
                Swal.fire({
                    title: "Erro!",
                    text: "A senha deve ter no mínimo 8 caracteres!",
                    icon: "error"
                }).then(() => {
                    window.history.back();
                });
                </script>
                </body>
                </html>
            ';
            return;
        }

        // Validação para senha com apenas números e sem espaços
if (!preg_match('/^\d+$/', $senha)) {
    echo '
        <!DOCTYPE html>
        <html>
        <head>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
        <script>
        Swal.fire({
            title: "Erro!",
            text: "A senha deve conter apenas números e não pode ter espaços!",
            icon: "error"
        }).then(() => {
            window.history.back();
        });
        </script>
        </body>
        </html>
    ';
    return;
}

        // validação para senha com no maximo 255 caracteres
        if(strlen($senha) > 255){
            echo '
                <!DOCTYPE html>
                <html>
                <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </head>
                <body>
                <script>
                Swal.fire({
                    title: "Erro!",
                    text: "Senha muito longa!",
                    icon: "error"
                }).then(() => {
                    window.history.back();
                });
                </script>
                </body>
                </html>
            ';
            return;
        }       

        // criptografia da senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $model = new UsuarioModel();
        $resultado = $model->cadastrar($nome, $email, $senhaHash);
        

        if($resultado == "emailExiste") {
            echo '
                <!DOCTYPE html>
                <html>
                <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </head>
                <body>
                <script>
                Swal.fire({
                    title: "Erro!",
                    text: "Este email já está cadastrado!",
                    icon: "error",
                    confirmButtonText: "Voltar"
                }).then(() => {
                    window.location.href = "../view/cadastro.php";
                });
                </script>
                </body>
                </html>
            ';
        } else {
            echo '
                <!DOCTYPE html>
                <html>
                <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </head>
                <body>
                <script>
                Swal.fire({
                    title: "Sucesso!",
                    text: "Usuário cadastrado com sucesso!",
                    icon: "success"
                }).then(() => {
                    window.location.href = "../view/login.php";
                });
                </script>
                </body>
                </html>
            ';
        }
    }

    // função de login
    public function login() {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';
        
        if(empty($email) || empty($senha)) {
            echo '
                <!DOCTYPE html>
                <html>
                <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </head>
                <body>
                <script>
                Swal.fire({
                    title: "Erro!",
                    text: "Preencha todos os campos!",
                    icon: "error"
                }).then(() => {
                    window.history.back();
                });
                </script>
                </body>
                </html>
            ';
            return;
        }

        // validação para email com o formato certo (luis@gmail.com)
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            echo '
                <!DOCTYPE html>
                <html>
                <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </head>
                <body>
                <script>
                Swal.fire({
                    title: "Erro!",
                    text: "Formato de email errado!",
                    icon: "error"
                }).then(() => {
                    window.history.back();
                });
                </script>
                </body>
                </html>
            ';
            return;
        }
        
        $model = new UsuarioModel();
        $usuario = $model->login($email, $senha);
        
        // validar o login, criar a sessão do usuário e liberar acesso ao sistema
        if (!empty($usuario)) {

            session_start();

            // SEGURANÇA
            session_regenerate_id(true);

            $_SESSION['id'] = $usuario[0]['idUsuario'];
            $_SESSION['nome'] = $usuario[0]['nomeUsuario'];
            
            header("Location: ../view/dashboard.php");
            exit;

        } else {
            echo '
                <!DOCTYPE html>
                <html>
                <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </head>
                <body>
                <script>
                Swal.fire({
                    title: "Erro!",
                    text: "Email ou Senha incorretos!",
                    icon: "error",
                    confirmButtonText: "Tentar Novamente"
                }).then(() => {
                    window.location.href = "../view/login.php";
                });
                </script>
                </body>
                </html>
            ';
        }
    }

    // função de editar
    public function mostrarEditar() {
        session_start();

        if(!isset($_SESSION['id'])){
            header('Location: ../view/login.php');
            exit;
        }
        
        $model = new UsuarioModel();

        $id = $_SESSION['id'];
        $usuario = $model->buscarPorId($id);
        
        require_once '../view/editar.php';
    }

    // função de atualizar
    public function atualizar() {
        session_start();
        $id = $_SESSION['id'];
        $nome = $_POST['nome'] ?? '';
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';
        
        if(empty($nome) || empty($email)) {
            echo '
                <!DOCTYPE html>
                <html>
                <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </head>
                <body>
                <script>
                Swal.fire({
                    title: "Erro!",
                    text: "Nome e email são obrigatórios!",
                    icon: "error"
                }).then(() => {
                    window.history.back();
                });
                </script>
                </body>
                </html>
            ';
            return;
        }

        if(!empty($senha) && strlen($senha) < 8){
            echo '
                <!DOCTYPE html>
                <html>
                <head>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                </head>
                <body>
                <script>
                Swal.fire({
                    title: "Erro!",
                    text: "A senha deve ter no mínimo 8 caracteres!",
                    icon: "error"
                }).then(() => {
                    window.history.back();
                });
                </script>
                </body>
                </html>
            ';
            return;
        }
        
        $model = new UsuarioModel();
        
        // hash ANTES de mandar pro model, pra deixar a senha criptografada
        if(!empty($senha)){
            $senha = password_hash($senha, PASSWORD_DEFAULT);
            $model->atualizar($id, $nome, $email, $senha);
        } else {
            $model->atualizar($id, $nome, $email);
        }
        
        session_start();
        $_SESSION['nome'] = $nome;
        
        echo '
            <!DOCTYPE html>
            <html>
            <head>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
            <script>
            Swal.fire({
                title: "Sucesso!",
                text: "Dados atualizados com sucesso!",
                icon: "success"
            }).then(() => {
                window.location.href = "../view/dashboard.php";
            });
            </script>
            </body>
            </html>
        ';
    }

    // função de excluir
    public function excluir() {
        session_start();
        $id = $_SESSION['id'];
        
        $model = new UsuarioModel();
        $model->excluir($id);
        
        session_start();
        session_destroy();
        
        echo '
            <!DOCTYPE html>
            <html>
            <head>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            </head>
            <body>
            <script>
            Swal.fire({
                title: "Conta removida!",
                text: "Sua conta foi excluída com sucesso!",
                icon: "success"
            }).then(() => {
                window.location.href = "../view/cadastro.php";
            });
            </script>
            </body>
            </html>
        ';
    }
    
    // função de logout (para sair da conta)
    public function logout() {
        session_start();
        session_destroy();
        header('Location: ../view/login.php');
    }
}
?>