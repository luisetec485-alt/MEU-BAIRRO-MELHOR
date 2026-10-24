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
