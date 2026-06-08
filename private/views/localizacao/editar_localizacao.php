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

            <!-- CABEÇALHO -->
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

                <div>

                    <h1 class="fw-bold mb-1">
                        Editar Localização
                    </h1>

                    <p class="text-muted mb-0">
                        Atualização da informação associada à localização física.
                    </p>

                </div>

                <div class="d-flex gap-2">

                    <a href="localizacao.php" class="btn btn-outline-secondary">

                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Voltar

                    </a>
                </div>

            </div>


            <!-- FORMULÁRIO -->
            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body p-4 p-lg-5">

                    <form>

                        <!-- DADOS DA LOCALIZAÇÃO -->
                        <h4 class="fw-bold mb-4">

                            <i class="fa-solid fa-location-dot me-2 text-primary"></i>
                            Dados da localização

                        </h4>
                        <!-- ALERTAS -->
                        <div id="alertasDadosGerais" class="alert alert-danger mb-4">

                            <h6 class="alert-heading mb-2">

                                <i class="fa-solid fa-circle-exclamation me-2"></i>
                                Foram encontrados erros

                            </h6>

                            <ul class="mb-0">

                                <li>Código interno é obrigatório.</li>

                                <li>Categoria é obrigatória.</li>

                            </ul>

                        </div>

                        <div class="row g-4">

                            <!-- Código -->
                            <div class="col-md-6">

                                <label for="codigoLocalizacao" class="form-label fw-bold">

                                    Código da Localização

                                </label>

                                <input type="text" id="codigoLocalizacao" class="form-control" value="LOC-001">

                            </div>

                            <!-- Edifício -->
                            <div class="col-md-6">

                                <label for="edificio" class="form-label fw-bold">

                                    Edifício

                                </label>

                                <select id="edificio" class="form-select">

                                    <option selected>
                                        Hospital Central
                                    </option>

                                    <option>
                                        Clínica Norte
                                    </option>

                                    <option>
                                        Bloco Operatório
                                    </option>

                                </select>

                            </div>

                            <!-- Piso -->
                            <div class="col-md-6">

                                <label for="piso" class="form-label fw-bold">

                                    Piso

                                </label>

                                <select id="piso" class="form-select">

                                    <option>
                                        Piso 0
                                    </option>

                                    <option>
                                        Piso 1
                                    </option>

                                    <option selected>
                                        Piso 2
                                    </option>

                                    <option>
                                        Piso 3
                                    </option>

                                </select>

                            </div>

                            <!-- Departamento -->
                            <div class="col-md-6">

                                <label for="departamento" class="form-label fw-bold">

                                    Departamento

                                </label>

                                <select id="departamento" class="form-select">

                                    <option selected>
                                        Unidade de Cuidados Intensivos
                                    </option>

                                    <option>
                                        Cardiologia
                                    </option>

                                    <option>
                                        Radiologia
                                    </option>

                                </select>

                            </div>

                            <!-- Sala -->
                            <div class="col-md-6">

                                <label for="sala" class="form-label fw-bold">

                                    Sala / Gabinete

                                </label>

                                <input type="text" id="sala" class="form-control" value="UCI-02">

                            </div>

                        </div>




                        <!-- BOTÕES -->
                        <div class="d-flex justify-content-end gap-3 mt-5">

                            <a href="localizacao.php" class="btn btn-outline-secondary">

                                Cancelar

                            </a>

                            <button type="submit" class="btn btn-primary-custom">

                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Guardar Alterações

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </main>
    </div>
    <?php include '../../includes/footer.php'; ?> 