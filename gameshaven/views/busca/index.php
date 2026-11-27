<?php $paginaAtual = 'busca'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Pesquisa</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/busca.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <?php if ($termo === ''): ?>
        <h1 class="titulo-pagina">Pesquisar jogos</h1>
        <p class="subtitulo">Digite o nome de um jogo na barra de pesquisa do topo.</p>
    <?php else: ?>
        <h1 class="titulo-pagina">Resultados para &ldquo;<?= esc($termo) ?>&rdquo;</h1>
        <p class="subtitulo"><?= count($jogos) ?> <?= count($jogos) === 1 ? 'jogo encontrado' : 'jogos encontrados' ?>.</p>

        <?php if ($jogos): ?>
        <ul class="resultados-lista">
            <?php foreach ($jogos as $jogo): ?>
                <li><a href="index.php?page=jogo&id=<?= (int) $jogo['id_jogos'] ?>"><?= esc($jogo['titulo']) ?></a></li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <div class="resultados-vazio">Nenhum jogo com esse nome. Tente outra palavra.</div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
