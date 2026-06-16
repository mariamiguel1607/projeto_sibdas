<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

// Receber e desencriptar o ID
$idEncriptado = $_GET['id'] ?? null;
$id = aes_decrypt($idEncriptado);

// Validar o ID
if (!$id || !is_numeric($id)) {
    header('Location: equipamentos.php');
    exit;
}

// Eliminar o equipamento da BD
try {
    $ligacao = ligar_bd();

    $stmt = $ligacao->prepare("DELETE FROM equipamentos WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $ligacao = null;

    header('Location: equipamentos.php?sucesso=eliminado');
    exit;
} catch (PDOException $e) {
    echo "<p class='text-danger'>Erro ao eliminar: " . $e->getMessage() . "</p>";
    exit;
}
