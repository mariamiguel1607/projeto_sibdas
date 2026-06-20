<?php
// --------------------------------------------------------------------
// SEGURANÇA: Proteção de acesso à página de edição
// Este ficheiro deve ser acedido apenas por utilizadores autenticados.
// Caso não exista sessão iniciada, o utilizador será redirecionado para o login.
// --------------------------------------------------------------------
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged(); // Inicia a sessão (se necessário) e verifica se o utilizador está autenticado

$pesquisa = trim($_GET['pesquisa'] ?? '');
$tipo = trim($_GET['tipo'] ?? '');

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
        fornecedores.*,
        COUNT(equipamentos_fornecedores.id_equipamento) AS total_equipamentos

    FROM fornecedores

    LEFT JOIN equipamentos_fornecedores
        ON fornecedores.id = equipamentos_fornecedores.id_fornecedor

    WHERE 1=1
    ";

    if (!empty($pesquisa)) {

        $sql .= "
        AND (
            fornecedores.codigo_fornecedor LIKE '%$pesquisa%'
            OR fornecedores.nome_empresa LIKE '%$pesquisa%'
            OR fornecedores.email LIKE '%$pesquisa%'
            OR fornecedores.telefone LIKE '%$pesquisa%'
        )
        ";
    }

    if (!empty($tipo) && $tipo != 'Todos') {

        $sql .= "
        AND fornecedores.tipo_fornecedor = '$tipo'
        ";
    }

    $sql .= "
    GROUP BY fornecedores.id

    ORDER BY fornecedores.codigo_fornecedor ASC
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
$paginaAtiva = 'fornecedores';
?>

<div class="private-layout">

    <?php include '../../includes/sidebar.php'; ?>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="private-main">

        <!-- TOPO -->
        <header class="private-topbar">

            <div>

                <h1>Fornecedores</h1>

                <p>
                    Gestão de fornecedores associados aos equipamentos médicos da plataforma.
                </p>

            </div>

            <?php if (in_array($_SESSION['perfil'] ?? '', ['Administrador', 'Técnico'])): ?>
                <a href="inserir_fornecedor.php" class="btn btn-primary-custom">
                    <i class="fa-solid fa-plus me-2"></i>
                    Adicionar Fornecedor
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

                            <label class="form-label">
                                Pesquisa
                            </label>

                            <input
                                type="text"
                                name="pesquisa"
                                class="form-control"
                                placeholder="Pesquisar fornecedor..."
                                value="<?= htmlspecialchars($pesquisa) ?>">
                        </div>

                        <!-- Tipo -->
                        <div class="col-md-3">

                            <label class="form-label">
                                Tipo de fornecedor
                            </label>

                            <select name="tipo" class="form-select">

                                <option <?= ($tipo == '' || $tipo == 'Todos') ? 'selected' : '' ?>>
                                    Todos
                                </option>

                                <option <?= ($tipo == 'Fabricante') ? 'selected' : '' ?>>
                                    Fabricante
                                </option>

                                <option <?= ($tipo == 'Distribuidor') ? 'selected' : '' ?>>
                                    Distribuidor
                                </option>

                                <option <?= ($tipo == 'Assistência Técnica') ? 'selected' : '' ?>>
                                    Assistência Técnica
                                </option>

                                <option <?= ($tipo == 'Consumíveis / Acessórios') ? 'selected' : '' ?>>
                                    Consumíveis / Acessórios
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
                        Não existem fornecedores registados.
                    </p>
                <?php else : ?>

                    <div class="table-responsive">

                        <table id="tabela-fornecedores" class="table table-hover align-middle">

                            <thead>

                                <tr>

                                    <th>Código</th>

                                    <th>Nome</th>

                                    <th>Contacto</th>

                                    <th>Email</th>

                                    <th>Equipamentos</th>

                                    <th>Tipo</th>

                                    <th class="text-end">
                                        Ações
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($resultados as $fornecedor) : ?>

                                    <tr>

                                        <td><?= htmlspecialchars($fornecedor->codigo_fornecedor) ?></td>

                                        <td><?= htmlspecialchars($fornecedor->nome_empresa) ?></td>

                                        <td class="text-nowrap">
                                            <?= htmlspecialchars($fornecedor->telefone) ?>
                                        </td>

                                        <td><?= htmlspecialchars($fornecedor->email) ?></td>

                                        <td>

                                            <span class="badge rounded-pill bg-light text-dark border px-2 py-1">

                                                <?= $fornecedor->total_equipamentos ?> equipamentos

                                            </span>

                                        </td>

                                        <td>

                                            <?php
                                            $tipo = $fornecedor->tipo_fornecedor;

                                            if ($tipo == 'Fabricante') {
                                                echo '<span class="badge rounded-pill bg-primary-subtle text-primary px-2 py-1">Fabricante</span>';
                                            } elseif ($tipo == 'Distribuidor') {
                                                echo '<span class="badge rounded-pill bg-warning-subtle text-warning-emphasis px-2 py-1">Distribuidor</span>';
                                            } elseif ($tipo == 'Assistência Técnica') {
                                                echo '<span class="badge rounded-pill bg-success-subtle text-success px-2 py-1">Assistência Técnica</span>';
                                            } elseif ($tipo == 'Consumíveis / Acessórios') {
                                                echo '<span class="badge rounded-pill bg-info-subtle text-info px-2 py-1">Consumíveis / Acessórios</span>';
                                            }
                                            ?>

                                        </td>

                                        <td class="text-end">

                                            <div class="d-flex gap-2 justify-content-end flex-nowrap">

                                                <a href="ficha_fornecedor.php?id=<?= aes_encrypt($fornecedor->id) ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    Ver
                                                </a>
                                                <?php if (in_array($_SESSION['perfil'] ?? '', ['Administrador', 'Técnico'])): ?>

                                                    <a href="editar_fornecedor.php?id=<?= aes_encrypt($fornecedor->id) ?>"
                                                        class="btn btn-sm btn-outline-warning">
                                                        Editar
                                                    </a>

                                                    <?php if ($fornecedor->ativo): ?>
                                                        <a href="eliminar_fornecedor.php?id=<?= aes_encrypt($fornecedor->id) ?>"
                                                            class="btn btn-sm btn-outline-danger">
                                                            <i class="fa-solid fa-ban me-1"></i>
                                                            Eliminar
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="reativar_fornecedor.php?id=<?= aes_encrypt($fornecedor->id) ?>"
                                                            class="btn btn-sm btn-outline-success">
                                                            <i class="fa-solid fa-circle-check me-1"></i>
                                                            Reativar
                                                        </a>
                                                    <?php endif; ?>

                                                <?php endif; ?>

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
    <!-- MODAL ELIMINAR FORNECEDOR -->
    <div class="modal fade"
        id="eliminarFornecedorModal"
        tabindex="-1"
        aria-labelledby="eliminarFornecedorModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="eliminarFornecedorModalLabel">

                        Confirmar eliminação

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    Tem a certeza que pretende eliminar este fornecedor?

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
    <script>
        $(document).ready(function() {
            $('#tabela-fornecedores').DataTable({
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