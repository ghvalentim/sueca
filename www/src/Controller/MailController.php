<?php

namespace Controller;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require_once __DIR__ . '/../../vendor/autoload.php';

class MailController {
    
    function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();
    }

    function sendActivationEmail(string $toEmail, string $activationLink) {
        $mail = new PHPMailer(true);
        try {
            // Configurações do Servidor SMTP
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'];
            $mail->Password   = $_ENV['MAIL_PASSWORD'];
            $mail->Port       = $_ENV['MAIL_PORT'];
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? PHPMailer::ENCRYPTION_STARTTLS;

            // Destinatário e remetente
            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($toEmail);

            // Conteúdo do Email
            $mail->isHTML(true);
            $mail->Subject = 'Ativação de Conta';
            $mail->Body    = "Clique no link para ativar sua conta: <a href='$activationLink'>$activationLink</a>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar email: {$mail->ErrorInfo}");
            return false;
        }
    }


}