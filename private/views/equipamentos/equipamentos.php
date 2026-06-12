<?php
// --------------------------------------------------------------------
// SEGURANÇA: Proteção de acesso à página de edição
// Este ficheiro deve ser acedido apenas por utilizadores autenticados.
// Caso não exista sessão iniciada, o utilizador será redirecionado para o login.
// --------------------------------------------------------------------
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged(); // Inicia a sessão (se necessário) e verifica se o utilizador está autenticado
$pesquisa = trim($_GET['pesquisa'] ?? '');
$categoria = trim($_GET['categoria'] ?? '');
$estado = trim($_GET['estado'] ?? '');

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
        equipamentos.*,
        categorias.nome_categoria,
        localizacoes.servico_departamento,
        estados.nome_estado

    FROM equipamentos

    INNER JOIN categorias
        ON equipamentos.id_categoria = categorias.id

    INNER JOIN localizacoes
        ON equipamentos.id_localizacao = localizacoes.id

    INNER JOIN estados
        ON equipamentos.id_estado = estados.id

    WHERE 1=1
    ";

    if (!empty($pesquisa)) {

        $sql .= "
        AND (
            equipamentos.codigo_interno LIKE '%$pesquisa%'
            OR equipamentos.designacao LIKE '%$pesquisa%'
            OR equipamentos.marca LIKE '%$pesquisa%'
            OR equipamentos.modelo LIKE '%$pesquisa%'
        )
        ";
    }
    if (!empty($categoria) && $categoria != 'Todas') {

        $sql .= "
    AND categorias.nome_categoria = '$categoria'
    ";
    }
    if (!empty($estado) && $estado != 'Todos') {

        $sql .= "
    AND estados.nome_estado = '$estado'
    ";
    }

    $sql .= "
    ORDER BY equipamentos.codigo_interno ASC
    ";

    $resultados = $ligacao->query($sql)
        ->fetchAll(PDO::FETCH_OBJ);

    $erro = '';
} catch (PDOException $err) {

    $erro = "Aconteceu um erro na ligação à base de dados.";
    $resultados = [];
}

$ligacao = null;
?>

<?php include '../../includes/header.php';
$paginaAtiva = 'equipamentos';
?>

