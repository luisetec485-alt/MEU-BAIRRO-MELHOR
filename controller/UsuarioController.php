<?php
require_once '../model/UsuarioModel.php';
require_once '../model/Conexao.php';

class UsuarioController {
    
    public function cadastrar() {   
        $nome = $_POST['nome'] ?? '';
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';

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

if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $senha)) {
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
            text: "A senha deve conter letras e números e ter no mínimo 8 caracteres!",
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

    public function login() {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';

        if(empty($email) || empty($senha)){
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

        $model = new UsuarioModel();
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
                    icon: "error"
                }).then(() => {
                    window.history.back();
                });
                </script>
                </body>
                </html>
            ';
        }
    }

    public function uploadFoto() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['id']) && isset($_FILES['foto'])) {
            $idUsuario = $_SESSION['id'];
            
            $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $extensoesPermitidas = ['jpg', 'jpeg', 'png'];
            $tiposPermitidos = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $tipoMime = $finfo ? finfo_file($finfo, $_FILES['foto']['tmp_name']) : false;
            if ($finfo) {
                finfo_close($finfo);
            }
    
            if (in_array($extensao, $extensoesPermitidas, true)
                && $tipoMime === $tiposPermitidos[$extensao]
                && $_FILES['foto']['size'] <= 5 * 1024 * 1024
            ) {
                $nomeFoto = bin2hex(random_bytes(16)) . "." . $extensao;
                $diretorioDestino = __DIR__ . "/../view/img/" . $nomeFoto;
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $diretorioDestino)) {
                    $caminhoBanco = "img/" . $nomeFoto;
                    $conexao = Conexao::getConexao();
                    $stmt = $conexao->prepare("UPDATE login SET foto = ? WHERE idUsuario = ?");
                    $resultado = $stmt->execute([$caminhoBanco, $idUsuario]);
                    if ($resultado) {
                        $_SESSION['foto'] = $caminhoBanco;
                        echo "
                        <script>
                            alert('Foto de perfil atualizada com sucesso!');
                            window.location.href = '../view/dashboard.php';
                        </script>
                        ";
                        exit;
                    } else {
                        echo "<script>alert('Erro ao salvar o caminho da foto no banco de dados.'); history.back();</script>";
                    }
                } else {
                    echo "<script>alert('Erro ao mover o arquivo para a pasta view/img/.'); history.back();</script>";
                }
            } else {
                echo "<script>alert('Extensão não permitida! Use apenas PNG, JPG ou JPEG.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Sessão expirada ou nenhum arquivo enviado.'); window.location.href = '../view/login.php';</script>";
        }
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

        if(!empty($senha) && (
            strlen($senha) < 8
            || !preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $senha)
        )){
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
                    text: "A senha deve conter letras e números e ter no mínimo 8 caracteres!",
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
        if(!empty($senha)){
            $senha = password_hash($senha, PASSWORD_DEFAULT);
            $model->atualizar($id, $nome, $email, $senha);
        } else {
            $model->atualizar($id, $nome, $email);
        }
        
        $_SESSION['nomeUsuario'] = $nome;
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

    public function excluir() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $id = $_SESSION['id'];
        $model = new UsuarioModel();
        $model->excluir($id);
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

    public function logout() {
        session_start();
        session_destroy();
        header('Location: ../view/login.php');
    }
}
?>