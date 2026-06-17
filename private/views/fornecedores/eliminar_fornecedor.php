<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

$idEncriptado = $_GET['id'] ?? $_POST['id'] ?? null;
$id = aes_decrypt($idEncriptado);

if (!$id || !is_numeric($id)) {
    header('Location: fornecedores.php');
    exit;
}

try {
    $ligacao = ligar_bd();

    // Buscar dados do fornecedor
    $stmt = $ligacao->prepare("SELECT * FROM fornecedores WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fornecedor) {
        header('Location: fornecedores.php');
        exit;
    }

    // Buscar equipamentos associados a este fornecedor
    $stmtEqs = $ligacao->prepare("
        SELECT e.id, e.codigo_interno, e.designacao
        FROM equipamentos e
        INNER JOIN equipamentos_fornecedores ef ON e.id = ef.id_equipamento
        WHERE ef.id_fornecedor = :id
    ");
    $stmtEqs->execute([':id' => $id]);
    $equipamentosAssociados = $stmtEqs->fetchAll(PDO::FETCH_ASSOC);

    // Buscar outros fornecedores ativos para escolha
    $stmtFornecs = $ligacao->prepare("
        SELECT * FROM fornecedores 
        WHERE id != :id AND ativo = 1 
        ORDER BY codigo_fornecedor
    ");
    $stmtFornecs->execute([':id' => $id]);
    $outrosFornecedores = $stmtFornecs->fetchAll(PDO::FETCH_ASSOC);

    // Processar POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $novoFornecedor = $_POST['novo_fornecedor'] ?? null;
        $erro = '';

        if (!empty($equipamentosAssociados) && !$novoFornecedor) {
            $erro = 'Tens de selecionar um novo fornecedor para os equipamentos associados.';
        }

        if (empty($erro)) {
            // Mover equipamentos para novo fornecedor
            if (!empty($equipamentosAssociados) && $novoFornecedor) {
                foreach ($equipamentosAssociados as $eq) {
                    // Verificar se o equipamento já está associado ao novo fornecedor
                    $stmtVerif = $ligacao->prepare("
        SELECT id_equipamento FROM equipamentos_fornecedores 
        WHERE id_equipamento = :id_eq AND id_fornecedor = :novo
    ");
                    $stmtVerif->execute([':id_eq' => $eq['id'], ':novo' => $novoFornecedor]);

                    if ($stmtVerif->fetch()) {
                        // Já existe associação ao novo fornecedor — apaga só a antiga
                        $stmtDel = $ligacao->prepare("
            DELETE FROM equipamentos_fornecedores 
            WHERE id_equipamento = :id_eq AND id_fornecedor = :antigo
        ");
                        $stmtDel->execute([':id_eq' => $eq['id'], ':antigo' => $id]);
                    } else {
                        // Não existe — faz o UPDATE normal
                        $stmtUpdate = $ligacao->prepare("
            UPDATE equipamentos_fornecedores 
            SET id_fornecedor = :novo 
            WHERE id_equipamento = :id_eq AND id_fornecedor = :antigo
        ");
                        $stmtUpdate->execute([
                            ':novo'   => $novoFornecedor,
                            ':id_eq'  => $eq['id'],
                            ':antigo' => $id,
                        ]);
                    }

                    registar_historico(
                        $ligacao,
                        $eq['id'],
                        'Fornecedor alterado',
                        'Fornecedor alterado de ' . $fornecedor['codigo_fornecedor'] . ' devido à desativação do fornecedor original.'
                    );
                }
            }

            // Desativar fornecedor
            $stmt = $ligacao->prepare("UPDATE fornecedores SET ativo = 0 WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $ligacao = null;
            header('Location: fornecedores.php?sucesso=desativado');
            exit;
        }
    }

    $ligacao = null;
} catch (PDOException $e) {
    echo "<p class='text-danger'>Erro: " . $e->getMessage() . "</p>";
    exit;
}
?>

<?php include '../../includes/header.php';
$paginaAtiva = 'fornecedores';
?>

<div class="private-layout">
    <?php include '../../includes/sidebar.php'; ?>

    <main class="private-main">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-1">Desativar Fornecedor</h1>
                <p class="text-muted mb-0">Confirmar desativação do fornecedor.</p>
            </div>
            <a href="fornecedores.php" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Voltar
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-lg-5">

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <?= htmlspecialchars($erro) ?>
                    </div>
                <?php endif; ?>

                <div class="alert alert-warning">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    Está prestes a desativar o fornecedor <strong><?= htmlspecialchars($fornecedor['codigo_fornecedor']) ?></strong>
                    — <?= htmlspecialchars($fornecedor['nome_empresa']) ?>.
                </div>

                <?php if (!empty($equipamentosAssociados)): ?>
                    <div class="alert alert-info">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        Este fornecedor tem <strong><?= count($equipamentosAssociados) ?> equipamento(s)</strong> associado(s).
                        Tens de selecionar um novo fornecedor antes de desativar.
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($idEncriptado) ?>">

                    <?php if (!empty($equipamentosAssociados)): ?>

                        <h5 class="fw-bold mb-3">Equipamentos associados</h5>
                        <div class="table-responsive mb-4">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Designação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($equipamentosAssociados as $eq): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($eq['codigo_interno']) ?></td>
                                            <td><?= htmlspecialchars($eq['designacao']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Novo Fornecedor para os Equipamentos</label>
                            <select class="form-select" name="novo_fornecedor" required>
                                <option value="">Selecionar fornecedor...</option>
                                <?php foreach ($outrosFornecedores as $forn): ?>
                                    <option value="<?= $forn['id'] ?>">
                                        <?= htmlspecialchars($forn['codigo_fornecedor']) ?> —
                                        <?= htmlspecialchars($forn['nome_empresa']) ?>
                                        (<?= htmlspecialchars($forn['tipo_fornecedor']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    <?php else: ?>
                        <p class="text-muted">Este fornecedor não tem equipamentos associados. Podes desativá-lo diretamente.</p>
                    <?php endif; ?>

                    <div class="d-flex justify-content-end gap-3">
                        <a href="fornecedores.php" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-ban me-2"></i>
                            Confirmar Desativação
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </main>
</div>

<?php include '../../includes/footer.php'; ?>