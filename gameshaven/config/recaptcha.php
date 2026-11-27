<?php
/**
 * reCAPTCHA Enterprise (Google Cloud).
 * O site só libera a Home depois que o visitante passa na verificação.
 *
 * Basta preencher project_id, api_key e site_key: a verificação liga sozinha.
 * Sem essas chaves, o site funciona sem verificação. Para desligar mesmo com
 * as chaves preenchidas, use 'ativo' => false.
 *
 * 'api_key' fica só no servidor (nunca aparece no navegador).
 * 'site_key' é pública: ela vai para a página de verificação.
 */
return [
    'ativo' => true,                    // false = desliga a verificação, mesmo com as chaves preenchidas

    'endpoint' => 'https://recaptchaenterprise.googleapis.com',
    'project_id' => '',                 // ID do projeto no Google Cloud
    'api_key' => '',                    // chave de API restrita ao reCAPTCHA Enterprise
    'site_key' => '',                   // chave do site (pública)

    'acao' => 'HOME',                   // ação esperada no token (conferida no servidor)
    'score_minimo' => 0.5,              // 0.0 = bot, 1.0 = humano; aceite a partir daqui

    'verificar_certificado' => true,
    'timeout' => 10,
];
