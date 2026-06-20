<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();
if (!in_array($_SESSION['perfil'] ?? '', ['Administrador', 'Técnico'])) {
    header('Location: equipamentos.php');
    exit;
}

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

    // Buscar equipamentos que foram movidos deste fornecedor via histórico
    $stmtEqs = $ligacao->prepare("
        SELECT DISTINCT
            h.id_equipamento,
            e.codigo_interno,
            e.designacao,
            f.codigo_fornecedor AS fornecedor_atual,
            f.nome_empresa AS nome_fornecedor_atual
        FROM historico_equipamentos h
        INNER JOIN equipamentos e ON h.id_equipamento = e.id
        INNER JOIN equipamentos_fornecedores ef ON e.id = ef.id_equipamento
        INNER JOIN fornecedores f ON ef.id_fornecedor = f.id
        WHERE h.acao = 'Fornecedor alterado'
        AND h.descricao LIKE :codigo
        ORDER BY e.codigo_interno
    ");
    $stmtEqs->execute([':codigo' => '%' . $fornecedor['codigo_fornecedor'] . '%']);
    $equipamentosMovidos = $stmtEqs->fetchAll(PDO::FETCH_ASSOC);

    // Processar POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Reativar fornecedor
        $stmt = $ligacao->prepare("UPDATE fornecedores SET ativo = 1 WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Repor equipamentos selecionados
        $equipamentosRepor = $_POST['repor_equipamentos'] ?? [];

        foreach ($equipamentosRepor as $idEquipamento) {
            // Verificar se já tem este fornecedor associado
            $stmtVerif = $ligacao->prepare("
                SELECT id_equipamento FROM equipamentos_fornecedores 
                WHERE id_equipamento = :id_eq AND id_fornecedor = :id_forn
            ");
            $stmtVerif->execute([':id_eq' => $idEquipamento, ':id_forn' => $id]);

            if (!$stmtVerif->fetch()) {
                $stmtInsert = $ligacao->prepare("
                    INSERT INTO equipamentos_fornecedores (id_equipamento, id_fornecedor, tipo_relacao)
                    SELECT :id_eq, :id_forn, tipo_relacao 
                    FROM equipamentos_fornecedores 
                    WHERE id_equipamento = :id_eq2
                    LIMIT 1
                ");
                $stmtInsert->execute([
                    ':id_eq'   => $idEquipamento,
                    ':id_forn' => $id,
                    ':id_eq2'  => $idEquipamento,
                ]);
            }

            registar_historico(
                $ligacao,
                $idEquipamento,
                'Fornecedor restaurado',
                'Equipamento reposto no fornecedor ' . $fornecedor['codigo_fornecedor'] . ' após reativação.'
            );
        }

        $ligacao = null;
        header('Location: fornecedores.php?sucesso=reativado');
        exit;
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
                <h1 class="fw-bold mb-1">Reativar Fornecedor</h1>
                <p class="text-muted mb-0">Reativar o fornecedor e repor equipamentos associados.</p>
            </div>
            <a href="fornecedores.php" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Voltar
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-lg-5">

                <div class="alert alert-info">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    Está prestes a reativar o fornecedor <strong><?= htmlspecialchars($fornecedor['codigo_fornecedor']) ?></strong>
                    — <?= htmlspecialchars($fornecedor['nome_empresa']) ?>.
                </div>

                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($idEncriptado) ?>">

                    <?php if (!empty($equipamentosMovidos)): ?>
                        <h5 class="fw-bold mb-3">Equipamentos que estavam associados a este fornecedor</h5>
                        <p class="text-muted mb-3">Seleciona os equipamentos que queres repor neste fornecedor:</p>

                        <div class="table-responsive mb-4">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Repor</th>
                                        <th>Código</th>
                                        <th>Designação</th>
                                        <th>Fornecedor Atual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($equipamentosMovidos as $eq): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox"
                                                    class="form-check-input"
                                                    name="repor_equipamentos[]"
                                                    value="<?= $eq['id_equipamento'] ?>"
                                                    checked>
                                            </td>
                                            <td><?= htmlspecialchars($eq['codigo_interno']) ?></td>
                                            <td><?= htmlspecialchars($eq['designacao']) ?></td>
                                            <td><?= htmlspecialchars($eq['fornecedor_atual']) ?> — <?= htmlspecialchars($eq['nome_fornecedor_atual']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-4">Não existem equipamentos para repor neste fornecedor.</p>
                    <?php endif; ?>

                    <div class="d-flex justify-content-end gap-3">
                        <a href="fornecedores.php" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            Confirmar Reativação
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </main>
</div>

<?php include '../../includes/footer.php'; ?>