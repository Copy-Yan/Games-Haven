<?php
/* Mostrada no lugar do site quando o banco ainda não foi atualizado. */
$pendencias = $pendencias ?? [];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Atualização do banco necessária</title>
<link rel="stylesheet" href="public/css/base.css">
</head>
<body>
<div class="wrapper">
<div class="conteudo pagina">
    <h1 class="titulo-pagina">O banco de dados precisa ser atualizado</h1>
    <p class="subtitulo">Esta versão do GamesHaven usa colunas e tabelas que ainda não existem no seu banco. Isso não é um erro do site: basta rodar uma vez o arquivo de atualização.</p>

    <div class="painel">
        <h3>Como resolver</h3>
        <ol style="line-height:1.9;">
            <li>Abra o <strong>phpMyAdmin</strong> (http://localhost/phpmyadmin).</li>
            <li>Clique no banco <strong>gameshaven</strong> na coluna da esquerda.</li>
            <li>Abra a aba <strong>SQL</strong>.</li>
            <li>Abra o arquivo <strong>atualizar_banco.sql</strong> (está na pasta do projeto), copie todo o conteúdo e cole na caixa de SQL.</li>
            <li>Clique em <strong>Executar</strong>.</li>
            <li>Volte aqui e recarregue a página.</li>
        </ol>
        <p><small>Pode rodar o arquivo mais de uma vez: ele não duplica nada.</small></p>
        <?php if ($pendencias): ?>
            <p><small>Itens que ainda faltam: <?= esc(implode(', ', $pendencias)) ?></small></p>
        <?php endif; ?>
    </div>
</div>
</div>
</body>
</html>