<div class="private-layout">

    <?php include '../../includes/sidebar.php'; ?>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="private-main">
        <!-- TOPO -->
        <header class="private-topbar">
            <div>
                <h1>Equipamentos</h1>
                <p>Gestão e consulta dos equipamentos médicos registados na plataforma.</p>
            </div>

            <a href="inserir_equipamentos.php" class="btn btn-primary-custom">
                <i class="fa-solid fa-plus me-2"></i>
                Adicionar Equipamento
            </a>
        </header>


        <!-- FILTROS -->
        <section class="card filter-card mb-4">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-3 align-items-end">

                        <div class="col-md-4">
                            <label for="pesquisa" class="form-label">Pesquisar equipamento</label>
                            <input
                                type="text"
                                id="pesquisa"
                                name="pesquisa"
                                class="form-control"
                                placeholder="Código, designação, marca ou modelo"
                                value="<?= htmlspecialchars($_GET['pesquisa'] ?? '') ?>">
                        </div>

                        <div class="col-md-3">
                            <label for="categoria" class="form-label">Categoria</label>
                            <select id="categoria" name="categoria" class="form-select">

                                <option <?= ($categoria == '' || $categoria == 'Todas') ? 'selected' : '' ?>>
                                    Todas
                                </option>

                                <option <?= ($categoria == 'Monitorização') ? 'selected' : '' ?>>
                                    Monitorização
                                </option>

                                <option <?= ($categoria == 'Suporte de Vida') ? 'selected' : '' ?>>
                                    Suporte de Vida
                                </option>

                                <option <?= ($categoria == 'Diagnóstico') ? 'selected' : '' ?>>
                                    Diagnóstico
                                </option>

                                <option <?= ($categoria == 'Terapia') ? 'selected' : '' ?>>
                                    Terapia
                                </option>

                                <option <?= ($categoria == 'Laboratório') ? 'selected' : '' ?>>
                                    Laboratório
                                </option>

                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="estado" class="form-label">Estado atual
                                <button
                                    type="button"
                                    class="btn btn-sm border-0 p-0 ms-1"
                                    data-bs-toggle="popover"
                                    data-bs-trigger="hover focus"
                                    data-bs-html="true"
                                    title="Estados dos Equipamentos"
                                    data-bs-content="
            <b>Ativo</b> - Disponível e operacional.<br>
            <b>Em manutenção</b> - Em intervenção técnica programada ou corretiva.<br>
            <b>Inativo</b> - Temporariamente indisponível para utilização.<br>
            <b>Em calibração</b> - Em processo de calibração ou validação metrológica.">

                                    <i class="fa-solid fa-circle-question text-primary"></i>

                                </button>
                            </label>
                            <select id="estado" name="estado" class="form-select">

                                <option <?= ($estado == '' || $estado == 'Todos') ? 'selected' : '' ?>>
                                    Todos
                                </option>

                                <option <?= ($estado == 'Ativo') ? 'selected' : '' ?>>
                                    Ativo
                                </option>

                                <option <?= ($estado == 'Em manutenção') ? 'selected' : '' ?>>
                                    Em manutenção
                                </option>

                                <option <?= ($estado == 'Inativo') ? 'selected' : '' ?>>
                                    Inativo
                                </option>

                                <option <?= ($estado == 'Em calibração') ? 'selected' : '' ?>>
                                    Em calibração
                                </option>

                            </select>
                        </div>

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
                        Não existem equipamentos registados.
                    </p>
                <?php else : ?>
                    <div class="table-responsive">
                        <table id="tabela-equipamentos" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Designação</th>
                                    <th>Categoria</th>
                                    <th>Marca/Modelo</th>
                                    <th>Localização</th>
                                    <th>Estado</th>
                                    <th>Criticidade</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($resultados as $equipamento) : ?>
                                    <tr>
                                        <td class="codigo-equipamento"><?= htmlspecialchars($equipamento->codigo_interno) ?></td>
                                        <td><?= htmlspecialchars($equipamento->designacao) ?></td>
                                        <td><?= htmlspecialchars($equipamento->nome_categoria) ?></td>
                                        <td style="white-space: nowrap;">
                                            <?= htmlspecialchars($equipamento->marca) ?>
                                            <?= htmlspecialchars($equipamento->modelo) ?>
                                        </td>
                                        <td><?= htmlspecialchars($equipamento->servico_departamento) ?></td>
                                        <td>
                                            <?php
                                            $estado = $equipamento->nome_estado;
                                            if ($estado == 'Ativo') echo '<span class="badge bg-success">Ativo</span>';
                                            elseif ($estado == 'Em manutenção') echo '<span class="badge bg-warning text-dark">Em manutenção</span>';
                                            elseif ($estado == 'Inativo') echo '<span class="badge bg-secondary">Inativo</span>';
                                            elseif ($estado == 'Em calibração') echo '<span class="badge bg-info text-dark">Em calibração</span>';
                                            elseif ($estado == 'Avariado') echo '<span class="badge bg-danger">Avariado</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $criticidade = $equipamento->criticidade;
                                            if ($criticidade == 'Suporte de Vida') echo '<span class="badge bg-danger">Suporte de Vida</span>';
                                            elseif ($criticidade == 'Alta') echo '<span class="badge bg-warning text-dark">Alta</span>';
                                            elseif ($criticidade == 'Média') echo '<span class="badge bg-primary">Média</span>';
                                            elseif ($criticidade == 'Baixa') echo '<span class="badge bg-secondary">Baixa</span>';
                                            ?>
                                        </td>
                                        <td class="text-end acoes-nowrap">
                                            <a href="ficha_equipamento.php?id=<?= $equipamento->id ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                Ver
                                            </a>

                                            <a href="editar_equipamentos.php?id=<?= $equipamento->id ?>"
                                                class="btn btn-sm btn-outline-warning">
                                                Editar
                                            </a>

                                            <button
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#eliminarEquipamentoModal"
                                                data-id="<?= $equipamento->id ?>">
                                                Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col">
                        <p class="mb-5">Total: <strong> <?= count($resultados) ?> </strong></p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </main>

</div>
<!-- MODAL ELIMINAR EQUIPAMENTO -->
<div class="modal fade" id="eliminarEquipamentoModal" tabindex="-1"
    aria-labelledby="eliminarEquipamentoModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">

                <h5 class="modal-title" id="eliminarEquipamentoModalLabel">

                    Confirmar eliminação

                </h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>

            <!-- BODY -->
            <div class="modal-body">

                Tem a certeza que pretende eliminar este equipamento?

                <br><br>

                <small class="text-muted">

                    Esta ação não pode ser revertida.

                </small>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal">

                    Cancelar

                </button>

                <button type="button"
                    class="btn btn-danger">

                    Eliminar

                </button>

            </div>

        </div>

    </div>

</div>
<script>
    $(document).ready(function() {
        $('#tabela-equipamentos').DataTable({
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


<?php include '../../includes/footer.php'; ?>