<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();
if (!in_array($_SESSION['perfil'] ?? '', ['Administrador', 'Técnico'])) {
    header('Location: localizacao.php'); 
    exit;
}

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

    // Verificar se tem equipamentos associados
    $stmtEqs = $ligacao->prepare("SELECT COUNT(*) FROM equipamentos WHERE id_localizacao = :id");
    $stmtEqs->execute([':id' => $id]);
    $totalEquipamentos = $stmtEqs->fetchColumn();

    // Processar o POST (confirmação)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $novaLocalizacao = $_POST['nova_localizacao'] ?? null;

        if ($totalEquipamentos > 0) {
            if (!$novaLocalizacao || !is_numeric($novaLocalizacao)) {
                $erro = 'Tens de selecionar uma nova localização para os equipamentos.';
            } else {
                // Buscar equipamentos afetados para registar no histórico
                $stmtEqsAfetados = $ligacao->prepare("SELECT id, codigo_interno FROM equipamentos WHERE id_localizacao = :id");
                $stmtEqsAfetados->execute([':id' => $id]);
                $eqsAfetados = $stmtEqsAfetados->fetchAll(PDO::FETCH_ASSOC);

                // Buscar nome da nova localização
                $stmtNovaLoc = $ligacao->prepare("SELECT codigo_localizacao FROM localizacoes WHERE id = :id");
                $stmtNovaLoc->execute([':id' => $novaLocalizacao]);
                $novaLoc = $stmtNovaLoc->fetch(PDO::FETCH_ASSOC);

                // Mover equipamentos para nova localização
                $stmt = $ligacao->prepare("UPDATE equipamentos SET id_localizacao = :nova WHERE id_localizacao = :antiga");
                $stmt->execute([':nova' => $novaLocalizacao, ':antiga' => $id]);

                // Registar no histórico para cada equipamento movido
                foreach ($eqsAfetados as $eq) {
                    registar_historico(
                        $ligacao,
                        $eq['id'],
                        'Localização alterada',
                        'Localização alterada de ' . $localizacao['codigo_localizacao'] . ' para ' . $novaLoc['codigo_localizacao'] . ' devido à desativação da localização original.',
                        $id  // id_localizacao_anterior
                    );
                }

                // Desativar localização
                $stmt = $ligacao->prepare("UPDATE localizacoes SET ativo = 0 WHERE id = :id");
                $stmt->execute([':id' => $id]);

                $ligacao = null;
                header('Location: localizacao.php?sucesso=desativado');
                exit;
            }
        } else {
            // Sem equipamentos, desativa diretamente
            $stmt = $ligacao->prepare("UPDATE localizacoes SET ativo = 0 WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $ligacao = null;
            header('Location: localizacao.php?sucesso=desativado');
            exit;
        }
    }

    // Buscar outras localizações ativas para escolha
    $stmtLocs = $ligacao->prepare("SELECT * FROM localizacoes WHERE id != :id AND ativo = 1 ORDER BY codigo_localizacao");
    $stmtLocs->execute([':id' => $id]);
    $outrasLocalizacoes = $stmtLocs->fetchAll(PDO::FETCH_ASSOC);

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
                <h1 class="fw-bold mb-1">Eliminar Localização</h1>
                <p class="text-muted mb-0">Confirmar eliminação da localização.</p>
            </div>
            <a href="localizacao.php" class="btn btn-outline-secondary">
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
                    Está prestes a eliminar a localização <strong><?= htmlspecialchars($localizacao['codigo_localizacao']) ?></strong>
                    — <?= htmlspecialchars($localizacao['edificio']) ?>, <?= htmlspecialchars($localizacao['servico_departamento']) ?>.
                </div>

                <?php if ($totalEquipamentos > 0): ?>
                    <div class="alert alert-info">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        Esta localização tem <strong><?= $totalEquipamentos ?> equipamento(s)</strong> associado(s).
                        Tens de selecionar uma nova localização para os mover antes de desativar.
                    </div>

                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($idEncriptado) ?>">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Nova Localização para os Equipamentos</label>
                            <select class="form-select" name="nova_localizacao" required>
                                <option value="">Selecionar localização...</option>
                                <?php foreach ($outrasLocalizacoes as $loc): ?>
                                    <option value="<?= $loc['id'] ?>">
                                        <?= htmlspecialchars($loc['codigo_localizacao']) ?> —
                                        <?= htmlspecialchars($loc['edificio']) ?>,
                                        <?= htmlspecialchars($loc['servico_departamento']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-3">
                            <a href="localizacao.php" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fa-solid fa-ban me-2"></i>
                                Confirmar Eliminação
                            </button>
                        </div>
                    </form>

                <?php else: ?>

                    <p class="text-muted">Esta localização não tem equipamentos associados. Podes desativá-la diretamente.</p>

                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($idEncriptado) ?>">
                        <div class="d-flex justify-content-end gap-3">
                            <a href="localizacao.php" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fa-solid fa-ban me-2"></i>
                                Confirmar Desativação
                            </button>
                        </div>
                    </form>

                <?php endif; ?>

            </div>
        </div>

    </main>
</div>

<?php include '../../includes/footer.php'; ?>