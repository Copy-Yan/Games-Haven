<?php
/* Formulário compartilhado de Adicionar e Editar Jogo (mesmos campos do GameStormy).
   Espera: $valores, $desenvolvedoras, $publicadoras, $categorias, $plataformas,
           $capaAtual (ou null), $acaoForm, $textoBotao, $linkNovaDev, $linkNovaPub, $linkCancelar. */
$checado = fn($lista, $id) => in_array((int) $id, array_map('intval', $lista ?? []), true) ? 'checked' : '';
$selecionado = fn($atual, $valor) => (string) $atual === (string) $valor ? 'selected' : '';
?>
<form method="post" action="<?= esc($acaoForm) ?>" enctype="multipart/form-data">

    <label>Capa do jogo (imagem)</label>
    <?php if (imagemExiste($capaAtual ?? null)): ?>
        <div style="margin:6px 0 10px;"><img src="<?= esc($capaAtual) ?>" alt="Capa atual" style="width:120px;height:160px;object-fit:cover;border-radius:8px;"></div>
    <?php endif; ?>
    <input type="file" name="capa" accept=".jpg,.jpeg,.png,.gif,.webp,image/*">
    <small style="color:var(--text-muted);">JPG, PNG, GIF ou WEBP, até 5 MB.<?= !empty($capaAtual) ? ' Deixe em branco para manter a capa atual.' : ' Se não enviar, o jogo fica sem capa.' ?></small>

    <label>Ícone do jogo (opcional, pequeno)</label>
    <?php if (imagemExiste($iconeAtual ?? null)): ?>
        <div style="margin:6px 0 10px;"><img src="<?= esc($iconeAtual) ?>" alt="Ícone atual" style="width:48px;height:48px;object-fit:cover;border-radius:8px;"></div>
    <?php endif; ?>
    <input type="file" name="icone" accept=".jpg,.jpeg,.png,.gif,.webp,image/*">
    <small style="color:var(--text-muted);">Miniatura mostrada nas listas (Home, painel). JPG, PNG, GIF ou WEBP, até 1 MB.<?= imagemExiste($iconeAtual ?? null) ? ' Deixe em branco para manter o ícone atual.' : ' Se não enviar, a lista usa a capa.' ?></small>

    <label>Título *</label>
    <input type="text" name="titulo" value="<?= esc($valores['titulo']) ?>" placeholder="Ex: Stardew Valley" required>

    <label>Descrição *</label>
    <textarea name="descricao" rows="4" placeholder="Ex: Você herdou a antiga fazenda do seu avô..." required><?= esc($valores['descricao']) ?></textarea>

    <div style="display:flex; gap:16px;">
        <div style="flex:1;">
            <label>Desenvolvedora *</label>
            <select name="id_desenvolvedora" required>
                <option value="">-- Selecione --</option>
                <?php foreach ($desenvolvedoras as $d): ?>
                    <option value="<?= $d['id_desenvolvedoras'] ?>" <?= $selecionado($valores['id_desenvolvedoras'], $d['id_desenvolvedoras']) ?>><?= esc($d['nome']) ?> (<?= esc($d['pais']) ?>)</option>
                <?php endforeach; ?>
            </select>
            <small><a href="<?= esc($linkNovaDev) ?>" style="text-decoration:underline;">+ Cadastrar nova desenvolvedora</a></small>
        </div>
        <div style="flex:1;">
            <label>Publicadora *</label>
            <select name="id_publicadora" required>
                <option value="">-- Selecione --</option>
                <?php foreach ($publicadoras as $p): ?>
                    <option value="<?= $p['id_publicadoras'] ?>" <?= $selecionado($valores['id_publicadoras'], $p['id_publicadoras']) ?>><?= esc($p['nome']) ?> (<?= esc($p['pais']) ?>)</option>
                <?php endforeach; ?>
            </select>
            <small><a href="<?= esc($linkNovaPub) ?>" style="text-decoration:underline;">+ Cadastrar nova publicadora</a></small>
        </div>
    </div>

    <div style="display:flex; gap:16px;">
        <div style="flex:1;">
            <label>Preço (R$) *</label>
            <input type="text" name="preco" value="<?= esc($valores['preco']) ?>" placeholder="24.99" required>
        </div>
        <div style="flex:1;">
            <label>Data de lançamento *</label>
            <input type="date" name="data_lancamento" value="<?= esc($valores['data_lancamento']) ?>" required>
        </div>
    </div>

    <div style="display:flex; gap:16px;">
        <div style="flex:1;">
            <label>Classificação etária</label>
            <select name="classificacao_etaria">
                <?php foreach (Jogo::CLASSIFICACOES as $c): ?>
                    <option value="<?= $c ?>" <?= $selecionado($valores['classificacao_etaria'], $c) ?>><?= $c === 'L' ? 'L - Livre' : $c . ' anos' ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="flex:1;">
            <label>Tamanho de download (GB)</label>
            <input type="text" name="tamanho_download" value="<?= esc($valores['tamanho_download']) ?>" placeholder="0.5">
        </div>
    </div>

    <label>Requisitos mínimos</label>
    <input type="text" name="requisitos_minimos" value="<?= esc($valores['requisitos_minimos']) ?>" placeholder="Ex: Windows 7+, 2GB RAM, 500MB HD">

    <label>Requisitos recomendados</label>
    <input type="text" name="requisitos_recomendados" value="<?= esc($valores['requisitos_recomendados']) ?>" placeholder="Ex: Windows 10, 4GB RAM">

    <div style="display:flex; gap:16px;">
        <div style="flex:1;">
            <label>Tag</label>
            <select name="tag">
                <?php foreach (Jogo::TAGS as $t): ?>
                    <option value="<?= esc($t) ?>" <?= $selecionado($valores['tag'], $t) ?>><?= esc($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="flex:1;">
            <label>Nota crítica (0 a 10)</label>
            <input type="text" name="nota" value="<?= esc($valores['nota_critica'] ?? '') ?>" placeholder="9.5">
        </div>
    </div>

    <label>Categorias</label>
    <div style="display:flex; flex-wrap:wrap; gap:10px; margin:6px 0 14px;">
        <?php foreach ($categorias as $cat): ?>
            <label style="display:flex; align-items:center; gap:6px; margin:0; cursor:pointer;">
                <input type="checkbox" name="categorias[]" value="<?= $cat['id_categorias'] ?>" <?= $checado($valores['categorias'], $cat['id_categorias']) ?> style="width:auto;">
                <?= esc($cat['nome']) ?>
            </label>
        <?php endforeach; ?>
        <?php if (!$categorias): ?><small>Nenhuma categoria cadastrada.</small><?php endif; ?>
    </div>

    <label>Plataformas</label>
    <div style="display:flex; flex-wrap:wrap; gap:10px; margin:6px 0 14px;">
        <?php foreach ($plataformas as $pl): ?>
            <label style="display:flex; align-items:center; gap:6px; margin:0; cursor:pointer;">
                <input type="checkbox" name="plataformas[]" value="<?= $pl['id_plataformas'] ?>" <?= $checado($valores['plataformas'], $pl['id_plataformas']) ?> style="width:auto;">
                <?= esc($pl['nome']) ?>
            </label>
        <?php endforeach; ?>
    </div>

    <div style="margin-top:22px; display:flex; gap:12px;">
        <button type="submit" class="btn"><?= esc($textoBotao) ?></button>
        <a href="<?= esc($linkCancelar) ?>" class="btn outline">Cancelar</a>
    </div>
</form>
