<?php
// --------------------------------------------------------------------
// SEGURANÇA: Proteção de acesso à página
// --------------------------------------------------------------------
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

$pesquisa = trim($_GET['pesquisa'] ?? '');
$edificio = trim($_GET['edificio'] ?? '');

try {

    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST .
            ";port=" . MYSQL_PORT .
            ";dbname=" . MYSQL_DATABASE .
            ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );

    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
    SELECT
        localizacoes.*,
        COUNT(equipamentos.id) AS total_equipamentos

    FROM localizacoes

    LEFT JOIN equipamentos
        ON equipamentos.id_localizacao = localizacoes.id

    WHERE 1=1
    ";

    if (!empty($pesquisa)) {

        $sql .= "
        AND (
            localizacoes.codigo_localizacao LIKE '%$pesquisa%'
            OR localizacoes.edificio LIKE '%$pesquisa%'
            OR localizacoes.piso LIKE '%$pesquisa%'
            OR localizacoes.servico_departamento LIKE '%$pesquisa%'
            OR localizacoes.sala_gabinete LIKE '%$pesquisa%'
        )
        ";
    }
    if (!empty($edificio) && $edificio != 'Todos') {

        $sql .= "
    AND localizacoes.edificio = '$edificio'
    ";
    }

    $sql .= "
    GROUP BY localizacoes.id

    ORDER BY localizacoes.codigo_localizacao ASC
    ";

    $resultados = $ligacao->query($sql)
        ->fetchAll(PDO::FETCH_OBJ);

    $erro = '';
} catch (PDOException $err) {

    $erro = "Aconteceu um erro na ligação à base de dados.";
    $resultados = [];
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

            <a href="inserir_localizacao.php" class="btn btn-primary-custom">
                <i class="fa-solid fa-plus me-2"></i>
                Adicionar Localização
            </a>
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
                                placeholder="Edifício, piso, departamento ou sala"
                                value="<?= htmlspecialchars($pesquisa) ?>">

                        </div>

                        <!-- Edifício -->
                        <div class="col-md-3">

                            <label for="categoria" class="form-label">
                                Edifício
                            </label>

                            <select id="edificio" name="edificio" class="form-select">

                                <option <?= ($edificio == '' || $edificio == 'Todos') ? 'selected' : '' ?>>
                                    Todos
                                </option>

                                <option <?= ($edificio == 'Hospital Central') ? 'selected' : '' ?>>
                                    Hospital Central
                                </option>

                                <option <?= ($edificio == 'Clínica Norte') ? 'selected' : '' ?>>
                                    Clínica Norte
                                </option>

                                <option <?= ($edificio == 'Centro de Reabilitação') ? 'selected' : '' ?>>
                                    Centro de Reabilitação
                                </option>

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

                                    <th class="text-end">
                                        Ações
                                    </th>

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
                                                class="badge rounded-pill bg-light text-dark border px-3 py-2"
                                                style="cursor:pointer;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEquipamentosLOC0001">

                                                <?= $localizacao->total_equipamentos ?> equipamentos

                                            </button>

                                        </td>

                                        <td class="text-end">

                                            <div class="d-flex gap-2 justify-content-end flex-nowrap">

                                                <a href="editar_localizacao.php?id=<?= $localizacao->id ?>"
                                                    class="btn btn-sm btn-outline-warning">
                                                    Editar
                                                </a>

                                                <button
                                                    class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#eliminarLocalizacaoModal"
                                                    data-id="<?= $localizacao->id ?>">

                                                    Eliminar

                                                </button>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>
                        </table>

                    </div>

                <?php endif; ?>
            </div>

        </section>
    </main>
    <!-- MODAL ELIMINAR LOCALIZAÇÃO -->
    <div class="modal fade"
        id="eliminarLocalizacaoModal"
        tabindex="-1"
        aria-labelledby="eliminarLocalizacaoModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="eliminarLocalizacaoModalLabel">

                        Confirmar eliminação

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    Tem a certeza que pretende eliminar esta localização?

                    <br><br>

                    <small class="text-muted">

                        Esta ação não pode ser revertida.

                    </small>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">

                        Cancelar

                    </button>

                    <button
                        type="button"
                        class="btn btn-danger">

                        Eliminar

                    </button>

                </div>

            </div>

        </div>

    </div>
    <!-- MODAL EQUIPAMENTOS DA LOCALIZAÇÃO -->

    <div class="modal fade" id="modalEquipamentosLOC0001" tabindex="-1">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Equipamentos da Localização LOC-0001

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

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

                            <tr>

                                <td>EQ-0001</td>

                                <td>Ventilador Pulmonar</td>

                                <td>

                                    <span class="badge bg-success">
                                        Ativo
                                    </span>

                                </td>

                                <td>

                                    <a href="../equipamentos/ficha_equipamento.php"
                                        class="btn btn-sm btn-outline-primary">

                                        <i class="fa-solid fa-eye me-1"></i>
                                        Ver Ficha

                                    </a>

                                </td>

                            </tr>

                            <tr>

                                <td>EQ-0024</td>

                                <td>Monitor Multiparamétrico</td>

                                <td>

                                    <span class="badge bg-success">
                                        Ativo
                                    </span>

                                </td>

                                <td>

                                    <a href="../equipamentos/ficha_equipamento.php"
                                        class="btn btn-sm btn-outline-primary">

                                        <i class="fa-solid fa-eye me-1"></i>
                                        Ver Ficha

                                    </a>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
</div>
<script>
    $(document).ready(function() {
        $('#tabela-localizacoes').DataTable({
            paging: true,
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
<?php include '../../includes/footer.php'; ?>