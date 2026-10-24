<?php

require_once '../model/RecuperacaoSenhaModel.php';
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class RecuperacaoSenhaController
{
    public function solicitarRecuperacao()
    {
        $email = strtolower(trim($_POST['email'] ?? ''));

        if (empty($email)) {
            echo "Informe seu e-mail.";
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Formato de e-mail inválido.";
            return;
        }

        $model = new RecuperacaoSenhaModel();

        $usuario = $model->buscarUsuarioPorEmail($email);

        // Não revela se o e-mail existe ou não.
        if (!$usuario) {
            echo "Se o e-mail estiver cadastrado, você receberá um link para redefinir sua senha.";
            return;
        }

        // Gera um token aleatório.
        $token = bin2hex(random_bytes(32));

        // Salva somente o hash do token no banco.
        $tokenHash = hash('sha256', $token);

        // Token válido por 30 minutos.
        $expiraEm = date(
            'Y-m-d H:i:s',
            strtotime('+30 minutes')
        );

        $salvou = $model->salvarToken(
            $usuario['idUsuario'],
            $tokenHash,
            $expiraEm
        );

        if (!$salvou) {
            echo "Não foi possível iniciar a recuperação da senha.";
            return;
        }

        // Configurações do e-mail e URL do sistema.
        $config = require '../config/email.php';

        $mail = new PHPMailer(true);

        try {

            // Configuração SMTP do Gmail.
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;

            $mail->Username = $config['email'];
            $mail->Password = $config['senha'];

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->CharSet = 'UTF-8';

            // Remetente.
            $mail->setFrom(
                $config['email'],
                'Meu Bairro Melhor'
            );

            // Destinatário.
            $mail->addAddress(
                $usuario['Email'],
                $usuario['nomeUsuario']
            );

            // Link de recuperação.
            $link = $config['url']
                . '/controller/routes.php?acao=mostrarRedefinicao&token='
                . urlencode($token);

            // E-mail em HTML.
            $mail->isHTML(true);

            $mail->Subject = 'Recuperação de senha - Meu Bairro Melhor';

            $mail->Body = "
                <h2>Recuperação de senha</h2>

                <p>Olá, {$usuario['nomeUsuario']}.</p>

                <p>
                    Recebemos uma solicitação para redefinir
                    sua senha.
                </p>

                <p>
                    <a href='{$link}'>
                        Clique aqui para redefinir sua senha
                    </a>
                </p>

                <p>
                    Este link é válido por 30 minutos.
                </p>

                <p>
                    Se você não solicitou a recuperação da senha,
                    ignore este e-mail.
                </p>
            ";

            // Versão para clientes de e-mail sem HTML.
            $mail->AltBody =
                "Acesse este link para redefinir sua senha: " . $link;

            // Envia o e-mail.
            $mail->send();

            echo "
                <script>
                    alert('Se o e-mail estiver cadastrado, você receberá um link para redefinir sua senha.');
                    window.location.href = '../view/login.php';
                </script>
            ";

        } catch (Exception $e) {

            error_log('Falha no envio de recuperação de senha: ' . $mail->ErrorInfo);
            echo "Não foi possível enviar o e-mail no momento.";
        }
    }


    public function mostrarRedefinicao()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            echo "Token inválido.";
            return;
        }

        // Calcula o hash do token recebido.
        $tokenHash = hash('sha256', $token);

        $model = new RecuperacaoSenhaModel();

        // Verifica se o token existe e ainda não expirou.
        $dadosToken = $model->buscarTokenValido($tokenHash);

        if (!$dadosToken) {
            echo "Este link é inválido ou expirou.";
            return;
        }

        // Abre o formulário para criar uma nova senha.
        include '../view/redefinirSenha.php';
    }


    public function redefinirSenha()
    {
        $token = $_POST['token'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmarSenha'] ?? '';

        if (
            empty($token) ||
            empty($senha) ||
            empty($confirmarSenha)
        ) {
            echo "Preencha todos os campos.";
            return;
        }

        // Verifica se as senhas são iguais.
        if ($senha !== $confirmarSenha) {
            echo "As senhas não coincidem.";
            return;
        }

        // Exige pelo menos 8 caracteres.
        if (strlen($senha) < 8 || !preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $senha)) {
            echo "A senha deve conter letras e números e ter pelo menos 8 caracteres.";
            return;
        }

        // Calcula novamente o hash do token.
        $tokenHash = hash('sha256', $token);

        $model = new RecuperacaoSenhaModel();

        // Valida novamente o token.
        $dadosToken = $model->buscarTokenValido($tokenHash);

        if (!$dadosToken) {
            echo "Este link é inválido ou expirou.";
            return;
        }

        // Cria um hash seguro para a nova senha.
        $senhaHash = password_hash(
            $senha,
            PASSWORD_DEFAULT
        );

        // Atualiza a senha do usuário.
        $atualizou = $model->atualizarSenha(
            $dadosToken['idUsuario'],
            $senhaHash
        );

        if (!$atualizou) {
            echo "Não foi possível alterar a senha.";
            return;
        }

        // Invalida o token para impedir reutilização.
        $model->excluirToken(
            $dadosToken['id']
        );

        echo "
            <script>
                alert('Senha alterada com sucesso!');
                window.location.href = '../view/login.php';
            </script>
        ";
    }
}
?>