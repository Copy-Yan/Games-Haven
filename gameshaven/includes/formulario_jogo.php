<?php
/**
 * Formulário de jogo compartilhado entre o admin e o criador: leitura e
 * validação dos campos, e cadastro de desenvolvedora/publicadora.
 */
require_once __DIR__ . '/../models/Jogo.php';

/**
 * Lê e valida os campos do formulário de jogo (usado em "Adicionar" e "Editar").
 * Retorna [dados, erros]: se $erros estiver vazio, os dados podem ser gravados.
 */
function lerFormularioJogo(Jogo $jogoModelo) {
    $erros = [];
    $f = [
        'titulo' => trim($_POST['titulo'] ?? ''),
        'descricao' => trim($_POST['descricao'] ?? ''),
        'id_desenvolvedoras' => (int) ($_POST['id_desenvolvedora'] ?? 0),
        'id_publicadoras' => (int) ($_POST['id_publicadora'] ?? 0),
        'preco' => str_replace(',', '.', trim($_POST['preco'] ?? '0')),
        'data_lancamento' => trim($_POST['data_lancamento'] ?? ''),
        'classificacao_etaria' => trim($_POST['classificacao_etaria'] ?? 'L'),
        'tamanho_download' => str_replace(',', '.', trim($_POST['tamanho_download'] ?? '0')),
        'requisitos_minimos' => trim($_POST['requisitos_minimos'] ?? '') ?: 'Não informado',
        'requisitos_recomendados' => trim($_POST['requisitos_recomendados'] ?? '') ?: 'Não informado',
        'nota_critica' => str_replace(',', '.', trim($_POST['nota'] ?? '')),
        'tag' => trim($_POST['tag'] ?? 'Jogo'),
        'categorias' => array_map('intval', (array) ($_POST['categorias'] ?? [])),
        'plataformas' => array_map('intval', (array) ($_POST['plataformas'] ?? [])),
    ];

    if ($f['titulo'] === '') $erros[] = 'Informe o título do jogo.';
    elseif (contarCaracteres($f['titulo']) > 100) $erros[] = 'O título pode ter no máximo 100 caracteres.';
    if ($f['descricao'] === '') $erros[] = 'Informe a descrição do jogo.';
    elseif (contarCaracteres($f['descricao']) > 250) $erros[] = 'A descrição pode ter no máximo 250 caracteres.';

    $devIds = array_column($jogoModelo->listarDesenvolvedoras(), 'id_desenvolvedoras');
    $pubIds = array_column($jogoModelo->listarPublicadoras(), 'id_publicadoras');
    if (!in_array($f['id_desenvolvedoras'], array_map('intval', $devIds), true)) $erros[] = 'Selecione uma desenvolvedora.';
    if (!in_array($f['id_publicadoras'], array_map('intval', $pubIds), true)) $erros[] = 'Selecione uma publicadora.';

    if (!is_numeric($f['preco']) || $f['preco'] < 0) $erros[] = 'Preço inválido.';
    $data = DateTime::createFromFormat('Y-m-d', $f['data_lancamento']);
    if (!$data || $data->format('Y-m-d') !== $f['data_lancamento']) $erros[] = 'Informe a data de lançamento.';
    if (!in_array($f['classificacao_etaria'], Jogo::CLASSIFICACOES, true)) $erros[] = 'Classificação etária inválida.';
    if (!is_numeric($f['tamanho_download']) || $f['tamanho_download'] < 0) $erros[] = 'Tamanho inválido.';
    if ($f['nota_critica'] !== '' && (!is_numeric($f['nota_critica']) || $f['nota_critica'] < 0 || $f['nota_critica'] > 10)) {
        $erros[] = 'A nota deve ser um número entre 0 e 10.';
    }
    if (!in_array($f['tag'], Jogo::TAGS, true)) $f['tag'] = 'Jogo';

    return [$f, $erros];
}

/**
 * Cadastro de desenvolvedora (usado pelo admin e pelo criador).
 * Retorna true se cadastrou; em caso de erro preenche $erro e $valores.
 */
function cadastrarDesenvolvedoraForm(&$erro, &$valores) {
    $valores = [
        'nome' => trim($_POST['nome'] ?? ''),
        'data_fundacao' => trim($_POST['data_fundacao'] ?? ''),
        'pais' => trim($_POST['pais'] ?? ''),
    ];
    $data = DateTime::createFromFormat('Y-m-d', $valores['data_fundacao']);
    if ($valores['nome'] === '' || $valores['pais'] === '' || !$data) {
        $erro = 'Preencha nome, data de fundação e país.';
        return false;
    }
    (new Jogo())->criarDesenvolvedora($valores['nome'], $valores['data_fundacao'], $valores['pais']);
    return true;
}

/** Cadastro de publicadora (usado pelo admin e pelo criador). Mesma regra de retorno da desenvolvedora. */
function cadastrarPublicadoraForm(&$erro, &$valores) {
    $valores = [
        'nome' => trim($_POST['nome'] ?? ''),
        'pais' => trim($_POST['pais'] ?? ''),
    ];
    if ($valores['nome'] === '' || $valores['pais'] === '') {
        $erro = 'Preencha nome e país.';
        return false;
    }
    (new Jogo())->criarPublicadora($valores['nome'], $valores['pais']);
    return true;
}
