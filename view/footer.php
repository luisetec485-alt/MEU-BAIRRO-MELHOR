
<?php
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$inController = strpos($scriptName, '/controller/') !== false;
$viewBase = $inController ? '../view/' : '';
?>
<footer class="site-footer">
    <p>&copy; <?= date('Y') ?> Meu Bairro Melhor</p>
    <p>Grupo: Meu Bairro Melhor</p>
    <p>Contato: <a href="mailto:meubairromelhor9@gmail.com">meubairromelhor9@gmail.com</a></p>
    <a href="<?= htmlspecialchars($viewBase . 'politicaPrivacidade.php', ENT_QUOTES, 'UTF-8') ?>">Política de Privacidade</a>
</footer>
</body>
</html>