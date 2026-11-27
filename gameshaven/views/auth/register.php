<?php $paginaAtual = 'register'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - Cadastro</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/auth.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina auth-page">
    <div class="painel auth-card">
        <?php if ($erro): ?><div class="erro"><?= esc($erro) ?></div><?php endif; ?>
        <form method="post" action="index.php?page=register">
        <div class="TXT-Auth">    
            <t1><strong>Crie sua conta no refúgio aqui!</strong></t1>
        </div>
            <label>Digite seu nome de usuário</label>
            <input type="text" name="nome_usuario" value="<?= esc($dados['nome_usuario']) ?>" required>

            <label>Digite seu nickname</label>
            <input type="text" name="nickname" value="<?= esc($dados['nickname']) ?>" required>

            <label>Digite seu e-mail</label>
            <input type="email" name="email" value="<?= esc($dados['email']) ?>" required>

            <label>Digite sua senha</label>
            <input type="password" name="senha" required>

            <label>Confirme sua senha</label>
            <input type="password" name="confirmar_senha" required>

            <div class="centro" style="margin-top:20px;">
                <label style="text-align:center;">Data de Nascimento</label>
                <div style="display:flex; gap:10px; justify-content:center;">
                    <select name="dia" required>
                        <option value="">Dia</option>
                        <?php for ($d = 1; $d <= 31; $d++): ?>
                            <option value="<?= sprintf('%02d', $d) ?>"><?= $d ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="mes" required>
                        <option value="">Mês</option>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= sprintf('%02d', $m) ?>"><?= $m ?></option>
                        <?php endfor; ?>
                    </select>
                    <select name="ano" required>
                        <option value="">Ano</option>
                        <?php for ($a = date('Y') - 10; $a >= date('Y') - 100; $a--): ?>
                            <option value="<?= $a ?>"><?= $a ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="centro" style="margin-top:22px;">
                <button type="submit" class="btn">Enviar</button>
            </div>
        </form>
    </div>
     <div class="rodape-link">
        Já possui uma conta? <a href="index.php?page=login" class="btn outline small">Login</a>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
