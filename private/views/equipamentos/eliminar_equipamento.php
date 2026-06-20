<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();
// Só Administrador e Técnico podem inserir equipamentos
if (!in_array($_SESSION['perfil'] ?? '', ['Administrador', 'Técnico'])) {
    header('Location: equipamentos.php');
    exit;
}

$idEncriptado = $_GET['id'] ?? null;
$id = aes_decrypt($idEncriptado);

if (!$id || !is_numeric($id)) {
    header('Location: equipamentos.php');
    exit;
}

try {
    $ligacao = ligar_bd();
    $stmt = $ligacao->prepare("UPDATE equipamentos SET ativo = 0 WHERE id = :id");
    $stmt->execute([':id' => $id]);

    registar_historico($ligacao, $id, 'Equipamento desativado', 'O equipamento foi desativado pelo utilizador.');

    $ligacao = null;

    header('Location: equipamentos.php?sucesso=desativado');
    exit;
} catch (PDOException $e) {
    echo "<p class='text-danger'>Erro: " . $e->getMessage() . "</p>";
    exit;
}
