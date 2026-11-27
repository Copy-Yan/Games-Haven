<?php
/** Acesso às telas do criador (usado pelo painel do criador e pelas notas fiscais). */
function exigirCriadorLogado() {
    exigirLogin();
    $criadorModelo = new Criador();
    $criador = $criadorModelo->buscarPorUsuario($_SESSION['usuario_id']);
    if (!$criador) {
        mensagemFlash('erro', 'Você ainda não é um criador. Cadastre-se como criador primeiro.');
        redirecionar('register_creator');
    }
    return $criador;
}
