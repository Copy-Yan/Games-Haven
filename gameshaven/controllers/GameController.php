<?php
require_once __DIR__ . '/../models/Jogo.php';
require_once __DIR__ . '/../models/Avaliacao.php';
require_once __DIR__ . '/../models/Carrinho.php';

function handleGameDetail() {
    $id = (int) ($_GET['id'] ?? 0);
    $jogoModelo = new Jogo();
    $avaliacaoModelo = new Avaliacao();
    $jogo = $jogoModelo->buscarPorId($id);
    if (!$jogo) redirecionar('home');
    if (!jogoPermitidoParaUsuario($jogo)) {
        mensagemFlash('erro', 'Este jogo não está disponível para a sua faixa etária.');
        redirecionar('home');
    }
    $avaliacoes = $avaliacaoModelo->listarPorJogo($id);
    $erro = null;
    $sucesso = mensagemFlash('sucesso');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        exigirLogin();
        $acao = $_POST['acao'] ?? '';

        if ($acao === 'avaliar') {
            $nota = (int) ($_POST['nota'] ?? 0);
            $comentario = trim($_POST['comentario'] ?? '');
            if ($nota < 0 || $nota > 5 || $comentario === '') {
                $erro = 'Escolha uma nota de 0 a 5 e escreva um comentário.';
            } else {
                $avaliacaoModelo->adicionar($_SESSION['usuario_id'], $id, $nota, $comentario);
                redirecionar('jogo', ['id' => $id]);
            }
        } elseif ($acao === 'carrinho') {
            (new Carrinho())->adicionar($_SESSION['usuario_id'], $id);
            mensagemFlash('sucesso', 'Jogo adicionado ao carrinho!');
            redirecionar('jogo', ['id' => $id]);
        }
    }

    require __DIR__ . '/../views/game/detail.php';
}
