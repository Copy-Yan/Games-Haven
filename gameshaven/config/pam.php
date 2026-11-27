<?php
/**
 * Integração com a ManageEngine PAM360 (API Password Validator).
 * Valida a senha do cadastro contra a política de senhas da organização.
 *
 * Preencha os dados abaixo e mude 'ativo' para true. Enquanto estiver false,
 * o cadastro funciona normalmente, sem consultar a ManageEngine.
 *
 * ATENÇÃO: o 'authtoken' dá acesso à API do PAM360. Não o coloque em arquivos
 * acessíveis pelo navegador e não o compartilhe.
 */
return [
    'ativo' => false,

    'protocolo' => 'https',                 // https em produção
    'host' => 'pam.suaempresa.local',       // nome ou IP do servidor PAM360
    'porta' => 8282,                        // porta do PAM360

    'authtoken' => '',                      // token gerado no PAM360 (AUTHTOKEN)
    'org_nome' => '',                       // nome da organização (opcional)

    'politica_nome' => 'Strong',            // nome da política de senha (obrigatório)
    'politica_id' => null,                  // id da política (opcional, inteiro)

    'verificar_certificado' => true,        // false só se o PAM usar certificado autoassinado (não recomendado)
    'timeout' => 10,                        // segundos

    // O que fazer se o PAM360 não responder:
    // 'bloquear'  -> não cria a conta até a validação funcionar (mais seguro)
    // 'permitir'  -> cria a conta sem validar pela ManageEngine
    'se_indisponivel' => 'bloquear',
];
