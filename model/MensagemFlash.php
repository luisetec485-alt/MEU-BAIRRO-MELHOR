<?php
require_once __DIR__ . '/Seguranca.php';

class MensagemFlash
{
    public static function redirecionar($destino, $mensagem, $tipo = 'sucesso')
    {
        if (!in_array($tipo, ['sucesso', 'erro'], true)) {
            throw new InvalidArgumentException('Tipo de mensagem inválido.');
        }

        Seguranca::iniciarSessao();
        $_SESSION['mensagemFlash'] = [
            'texto' => $mensagem,
            'tipo' => $tipo
        ];

        header('Location: ' . $destino, true, 303);
        exit;
    }

    public static function consumir()
    {
        Seguranca::iniciarSessao();
        $mensagem = $_SESSION['mensagemFlash'] ?? null;
        unset($_SESSION['mensagemFlash']);

        if (!is_array($mensagem)
            || !isset($mensagem['texto'], $mensagem['tipo'])
            || !in_array($mensagem['tipo'], ['sucesso', 'erro'], true)
        ) {
            return null;
        }

        return $mensagem;
    }
}
