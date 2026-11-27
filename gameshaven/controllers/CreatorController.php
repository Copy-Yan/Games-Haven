<?php
require_once __DIR__ . '/../models/Criador.php';
require_once __DIR__ . '/../includes/criador.php';
require_once __DIR__ . '/../models/Jogo.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Doacao.php';
require_once __DIR__ . '/../models/NotaFiscal.php';
require_once __DIR__ . '/../includes/formulario_jogo.php';

/**
 * Confere se o usuário logado é um criador cadastrado e devolve a linha de
 * "criadores" correspondente. Se não for, manda para a tela de cadastro de
 * criador em vez de deixar passar.
 */
function handleCreatorDashboard() {
    $criador = exigirCriadorLogado();
    $jogoModelo = new Jogo();
    $jogos = $jogoModelo->listarPorCriador($criador['id_criadores']);
    require __DIR__ . '/../views/creator/dashboard.php';
}

function handleCreatorAddGame() {
    $criador = exigirCriadorLogado();
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
            // Vincula o jogo ao criador: ele já aparece na Home para todos (mesma tabela "jogos").
            $jogoModelo->vincularCriador($idJogo, $criador['id_criadores']);
            $jogoModelo->definirCategorias($idJogo, $f['categorias']);
            $jogoModelo->definirPlataformas($idJogo, $f['plataformas']);
            mensagemFlash('sucesso', 'Jogo "' . $f['titulo'] . '" enviado com sucesso! Ele já está disponível na página inicial da loja.');
            redirecionar('criador');
        }
        $erro = implode(' ', $erros);
        $valores = $f;
    }

    $desenvolvedoras = $jogoModelo->listarDesenvolvedoras();
    $publicadoras = $jogoModelo->listarPublicadoras();
    $categorias = $jogoModelo->listarCategorias();
    $plataformas = $jogoModelo->listarPlataformas();
    $linkNovaDev = 'index.php?page=criador_add_desenvolvedora';
    $linkNovaPub = 'index.php?page=criador_add_publicadora';
    $linkCancelar = 'index.php?page=criador';
    require __DIR__ . '/../views/creator/add_game.php';
}

/** Cadastro de desenvolvedora pelo criador (mesmo formulário do admin). */
function handleCreatorAddDesenvolvedora() {
    exigirCriadorLogado();
    $erro = null;
    $valores = ['nome' => '', 'data_fundacao' => '', 'pais' => ''];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && cadastrarDesenvolvedoraForm($erro, $valores)) {
        mensagemFlash('sucesso', 'Desenvolvedora "' . $valores['nome'] . '" cadastrada. Ela já aparece na lista do jogo.');
        redirecionar('criador_add_game');
    }
    $acaoForm = 'index.php?page=criador_add_desenvolvedora';
    $linkVoltar = 'index.php?page=criador_add_game';
    require __DIR__ . '/../views/admin/add_desenvolvedora.php';
}

/** Cadastro de publicadora pelo criador (mesmo formulário do admin). */
function handleCreatorAddPublicadora() {
    exigirCriadorLogado();
    $erro = null;
    $valores = ['nome' => '', 'pais' => ''];
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && cadastrarPublicadoraForm($erro, $valores)) {
        mensagemFlash('sucesso', 'Publicadora "' . $valores['nome'] . '" cadastrada. Ela já aparece na lista do jogo.');
        redirecionar('criador_add_game');
    }
    $acaoForm = 'index.php?page=criador_add_publicadora';
    $linkVoltar = 'index.php?page=criador_add_game';
    require __DIR__ . '/../views/admin/add_publicadora.php';
}

/** "Detalhamento dos Jogos": quem comprou e quem doou para os jogos deste criador. */
function handleCreatorGameDetails() {
    $criador = exigirCriadorLogado();
    $compras = (new Pedido())->listarCompradoresPorCriador($criador['id_criadores']);
    $doacoes = (new Doacao())->listarPorCriador($criador['id_criadores']);
    require __DIR__ . '/../views/creator/game_details.php';
}

/**
 * Mostra (e emite, se ainda não existir) a nota fiscal de UMA compra ou
 * doação específica - só se ela pertencer mesmo a um jogo deste criador,
 * pra ele não conseguir ver a nota fiscal de vendas de outro criador.
 */
function handleCreatorInvoice() {
    $criador = exigirCriadorLogado();
    $tipo = $_GET['tipo'] ?? '';
    $id = (int) ($_GET['id'] ?? 0);
    $nf = new NotaFiscal();

    if ($tipo === 'pedido' && (new Pedido())->pertenceAoCriador($id, $criador['id_criadores'])) {
        $nota = $nf->emitirPedidoParaCriador($id, $criador['id_criadores']);
    } elseif ($tipo === 'doacao' && (new Doacao())->pertenceAoCriador($id, $criador['id_criadores'])) {
        $nota = $nf->buscarPorDoacao($id) ?: $nf->emitirParaDoacao($id);
    } else {
        redirecionar('criador_jogos_detalhes');
    }
    if (!$nota) redirecionar('criador_jogos_detalhes');
    redirecionar('nota_detalhe', ['id' => $nota['id_fiscal']]);
}

function handleCreatorRemoveGame() {
    $criador = exigirCriadorLogado();
    $jogoModelo = new Jogo();
    $id = (int) ($_GET['id'] ?? 0);
    if ($id && $jogoModelo->pertenceAoCriador($id, $criador['id_criadores'])) {
        try {
            $jogoModelo->remover($id);
            mensagemFlash('sucesso', 'Jogo removido com sucesso.');
        } catch (Exception $e) {
            mensagemFlash('erro', $e->getMessage());
        }
    }
    redirecionar('criador');
}
