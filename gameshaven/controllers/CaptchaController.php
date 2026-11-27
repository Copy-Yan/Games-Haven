<?php
require_once __DIR__ . '/../includes/recaptcha.php';

/**
 * Recebe o token do reCAPTCHA, valida no Google (servidor) e libera a sessão.
 * Só POST faz a validação; um GET apenas volta para a tela de verificação.
 */
function handleCaptcha() {
    $cfg = recaptchaConfig();
    $erro = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $resultado = recaptchaAvaliar($_POST['token'] ?? '');
        if (in_array($resultado['status'], ['aprovado', 'desativado'], true)) {
            $_SESSION['captcha_ok'] = true;
            redirecionar('home');
        }
        $erro = $resultado['motivo'] ?: 'Não foi possível verificar. Tente novamente.';
    }

    $siteKey = $cfg['site_key'];
    $acao = $cfg['acao'];
    require __DIR__ . '/../views/captcha/index.php';
}
