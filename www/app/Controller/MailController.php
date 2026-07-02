<?php

namespace Controller;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailController {
    
    // Envia um email de ativação para o utilizador
    public function sendActivationEmail(string $toEmail, string $activationLink) {
        $mail = new PHPMailer(true);
        $host = $_ENV['MAIL_HOST'] ?? '';
        $port = $_ENV['MAIL_PORT'] ?? '';
        $username = $_ENV['MAIL_USERNAME'] ?? '';
        $password = $_ENV['MAIL_PASSWORD'] ?? '';
        $from = $_ENV['MAIL_FROM_ADDRESS'] ?? '';
        $fromName = $_ENV['MAIL_FROM_NAME'] ?? '';

        try {
            // Configurações do Servidor SMTP
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $username;
            $mail->Password   = $password;
            $mail->Port       = $port;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPDebug = 0; // Desativar debug para produção
            $mail->Debugoutput = 'error_log'; // Log de erros para o log do PHP

            // Destinatário e remetente
            $mail->setFrom($from, $fromName);
            $mail->addAddress($toEmail);

            // Conteúdo do Email
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
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