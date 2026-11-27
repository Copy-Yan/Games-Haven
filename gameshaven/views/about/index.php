<?php $paginaAtual = 'sobre'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Sobre</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/sobre.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina centro">Sobre</h1>

    <div class="sobre-topo-btns centro" style="justify-content:center;">
        <a href="index.php?page=home">Jogos Disponíveis</a>
        <a href="index.php?page=home">Criadores de jogos indies</a>
    </div>

    <div class="sobre-card">
        <div class="icone roxo">&#127918;</div>
        <div>
            <h3>&rsaquo; Acesse os Jogos</h3>
            <p>Explore nosso catálogo e aproveite jogos incríveis.</p>
        </div>
    </div>

    <div class="sobre-card direita">
        <div class="icone azul">&#128269;</div>
        <div>
            <h3>Descubra novos Projetos &lsaquo;</h3>
            <p>Encontre novos projetos e desenvolvedores independentes.</p>
        </div>
    </div>

    <div class="sobre-card">
        <div class="icone laranja">&#127871;</div>
        <div>
            <h3>&rsaquo; Doe e ajude novos Projetos</h3>
            <p>Contribua para o desenvolvimento de novos projetos.</p>
        </div>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
