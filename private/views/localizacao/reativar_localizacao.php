<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

$idEncriptado = $_GET['id'] ?? $_POST['id'] ?? null;
$id = aes_decrypt($idEncriptado);

if (!$id || !is_numeric($id)) {
    header('Location: localizacao.php');
    exit;
}

try {
    $ligacao = ligar_bd();

    // Buscar dados da localização
    $stmt = $ligacao->prepare("SELECT * FROM localizacoes WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $localizacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$localizacao) {
        header('Location: localizacao.php');
        exit;
    }

    // Buscar equipamentos que foram movidos desta localização
    $stmtEqs = $ligacao->prepare("
        SELECT DISTINCT
            historico_equipamentos.id_equipamento,
            equipamentos.codigo_interno,
            equipamentos.designacao,
            localizacoes.codigo_localizacao AS localizacao_atual
        FROM historico_equipamentos
        INNER JOIN equipamentos ON historico_equipamentos.id_equipamento = equipamentos.id
        INNER JOIN localizacoes ON equipamentos.id_localizacao = localizacoes.id
        WHERE historico_equipamentos.id_localizacao_anterior = :id
        AND historico_equipamentos.acao = 'Localização alterada'
    ");
    $stmtEqs->execute([':id' => $id]);
    $equipamentosMovidos = $stmtEqs->fetchAll(PDO::FETCH_ASSOC);

    // Processar POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Reativar localização
        $stmt = $ligacao->prepare("UPDATE localizacoes SET ativo = 1 WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Se escolheu repor equipamentos
        $equipamentosRepor = $_POST['repor_equipamentos'] ?? [];

        foreach ($equipamentosRepor as $idEquipamento) {
            $stmt = $ligacao->prepare("UPDATE equipamentos SET id_localizacao = :id_loc WHERE id = :id_eq");
            $stmt->execute([':id_loc' => $id, ':id_eq' => $idEquipamento]);

            registar_historico(
                $ligacao,
                $idEquipamento,
                'Localização restaurada',
                'Equipamento reposto na localização ' . $localizacao['codigo_localizacao'] . ' após reativação.'
            );
        }

        $ligacao = null;
        header('Location: localizacao.php?sucesso=reativado');
        exit;
    }

    $ligacao = null;
} catch (PDOException $e) {
    echo "<p class='text-danger'>Erro: " . $e->getMessage() . "</p>";
    exit;
}
?>

<?php include '../../includes/header.php';
$paginaAtiva = 'localizacao';
?>

<div class="private-layout">
    <?php include '../../includes/sidebar.php'; ?>

    <main class="private-main">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-1">Reativar Localização</h1>
                <p class="text-muted mb-0">Reativar a localização e repor equipamentos associados.</p>
            </div>
            <a href="localizacao.php" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Voltar
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-lg-5">

                <div class="alert alert-info">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    Está prestes a reativar a localização <strong><?= htmlspecialchars($localizacao['codigo_localizacao']) ?></strong>
                    — <?= htmlspecialchars($localizacao['edificio']) ?>, <?= htmlspecialchars($localizacao['servico_departamento']) ?>.
                </div>

                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($idEncriptado) ?>">

                    <?php if (!empty($equipamentosMovidos)): ?>
                        <h5 class="fw-bold mb-3">Equipamentos que estavam nesta localização</h5>
                        <p class="text-muted mb-3">Seleciona os equipamentos que queres repor nesta localização:</p>

                        <div class="table-responsive mb-4">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Repor</th>
                                        <th>Código</th>
                                        <th>Designação</th>
                                        <th>Localização Atual</th>
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
                                            <td><?= htmlspecialchars($eq['localizacao_atual']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-4">Não existem equipamentos para repor nesta localização.</p>
                    <?php endif; ?>

                    <div class="d-flex justify-content-end gap-3">
                        <a href="localizacao.php" class="btn btn-outline-secondary">Cancelar</a>
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