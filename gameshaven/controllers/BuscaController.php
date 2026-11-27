<?php
require_once __DIR__ . '/../models/Jogo.php';

/** Página de resultados da pesquisa: mostra só os nomes dos jogos encontrados. */
function handleBusca() {
    $termo = trim($_GET['busca'] ?? '');
    $jogos = [];
    if ($termo !== '') {
        $jogos = (new Jogo())->buscarPorNome($termo);
    }
    require __DIR__ . '/../views/busca/index.php';
}
