<?php
require_once __DIR__ . '/../models/Jogo.php';
require_once __DIR__ . '/../models/Suporte.php';
require_once __DIR__ . '/../models/Doacao.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Criador.php';
require_once __DIR__ . '/../includes/formulario_jogo.php';

function handleAdminDashboard() {
    exigirAdmin();
    $jogoModelo = new Jogo();
    $doacaoModelo = new Doacao();
    $pedidoModelo = new Pedido();
    $criadorModelo = new Criador();
    $jogos = $jogoModelo->listarTodos();
    $totalDoacoes = $doacaoModelo->total();
    $totalCompras = $pedidoModelo->total();
    $totalCriadores = $criadorModelo->total();
    require __DIR__ . '/../views/admin/dashboard.php';
}

function handleAdminGames() {
    exigirAdmin();
    $jogoModelo = new Jogo();
    $jogos = $jogoModelo->listarTodos();
    require __DIR__ . '/../views/admin/games.php';
}

function handleAdminRemoveGame() {
    exigirAdmin();
    $id = (int) ($_GET['id'] ?? 0);
    if ($id) {
        $jogoModelo = new Jogo();
        $imagens = $jogoModelo->buscarImagens($id);
        try {
            $jogoModelo->remover($id);
            removerArquivoUpload($imagens['capa']);
            removerArquivoUpload($imagens['icone']);
            mensagemFlash('sucesso', 'Jogo removido com sucesso.');
        } catch (Exception $e) {
            mensagemFlash('erro', $e->getMessage());
        }
    }
    redirecionar('admin_games');
}

function handleAdminAddGame() {
    exigirAdmin();
    $jogoModelo = new Jogo();
    $erro = null;
    $valores = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        [$f, $erros] = lerFormularioJogo($jogoModelo);
        $capa = null;
        $icone = null;
        if (!$erros) {
            try {
                $capa = salvarCapaUpload('capa');
                $icone = salvarIconeUpload('icone');
            } catch (Exception $e) {
                removerArquivoUpload($capa);
                $erros[] = $e->getMessage();
            }
        }
        if (!$erros) {
            $idJogo = $jogoModelo->criar(array_merge($f, ['capa' => $capa, 'icone' => $icone]));
            $jogoModelo->definirCategorias($idJogo, $f['categorias']);
            $jogoModelo->definirPlataformas($idJogo, $f['plataformas']);
            mensagemFlash('sucesso', 'Jogo "' . $f['titulo'] . '" adicionado com sucesso!');
            redirecionar('admin_games');
        }
        $erro = implode(' ', $erros);
        $valores = $f;
    }

    $desenvolvedoras = $jogoModelo->listarDesenvolvedoras();
    $publicadoras = $jogoModelo->listarPublicadoras();
    $categorias = $jogoModelo->listarCategorias();
    $plataformas = $jogoModelo->listarPlataformas();
    $linkNovaDev = 'index.php?page=admin_add_desenvolvedora';
    $linkNovaPub = 'index.php?page=admin_add_publicadora';
    $linkCancelar = 'index.php?page=admin_games';
    require __DIR__ . '/../views/admin/add_game.php';
}

