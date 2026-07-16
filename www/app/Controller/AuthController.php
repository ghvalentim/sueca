<?php
namespace Controller;

use Model\User;
use Controller\MailController;
use Config\App;
use Services\JWTService;

class AuthController {
    
    private const SESSION_TIMEOUT = 3600;
    public function showRegister() {
        require_once __DIR__ . '/../../src/view/register.php';
    }

    public function register() {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $pswdRule = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.#])[A-Za-z\d@$!%*?&.#]{8,}$/';
        

        if (empty($username) || empty($email) || empty($password)) {
            $error = "Todos os campos são de preenchimento obrigatório.";
            require_once __DIR__ . '/../View/register.php';
            return;
        }

        if ($password !== $confirmPassword) {
            $error = "As passwords não coincidem.";
            require_once __DIR__ . '/../../src/view/register.php';
            return;
        }
        
        if (!preg_match($pswdRule, $password)) {
            $error = "A password deve ter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma letra minúscula, um número e um caractere especial.
            São permitidos: @ $ ! % * ? & . #";
            require_once __DIR__ . '/../../src/view/register.php';
            return;
        }

        $userModel = new User();
        $mailController = new MailController();

        if ($userModel->checkExists($username, $email)) {
            $error = "O username ou email já se encontram registados.";
            require_once __DIR__ . '/../../src/view/register.php';
            return;
        }

        $token = $userModel->create($username, $email, $password);

        if ($token) {
            $host = App::appURL() ?? 'http://localhost';
            $activationLink = $host . '/activate?token=' . urlencode($token);
            
            if ($mailController->sendActivationEmail($email, $activationLink)) {
                $success = "Registo efetuado com sucesso! Verifique o seu email para ativar a conta.";
                require_once __DIR__ . '/../../src/view/register.php';
            } else {
                $userModel->deleteAccount($userModel->getLastInsertedId());
                $error = "Erro ao enviar o email de ativação. Por favor, tente novamente mais tarde.";
                require_once __DIR__ . '/../../src/view/register.php';
            }
        } else {
            if(method_exists($userModel, 'getLastInsertedId')) {
                $userModel->deleteAccount($userModel->getLastInsertedId());
            }
            $error = "Erro ao criar a conta. Por favor, tente novamente.";
            require_once __DIR__ . '/../../src/view/register.php';
        }
    }

    public function activate() {
        $token = $_GET['token'] ?? ''; // Obter o token da query string
                
        if (empty($token)) { // Se o token estiver vazio, mostrar a página de erro
            $error = "Token de ativação inválido."; 
            require_once __DIR__ . '/../../src/view/activation-error.php'; 
            return;
        } else {
            $userModel = new User();
            if ($userModel->activateAccount($token)) { // Se a ativação for bem-sucedida, mostrar a página de sucesso
                $success = "Conta ativada com sucesso! Pode agora efetuar o login.";
                require_once __DIR__ . '/../../src/view/activation-success.php';
                return;
            } else { // Se a ativação falhar, mostrar a página de erro
                $error = "Token de ativação inválido ou a conta já foi ativada.";
                require_once __DIR__ . '/../../src/view/activation-error.php';
                return;
            }
        }
    }

    public function showLogin() {
        require_once __DIR__ . '/../../src/view/login.php'; //apenas mostra a View de Login, não processa o Login
    }

   
    public function login() { 
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = "Preencha todos os campos.";
            require_once __DIR__ . '/../../src/view/login.php';
            return;
        }

        $userModel = new User();
        $user = $userModel->verifyCredentials($username, $password);

        if (!$user) { 
            $error = "Credenciais inválidas.";
            require_once __DIR__ . '/../../src/view/login.php';
            return;
        }

        
        if ($user['is_active'] == 0) {
            $error = "A sua conta ainda não foi ativada. Verifique o seu email.";
            require_once __DIR__ . '/../../src/view/login.php';
            return;
        }

      
        $_SESSION['user_id'] = $user['id']; 
        $_SESSION['username'] = $user['username']; 

        try {
            
            $jwtToken = $userModel->getJwtTokenForUser($user['email'], $password);
            

            if ($jwtToken === null) { 
                $error = "Erro ao obter o Token JWT da API.";
                $_SESSION = [];
                session_destroy(); 
                require_once __DIR__ . '/../../src/view/login.php';
                return;
            }

            $_SESSION['jwt_token'] = $jwtToken;
            $_SESSION['last_activity'] = time();
        
        } catch (\Exception $e) {
            $error = "Erro ao comunicar com a API: " . $e->getMessage();
            $_SESSION = [];
            session_destroy();
            require_once __DIR__ . '/../../src/view/login.php';
            return;
        }

        header("Location: /");
        exit;
    }

    public function logout() {
        session_destroy();
        header("Location: /");
        exit;
    }

    public function isAuthenticated() {

        $timeOut = $this->isTimedOut(self::SESSION_TIMEOUT);
        $token = $_SESSION['jwt_token'] ?? null;
        $jwtExpired = $token ? JWTService::isExpired($token) : true;
        
        if (!isset($_SESSION['user_id']) || !$token) {
            return false;
        }

        if ($timeOut || $jwtExpired) {
            session_destroy();
            return false;
        }

        return true;
    }


    public function forgotPassword() {
        require_once __DIR__ . '/../../src/view/forgot.php';
    }

    public function processForgotPassword(){
        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $error = "Por favor, insira o seu email.";
            require_once __DIR__ . '/../../src/view/forgot.php';
            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $userModel->setRecoveryToken($user['id'], $token);
            $host = App::appURL() ?? 'http://localhost';
            $recoveryLink = $host . '/reset?token=' . urlencode($token);
            $mailController = new MailController();
            $mailController->sendRecoveryEmail($email, $recoveryLink);
        }

        $success = "Se o email existir no sistema, receberá um email com instruções para redefinir a password.";
        require_once __DIR__ . '/../../src/view/forgot.php';
    }

    public function resetPassword() {
        $token = $_GET['token'] ?? '';

        if(empty($token)) {
            die("Token de recuperação inválido.");
        }

        $userModel = new User();
        $user = $userModel->findByRecoveryToken($token);

        if(!$user) {
            die("Token de recuperação inválido ou expirado.");
        }

        require_once __DIR__ . '/../../src/view/reset.php';
    }

    public function processResetPassword() {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $pswdRule = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

        if(empty($password) || $password !== $confirmPassword) {
            $error = "As passwords não coincidem ou estão vazias.";
            require_once __DIR__ . '/../../src/view/reset.php';
            return;
        }

        if (!preg_match($pswdRule, $password)) {
            $error = "A password deve ter pelo menos 8 caracteres, incluindo uma letra maiúscula, uma letra minúscula, um número e um caractere especial.";
            require_once __DIR__ . '/../../src/view/reset.php';
            return;
        }

        $userModel = new User();
        if($userModel->resetPasswordWithToken($token, $password)) {
            header("Location: /login?reset_success");
            exit;
        } else {
            $error = "Ocorreu um erro. Por favor, tente novamente.";
            require_once __DIR__ . '/../../src/view/reset.php';
        }
    }


    public function isTimedOut(int $time): bool {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $time)) {
            return true;
        }
        return false;
    }

}