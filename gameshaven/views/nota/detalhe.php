<?php
/* Nota fiscal detalhada (visualizar e imprimir). Espera: $nota, $itens, $loja, $escopo,
   $beneficiario. Os dados do comprador vêm do retrato gravado na emissão. */
$paginaAtual = $escopo === 'admin' ? 'admin' : 'criador';
$isVenda = !empty($nota['id_pedidos']);
$origemTxt = $isVenda ? 'Pedido #' . (int) $nota['id_pedidos'] : 'Doação #' . (int) $nota['id_doacoes'];
$metodoNome = ['cartao credito' => 'Cartão de crédito', 'cartao debito' => 'Cartão de débito', 'pix' => 'Pix'];
$endereco = trim(implode(', ', array_filter([
    $nota['end_rua'] ? $nota['end_rua'] . ($nota['end_numero'] ? ', ' . $nota['end_numero'] : '') : null,
    $nota['end_complemento'] ?: null,
    $nota['end_cidade'] ? $nota['end_cidade'] . ($nota['end_estado'] ? '/' . $nota['end_estado'] : '') : null,
    $nota['end_cep'] ? 'CEP ' . substr($nota['end_cep'], 0, 5) . '-' . substr($nota['end_cep'], 5) : null,
])));
$totalItens = 0;
foreach ($itens as $i) $totalItens += $i['subtotal'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Nota Fiscal <?= esc($nota['numero_nota']) ?> - GamesHaven</title>
<link rel="stylesheet" href="public/css/base.css">
<link rel="stylesheet" href="public/css/nota.css">
</head>
<body>
<div class="wrapper">
<?php require __DIR__ . '/../../includes/header.php'; ?>
<div class="conteudo pagina">

    <div class="nota-acoes nao-imprimir">
        <a href="index.php?page=<?= $escopo === 'admin' ? 'admin_notas' : 'criador_notas' ?>" class="btn small outline">&larr; Voltar às notas</a>
        <button type="button" class="btn small" onclick="window.print()">&#128424; Imprimir / Salvar em PDF</button>
    </div>

    <article class="nota-folha">
        <header class="nota-cabecalho">
            <div class="nota-emitente">
                <div class="nota-marca"><?= esc($loja['nome_fantasia']) ?></div>
                <div><strong><?= esc($loja['razao_social']) ?></strong></div>
                <div>CNPJ: <?= esc($loja['cnpj']) ?> &middot; IE: <?= esc($loja['inscricao_estadual']) ?></div>
                <div><?= esc($loja['endereco']) ?></div>
                <div><?= esc($loja['telefone']) ?> &middot; <?= esc($loja['email']) ?></div>
            </div>
            <div class="nota-identificacao">
                <div class="nota-titulo"><?= $isVenda ? 'NOTA FISCAL DE VENDA' : 'NOTA FISCAL DE DOAÇÃO' ?></div>
                <div><strong>Nº</strong> <?= esc($nota['numero_nota']) ?></div>
                <div><strong>Série</strong> <?= esc($nota['serie']) ?></div>
                <div><strong>Emissão</strong> <?= formatarData($nota['data_emissao']) ?></div>
                <div><strong>Status</strong> <?= esc(ucfirst($nota['status'])) ?></div>
            </div>
        </header>

        <section class="nota-chave">
            <strong>Chave de acesso (simulada):</strong> <span class="nota-mono"><?= esc($nota['chave_acesso'] ?: '-') ?></span>
        </section>

        <section class="nota-duas-colunas">
            <div class="nota-bloco">
                <h4>Destinatário</h4>
                <div><strong><?= esc($nota['comprador_nome'] ?: '-') ?></strong></div>
                <div><?= esc($nota['comprador_email'] ?: '-') ?></div>
                <div>Endereço: <?= esc($endereco ?: 'Não informado') ?></div>
            </div>
            <div class="nota-bloco">
                <h4>Pagamento e origem</h4>
                <div>Forma: <?= esc($metodoNome[$nota['forma_pagamento']] ?? ($nota['forma_pagamento'] ?: 'Não informada')) ?></div>
                <div>Situação do pagamento: <?= esc(ucfirst($nota['status_pagamento'] ?: '-')) ?></div>
                <div>Origem: <?= esc($origemTxt) ?></div>
                <div>Beneficiário (criador): <?= esc($beneficiario) ?></div>
            </div>
        </section>

        <section>
            <table class="nota-itens">
                <thead>
                    <tr><th>#</th><th>Descrição</th><th class="num">Qtd.</th><th class="num">Preço unit.</th><th class="num">Subtotal</th></tr>
                </thead>
                <tbody>
                <?php foreach ($itens as $k => $i): ?>
                    <tr>
                        <td><?= $k + 1 ?></td>
                        <td><?= esc($i['titulo']) ?></td>
                        <td class="num"><?= (int) $i['quantidade'] ?></td>
                        <td class="num"><?= formatarPreco($i['preco']) ?></td>
                        <td class="num"><?= formatarPreco($i['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$itens): ?>
                    <tr><td colspan="5" class="vazio">Sem itens registrados.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section class="nota-totais">
            <div><span>Valor dos produtos</span><strong><?= formatarPreco($nota['valor_produtos']) ?></strong></div>
            <div><span>Descontos</span><strong><?= formatarPreco(0) ?></strong></div>
            <div class="nota-total-final"><span>Valor total</span><strong><?= formatarPreco($nota['valor_total']) ?></strong></div>
        </section>

        <footer class="nota-rodape">
            <p><?= esc($loja['aviso']) ?></p>
            <p>Nota gerada em <?= formatarData($nota['data_emissao']) ?> · <?= esc($nota['numero_nota']) ?></p>
        </footer>
    </article>
</div>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
</div>
</body>
</html>
