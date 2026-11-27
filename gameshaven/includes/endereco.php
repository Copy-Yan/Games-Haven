<?php
/** Estados brasileiros (sigla de 2 letras, coluna estado char(2)). */
const UFS_BRASIL = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];

/**
 * Lê os campos de endereço do POST e valida conforme a tabela enderecos.
 * Retorna [dados, erro]: dados sempre vem com o que foi digitado (para
 * repreencher o formulário); erro é null quando está tudo certo.
 */
function lerEnderecoPost() {
    $dados = [
        'rua' => trim($_POST['end_rua'] ?? ''),
        'numero' => trim($_POST['end_numero'] ?? ''),
        'complemento' => trim($_POST['end_complemento'] ?? ''),
        'cidade' => trim($_POST['end_cidade'] ?? ''),
        'estado' => strtoupper(trim($_POST['end_estado'] ?? '')),
        'cep' => preg_replace('/\D/', '', $_POST['end_cep'] ?? ''),
    ];
    if ($dados['rua'] === '' || $dados['numero'] === '' || $dados['cidade'] === '' || $dados['estado'] === '' || $dados['cep'] === '') {
        return [$dados, 'Preencha o endereço: rua, número, cidade, estado e CEP.'];
    }
    if (!ctype_digit($dados['numero']) || (int) $dados['numero'] > 2147483647 || (int) $dados['numero'] <= 0) {
        return [$dados, 'O número do endereço deve ser um número inteiro maior que zero.'];
    }
    if (strlen($dados['rua']) > 100) return [$dados, 'A rua pode ter no máximo 100 caracteres.'];
    if (strlen($dados['cidade']) > 50) return [$dados, 'A cidade pode ter no máximo 50 caracteres.'];
    if (strlen($dados['complemento']) > 55) return [$dados, 'O complemento pode ter no máximo 55 caracteres.'];
    if (!in_array($dados['estado'], UFS_BRASIL, true)) return [$dados, 'Informe um estado válido (sigla de 2 letras, ex.: SP).'];
    if (strlen($dados['cep']) !== 8) return [$dados, 'O CEP deve ter 8 dígitos.'];
    return [$dados, null];
}
