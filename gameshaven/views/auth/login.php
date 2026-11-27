<?php $paginaAtual = 'login'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Login</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/auth.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina auth-page">
    <div class="painel auth-card">
        <?php if ($erro): ?><div class="erro"><?= esc($erro) ?></div><?php endif; ?>
        <form method="post" action="index.php?page=login">
        <div class="TXT-Auth">    
        <t1><strong>Seja Bem-Vindo(a) de volta!</strong></t1>
        </div>
            <label>Insira seu nome de usuário ou e-mail</label>
            <input type="text" name="login" required>

            <label>Insira sua senha</label>
            <input type="password" name="senha" required>

            <div class="centro" style="margin-top:22px;">
                <button type="submit" class="btn">Enviar</button>
            </div>
        </form>
    </div>
    
    <div class="rodape-link">
        Não possui conta?
        <a href="index.php?page=register" class="btn outline small">Registre-se</a>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
