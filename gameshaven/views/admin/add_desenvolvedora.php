<?php $paginaAtual = 'admin_add_game'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Nova Desenvolvedora</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/admin.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Nova Desenvolvedora</h1>
    <p class="subtitulo"><a href="<?= esc($linkVoltar) ?>" style="text-decoration:underline;">&larr; Voltar para Adicionar Jogo</a></p>
    <?php if ($erro): ?><div class="erro"><?= esc($erro) ?></div><?php endif; ?>

    <div class="painel peach">
        <form method="post" action="<?= esc($acaoForm) ?>">
            <label>Nome *</label>
            <input type="text" name="nome" value="<?= esc($valores['nome']) ?>" required>
            <label>Data de fundação *</label>
            <input type="date" name="data_fundacao" value="<?= esc($valores['data_fundacao']) ?>" required>
            <label>País *</label>
            <input type="text" name="pais" value="<?= esc($valores['pais']) ?>" required>
            <div style="margin-top:22px; display:flex; gap:12px;">
                <button type="submit" class="btn">Cadastrar</button>
                <a href="<?= esc($linkVoltar) ?>" class="btn outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
