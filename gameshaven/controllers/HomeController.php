<?php
require_once __DIR__ . '/../models/Jogo.php';

function handleHome() {
    $jogoModelo = new Jogo();
    $busca = trim($_GET['busca'] ?? '');
    if ($busca !== '') {
        // A pesquisa tem página própria, que mostra só os nomes encontrados.
        redirecionar('busca', ['busca' => $busca]);
    }
    $categorias = $jogoModelo->listarCategorias();
    $recentes = $jogoModelo->listarRecentes(6);
    $todos = $jogoModelo->listarTodos($busca);

    // Critério próprio para cada vitrine da Home (em vez de repetir os mesmos 3 jogos em tudo).
    $novosLancamentos = $jogoModelo->listarNovosLancamentos(3);
    $emDestaque = $jogoModelo->listarEmDestaque(3);
    $maisJogados = $jogoModelo->listarMaisJogados(3);
    $preLancamentos = $jogoModelo->listarPreLancamentos(3);
    // Sem pré-lançamento (data futura), a seção mostra os jogos recém-cadastrados.
    $tituloPreLancamentos = 'Pré-Lançamentos';
    if (!$preLancamentos) {
        $preLancamentos = $jogoModelo->listarRecentesCadastros(3);
        $tituloPreLancamentos = 'Recém cadastrados';
    }
    $idUsuarioLogado = estaLogado() ? $_SESSION['usuario_id'] : null;
    $paraVoce = $busca === '' ? $jogoModelo->listarParaVoce($idUsuarioLogado, 8) : $todos;

    require __DIR__ . '/../views/home/index.php';
}
