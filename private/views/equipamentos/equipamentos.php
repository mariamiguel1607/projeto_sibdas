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
$criticidade = trim($_GET['criticidade'] ?? '');
$servico = trim($_GET['servico'] ?? '');
$fornecedor = trim($_GET['fornecedor'] ?? '');

try {

    $ligacao = ligar_bd();

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

    // Array que irá conter os valores dos filtros de forma segura,
    // passados separadamente à query para evitar SQL Injection
    $params = [];

    if (!empty($pesquisa)) {
        $sql .= "
    AND (
        equipamentos.codigo_interno LIKE ?
        OR equipamentos.designacao LIKE ?
        OR equipamentos.marca LIKE ?
        OR equipamentos.modelo LIKE ?
    )
    ";
        $params[] = '%' . $pesquisa . '%';
        $params[] = '%' . $pesquisa . '%';
        $params[] = '%' . $pesquisa . '%';
        $params[] = '%' . $pesquisa . '%';
    }
    if (!empty($categoria) && $categoria != 'Todas') {
        $sql .= "AND categorias.nome_categoria = ? ";
        $params[] = $categoria;
    }
    if (!empty($estado) && $estado != 'Todos') {
        $sql .= "AND estados.nome_estado = ? ";
        $params[] = $estado;
    }
    if (!empty($criticidade) && $criticidade != 'Todas') {
        $sql .= "AND equipamentos.criticidade = ? ";
        $params[] = $criticidade;
    }
    if (!empty($servico) && $servico != 'Todos') {
        $sql .= "AND localizacoes.servico_departamento = ? ";
        $params[] = $servico;
    }
    if (!empty($fornecedor) && $fornecedor != 'Todos') {
        $sql .= "AND equipamentos.id IN (
            SELECT id_equipamento FROM equipamentos_fornecedores
            INNER JOIN fornecedores ON equipamentos_fornecedores.id_fornecedor = fornecedores.id
            WHERE fornecedores.nome_empresa = ?
        ) ";
        $params[] = $fornecedor;
    }

    $sql .= "
    ORDER BY equipamentos.codigo_interno ASC
    ";

    $stmt = $ligacao->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_OBJ);

    $erro = '';
} catch (PDOException $err) {

    $erro = "Aconteceu um erro na ligação à base de dados.";
    $resultados = [];
}

$ligacao = null;

