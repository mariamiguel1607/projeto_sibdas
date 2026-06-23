<?php
require_once __DIR__ . '/includes/funcoes.php';
redirect_if_not_logged();

$ligacao = ligar_bd();

$id = intval($_POST['id'] ?? 0);

if ($id > 0) {
    $stmt = $ligacao->prepare("UPDATE mensagens_contacto SET lida = 1 WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

$redirect = $_POST['redirect'] ?? '/techmedsolutions/private/views/dashboard/dashboard.php';
header('Location: ' . $redirect);
exit;