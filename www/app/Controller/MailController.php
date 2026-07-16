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
            $mail->SMTPDebug = 1; 
            $mail->Debugoutput = 'error_log'; 
            $mail->setFrom($from, $fromName);
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Ativação de Conta';
            $mail->Body    = 'Obrigado por se registar! Clique no seguinte link para ativar a sua conta: <a href="' . $activationLink . '">Ativar Conta</a>';
            $mail->AltBody = 'Obrigado por se registar! Copie e cole o seguinte link no seu navegador para ativar a sua conta: ' . $activationLink;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar email: {$mail->ErrorInfo}");
            return false;
        }
    }

    public function sendRecoveryEmail(string $toEmail, string $recoveryLink) {
        $mail = new PHPMailer(true);
        $host = $_ENV['MAIL_HOST'] ?? '';
        $port = $_ENV['MAIL_PORT'] ?? '';
        $username = $_ENV['MAIL_USERNAME'] ?? '';
        $password = $_ENV['MAIL_PASSWORD'] ?? '';
        $from = $_ENV['MAIL_FROM_ADDRESS'] ?? '';
        $fromName = $_ENV['MAIL_FROM_NAME'] ?? '';

        try {
            
            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $username;
            $mail->Password   = $password;
            $mail->Port       = $port;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->SMTPDebug = 1;
            $mail->Debugoutput = 'error_log';
            $mail->setFrom($from, $fromName);
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Recuperação de Conta';
            $mail->Body    = '
                <h2>Recuperação de Conta</h2>
                <p>Recebemos um pedido para repor a password da sua conta no Jogosueca.</p>
                <p>Clique no botão abaixo para escolher uma nova password:</p>
                <a href="' . $recoveryLink . '" style="display:inline-block; padding:10px 20px; background-color:#198754; color:white; text-decoration:none; border-radius:5px;">Redefinir Password</a>
                <p><small>Se não pediu a recuperação de password, ignore este email.</small></p>
            ';
            $mail->AltBody = 'Clique no seguinte link para redefinir a sua password: ' . $recoveryLink;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar email de recuperação: {$mail->ErrorInfo}");
            return false;
        }
    }


}