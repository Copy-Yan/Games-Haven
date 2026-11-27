<?php $paginaAtual = 'admin_games'; $erroFlash = null; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Editar Jogo</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/admin.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Editar Jogo</h1>
    <p class="subtitulo"><a href="index.php?page=admin_games" style="text-decoration:underline;">&larr; Voltar para a lista de jogos</a></p>
    <?php if ($erro): ?><div class="erro"><?= esc($erro) ?></div><?php endif; ?>

    <div class="painel peach largo">
        <?php
        $capaAtual = $jogo['capa'] ?? null;
        $iconeAtual = $jogo['icone'] ?? null;
        $acaoForm = 'index.php?page=admin_edit_game&id=' . (int) $jogo['id_jogos'];
        $textoBotao = 'Salvar Alterações';
        require __DIR__ . '/_form_jogo.php';
        ?>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
