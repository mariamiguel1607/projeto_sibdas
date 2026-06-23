<?php
// --------------------------------------------------------------------
// SEGURANÇA: Proteção de acesso à página
// --------------------------------------------------------------------
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

$pesquisa = trim($_GET['pesquisa'] ?? '');
$edificio = trim($_GET['edificio'] ?? '');

try {

    $ligacao = ligar_bd();

    $sql = "
    SELECT
        localizacoes.*,
        COUNT(equipamentos.id) AS total_equipamentos
    FROM localizacoes
    LEFT JOIN equipamentos ON equipamentos.id_localizacao = localizacoes.id
    WHERE 1=1
    ";

    // Array que irá conter os valores dos filtros de forma segura,
    // passados separadamente à query para evitar SQL Injection
    $params = [];

    if (!empty($pesquisa)) {
        $sql .= "
        AND (
            localizacoes.codigo_localizacao LIKE ?
            OR localizacoes.edificio LIKE ?
            OR localizacoes.piso LIKE ?
            OR localizacoes.servico_departamento LIKE ?
            OR localizacoes.sala_gabinete LIKE ?
        )
        ";
        $params[] = '%' . $pesquisa . '%';
        $params[] = '%' . $pesquisa . '%';
        $params[] = '%' . $pesquisa . '%';
        $params[] = '%' . $pesquisa . '%';
        $params[] = '%' . $pesquisa . '%';
    }

    if (!empty($edificio) && $edificio != 'Todos') {
        $sql .= "AND localizacoes.edificio = ? ";
        $params[] = $edificio;
    }

    $sql .= "
    GROUP BY localizacoes.id
    ORDER BY localizacoes.codigo_localizacao ASC
    ";

    $stmt = $ligacao->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Buscar equipamentos por localização
    $stmtEqs = $ligacao->prepare("
        SELECT
            equipamentos.id,
            equipamentos.codigo_interno,
            equipamentos.designacao,
            equipamentos.id_localizacao,
            estados.nome_estado
        FROM equipamentos
        INNER JOIN estados ON equipamentos.id_estado = estados.id
        ORDER BY equipamentos.codigo_interno
    ");
    $stmtEqs->execute();
    $todosEquipamentos = $stmtEqs->fetchAll(PDO::FETCH_ASSOC);

    // Organizar por localização
    $equipamentosPorLocalizacao = [];
    foreach ($todosEquipamentos as $eq) {
        $equipamentosPorLocalizacao[$eq['id_localizacao']][] = $eq;
    }

    // Buscar edifícios distintos para o filtro dinâmico
    $stmtEdificios = $ligacao->query("SELECT DISTINCT edificio FROM localizacoes ORDER BY edificio ASC");
    $edificiosLista = $stmtEdificios->fetchAll(PDO::FETCH_COLUMN);

    $erro = '';
} catch (PDOException $err) {
    $erro = "Aconteceu um erro na ligação à base de dados.";
    $resultados = [];
    $equipamentosPorLocalizacao = [];
    $edificiosLista = [];
}

$ligacao = null;

$paginaAtiva = 'localizacao';
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/header.php';
$paginaAtiva = 'localizacao';
?>

<div class="private-layout">

    <?php include '../../includes/sidebar.php'; ?>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="private-main">

        <!-- TOPO -->
        <header class="private-topbar">
            <div>
                <h1>Localização</h1>
                <p>Gestão e consulta das localizações físicas associadas aos equipamentos médicos.</p>
            </div>

            <?php if (in_array($_SESSION['perfil'] ?? '', ['Administrador', 'Técnico'])): ?>
                <a href="inserir_localizacao.php" class="btn btn-primary-custom">
                    <i class="fa-solid fa-plus me-2"></i>
                    Adicionar Localização
                </a>
            <?php endif; ?>
        </header>
        <!-- FILTROS -->
        <section class="card filter-card mb-4">

            <div class="card-body">
                <form method="GET">

                    <div class="row g-3 align-items-end">

                        <!-- Pesquisa -->
                        <div class="col-md-4">

                            <label for="pesquisa" class="form-label">
                                Pesquisar localização
                            </label>

                            <input
                                type="text"
                                id="pesquisa"
                                name="pesquisa"
                                class="form-control"
                                placeholder="Piso, departamento ou sala"
                                value="<?= htmlspecialchars($pesquisa) ?>">

                        </div>

                        <!-- Edifício -->
                        <div class="col-md-3">

                            <label for="categoria" class="form-label">
                                Edifício
                            </label>

                            <select id="edificio" name="edificio" class="form-select">
                                <option <?= ($edificio == '' || $edificio == 'Todos') ? 'selected' : '' ?>>Todos</option>
                                <?php foreach ($edificiosLista as $e): ?>
                                    <option <?= ($edificio == $e) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($e) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                        </div>

                        <!-- Botão -->
                        <div class="col-md-2">

                            <button type="submit" class="btn btn-outline-secondary w-100">

                                <i class="fa-solid fa-filter me-2"></i>
                                Filtrar

                            </button>

                        </div>

                    </div>
                </form>

            </div>

        </section>


        <!-- TABELA -->
        <section class="card table-card">

            <div class="card-body">
                <?php if (!empty($erro)) : ?>

                    <div class="alert alert-danger">
                        <?= $erro ?>
                    </div>

                <?php elseif (count($resultados) == 0) : ?>

                    <p class="text-muted">
                        Não existem localizações registadas.
                    </p>
                <?php else : ?>

                    <div class="table-responsive">

                        <table id="tabela-localizacoes" class="table table-hover align-middle">

                            <thead>

                                <tr>

                                    <th>Código</th>

                                    <th>Edifício</th>

                                    <th>Piso</th>

                                    <th>Departamento</th>

                                    <th>Sala/Gabinete</th>

                                    <th>Equipamentos</th>

                                    <?php if (in_array($_SESSION['perfil'] ?? '', ['Administrador', 'Técnico'])): ?>
                                        <th class="text-end">Ações</th>
                                    <?php endif; ?>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($resultados as $localizacao) : ?>

                                    <tr>

                                        <td><?= htmlspecialchars($localizacao->codigo_localizacao) ?></td>

                                        <td><?= htmlspecialchars($localizacao->edificio) ?></td>

                                        <td><?= htmlspecialchars($localizacao->piso) ?></td>

                                        <td><?= htmlspecialchars($localizacao->servico_departamento) ?></td>

                                        <td><?= htmlspecialchars($localizacao->sala_gabinete) ?></td>

                                        <td>

                                            <button
                                                class="badge rounded-pill bg-primary-subtle text-primary border px-3 py-2"
                                                style="cursor:pointer;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEquipamentos<?= $localizacao->id ?>"
                                                data-id="<?= aes_encrypt($localizacao->id) ?>"
                                                data-codigo="<?= htmlspecialchars($localizacao->codigo_localizacao) ?>">
                                                <?= $localizacao->total_equipamentos ?> equipamentos
                                            </button>
                                        </td>

                                        <?php if (in_array($_SESSION['perfil'] ?? '', ['Administrador', 'Técnico'])): ?>
                                            <td class="text-end">
                                                <div class="d-flex gap-2 justify-content-end flex-nowrap">

                                                    <?php if ($localizacao->ativo): ?>
                                                        <a href="editar_localizacao.php?id=<?= aes_encrypt($localizacao->id) ?>"
                                                            class="btn btn-sm btn-outline-warning">
                                                            Editar
                                                        </a>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-outline-secondary" disabled>
                                                            Editar
                                                        </button>
                                                    <?php endif; ?>

                                                    <?php if ($localizacao->ativo): ?>
                                                        <a href="eliminar_localizacao.php?id=<?= aes_encrypt($localizacao->id) ?>"
                                                            class="btn btn-sm btn-outline-danger">
                                                            <i class="fa-solid fa-ban me-1"></i>
                                                            Eliminar
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="reativar_localizacao.php?id=<?= aes_encrypt($localizacao->id) ?>"
                                                            class="btn btn-sm btn-outline-success">
                                                            <i class="fa-solid fa-circle-check me-1"></i>
                                                            Reativar
                                                        </a>
                                                    <?php endif; ?>

                                                </div>
                                            </td>
                                        <?php endif; ?>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>
                        </table>

                    </div>

                <?php endif; ?>
            </div>

        </section>
    </main>
    <!-- MODAL ELIMINAR/REATIVAR LOCALIZAÇÃO -->
    <div class="modal fade"
        id="eliminarLocalizacaoModal"
        tabindex="-1"
        aria-labelledby="eliminarLocalizacaoModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarLocalizacaoModalLabel">
                        Confirmar ação
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="modalLocalizacaoBody">
                    <!-- preenchido pelo JavaScript -->
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" id="btnConfirmarAcaoLocalizacao" class="btn btn-danger">
                        <!-- texto preenchido pelo JavaScript -->
                    </button>
                </div>

            </div>

        </div>

    </div>
</div>
<!-- MODAL EQUIPAMENTOS DA LOCALIZAÇÃO -->
<?php foreach ($resultados as $loc): ?>
    <div class="modal fade" id="modalEquipamentos<?= $loc->id ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Equipamentos da Localização <?= htmlspecialchars($loc->codigo_localizacao) ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php if (empty($equipamentosPorLocalizacao[$loc->id])): ?>
                        <p class="text-muted">Nenhum equipamento associado a esta localização.</p>
                    <?php else: ?>
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Designação</th>
                                    <th>Estado</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($equipamentosPorLocalizacao[$loc->id] as $eq): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($eq['codigo_interno']) ?></td>
                                        <td><?= htmlspecialchars($eq['designacao']) ?></td>
                                        <td>
                                            <?php
                                            $estado = $eq['nome_estado'];
                                            if ($estado == 'Ativo') echo '<span class="badge bg-success">Ativo</span>';
                                            elseif ($estado == 'Em manutenção') echo '<span class="badge bg-warning text-dark">Em manutenção</span>';
                                            elseif ($estado == 'Inativo') echo '<span class="badge bg-secondary">Inativo</span>';
                                            elseif ($estado == 'Em calibração') echo '<span class="badge bg-info text-dark">Em calibração</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <a href="../equipamentos/ficha_equipamento.php?id=<?= aes_encrypt($eq['id']) ?>&origem=localizacao&id_localizacao=<?= aes_encrypt($loc->id) ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye me-1"></i>
                                                Ver Ficha
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<script>
    $(document).ready(function() {
        $('#tabela-localizacoes').DataTable({
            paging: true,
            pageLength: 5,
            searching: false,
            lengthChange: false,
            info: false,
            ordering: false,
            language: {
                emptyTable: "Sem dados disponíveis na tabela.",
                zeroRecords: "Nenhum registo encontrado.",
                paginate: {
                    next: "Seguinte",
                    previous: "Anterior"
                }
            }
        });
    });
</script>
<script>
    document.getElementById('eliminarLocalizacaoModal').addEventListener('show.bs.modal', function(event) {
        const botao = event.relatedTarget;
        const idEncriptado = botao.getAttribute('data-id');
        const acao = botao.getAttribute('data-acao');

        const btnConfirmar = document.getElementById('btnConfirmarAcaoLocalizacao');
        const body = document.getElementById('modalLocalizacaoBody');
        const titulo = document.getElementById('eliminarLocalizacaoModalLabel');

        btnConfirmar.setAttribute('data-id', idEncriptado);
        btnConfirmar.setAttribute('data-acao', acao);

        if (acao === 'reativar') {
            titulo.textContent = 'Reativar Localização';
            body.innerHTML = 'Tem a certeza que pretende <strong>reativar</strong> esta localização?<br><br><small class="text-muted">A localização voltará a estar disponível para associar a equipamentos.</small>';
            btnConfirmar.textContent = 'Reativar';
            btnConfirmar.className = 'btn btn-success';
        } else {
            titulo.textContent = 'Confirmar eliminação';
            body.innerHTML = 'Tem a certeza que pretende <strong>eliminar</strong> esta localização?<br><br><small class="text-muted">A localização ficará inativa e não poderá ser associada a novos equipamentos.</small>';
            btnConfirmar.textContent = 'Eliminar';
            btnConfirmar.className = 'btn btn-danger';
        }
    });

    document.getElementById('btnConfirmarAcaoLocalizacao').addEventListener('click', function() {
        const idEncriptado = this.getAttribute('data-id');
        const acao = this.getAttribute('data-acao');

        if (acao === 'reativar') {
            window.location.href = 'reativar_localizacao.php?id=' + idEncriptado;
        } else {
            window.location.href = 'eliminar_localizacao.php?id=' + idEncriptado;
        }
    });
</script>
<?php include '../../includes/footer.php'; ?>