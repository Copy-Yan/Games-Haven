<?php $paginaAtual = 'register_creator'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Torne-se um Criador</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/auth.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina auth-page">
    <div class="painel peach auth-card">
        <?php if ($erro): ?><div class="erro"><?= esc($erro) ?></div><?php endif; ?>
        <form method="post" action="index.php?page=register_creator">
        <div class="TXT-Auth">    
            <t1><strong>Faça parte de nossa família no refúgio.</strong></t1>
        </div>
            <label>Confirme sua senha dentro do site</label>
            <input type="password" name="senha_confirmar" required>

            <label>Digite seu CPF</label>
            <input type="text" name="cpf" maxlength="14" placeholder="000.000.000-00" required>

            <label>Digite seu número de telefone</label>
            <input type="tel" name="telefone" placeholder="(00) 00000-0000" required>

            <label>Digite sua agência bancária</label>
            <input type="text" name="agencia" required>

            <label>Digite seu pix</label>
            <input type="text" name="pix" placeholder="Caso seja seu telefone ou CPF, apenas repita." required>

            <div class="centro" style="margin-top:22px;">
                <button type="submit" class="btn">Enviar</button>
            </div>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
