<?php

class Seguranca
{
    private const TEMPO_INATIVIDADE = 900;

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

        if (isset($_SESSION['id'])) {
            $agora = time();
            $ultimaAtividade = $_SESSION['ultimaAtividade'] ?? null;

            if (is_numeric($ultimaAtividade)
                && $agora - (int) $ultimaAtividade >= self::TEMPO_INATIVIDADE
            ) {
                $_SESSION = [];
                session_regenerate_id(true);
                $_SESSION['mensagemFlash'] = [
                    'texto' => 'Sua sessão expirou por inatividade. Faça login novamente.',
                    'tipo' => 'erro'
                ];
            } else {
                $_SESSION['ultimaAtividade'] = $agora;
            }
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

        if (self::ehAdministrador()) {
            return;
        }

        self::negarAcesso();
    }

    public static function ehAdministrador()
    {
        self::iniciarSessao();
        return isset($_SESSION['id']) && (int) ($_SESSION['nivelAcesso'] ?? 1) === 0;
    }

    // 3.2 - Usuário comum tentando abrir uma função de administrador (pelo menu ou
    // digitando a URL): responde 403 e mostra uma página amigável, com o menu do site.
    public static function negarAcesso()
    {
        self::iniciarSessao();
        http_response_code(403);
        $pageTitle = 'Acesso negado - Meu Bairro Melhor';
        include __DIR__ . '/../view/acessoNegado.php';
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