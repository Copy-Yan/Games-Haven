<?php
/**
 * Cliente do reCAPTCHA Enterprise (recaptchaenterprise.googleapis.com).
 * Chamado no servidor: a api_key nunca vai para o navegador.
 *
 * recaptchaAvaliar() retorna ['status' => ..., 'motivo' => ..., 'score' => ...]:
 *   'desativado'   -> verificação desligada em config/recaptcha.php
 *   'aprovado'     -> token válido, ação correta e score acima do mínimo
 *   'reprovado'    -> token inválido, ação diferente ou score baixo (suspeito)
 *   'indisponivel' -> não foi possível consultar o Google
 */
function recaptchaConfig() {
    return require __DIR__ . '/../config/recaptcha.php';
}

/**
 * A verificação fica ativa quando 'ativo' não é false e as três chaves
 * (project_id, api_key e site_key) estão preenchidas.
 */
function recaptchaAtivo() {
    $cfg = recaptchaConfig();
    if (array_key_exists('ativo', $cfg) && $cfg['ativo'] === false) return false;
    return trim((string) ($cfg['project_id'] ?? '')) !== ''
        && trim((string) ($cfg['api_key'] ?? '')) !== ''
        && trim((string) ($cfg['site_key'] ?? '')) !== '';
}

function recaptchaAvaliar($token) {
    if (!recaptchaAtivo()) {
        return ['status' => 'desativado', 'motivo' => '', 'score' => null];
    }
    $cfg = recaptchaConfig();
    if (trim((string) $token) === '') {
        return ['status' => 'reprovado', 'motivo' => 'Verificação não recebida. Tente novamente.', 'score' => null];
    }

    $url = rtrim($cfg['endpoint'], '/') . '/v1/projects/' . rawurlencode($cfg['project_id'])
         . '/assessments?key=' . urlencode($cfg['api_key']);
    $corpo = json_encode(['event' => [
        'token' => $token,
        'expectedAction' => $cfg['acao'],
        'siteKey' => $cfg['site_key'],
    ]]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $corpo,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => (int) $cfg['timeout'],
        CURLOPT_SSL_VERIFYPEER => (bool) $cfg['verificar_certificado'],
        CURLOPT_SSL_VERIFYHOST => $cfg['verificar_certificado'] ? 2 : 0,
    ]);
    $resposta = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resposta === false || $http !== 200) {
        return ['status' => 'indisponivel', 'motivo' => 'Não foi possível verificar agora. Tente novamente.', 'score' => null];
    }

    $json = json_decode($resposta, true);
    $tp = $json['tokenProperties'] ?? null;
    if (!is_array($tp) || !array_key_exists('valid', $tp)) {
        return ['status' => 'indisponivel', 'motivo' => 'Resposta inesperada da verificação.', 'score' => null];
    }
    if (empty($tp['valid'])) {
        return ['status' => 'reprovado', 'motivo' => 'Verificação inválida ou expirada. Tente novamente.', 'score' => null];
    }
    if (($tp['action'] ?? '') !== $cfg['acao']) {
        return ['status' => 'reprovado', 'motivo' => 'Verificação feita para outra ação.', 'score' => null];
    }

    $score = isset($json['riskAnalysis']['score']) ? (float) $json['riskAnalysis']['score'] : 0.0;
    if ($score < (float) $cfg['score_minimo']) {
        return ['status' => 'reprovado', 'motivo' => 'Não foi possível confirmar que você é uma pessoa.', 'score' => $score];
    }
    return ['status' => 'aprovado', 'motivo' => '', 'score' => $score];
}
