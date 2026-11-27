<?php $paginaAtual = 'biblioteca'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Biblioteca</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/biblioteca.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Biblioteca</h1>
    <p class="subtitulo">Jogos salvos na sua conta</p>

    <div class="aviso-biblioteca">
        <img class="aviso-brilho" src="public/assets/shining.png" alt="">Seus Jogos, todos em um só lugar.<br>
        Divirta-se e aproveite sua coleção!
    </div>

    <div class="grade-jogos">
        <?php foreach ($jogos as $jogo): ?>
        <a class="card-jogo" href="index.php?page=jogo&id=<?= (int) $jogo['id_jogos'] ?>">
            <div class="capa"><?php if (imagemExiste($jogo['capa'])): ?><img src="<?= esc($jogo['capa']) ?>" alt=""><?php else: ?>colocar imagem<?php endif; ?></div>
            <div class="info">
                <?= esc($jogo['titulo']) ?><br>
                <span class="preco"><?= formatarPreco($jogo['preco']) ?></span>
            </div>
        </a>
        <?php endforeach; ?>
        
</div>

   <div class="biblioteca-rodape-jogos"></div>
        <?php if (!$jogos): ?>
            <p class="biblioteca-rodape-jogos">Você ainda não possui jogos na biblioteca. <a href="index.php?page=home" style="text-decoration:underline;">Explorar loja</a></p>
        <?php endif; ?>
    </div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
