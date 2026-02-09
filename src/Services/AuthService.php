<?php
    class AuthService {
    private $userModel;
    private $auditModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->auditModel = new AuditModel(); 
    }

    public function login($email, $password, $turnstileResponse) {
        if (!$this->verifyCaptcha($turnstileResponse)) {
            return ['success' => false, 'message' => 'CAPTCHA verification failed.'];
        }

        $user = $this->userModel->getByEmail($email);
        echo "<script>alert('$user');</script>";
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid credentials.'];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid credentials.'];
        }

        $this->createSession($user);

        $this->auditModel->log($user['user_id'], 'AUTH_LOGIN', $user['user_id'], [
            'ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ]);

        return ['success' => true, 'user' => $user];
    }

    private function verifyCaptcha($response) {
        if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1') return true;

        $secret = "0x4AAAAAACYTiD3TFmSUAotsvQ3OOVQbj7Q";
        $verifyUrl = "https://challenges.cloudflare.com/turnstile/v0/siteverify";

        $ch = curl_init($verifyUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'secret' => $secret,
            'response' => $response
        ]);

        $res = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return (bool)$res['success'];
    }

    private function createSession($user) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role_name'] ?? 'Member';
        $_SESSION['last_activity'] = time();
        
        session_regenerate_id(true);
    }

    public function logout($userId) {
        $this->auditModel->log($userId, 'AUTH_LOGOUT', $userId);
        session_start();
        session_destroy();
    }
}
?>