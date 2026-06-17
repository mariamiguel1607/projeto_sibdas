<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

$idEncriptado = $_GET['id'] ?? null;
$id = aes_decrypt($idEncriptado);

if (!$id || !is_numeric($id)) {
    header('Location: localizacao.php');
    exit;
}

try {
    $ligacao = ligar_bd();
    $stmt = $ligacao->prepare("UPDATE localizacoes SET ativo = 0 WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $ligacao = null;

    header('Location: localizacao.php?sucesso=desativado');
    exit;
} catch (PDOException $e) {
    echo "<p class='text-danger'>Erro: " . $e->getMessage() . "</p>";
    exit;
}
