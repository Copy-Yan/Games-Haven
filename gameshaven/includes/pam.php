<?php
/**
 * Cliente da API Password Validator do ManageEngine PAM360.
 * Chamado no servidor (PHP): o AUTHTOKEN nunca vai para o navegador.
 *
 * Retorna sempre um array ['status' => ..., 'mensagem' => ...], com status:
 *   'desativado'   -> integração desligada em config/pam.php
 *   'valida'       -> senha aprovada pela política
 *   'invalida'     -> senha recusada pela política (mensagem traz o motivo)
 *   'indisponivel' -> não foi possível consultar o PAM360 (rede, token, resposta inválida)
 */
function pamConfig() {
    return require __DIR__ . '/../config/pam.php';
}

function pamValidarSenha($senha) {
    $cfg = pamConfig();
    if (empty($cfg['ativo'])) {
        return ['status' => 'desativado', 'mensagem' => ''];
    }

    $detalhes = [
        'password' => $senha,
        'passwordPolicy' => $cfg['politica_nome'],
    ];
    if (!empty($cfg['politica_id'])) {
        $detalhes['passwordPolicyId'] = (string) $cfg['politica_id'];
    }
    $inputData = json_encode(['operation' => ['details' => $detalhes]]);

    $url = $cfg['protocolo'] . '://' . $cfg['host'] . ':' . $cfg['porta'] . '/restapi/json/v1/passwords/validate';
    $cabecalhos = [
        'AUTHTOKEN: ' . $cfg['authtoken'],
        'Content-Type: application/x-www-form-urlencoded',
    ];
    if (!empty($cfg['org_nome'])) {
        $cabecalhos[] = 'orgName: ' . $cfg['org_nome'];
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query(['INPUT_DATA' => $inputData]),
        CURLOPT_HTTPHEADER => $cabecalhos,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => (int) $cfg['timeout'],
        CURLOPT_SSL_VERIFYPEER => (bool) $cfg['verificar_certificado'],
        CURLOPT_SSL_VERIFYHOST => $cfg['verificar_certificado'] ? 2 : 0,
    ]);
    $resposta = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resposta === false || $http !== 200) {
        return ['status' => 'indisponivel', 'mensagem' => 'Não foi possível consultar a política de senhas.'];
    }

    // Resposta esperada: {"operation":{"result":{"message":"Valid","status":"Success","statusCode":20000},...}}
    $json = json_decode($resposta, true);
    $resultado = $json['operation']['result'] ?? null;
    if (!is_array($resultado) || !isset($resultado['status'])) {
        return ['status' => 'indisponivel', 'mensagem' => 'Resposta inesperada da política de senhas.'];
    }

    $mensagem = trim((string) ($resultado['message'] ?? ''));
    if ($resultado['status'] === 'Success' && strcasecmp($mensagem, 'Valid') === 0) {
        return ['status' => 'valida', 'mensagem' => ''];
    }
    return ['status' => 'invalida', 'mensagem' => $mensagem !== '' ? $mensagem : 'A senha não atende à política de segurança.'];
}

/** true se o cadastro pode seguir mesmo sem resposta do PAM360. */
function pamPermiteSemValidacao() {
    return (pamConfig()['se_indisponivel'] ?? 'bloquear') === 'permitir';
}
