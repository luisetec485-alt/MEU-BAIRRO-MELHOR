<?php

require_once __DIR__.'/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuxiliarEmail
{
    public static function enviarEmail($paraEmail, $paraNome, $assunto, $corpo)
    {
        $config = require __DIR__.'/../config/email.php';
        $emailRemetente = $config['email'] ?? '';
        $senhaRemetente = $config['senha'] ?? '';

        if (empty($emailRemetente) || empty($senhaRemetente)) {
            error_log('Configuração de e-mail do sistema incompleta.');
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $emailRemetente;
            $mail->Password = $senhaRemetente;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($emailRemetente, 'Meu Bairro Melhor');
            $mail->addAddress($paraEmail, $paraNome);

            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body = $corpo;
            $mail->AltBody = strip_tags($corpo);

            return $mail->send();
        } catch (Exception $e) {
            error_log('Falha ao enviar e-mail: ' . $e->getMessage());
            return false;
        }
    }
}
?>