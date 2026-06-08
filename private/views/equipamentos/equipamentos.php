<?php
// --------------------------------------------------------------------
// SEGURANÇA: Proteção de acesso à página de edição
// Este ficheiro deve ser acedido apenas por utilizadores autenticados.
// Caso não exista sessão iniciada, o utilizador será redirecionado para o login.
// --------------------------------------------------------------------
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged(); // Inicia a sessão (se necessário) e verifica se o utilizador está autenticado
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
                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label for="pesquisa" class="form-label">Pesquisar equipamento</label>
                        <input type="text" id="pesquisa" class="form-control"
                            placeholder="Código, designação, marca ou modelo">
                    </div>

                    <div class="col-md-3">
                        <label for="categoria" class="form-label">Categoria</label>
                        <select id="categoria" class="form-select">
                            <option selected>Todas</option>
                            <option>Monitorização</option>
                            <option>Suporte de vida</option>
                            <option>Diagnóstico</option>
                            <option>Terapia</option>
                            <option>Laboratório</option>
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
                        <select id="estado" class="form-select">
                            <option selected>Todos</option>
                            <option>Ativo</option>
                            <option>Em manutenção</option>
                            <option>Inativo</option>
                            <option>Em calibração</option>
                        </select>
                    </div>

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
                            <tr data-categoria="Suporte de vida" data-estado="Ativo">
                                <td>EQ-0001</td>
                                <td>Ventilador Pulmonar</td>
                                <td>Suporte de vida</td>
                                <td>Philips VX-200</td>
                                <td>Hospital Central • Piso 2</td>
                                <td>
                                    <span class="badge bg-success">Ativo</span>
                                </td>
                                <td>
                                    <span class="badge bg-danger">Crítica</span>
                                </td>
                                <td class="text-end">
                                    <a href="ficha_equipamento.php" class="btn btn-sm btn-outline-primary">
                                        Ver
                                    </a>
                                    <a href="editar_equipamentos.php" class="btn btn-sm btn-outline-warning">
                                        Editar
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#eliminarDocumentoModal">

                                        Eliminar

                                    </button>
                                </td>
                            </tr>

                            <tr data-categoria="Monitorização" data-estado="Em manutenção">
                                <td>EQ-0002</td>
                                <td>Monitor Multiparamétrico</td>
                                <td>Monitorização</td>
                                <td>Mindray BeneVision N12</td>
                                <td>Hospital Central • Piso 1</td>
                                <td>
                                    <span class="badge bg-warning text-dark">Em manutenção</span>
                                </td>
                                <td>
                                    <span class="badge bg-danger">Crítica</span>
                                </td>
                                <td class="text-end">
                                    <a href="ficha_equipamento.php" class="btn btn-sm btn-outline-primary">
                                        Ver
                                    </a>
                                    <a href="editar_equipamentos.php" class="btn btn-sm btn-outline-warning">
                                        Editar
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#eliminarDocumentoModal">

                                        Eliminar

                                    </button>
                                </td>
                            </tr>

                            <tr data-categoria="Terapia" data-estado="Em calibração">
                                <td>EQ-0003</td>
                                <td>Bomba de Infusão</td>
                                <td>Terapia</td>
                                <td>B. Braun Infusomat Space</td>
                                <td>Clínica Norte • Piso 0</td>
                                <td>
                                    <span class="badge bg-info text-dark">Em calibração</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark">Alta</span>
                                </td>
                                <td class="text-end">
                                    <a href="ficha_equipamento.php" class="btn btn-sm btn-outline-primary">
                                        Ver
                                    </a>
                                    <a href="editar_equipamentos.php" class="btn btn-sm btn-outline-warning">
                                        Editar
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#eliminarDocumentoModal">

                                        Eliminar

                                    </button>
                                </td>
                            </tr>

                            <tr data-categoria="Suporte de vida" data-estado="Ativo">
                                <td>EQ-0004</td>
                                <td>Desfibrilhador</td>
                                <td>Suporte de vida</td>
                                <td>Zoll R Series</td>
                                <td>Urgência • Piso 2</td>
                                <td>
                                    <span class="badge bg-success">Ativo</span>
                                </td>
                                <td>
                                    <span class="badge bg-danger">Crítica</span>
                                </td>
                                <td class="text-end">
                                    <a href="ficha_equipamento.php" class="btn btn-sm btn-outline-primary">
                                        Ver
                                    </a>
                                    <a href="editar_equipamentos.php" class="btn btn-sm btn-outline-warning">
                                        Editar
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                        data-bs-target="#eliminarDocumentoModal">

                                        Eliminar

                                    </button>
                                </td>
                            </tr>

                            <tr data-categoria="Diagnóstico" data-estado="Inativo">
                                <td>EQ-0005</td>
                                <td>Eletrocardiógrafo</td>
                                <td>Diagnóstico</td>
                                <td>GE MAC 2000</td>
                                <td>Hospital Central • Piso -1</td>
                                <td>
                                    <span class="badge bg-secondary">Inativo</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">Média</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-2 justify-content-end flex-nowrap">
                                        <a href="ficha_equipamento.php" class="btn btn-sm btn-outline-primary">
                                            Ver
                                        </a>
                                        <a href="editar_equipamentos.php" class="btn btn-sm btn-outline-warning">
                                            Editar
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#eliminarDocumentoModal">

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

</div>
<!-- MODAL ELIMINAR DOCUMENTO -->
<div class="modal fade" id="eliminarDocumentoModal" tabindex="-1" aria-labelledby="eliminarDocumentoModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">

                <h5 class="modal-title" id="eliminarDocumentoModalLabel">

                    Confirmar eliminação

                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

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

<?php include '../../includes/footer.php'; ?>