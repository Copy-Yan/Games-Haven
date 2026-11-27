<?php
require_once __DIR__ . '/../models/NotaFiscal.php';
require_once __DIR__ . '/../models/Criador.php';
require_once __DIR__ . '/../includes/criador.php';

/** Filtros da listagem de notas, lidos da URL (todos opcionais). */
function lerFiltrosNota() {
    return [
        'tipo' => in_array($_GET['tipo'] ?? '', ['venda', 'doacao'], true) ? $_GET['tipo'] : '',
        'metodo' => in_array($_GET['metodo'] ?? '', ['cartao credito', 'cartao debito', 'pix'], true) ? $_GET['metodo'] : '',
        'de' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['de'] ?? '') ? $_GET['de'] : '',
        'ate' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['ate'] ?? '') ? $_GET['ate'] : '',
        'numero' => trim($_GET['numero'] ?? ''),
        'criador' => (int) ($_GET['criador'] ?? 0),
    ];
}

/** Lista de todas as notas (admin): análise completa, filtros e emissão das pendentes. */
function handleNotasAdmin() {
    exigirAdmin();
    $nf = new NotaFiscal();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'emitir_pendentes') {
        $criadas = $nf->emitirPendentes();
        $nf->completarRetratos();
        mensagemFlash('sucesso', 'Notas emitidas agora: ' . $criadas . '.');
        redirecionar('admin_notas');
    }

    $filtros = lerFiltrosNota();
    $notas = $nf->listar($filtros);
    $resumo = $nf->resumo($filtros, null, true);
    $criadores = $nf->criadores();
    $escopo = 'admin';
    $linkBase = 'admin_notas';
    $titulo = 'Notas fiscais';
    require __DIR__ . '/../views/nota/lista.php';
}

/** Lista das notas do próprio criador (mesma análise, só os dados dele). */
function handleNotasCriador() {
    $criador = exigirCriadorLogado();
    $nf = new NotaFiscal();
    $filtros = lerFiltrosNota();
    $notas = $nf->listar($filtros, $criador['id_criadores']);
    $resumo = $nf->resumo($filtros, $criador['id_criadores'], false);
    $criadores = [];
    $escopo = 'criador';
    $linkBase = 'criador_notas';
    $titulo = 'Minhas notas fiscais';
    require __DIR__ . '/../views/nota/lista.php';
}

/**
 * Nota detalhada (para visualizar e imprimir). Admin vê qualquer nota;
 * criador vê apenas as notas emitidas para ele.
 */
function handleNotaDetalhe() {
    $id = (int) ($_GET['id'] ?? 0);
    $nf = new NotaFiscal();
    $nota = $nf->buscarPorId($id);
    if (!$nota) redirecionar('home');

    if (ehAdmin()) {
        $escopo = 'admin';
    } else {
        if (!estaLogado()) redirecionar('login');
        $criador = (new Criador())->buscarPorUsuario($_SESSION['usuario_id']);
        if (!$criador || (int) $criador['id_criadores'] !== (int) $nota['id_criadores']) {
            mensagemFlash('erro', 'Você não tem acesso a esta nota fiscal.');
            redirecionar('home');
        }
        $escopo = 'criador';
    }

    $nf->completarRetrato($nota);
    $nota = $nf->buscarPorId($id);
    $itens = $nf->itensDaNota($nota);
    $loja = require __DIR__ . '/../config/loja.php';
    $beneficiario = $nf->nomeCriador($nota['id_criadores']);
    require __DIR__ . '/../views/nota/detalhe.php';
}
