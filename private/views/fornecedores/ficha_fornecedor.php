<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

// Receber e desencriptar o ID
$idEncriptado = $_GET['id'] ?? null;
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

    // Buscar equipamentos associados
    $stmtEqs = $ligacao->prepare("
        SELECT 
            equipamentos.id,
            equipamentos.codigo_interno,
            equipamentos.designacao,
            equipamentos.marca,
            equipamentos.modelo,
            equipamentos.criticidade,
            estados.nome_estado
        FROM equipamentos
        INNER JOIN equipamentos_fornecedores ON equipamentos.id = equipamentos_fornecedores.id_equipamento
        INNER JOIN estados ON equipamentos.id_estado = estados.id
        WHERE equipamentos_fornecedores.id_fornecedor = :id
        ORDER BY equipamentos.codigo_interno
    ");
    $stmtEqs->execute([':id' => $id]);
    $equipamentos = $stmtEqs->fetchAll(PDO::FETCH_ASSOC);

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

        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-3">
                    <h1 class="fw-bold mb-1"><?= htmlspecialchars($fornecedor['nome_empresa']) ?></h1>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">
                        <?= htmlspecialchars($fornecedor['codigo_fornecedor']) ?>
                    </span>
                    <?php if (!$fornecedor['ativo']): ?>
                        <span class="badge bg-dark px-3 py-2 rounded-pill">
                            <i class="fa-solid fa-ban me-1"></i>
                            Descontinuado
                        </span>
                    <?php endif; ?>
                </div>
                <p class="text-muted mb-0">Perfil detalhado do fornecedor e equipamentos associados.</p>
            </div>

            <div class="d-flex gap-2">
                <a href="fornecedores.php" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Voltar
                </a>
                <a href="editar_fornecedor.php?id=<?= aes_encrypt($fornecedor['id']) ?>" class="btn btn-primary-custom">
                    <i class="fa-solid fa-pen-to-square me-2"></i>
                    Editar Dados
                </a>
            </div>
        </div>

        <div class="row g-4">

            <div class="col-lg-8">

                <!-- INFORMAÇÃO INSTITUCIONAL -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 text-primary">
                            <i class="fa-solid fa-building me-2"></i>Informação Institucional
                        </h5>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Código Interno</span>
                                <span class="fw-bold text-dark"><?= htmlspecialchars($fornecedor['codigo_fornecedor']) ?></span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Tipo de Fornecedor</span>
                                <span class="fw-bold text-dark"><?= htmlspecialchars($fornecedor['tipo_fornecedor']) ?></span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">NIF</span>
                                <span class="fw-bold text-dark"><?= htmlspecialchars($fornecedor['nif']) ?></span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block small">Website</span>
                                <?php if (!empty($fornecedor['website'])): ?>
                                    <a href="<?= htmlspecialchars($fornecedor['website']) ?>" target="_blank" class="text-decoration-none fw-bold">
                                        <?= htmlspecialchars($fornecedor['website']) ?>
                                        <i class="fa-solid fa-arrow-up-right-from-square ms-1 small"></i>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Não disponível</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-12">
                                <span class="text-muted d-block small">Morada Principal</span>
                                <span class="text-dark"><?= htmlspecialchars($fornecedor['morada'] ?? '-') ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- EQUIPAMENTOS ASSOCIADOS -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 text-primary">
                            <i class="fa-solid fa-screwdriver-wrench me-2"></i>Equipamentos Associados
                        </h5>

                        <?php if (empty($equipamentos)): ?>
                            <p class="text-muted">Nenhum equipamento associado a este fornecedor.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Designação</th>
                                            <th>Marca / Modelo</th>
                                            <th>Criticidade</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($equipamentos as $eq): ?>
                                            <tr>
                                                <td class="fw-bold"><?= htmlspecialchars($eq['codigo_interno']) ?></td>
                                                <td><?= htmlspecialchars($eq['designacao']) ?></td>
                                                <td><?= htmlspecialchars($eq['marca']) ?> / <?= htmlspecialchars($eq['modelo']) ?></td>
                                                <td>
                                                    <?php
                                                    $crit = $eq['criticidade'];
                                                    if ($crit == 'Suporte de Vida') echo '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill">Suporte de Vida</span>';
                                                    elseif ($crit == 'Alta') echo '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill text-dark">Alta</span>';
                                                    elseif ($crit == 'Média') echo '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill">Média</span>';
                                                    elseif ($crit == 'Baixa') echo '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill">Baixa</span>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="../equipamentos/ficha_equipamento.php?id=<?= aes_encrypt($eq['id']) ?>&origem=fornecedor&id_fornecedor=<?= aes_encrypt($fornecedor['id']) ?>"
                                                        class="btn btn-sm btn-outline-primary py-1">
                                                        <i class="fa-solid fa-eye"></i> Ver Ficha
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div><!-- fim col-lg-8 -->

            <div class="col-lg-4">

                <!-- CANAIS DE CONTACTO -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 text-primary">
                            <i class="fa-solid fa-address-book me-2"></i>Canais de Contacto
                        </h5>

                        <div class="mb-4">
                            <span class="text-muted d-block small mb-1">
                                <i class="fa-solid fa-phone me-2 text-secondary"></i>Telefone Geral
                            </span>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($fornecedor['telefone'] ?? '-') ?></span>
                        </div>

                        <div class="mb-4">
                            <span class="text-muted d-block small mb-1">
                                <i class="fa-solid fa-envelope me-2 text-secondary"></i>Email Geral
                            </span>
                            <span class="fw-bold text-dark"><?= htmlspecialchars($fornecedor['email'] ?? '-') ?></span>
                        </div>

                        <hr class="my-3">

                        <div class="bg-light p-3 rounded-3 mt-3">
                            <h6 class="fw-bold mb-3 text-dark">
                                <i class="fa-solid fa-user-tie me-2 text-primary"></i>Pessoa de Contacto
                            </h6>
                            <div class="mb-2">
                                <span class="text-muted d-block small">Nome</span>
                                <span class="fw-bold text-dark"><?= htmlspecialchars($fornecedor['pessoa_contacto'] ?? '-') ?></span>
                            </div>
                            <div>
                                <span class="text-muted d-block small">Telefone Direto</span>
                                <span class="fw-bold text-dark"><?= htmlspecialchars($fornecedor['telefone_contacto'] ?? '-') ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OBSERVAÇÕES -->
                <?php if (!empty($fornecedor['observacoes'])): ?>
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3 text-primary">
                                <i class="fa-solid fa-note-sticky me-2"></i>Observações Internas
                            </h5>
                            <p class="text-dark bg-warning bg-opacity-10 border border-warning border-opacity-25 p-3 rounded-3 mb-0 small lh-base">
                                <?= htmlspecialchars($fornecedor['observacoes']) ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    </main>
</div>

<?php include '../../includes/footer.php'; ?>