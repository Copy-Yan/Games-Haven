<?php
function handleLibrary() {
    exigirLogin();
    $db = getConexao();
    $stmt = $db->prepare(
        'SELECT j.* FROM biblioteca b JOIN jogos j ON j.id_jogos = b.id_jogos WHERE b.id_usuarios = ? AND ' . condicaoIdadeSql('j.') . ' ORDER BY b.data_compra DESC'
    );
    $stmt->execute([$_SESSION['usuario_id']]);
    $jogos = $stmt->fetchAll();
    require __DIR__ . '/../views/library/index.php';
}
