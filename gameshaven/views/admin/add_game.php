<?php $paginaAtual = 'admin_add_game'; $sucesso = mensagemFlash('sucesso'); ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Adicionar Jogo</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/admin.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina">Adicionar Jogo</h1>
    <?php if ($sucesso): ?><div class="sucesso"><?= esc($sucesso) ?></div><?php endif; ?>
    <?php if ($erro): ?><div class="erro"><?= esc($erro) ?></div><?php endif; ?>

    <div class="painel peach largo">
        <?php
        $valores = $valores ?? ['titulo'=>'','descricao'=>'','id_desenvolvedoras'=>0,'id_publicadoras'=>0,'preco'=>'','data_lancamento'=>'',
            'classificacao_etaria'=>'L','tamanho_download'=>'','requisitos_minimos'=>'','requisitos_recomendados'=>'','nota_critica'=>'',
            'tag'=>'Jogo','categorias'=>[],'plataformas'=>[]];
        $capaAtual = null;
        $acaoForm = 'index.php?page=admin_add_game';
        $textoBotao = 'Salvar Jogo';
        require __DIR__ . '/_form_jogo.php';
        ?>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
