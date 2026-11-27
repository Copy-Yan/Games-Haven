<?php
/**
 * Classificação etária dos jogos e faixa etária do usuário.
 *
 * Regras:
 *  - classificação 'L' vale 0; '10', '12', '14', '16' e '18' valem o próprio número;
 *  - o usuário vê (e pode comprar/doar) jogos com classificação <= sua idade;
 *  - sem login, só jogos 'L' (não há como saber a idade);
 *  - administradores não têm restrição (gerenciam todo o catálogo).
 */

/** Valor numérico da classificação: 'L' -> 0, '18' -> 18. */
function classificacaoValor($classificacao) {
    $c = strtoupper(trim((string) $classificacao));
    return $c === 'L' ? 0 : (int) $c;
}

/** Idade em anos completos a partir de uma data (Y-m-d). Retorna null se inválida. */
function idadeEmAnos($dataNascimento) {
    $nascimento = DateTime::createFromFormat('Y-m-d', (string) $dataNascimento);
    if (!$nascimento) return null;
    return (int) $nascimento->diff(new DateTime('today'))->y;
}

/** Maior classificação que o usuário atual pode ver. 99 = sem restrição (admin). */
function limiteEtarioUsuario() {
    if (!estaLogado()) return 0;
    if (ehAdmin()) return 99;
    $idade = idadeEmAnos($_SESSION['usuario']['data_nascimento'] ?? null);
    if ($idade === null) return 0; // sem data válida: fica na regra mais restritiva
    return $idade;
}

/** true se o jogo (array com 'classificacao_etaria') pode ser visto pelo usuário atual. */
function jogoPermitidoParaUsuario($jogo) {
    return classificacaoValor($jogo['classificacao_etaria'] ?? 'L') <= limiteEtarioUsuario();
}

/**
 * Trecho de SQL que filtra jogos pela idade do usuário atual.
 * $alias: prefixo da tabela (ex.: 'j.') ou '' se a tabela não tem alias.
 * O limite é um inteiro calculado no servidor, então não há risco de injeção.
 */
function condicaoIdadeSql($alias = '') {
    $limite = (int) limiteEtarioUsuario();
    if ($limite >= 99) return '1 = 1';
    $c = $alias . 'classificacao_etaria';
    return "(CASE WHEN $c = 'L' THEN 0 ELSE CAST($c AS UNSIGNED) END) <= $limite";
}