// Buscar serviços e fornecedores para os filtros dinâmicos
try {
    $ligacao2 = ligar_bd();

    $stmtServicos = $ligacao2->query("SELECT DISTINCT servico_departamento FROM localizacoes ORDER BY servico_departamento ASC");
    $servicos = $stmtServicos->fetchAll(PDO::FETCH_COLUMN);

    $stmtFornecedores = $ligacao2->query("SELECT DISTINCT nome_empresa FROM fornecedores WHERE ativo = 1 ORDER BY nome_empresa ASC");
    $fornecedoresLista = $stmtFornecedores->fetchAll(PDO::FETCH_COLUMN);

    $ligacao2 = null;
} catch (PDOException $e) {
    $servicos = [];
    $fornecedoresLista = [];
}
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

            <?php if (in_array($_SESSION['perfil'] ?? '', ['Administrador', 'Técnico'])): ?>
                <a href="inserir_equipamentos.php" class="btn btn-primary-custom">
                    <i class="fa-solid fa-plus me-2"></i>
                    Adicionar Equipamento
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
                            <label for="pesquisa" class="form-label">Pesquisar equipamento</label>
                            <input
                                type="text"
                                id="pesquisa"
                                name="pesquisa"
                                class="form-control"
                                placeholder="Código, designação, marca ou modelo"
                                value="<?= htmlspecialchars($_GET['pesquisa'] ?? '') ?>">
                        </div>

                        <!-- Categoria -->
                        <div class="col-md-2">
                            <label for="categoria" class="form-label">Categoria</label>
                            <select id="categoria" name="categoria" class="form-select">
                                <option <?= ($categoria == '' || $categoria == 'Todas') ? 'selected' : '' ?>>Todas</option>
                                <option <?= ($categoria == 'Monitorização') ? 'selected' : '' ?>>Monitorização</option>
                                <option <?= ($categoria == 'Suporte de Vida') ? 'selected' : '' ?>>Suporte de Vida</option>
                                <option <?= ($categoria == 'Diagnóstico') ? 'selected' : '' ?>>Diagnóstico</option>
                                <option <?= ($categoria == 'Terapia') ? 'selected' : '' ?>>Terapia</option>
                                <option <?= ($categoria == 'Laboratório') ? 'selected' : '' ?>>Laboratório</option>
                            </select>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-2">
                            <label for="estado" class="form-label">Estado
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
                                <option <?= ($estado == '' || $estado == 'Todos') ? 'selected' : '' ?>>Todos</option>
                                <option <?= ($estado == 'Ativo') ? 'selected' : '' ?>>Ativo</option>
                                <option <?= ($estado == 'Em manutenção') ? 'selected' : '' ?>>Em manutenção</option>
                                <option <?= ($estado == 'Inativo') ? 'selected' : '' ?>>Inativo</option>
                                <option <?= ($estado == 'Em calibração') ? 'selected' : '' ?>>Em calibração</option>
                            </select>
                        </div>

                        <!-- Criticidade -->
                        <div class="col-md-2">
                            <label for="criticidade" class="form-label">Criticidade</label>
                            <select id="criticidade" name="criticidade" class="form-select">
                                <option <?= ($criticidade == '' || $criticidade == 'Todas') ? 'selected' : '' ?>>Todas</option>
                                <option <?= ($criticidade == 'Baixa') ? 'selected' : '' ?>>Baixa</option>
                                <option <?= ($criticidade == 'Média') ? 'selected' : '' ?>>Média</option>
                                <option <?= ($criticidade == 'Alta') ? 'selected' : '' ?>>Alta</option>
                                <option <?= ($criticidade == 'Suporte de Vida') ? 'selected' : '' ?>>Suporte de Vida</option>
                            </select>
                        </div>

                        <!-- Botão Filtrar -->
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                <i class="fa-solid fa-filter me-2"></i>
                                Filtrar
                            </button>
                        </div>

                    </div>

                    <!-- Segunda linha de filtros -->
                    <div class="row g-3 align-items-end mt-1">

                        <!-- Serviço -->
                        <div class="col-md-4">
                            <label for="servico" class="form-label">Serviço / Departamento</label>
                            <select id="servico" name="servico" class="form-select">
                                <option <?= ($servico == '' || $servico == 'Todos') ? 'selected' : '' ?>>Todos</option>
                                <?php foreach ($servicos as $s): ?>
                                    <option <?= ($servico == $s) ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Fornecedor -->
                        <div class="col-md-4">
                            <label for="fornecedor" class="form-label">Fornecedor</label>
                            <select id="fornecedor" name="fornecedor" class="form-select">
                                <option <?= ($fornecedor == '' || $fornecedor == 'Todos') ? 'selected' : '' ?>>Todos</option>
                                <?php foreach ($fornecedoresLista as $f): ?>
                                    <option <?= ($fornecedor == $f) ? 'selected' : '' ?>><?= htmlspecialchars($f) ?></option>
                                <?php endforeach; ?>
                            </select>
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
                                            <a href="ficha_equipamento.php?id=<?= aes_encrypt($equipamento->id) ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                Ver
                                            </a>

                                            <?php if (in_array($_SESSION['perfil'] ?? '', ['Administrador', 'Técnico'])): ?>

                                                <?php if ($equipamento->ativo): ?>
                                                    <a href="editar_equipamentos.php?id_equipamento=<?= aes_encrypt($equipamento->id) ?>"
                                                        class="btn btn-sm btn-outline-warning">
                                                        Editar
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                                        Editar
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($equipamento->ativo): ?>
                                                    <button
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#eliminarEquipamentoModal"
                                                        data-id="<?= aes_encrypt($equipamento->id) ?>"
                                                        data-acao="desativar">
                                                        <i class="fa-solid fa-ban me-1"></i>
                                                        Eliminar
                                                    </button>
                                                <?php else: ?>
                                                    <button
                                                        class="btn btn-sm btn-outline-success"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#eliminarEquipamentoModal"
                                                        data-id="<?= aes_encrypt($equipamento->id) ?>"
                                                        data-acao="reativar">
                                                        <i class="fa-solid fa-circle-check me-1"></i>
                                                        Reativar
                                                    </button>
                                                <?php endif; ?>

                                            <?php endif; ?>
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
<!-- MODAL ELIMINAR/REATIVAR EQUIPAMENTO -->
<div class="modal fade" id="eliminarEquipamentoModal" tabindex="-1"
    aria-labelledby="eliminarEquipamentoModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="eliminarEquipamentoModalLabel">
                    Confirmar ação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="modalEquipamentoBody">
                <!-- preenchido pelo JavaScript -->
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" id="btnConfirmarEliminar" class="btn btn-danger">
                    <!-- preenchido pelo JavaScript -->
                </button>
            </div>

        </div>
    </div>

</div>
<script>
    document.getElementById('eliminarEquipamentoModal').addEventListener('show.bs.modal', function(event) {
        const botao = event.relatedTarget;
        const idEncriptado = botao.getAttribute('data-id');
        const acao = botao.getAttribute('data-acao');

        const btnConfirmar = document.getElementById('btnConfirmarEliminar');
        const body = document.getElementById('modalEquipamentoBody');
        const titulo = document.getElementById('eliminarEquipamentoModalLabel');

        btnConfirmar.setAttribute('data-id', idEncriptado);
        btnConfirmar.setAttribute('data-acao', acao);

        if (acao === 'reativar') {
            titulo.textContent = 'Reativar Equipamento';
            body.innerHTML = 'Tem a certeza que pretende <strong>reativar</strong> este equipamento?<br><br><small class="text-muted">O equipamento voltará a estar disponível.</small>';
            btnConfirmar.textContent = 'Reativar';
            btnConfirmar.className = 'btn btn-success';
        } else {
            titulo.textContent = 'Confirmar eliminação';
            body.innerHTML = 'Tem a certeza que pretende <strong>eliminar</strong> este equipamento?<br><br><small class="text-muted">O equipamento ficará inativo.</small>';
            btnConfirmar.textContent = 'Eliminar';
            btnConfirmar.className = 'btn btn-danger';
        }
    });

    document.getElementById('btnConfirmarEliminar').addEventListener('click', function() {
        const idEncriptado = this.getAttribute('data-id');
        const acao = this.getAttribute('data-acao');

        if (acao === 'reativar') {
            window.location.href = 'reativar_equipamento.php?id=' + idEncriptado;
        } else {
            window.location.href = 'eliminar_equipamento.php?id=' + idEncriptado;
        }
    });
</script>
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