function handleAdminEditGame() {
    exigirAdmin();
    $jogoModelo = new Jogo();
    $id = (int) ($_GET['id'] ?? 0);
    $jogo = $jogoModelo->buscarPorId($id);
    if (!$jogo) redirecionar('admin_games');

    $erro = null;
    $valores = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        [$f, $erros] = lerFormularioJogo($jogoModelo);
        $novaCapa = null;
        $novoIcone = null;
        if (!$erros) {
            try {
                $novaCapa = salvarCapaUpload('capa');
                $novoIcone = salvarIconeUpload('icone');
            } catch (Exception $e) {
                removerArquivoUpload($novaCapa);
                $erros[] = $e->getMessage();
            }
        }
        if (!$erros) {
            $jogoModelo->atualizar($id, array_merge($f, [
                'capa' => $novaCapa ?? $jogo['capa'],
                'icone' => $novoIcone ?? $jogo['icone'],
            ]));
            if ($novoIcone !== null) removerArquivoUpload($jogo['icone']);
            $jogoModelo->definirCategorias($id, $f['categorias']);
            $jogoModelo->definirPlataformas($id, $f['plataformas']);
            if ($novaCapa !== null) removerArquivoCapa($jogo['capa']);
            mensagemFlash('sucesso', 'Jogo atualizado com sucesso!');
            redirecionar('admin_games');
        }
        $erro = implode(' ', $erros);
        $valores = $f;
    }

    // Valores que aparecem no formulário: os enviados (se houve erro) ou os gravados no banco.
    $valores = $valores ?? [
        'titulo' => $jogo['titulo'],
        'descricao' => $jogo['descricao'],
        'id_desenvolvedoras' => (int) $jogo['id_desenvolvedoras'],
        'id_publicadoras' => (int) $jogo['id_publicadoras'],
        'preco' => $jogo['preco'],
        'data_lancamento' => $jogo['data_lancamento'],
        'classificacao_etaria' => $jogo['classificacao_etaria'],
        'tamanho_download' => $jogo['tamanho_download'],
        'requisitos_minimos' => $jogo['requisitos_minimos'],
        'requisitos_recomendados' => $jogo['requisitos_recomendados'],
        'nota_critica' => $jogo['nota_critica'],
        'tag' => $jogo['tag'],
        'categorias' => $jogoModelo->idsCategoriasDoJogo($id),
        'plataformas' => $jogoModelo->idsPlataformasDoJogo($id),
    ];

    $desenvolvedoras = $jogoModelo->listarDesenvolvedoras();
    $publicadoras = $jogoModelo->listarPublicadoras();
    $categorias = $jogoModelo->listarCategorias();
    $plataformas = $jogoModelo->listarPlataformas();
    $linkNovaDev = 'index.php?page=admin_add_desenvolvedora';
    $linkNovaPub = 'index.php?page=admin_add_publicadora';
    $linkCancelar = 'index.php?page=admin_games';
    require __DIR__ . '/../views/admin/edit_game.php';
}

function handleAdminAddDesenvolvedora() {
    exigirAdmin();
    $erro = null;
    $valores = ['nome' => '', 'data_fundacao' => '', 'pais' => ''];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && cadastrarDesenvolvedoraForm($erro, $valores)) {
        mensagemFlash('sucesso', 'Desenvolvedora "' . $valores['nome'] . '" cadastrada. Ela já aparece na lista do jogo.');
        redirecionar('admin_add_game');
    }
    $acaoForm = 'index.php?page=admin_add_desenvolvedora';
    $linkVoltar = 'index.php?page=admin_add_game';
    require __DIR__ . '/../views/admin/add_desenvolvedora.php';
}

function handleAdminAddPublicadora() {
    exigirAdmin();
    $erro = null;
    $valores = ['nome' => '', 'pais' => ''];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && cadastrarPublicadoraForm($erro, $valores)) {
        mensagemFlash('sucesso', 'Publicadora "' . $valores['nome'] . '" cadastrada. Ela já aparece na lista do jogo.');
        redirecionar('admin_add_game');
    }
    $acaoForm = 'index.php?page=admin_add_publicadora';
    $linkVoltar = 'index.php?page=admin_add_game';
    require __DIR__ . '/../views/admin/add_publicadora.php';
}

function handleAdminTickets() {
    exigirAdmin();
    $suporteModelo = new Suporte();
    $tickets = $suporteModelo->listarTodos();
    require __DIR__ . '/../views/admin/tickets.php';
}

function handleAdminTicketReply() {
    exigirAdmin();
    $id = (int) ($_GET['id'] ?? 0);
    $suporteModelo = new Suporte();
    $ticket = $suporteModelo->buscarPorId($id);
    if (!$ticket) redirecionar('admin_tickets');
    $suporteRespostas = $suporteModelo->respostas($id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $resposta = trim($_POST['resposta'] ?? '');
        if ($resposta !== '') {
            $suporteModelo->responder($id, $_SESSION['usuario_id'], $resposta);
            mensagemFlash('sucesso', 'Resposta enviada.');
            redirecionar('admin_tickets');
        }
    }
    require __DIR__ . '/../views/admin/ticket_reply.php';
}

function handleAdminDonations() {
    exigirAdmin();
    $doacaoModelo = new Doacao();
    $doacoes = $doacaoModelo->listarTodas();
    require __DIR__ . '/../views/admin/donations.php';
}

function handleAdminPurchases() {
    exigirAdmin();
    $pedidoModelo = new Pedido();
    $pedidos = $pedidoModelo->listarTodos();
    require __DIR__ . '/../views/admin/purchases.php';
}

function handleAdminCreators() {
    exigirAdmin();
    $criadorModelo = new Criador();
    $criadores = $criadorModelo->listarTodos();
    require __DIR__ . '/../views/admin/creators.php';
}
