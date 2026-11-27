<?php
/* Tela de verificação antes de entrar no site (reCAPTCHA Enterprise).
   Espera: $siteKey, $acao, $erro (ou null). */
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>GamesHaven - Verificação de segurança</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/auth.css">
</head>
<body>
<div class="wrapper">
<div class="conteudo pagina centro" style="padding-top:60px;">
    <h1 class="titulo-pagina">Verificação de segurança</h1>
    <p class="subtitulo">Antes de entrar na GamesHaven, confirmamos que você é uma pessoa.</p>

    <?php if (!empty($erro)): ?><div class="erro"><?= esc($erro) ?></div><?php endif; ?>

    <div class="painel" style="max-width:420px; margin:0 auto;">
        <p id="captcha-status">Verificando...</p>
        <form id="captcha-form" method="post" action="index.php?page=captcha">
            <input type="hidden" name="token" id="captcha-token" value="">
            <button type="button" id="captcha-botao" class="btn" onclick="confirmarHumano()" style="display:none;">Continuar</button>
        </form>
        <p><small>Esta verificação é feita pelo Google reCAPTCHA Enterprise.</small></p>
    </div>
</div>
</div>

<script src="https://www.google.com/recaptcha/enterprise.js?render=<?= esc($siteKey) ?>" async defer></script>
<script>
var CAPTCHA_SITE_KEY = <?= json_encode((string) $siteKey) ?>;
var CAPTCHA_ACAO = <?= json_encode((string) $acao) ?>;

function mostrarStatus(texto) {
    document.getElementById('captcha-status').textContent = texto;
}

function confirmarHumano() {
    mostrarStatus('Verificando...');
    grecaptcha.enterprise.ready(function () {
        grecaptcha.enterprise.execute(CAPTCHA_SITE_KEY, { action: CAPTCHA_ACAO }).then(function (token) {
            document.getElementById('captcha-token').value = token;
            document.getElementById('captcha-form').submit();
        }, function () {
            mostrarStatus('Não foi possível verificar agora. Recarregue a página e tente de novo.');
        });
    });
}

// Tenta sozinho assim que o script do Google carregar; se não carregar, mostra o botão.
window.addEventListener('load', function () {
    var tentativas = 0;
    var espera = setInterval(function () {
        tentativas++;
        if (window.grecaptcha && grecaptcha.enterprise) {
            clearInterval(espera);
            confirmarHumano();
        } else if (tentativas > 40) {
            clearInterval(espera);
            mostrarStatus('Não foi possível carregar a verificação. Verifique sua conexão.');
            document.getElementById('captcha-botao').style.display = 'inline-block';
        }
    }, 200);
});
</script>
</body>
</html>
