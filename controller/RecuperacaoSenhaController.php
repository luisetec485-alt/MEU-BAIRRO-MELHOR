<?php

require_once '../model/RecuperacaoSenhaModel.php';
require_once '../vendor/autoload.php';
require_once '../model/MensagemFlash.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class RecuperacaoSenhaController
{
    public function solicitarRecuperacao()
    {
        $email = strtolower(trim($_POST['email'] ?? ''));

        if (empty($email)) {
            MensagemFlash::redirecionar('../view/esqueceuSenha.php', 'Informe o e-mail da sua conta.', 'erro');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            MensagemFlash::redirecionar('../view/esqueceuSenha.php', 'Informe um endereço de e-mail válido.', 'erro');
        }

        $model = new RecuperacaoSenhaModel();

        $usuario = $model->buscarUsuarioPorEmail($email);

        // Não revela se o e-mail existe ou não.
        if (!$usuario) {
            MensagemFlash::redirecionar(
                '../view/login.php',
                'Se o e-mail estiver cadastrado, você receberá as instruções para redefinir sua senha.'
            );
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
            MensagemFlash::redirecionar(
                '../view/esqueceuSenha.php',
                'Não foi possível iniciar a recuperação agora. Tente novamente.',
                'erro'
            );
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

            MensagemFlash::redirecionar(
                '../view/login.php',
                'Se o e-mail estiver cadastrado, você receberá as instruções para redefinir sua senha.'
            );

        } catch (Exception $e) {

            error_log('Falha no envio de recuperação de senha: ' . $mail->ErrorInfo);
            MensagemFlash::redirecionar(
                '../view/esqueceuSenha.php',
                'Não foi possível enviar o e-mail agora. Tente novamente mais tarde.',
                'erro'
            );
        }
    }


    public function mostrarRedefinicao()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            MensagemFlash::redirecionar(
                '../view/esqueceuSenha.php',
                'O link de redefinição é inválido ou está incompleto.',
                'erro'
            );
        }

        // Calcula o hash do token recebido.
        $tokenHash = hash('sha256', $token);

        $model = new RecuperacaoSenhaModel();

        // Verifica se o token existe e ainda não expirou.
        $dadosToken = $model->buscarTokenValido($tokenHash);

        if (!$dadosToken) {
            MensagemFlash::redirecionar(
                '../view/esqueceuSenha.php',
                'Este link de redefinição é inválido ou expirou. Solicite um novo link.',
                'erro'
            );
        }

        // Abre o formulário para criar uma nova senha.
        include '../view/redefinirSenha.php';
    }


    public function redefinirSenha()
    {
        $token = $_POST['token'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmarSenha'] ?? '';
        $destinoRedefinicao = 'routes.php?acao=mostrarRedefinicao&token=' . urlencode($token);

        if (
            empty($token) ||
            empty($senha) ||
            empty($confirmarSenha)
        ) {
            MensagemFlash::redirecionar(
                $destinoRedefinicao,
                'Preencha todos os campos para redefinir sua senha.',
                'erro'
            );
        }

        // Verifica se as senhas são iguais.
        if ($senha !== $confirmarSenha) {
            MensagemFlash::redirecionar($destinoRedefinicao, 'As senhas informadas não coincidem.', 'erro');
        }

        // Exige pelo menos 8 caracteres.
        if (strlen($senha) < 8 || !preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $senha)) {
            MensagemFlash::redirecionar(
                $destinoRedefinicao,
                'A senha deve conter letras e números e ter pelo menos 8 caracteres.',
                'erro'
            );
        }

        // Calcula novamente o hash do token.
        $tokenHash = hash('sha256', $token);

        $model = new RecuperacaoSenhaModel();

        // Valida novamente o token.
        $dadosToken = $model->buscarTokenValido($tokenHash);

        if (!$dadosToken) {
            MensagemFlash::redirecionar(
                '../view/esqueceuSenha.php',
                'Este link de redefinição é inválido ou expirou. Solicite um novo link.',
                'erro'
            );
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
            MensagemFlash::redirecionar(
                $destinoRedefinicao,
                'Não foi possível alterar a senha agora. Tente novamente.',
                'erro'
            );
        }

        // Invalida o token para impedir reutilização.
        $model->excluirToken(
            $dadosToken['id']
        );

        MensagemFlash::redirecionar('../view/login.php', 'Senha alterada com sucesso. Faça login com a nova senha.');
    }
}
?>