
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
<script>
    document.querySelectorAll('.flash-message').forEach(notification => {
        const duration = Number(notification.dataset.duration);
        const dismissButton = notification.querySelector('.flash-message-dismiss');
        let remaining = duration;
        let startedAt;
        let timer;
        let removalTimer;
        let isPaused = false;
        let isClosing = false;
        let pointerActive = false;
        let focusActive = false;

        notification.style.setProperty('--flash-duration', `${duration}ms`);

        const close = () => {
            if (isClosing) return;
            isClosing = true;
            clearTimeout(timer);
            clearTimeout(removalTimer);
            notification.classList.add('flash-message-closing');
            removalTimer = setTimeout(() => notification.remove(), 300);
        };

        const resume = () => {
            if (!isPaused || isClosing || pointerActive || focusActive) return;
            isPaused = false;
            startedAt = Date.now();
            notification.classList.remove('flash-message-paused');
            timer = setTimeout(close, remaining);
        };

        const pause = () => {
            if (isPaused || isClosing) return;
            isPaused = true;
            remaining = Math.max(0, remaining - (Date.now() - startedAt));
            clearTimeout(timer);
            notification.classList.add('flash-message-paused');
        };

        dismissButton.addEventListener('click', close);
        notification.addEventListener('pointerenter', () => {
            pointerActive = true;
            pause();
        });
        notification.addEventListener('pointerleave', () => {
            pointerActive = false;
            resume();
        });
        notification.addEventListener('focusin', () => {
            focusActive = true;
            pause();
        });
        notification.addEventListener('focusout', event => {
            if (!notification.contains(event.relatedTarget)) {
                focusActive = false;
                resume();
            }
        });

        startedAt = Date.now();
        timer = setTimeout(close, remaining);
    });
</script>
</body>
</html>