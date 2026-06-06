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

                <a href="inserir_fornecedor.php" class="btn btn-primary-custom">

                    <i class="fa-solid fa-plus me-2"></i>
                    Adicionar Fornecedor

                </a>

            </header>


            <!-- FILTROS -->
            <section class="card filter-card mb-4">

                <div class="card-body">

                    <div class="row g-3 align-items-end">

                        <!-- Pesquisa -->
                        <div class="col-md-4">

                            <label class="form-label">
                                Pesquisa
                            </label>

                            <input type="text" class="form-control" placeholder="Pesquisar fornecedor...">

                        </div>

                        <!-- Tipo -->
                        <div class="col-md-3">

                            <label class="form-label">
                                Tipo de fornecedor
                            </label>

                            <select class="form-select">

                                <option selected>
                                    Todos
                                </option>

                                <option>
                                    Fabricante
                                </option>

                                <option>
                                    Distribuidor
                                </option>

                                <option>
                                    Assistência Técnica
                                </option>

                                <option>
                                    Consumíveis / Acessórios
                                </option>

                            </select>

                        </div>

                        <!-- Botão -->
                        <div class="col-md-2">

                            <button class="btn btn-outline-secondary w-100">

                                <i class="fa-solid fa-filter me-2"></i>
                                Filtrar

                            </button>

                        </div>

                    </div>

                </div>

            </section>


            <!-- TABELA -->
            <section class="card table-card">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle">

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

                                <!-- FORNECEDOR 1 -->
                                <tr>

                                    <td>FOR-0001</td>

                                    <td>Dräger</td>

                                    <td class="text-nowrap">
                                        +351 220 000 001
                                    </td>

                                    <td>info@draeger.pt</td>

                                    <td>

                                        <span class="badge rounded-pill bg-light text-dark border px-2 py-1">

                                            5 equipamentos

                                        </span>

                                    </td>

                                    <td>

                                        <span class="badge rounded-pill bg-primary-subtle text-primary px-2 py-1">

                                            Fabricante

                                        </span>

                                    </td>

                                    <td class="text-end">

                                        <div class="d-flex gap-2 justify-content-end flex-nowrap">

                                            <a href="ficha_fornecedor.php" class="btn btn-sm btn-outline-primary">

                                                Ver

                                            </a>

                                            <a href="editar_fornecedor.php" class="btn btn-sm btn-outline-warning">

                                                Editar

                                            </a>

                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#eliminarGarantiaModal">

                                                Eliminar

                                            </button>

                                        </div>

                                    </td>

                                </tr>


                                <!-- FORNECEDOR 2 -->
                                <tr>

                                    <td>FOR-0002</td>

                                    <td>MedEquip Portugal</td>

                                    <td class="text-nowrap">
                                        +351 220 000 002
                                    </td>

                                    <td>geral@medequip.pt</td>

                                    <td>

                                        <span class="badge rounded-pill bg-light text-dark border px-2 py-1">

                                            12 equipamentos

                                        </span>

                                    </td>

                                    <td>

                                        <span
                                            class="badge rounded-pill bg-warning-subtle text-warning-emphasis px-2 py-1">

                                            Distribuidor

                                        </span>

                                    </td>

                                    <td class="text-end">

                                        <div class="d-flex gap-2 justify-content-end flex-nowrap">

                                            <a href="ficha_fornecedor.php" class="btn btn-sm btn-outline-primary">

                                                Ver

                                            </a>

                                            <a href="editar_fornecedor.php" class="btn btn-sm btn-outline-warning">

                                                Editar

                                            </a>

                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#eliminarGarantiaModal">

                                                Eliminar

                                            </button>

                                        </div>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </section>

        </main>
        <!-- MODAL ELIMINAR -->
        <div class="modal fade" id="eliminarGarantiaModal" tabindex="-1" aria-labelledby="eliminarGarantiaModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title" id="eliminarGarantiaModalLabel">

                            Confirmar eliminação

                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal">
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

                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">

                            Cancelar

                        </button>

                        <button type="button" class="btn btn-danger">

                            Eliminar

                        </button>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <?php include '../../includes/footer.php'; ?> 