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

                    <div class="row g-3 align-items-end">

                        <!-- Pesquisa -->
                        <div class="col-md-4">

                            <label for="pesquisa" class="form-label">
                                Pesquisar localização
                            </label>

                            <input type="text" id="pesquisa" class="form-control"
                                placeholder="Edifício, piso, departamento ou sala">

                        </div>

                        <!-- Edifício -->
                        <div class="col-md-3">

                            <label for="categoria" class="form-label">
                                Edifício
                            </label>

                            <select id="categoria" class="form-select">

                                <option selected>Todos</option>

                                <option>Hospital Central</option>

                                <option>Clínica Norte</option>

                                <option>Bloco Operatório</option>

                                <option>Urgência</option>

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

                                <!-- LINHA 1 -->
                                <tr data-categoria="Hospital Central" data-estado="Ativa">

                                    <td>LOC-0001</td>

                                    <td>Hospital Central</td>

                                    <td>Piso 2</td>

                                    <td>Cardiologia</td>

                                    <td>Sala 2.14</td>

                                    <td>

                                        <button class="badge rounded-pill bg-light text-dark border px-3 py-2"
                                            style="cursor:pointer;" data-bs-toggle="modal"
                                            data-bs-target="#modalEquipamentosLOC0001">

                                            2 equipamentos

                                        </button>

                                    </td>

                                    <td class="text-end">

                                        <div class="d-flex gap-2 justify-content-end flex-nowrap">


                                            <a href="editar_localizacao.php" class="btn btn-sm btn-outline-warning">

                                                Editar
                                            </a>

                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#eliminarGarantiaModal">

                                                Eliminar
                                            </button>

                                        </div>

                                    </td>

                                </tr>


                                <!-- LINHA 2 -->
                                <tr data-categoria="Clínica Norte" data-estado="Em manutenção">

                                    <td>LOC-0002</td>

                                    <td>Clínica Norte</td>

                                    <td>Piso 1</td>

                                    <td>Radiologia</td>

                                    <td>Gabinete 4</td>

                                    <td>

                                        <button class="badge rounded-pill bg-light text-dark border px-3 py-2"
                                            style="cursor:pointer;" data-bs-toggle="modal"
                                            data-bs-target="#modalEquipamentosLOC0001">

                                            2 equipamentos

                                        </button>

                                    </td>

                                    <td class="text-end">

                                        <div class="d-flex gap-2 justify-content-end flex-nowrap">

                                            <a href="editar_localizacao.php" class="btn btn-sm btn-outline-warning">

                                                Editar
                                            </a>

                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#eliminarGarantiaModal">

                                                Eliminar

                                            </button>

                                        </div>

                                    </td>

                                </tr>


                                <!-- LINHA 3 -->
                                <tr data-categoria="Hospital Central" data-estado="Ativa">

                                    <td>LOC-0003</td>

                                    <td>Hospital Central</td>

                                    <td>Piso 0</td>

                                    <td>Urgência</td>

                                    <td>Sala de Triagem</td>

                                    <td>

                                        <button class="badge rounded-pill bg-light text-dark border px-3 py-2"
                                            style="cursor:pointer;" data-bs-toggle="modal"
                                            data-bs-target="#modalEquipamentosLOC0001">

                                            2 equipamentos

                                        </button>

                                    </td>

                                    <td class="text-end">

                                        <div class="d-flex gap-2 justify-content-end flex-nowrap">

                                            <a href="editar_localizacao.php" class="btn btn-sm btn-outline-warning">

                                                Editar
                                            </a>

                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#eliminarGarantiaModal">

                                                Eliminar

                                            </button>

                                        </div>

                                    </td>

                                </tr>


                                <!-- LINHA 4 -->
                                <tr data-categoria="Bloco Operatório" data-estado="Ativa">

                                    <td>LOC-0004</td>

                                    <td>Bloco Operatório</td>

                                    <td>Piso 3</td>

                                    <td>Cirurgia</td>

                                    <td>Bloco 3</td>

                                    <td>

                                        <button class="badge rounded-pill bg-light text-dark border px-3 py-2"
                                            style="cursor:pointer;" data-bs-toggle="modal"
                                            data-bs-target="#modalEquipamentosLOC0001">

                                            2 equipamentos

                                        </button>

                                    </td>

                                    <td class="text-end">

                                        <div class="d-flex gap-2 justify-content-end flex-nowrap">

                                            <a href="editar_localizacao.php" class="btn btn-sm btn-outline-warning">

                                                Editar
                                            </a>

                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#eliminarGarantiaModal">

                                                Eliminar

                                            </button>

                                        </div>

                                    </td>

                                </tr>


                                <!-- LINHA 5 -->
                                <tr data-categoria="Hospital Central" data-estado="Inativa">

                                    <td>LOC-0005</td>

                                    <td>Hospital Central</td>

                                    <td>Piso -1</td>

                                    <td>Armazém</td>

                                    <td>Sala Técnica</td>

                                    <td>

                                        <button class="badge rounded-pill bg-light text-dark border px-3 py-2"
                                            style="cursor:pointer;" data-bs-toggle="modal"
                                            data-bs-target="#modalEquipamentosLOC0001">

                                            2 equipamentos

                                        </button>

                                    </td>


                                    <td class="text-end">

                                        <div class="d-flex gap-2 justify-content-end flex-nowrap">

                                            <a href="editar_localizacao.php" class="btn btn-sm btn-outline-warning">

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

                        Tem a certeza que pretende eliminar esta localização?

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
     <?php include '../../includes/footer.php'; ?> 