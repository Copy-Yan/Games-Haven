<?php
require_once __DIR__ . '/../models/Suporte.php';

function handleSupport() {
    $erro = null;
    $sucesso = mensagemFlash('sucesso');
    $suporteModelo = new Suporte();
    $ticketAtual = null;
    $respostas = [];

    if (estaLogado()) {
        $ticketAtual = $suporteModelo->buscarPorUsuario($_SESSION['usuario_id']);
        if ($ticketAtual) {
            $respostas = $suporteModelo->respostas($ticketAtual['id_suporte']);
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        exigirLogin();
        $assunto = trim($_POST['assunto'] ?? '');
        $categoria = trim($_POST['categoria'] ?? 'Conta');
        $mensagem = trim($_POST['mensagem'] ?? '');

        if ($assunto === '' || $mensagem === '') {
            $erro = 'Preencha o assunto e a mensagem.';
        } else {
            $suporteModelo->abrirOuAtualizar($_SESSION['usuario_id'], $assunto, $mensagem, $categoria);
            mensagemFlash('sucesso', 'Sua mensagem foi enviada! Em breve nossa equipe responde.');
            redirecionar('suporte');
        }
    }

    require __DIR__ . '/../views/support/index.php';
}
