<?php

class Seguranca
{
    public static function iniciarSessao()
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_strict_mode', '1');
            $seguro = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'secure' => $seguro,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function exigirLogin()
    {
        self::iniciarSessao();

        if (isset($_SESSION['id'])) {
            return;
        }

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $loginPath = preg_match('~/controller/[^/]+$~i', $scriptName)
            ? '../view/login.php'
            : 'login.php';

        header('Location: ' . $loginPath);
        exit;
    }

    public static function exigirAdministrador()
    {
        self::exigirLogin();

        if ((int) ($_SESSION['nivelAcesso'] ?? 1) === 0) {
            return;
        }

        http_response_code(403);
        echo 'Acesso negado.';
        exit;
    }

    public static function token()
    {
        self::iniciarSessao();
        return $_SESSION['csrf_token'];
    }

    public static function validarToken($token)
    {
        self::iniciarSessao();
        return is_string($token)
            && hash_equals($_SESSION['csrf_token'], $token);
    }
}
