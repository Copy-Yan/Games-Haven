<?php
/* Lista de notas com filtros e análise. Espera: $notas, $resumo, $filtros, $criadores,
   $escopo ('admin'|'criador'), $linkBase, $titulo. */
$paginaAtual = $escopo === 'admin' ? 'admin' : 'criador';
$sucesso = mensagemFlash('sucesso');
$erroFlash = mensagemFlash('erro');
$pct = fn($valor) => $resumo['valor_total'] > 0 ? number_format(($valor / $resumo['valor_total']) * 100, 1, ',', '') . '%' : '-';
$mesNome = fn($ym) => date('m/Y', strtotime($ym . '-01'));
$metodoNome = ['cartao credito' => 'Cartão de crédito', 'cartao debito' => 'Cartão de débito', 'pix' => 'Pix', 'não informado' => 'Não informado'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>GamesHaven - <?= esc($titulo) ?></title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/admin.css">
<link rel="stylesheet" href="public/css/nota.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">
    <h1 class="titulo-pagina"><?= esc($titulo) ?></h1>
    <p class="subtitulo nao-imprimir">
        <?= $escopo === 'admin' ? 'Todas as notas emitidas pela loja.' : 'Notas emitidas pelas vendas e doações dos seus jogos.' ?>
        <a href="index.php?page=<?= $escopo === 'admin' ? 'admin' : 'criador' ?>" style="text-decoration:underline;">&larr; Voltar ao painel</a>
    </p>
    <?php if ($sucesso): ?><div class="sucesso nao-imprimir"><?= esc($sucesso) ?></div><?php endif; ?>
    <?php if ($erroFlash): ?><div class="erro nao-imprimir"><?= esc($erroFlash) ?></div><?php endif; ?>

    <div class="admin-cards nao-imprimir">
        <div class="admin-card">Notas emitidas <small><?= (int) $resumo['quantidade'] ?> nota(s)</small></div>
        <div class="admin-card">Valor total <small><?= formatarPreco($resumo['valor_total']) ?></small></div>
        <div class="admin-card">Ticket médio <small><?= formatarPreco($resumo['ticket_medio']) ?></small></div>
        <div class="admin-card">Vendas <small><?= (int) $resumo['vendas']['quantidade'] ?> · <?= formatarPreco($resumo['vendas']['valor']) ?></small></div>
        <div class="admin-card">Doações <small><?= (int) $resumo['doacoes']['quantidade'] ?> · <?= formatarPreco($resumo['doacoes']['valor']) ?></small></div>
    </div>

    <form method="get" action="index.php" class="nota-filtros nao-imprimir">
        <input type="hidden" name="page" value="<?= esc($linkBase) ?>">
        <label>Tipo
            <select name="tipo">
                <option value="">Todos</option>
                <option value="venda" <?= $filtros['tipo'] === 'venda' ? 'selected' : '' ?>>Venda</option>
                <option value="doacao" <?= $filtros['tipo'] === 'doacao' ? 'selected' : '' ?>>Doação</option>
            </select>
        </label>
        <label>Forma de pagamento
            <select name="metodo">
                <option value="">Todas</option>
                <?php foreach (['cartao credito' => 'Cartão de crédito', 'cartao debito' => 'Cartão de débito', 'pix' => 'Pix'] as $v => $n): ?>
                    <option value="<?= $v ?>" <?= $filtros['metodo'] === $v ? 'selected' : '' ?>><?= $n ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>De <input type="date" name="de" value="<?= esc($filtros['de']) ?>"></label>
        <label>Até <input type="date" name="ate" value="<?= esc($filtros['ate']) ?>"></label>
        <label>Número <input type="text" name="numero" value="<?= esc($filtros['numero']) ?>" placeholder="NF00000001"></label>
        <?php if ($escopo === 'admin'): ?>
        <label>Criador
            <select name="criador">
                <option value="">Todos</option>
                <?php foreach ($criadores as $c): ?>
                    <option value="<?= (int) $c['id_criadores'] ?>" <?= $filtros['criador'] === (int) $c['id_criadores'] ? 'selected' : '' ?>><?= esc($c['nickname']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <?php endif; ?>
        <div class="nota-filtros-acoes">
            <button type="submit" class="btn small">Filtrar</button>
            <a href="index.php?page=<?= esc($linkBase) ?>" class="btn small outline">Limpar</a>
            <button type="button" class="btn small outline" onclick="window.print()">Imprimir lista</button>
            <?php if ($escopo === 'admin'): ?>
            <button type="submit" name="acao" value="emitir_pendentes" formmethod="post" class="btn small" formaction="index.php?page=admin_notas" onclick="return confirm('Emitir as notas que ainda faltam para vendas e doações antigas?');">Emitir notas pendentes</button>
            <?php endif; ?>
        </div>
    </form>

    <div class="nota-analise">
        <div>
            <h3>Por forma de pagamento</h3>
            <table class="tabela"><thead><tr><th>Forma</th><th>Notas</th><th>Valor</th><th>%</th></tr></thead><tbody>
            <?php foreach ($resumo['por_metodo'] as $m => $v): ?>
                <tr><td><?= esc($metodoNome[$m] ?? $m) ?></td><td><?= (int) $v['quantidade'] ?></td><td><?= formatarPreco($v['valor']) ?></td><td><?= $pct($v['valor']) ?></td></tr>
            <?php endforeach; ?>
            <?php if (!$resumo['por_metodo']): ?><tr><td colspan="4" class="vazio">Sem dados no período.</td></tr><?php endif; ?>
            </tbody></table>
        </div>
        <div>
            <h3>Por mês (últimos 12)</h3>
            <table class="tabela"><thead><tr><th>Mês</th><th>Notas</th><th>Valor</th></tr></thead><tbody>
            <?php foreach ($resumo['por_mes'] as $ym => $v): ?>
                <tr><td><?= esc($mesNome($ym)) ?></td><td><?= (int) $v['quantidade'] ?></td><td><?= formatarPreco($v['valor']) ?></td></tr>
            <?php endforeach; ?>
            <?php if (!$resumo['por_mes']): ?><tr><td colspan="3" class="vazio">Sem dados no período.</td></tr><?php endif; ?>
            </tbody></table>
        </div>
        <?php if ($escopo === 'admin' && $resumo['por_criador']): ?>
        <div>
            <h3>Por criador</h3>
            <table class="tabela"><thead><tr><th>Criador</th><th>Notas</th><th>Valor</th></tr></thead><tbody>
            <?php foreach ($resumo['por_criador'] as $nome => $v): ?>
                <tr><td><?= esc($nome) ?></td><td><?= (int) $v['quantidade'] ?></td><td><?= formatarPreco($v['valor']) ?></td></tr>
            <?php endforeach; ?>
            </tbody></table>
        </div>
        <?php endif; ?>
        <div>
            <h3>Jogos mais vendidos</h3>
            <table class="tabela"><thead><tr><th>Jogo</th><th>Qtd.</th><th>Valor</th></tr></thead><tbody>
            <?php foreach ($resumo['top_jogos'] as $j): ?>
                <tr><td><?= esc($j['titulo']) ?></td><td><?= (int) $j['quantidade'] ?></td><td><?= formatarPreco($j['valor']) ?></td></tr>
            <?php endforeach; ?>
            <?php if (!$resumo['top_jogos']): ?><tr><td colspan="3" class="vazio">Nenhuma venda no período.</td></tr><?php endif; ?>
            </tbody></table>
        </div>
    </div>

    <h3 class="secao-titulo">Notas</h3>
    <div class="nota-tabela-wrap">
    <table class="tabela nota-tabela">
        <thead><tr>
            <th>Nº</th><th>Emissão</th><th>Tipo</th><th>Origem</th><th>Comprador</th>
            <?php if ($escopo === 'admin'): ?><th>Criador</th><?php endif; ?>
            <th>Forma</th><th>Itens</th><th>Valor</th><th>Status</th><th class="nao-imprimir"></th>
        </tr></thead>
        <tbody>
        <?php foreach ($notas as $n): ?>
            <tr>
                <td><?= esc($n['numero_nota']) ?></td>
                <td><?= formatarData($n['data_emissao']) ?></td>
                <td><?= $n['id_pedidos'] ? 'Venda' : 'Doação' ?></td>
                <td>#<?= (int) ($n['id_pedidos'] ?: $n['id_doacoes']) ?></td>
                <td><?= esc($n['comprador_nome'] ?: '-') ?></td>
                <?php if ($escopo === 'admin'): ?><td><?= esc($n['criador_nome'] ?: '#' . $n['id_criadores']) ?></td><?php endif; ?>
                <td><?= esc($metodoNome[$n['forma_pagamento'] ?: 'não informado'] ?? $n['forma_pagamento']) ?></td>
                <td><?= (int) $n['qtd_itens'] ?></td>
                <td><?= formatarPreco($n['valor_total']) ?></td>
                <td><?= esc(ucfirst($n['status'])) ?></td>
                <td class="nao-imprimir"><a class="acao" href="index.php?page=nota_detalhe&id=<?= (int) $n['id_fiscal'] ?>">Detalhes</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$notas): ?>
            <tr><td colspan="<?= $escopo === 'admin' ? 11 : 10 ?>" class="vazio">Nenhuma nota encontrada com esses filtros.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
