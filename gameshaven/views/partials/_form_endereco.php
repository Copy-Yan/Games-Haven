<?php
/* Seção de endereço (campos da tabela enderecos). Espera $endereco (array com rua, numero,
   complemento, cidade, estado, cep). Usada no pagamento e na doação. */
$end = $endereco ?? [];
?>
<div class="painel checkout-painel" >
    <h3 style="margin-top:0;">&#127968; Endereço</h3>
    <div style="display:flex; gap:16px;">
        <div style="flex:3;">
            <label>Rua *</label>
            <input type="text" name="end_rua" value="<?= esc($end['rua'] ?? '') ?>" maxlength="100" required id="input-checkout2">
        </div>
        <div style="flex:1;">
            <label>Número *</label>
            <input type="text" name="end_numero" value="<?= esc($end['numero'] ?? '') ?>" inputmode="numeric" maxlength="10" required id="input-checkout2">
        </div>
    </div>
    <label>Complemento</label>
    <input type="text" name="end_complemento" value="<?= esc($end['complemento'] ?? '') ?>" maxlength="55" placeholder="Apto, bloco, casa..." id="input-checkout2">
    <div style="display:flex; gap:16px;">
        <div style="flex:2;">
            <label>Cidade *</label>
            <input type="text" name="end_cidade" value="<?= esc($end['cidade'] ?? '') ?>" maxlength="50" required id="input-checkout2">
        </div>
        <div style="flex:1;">
            <label>Estado *</label>
            <select id=input-checkout2 required>
                <option value="">UF</option>
                <?php foreach (UFS_BRASIL as $uf): ?>
                    <option value="<?= $uf ?>" <?= (($end['estado'] ?? '') === $uf) ? 'selected' : '' ?>><?= $uf ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="flex:1;">
            <label>CEP *</label>
            <input type="text" name="end_cep" id="end_cep" value="<?= esc($end['cep'] ?? '') ?>" inputmode="numeric" maxlength="9" placeholder="00000000" required id="input-checkout2">
        </div>
    </div>
    <small id="end-cep-status" class="cep-status" aria-live="polite">Digite o CEP para preencher rua, cidade e estado automaticamente.</small>
</div>
<script src="public/js/endereco.js" defer></script>
