<?php include '../../includes/header.php'; 
$paginaAtiva = 'localizacao';
?>

    <div class="private-layout">

        <?php include '../../includes/sidebar.php'; ?>
        <!-- CONTEÚDO PRINCIPAL -->
        <main class="private-main">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h1 class="fw-bold mb-1">
                        Adicionar Localização
                    </h1>

                    <p class="text-muted mb-0">
                        Registo de uma nova localização física na plataforma.
                    </p>

                </div>

                <a href="localizacao.php" class="btn btn-outline-secondary">

                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Voltar

                </a>

            </div>

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body p-4 p-lg-5">

                    <form>

                        <!-- DADOS PRINCIPAIS -->
                        <h4 class="fw-bold mb-4">
                            Dados principais
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
                            <div class="col-md-4">

                                <label for="codigo" class="form-label fw-bold">
                                    Código da localização
                                </label>

                                <input type="text" class="form-control" id="codigo" placeholder="Ex: LOC-0006">

                            </div>

                            <!-- Edifício -->
                            <div class="col-md-8">

                                <label for="edificio" class="form-label fw-bold">
                                    Edifício
                                </label>

                                <input type="text" class="form-control" id="edificio"
                                    placeholder="Ex: Hospital Central">

                            </div>

                            <!-- Piso -->
                            <div class=" col-md-4">

                                <label for="piso" class="form-label fw-bold">
                                    Piso
                                </label>

                                <input type="text" class="form-control" id="piso" placeholder="Ex: Piso 2">

                            </div>

                            <!-- Departamento -->
                            <div class="col-md-4">

                                <label for="departamento" class="form-label fw-bold">
                                    Departamento / Serviço
                                </label>

                                <input type="text" class="form-control" id="departamento" placeholder="Ex: Cardiologia">

                            </div>

                            <!-- Sala -->
                            <div class="col-md-4">

                                <label for="sala" class="form-label fw-bold">
                                    Sala / Gabinete
                                </label>

                                <input type="text" class="form-control" id="sala" placeholder="Ex: Sala 2.14">

                            </div>

                        </div>



                        <!-- BOTÕES -->
                        <div class="d-flex justify-content-end gap-3 mt-5">

                            <a href="localizacao.php" class="btn btn-outline-secondary">

                                Cancelar

                            </a>

                            <button type="submit" class="btn btn-primary-custom">

                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Guardar Localização

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </main>
    </div>
     <?php include '../../includes/footer.php'; ?> 