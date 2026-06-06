<?php include '../../includes/header.php'; 
$paginaAtiva = 'fornecedores';
?>

    <div class="private-layout">

        <?php include '../../includes/sidebar.php'; ?> 

        <!-- CONTEÚDO PRINCIPAL -->
        <main class="private-main">

            <!-- CABEÇALHO -->
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

                <div>

                    <h1 class="fw-bold mb-1">
                        Adicionar Fornecedor
                    </h1>

                    <p class="text-muted mb-0">
                        Registo de novos fornecedores associados aos equipamentos médicos.
                    </p>

                </div>

                <a href="fornecedores.php" class="btn btn-outline-secondary">

                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Voltar

                </a>

            </div>


            <!-- FORMULÁRIO -->
            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body p-4 p-lg-5">

                    <form>

                        <!-- DADOS PRINCIPAIS -->
                        <h4 class="fw-bold mb-4">

                            <i class="fa-solid fa-building me-2 text-primary"></i>
                            Dados do fornecedor

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

                                <label for="codigoFornecedor" class="form-label fw-bold">
                                    Código
                                </label>

                                <input type="text" id="codigoFornecedor" class="form-control"
                                    placeholder="Ex: FOR-0005">

                            </div>

                            <!-- Nome -->
                            <div class="col-md-8">

                                <label for="nomeFornecedor" class="form-label fw-bold">
                                    Nome da empresa
                                </label>

                                <input type="text" id="nomeFornecedor" class="form-control" placeholder="Ex: Dräger">

                            </div>

                            <!-- Tipo -->
                            <div class="col-md-6">

                                <label for="tipoFornecedor" class="form-label fw-bold">
                                    Tipo de fornecedor
                                </label>

                                <select id="tipoFornecedor" class="form-select">

                                    <option selected disabled>
                                        Selecionar tipo
                                    </option>

                                    <option>
                                        Fabricante
                                    </option>

                                    <option>
                                        Distribuidor / Fornecedor Comercial
                                    </option>

                                    <option>
                                        Assistência Técnica
                                    </option>

                                    <option>
                                        Consumíveis / Acessórios
                                    </option>

                                </select>

                            </div>

                            <!-- NIF -->
                            <div class="col-md-4">

                                <label for="nifFornecedor" class="form-label fw-bold">
                                    NIF
                                </label>

                                <input type="text" id="nifFornecedor" class="form-control" placeholder="Ex: 509123456"
                                     inputmode="numeric">

                            </div>

                            <!-- Telefone -->
                            <div class="col-md-4">

                                <label for="telefoneFornecedor" class="form-label fw-bold">
                                    Contacto telefónico
                                </label>

                                <input type="text" id="telefoneFornecedor" class="form-control"
                                    placeholder="Ex: +351 220 000 000"  inputmode="tel">

                            </div>

                            <!-- Email -->
                            <div class="col-md-4">

                                <label for="emailFornecedor" class="form-label fw-bold">
                                    Email
                                </label>

                                <input type="email" id="emailFornecedor" class="form-control"
                                    placeholder="Ex: geral@empresa.pt">

                            </div>

                            <!-- Morada -->
                            <div class="col-md-8">

                                <label for="moradaFornecedor" class="form-label fw-bold">
                                    Morada
                                </label>

                                <input type="text" id="moradaFornecedor" class="form-control"
                                    placeholder="Ex: Rua da Tecnologia, Porto">

                            </div>

                            <!-- Website -->
                            <div class="col-md-4">

                                <label for="websiteFornecedor" class="form-label fw-bold">
                                    Website
                                </label>

                                <input type="url" id="websiteFornecedor" class="form-control"
                                    placeholder="Ex: www.empresa.pt">

                            </div>

                        </div>


                        <!-- CONTACTO RESPONSÁVEL -->
                        <hr class="my-5">

                        <h4 class="fw-bold mb-4">

                            <i class="fa-solid fa-user me-2 text-primary"></i>
                            Pessoa de contacto

                        </h4>

                        <div class="row g-4">

                            <!-- Nome -->
                            <div class="col-md-6">

                                <label for="pessoaContacto" class="form-label fw-bold">
                                    Nome da pessoa de contacto
                                </label>

                                <input type="text" id="pessoaContacto" class="form-control"
                                    placeholder="Ex: João Silva">

                            </div>

                            <!-- Telefone -->
                            <div class="col-md-6">

                                <label for="telefoneContacto" class="form-label fw-bold">
                                    Telefone da pessoa de contacto
                                </label>

                                <input type="text" id="telefoneContacto" class="form-control"
                                    placeholder="Ex: +351 912 000 000">

                            </div>

                        </div>

                        <!-- EQUIPAMENTOS ASSOCIADOS -->
                        <hr class="my-5">

                        <div class="row g-4">

                            <div class="col-12">

                                <h4 class="fw-bold mb-4">
                                    <i class="fa-solid fa-laptop-medical text-primary me-3"></i>
                                    Selecionar equipamentos associados
                                </h4>

                                <div class="row g-3 align-items-end">

                                    <!-- Código do equipamento -->
                                    <div class="col-md-8">

                                        <label for="equipamentoAssociado" class="form-label fw-bold">

                                            Código do equipamento

                                        </label>

                                        <input type="text" id="equipamentoAssociado" class="form-control"
                                            placeholder="Ex: EQ-0001">

                                    </div>

                                    <!-- Botão adicionar -->
                                    <div class="col-md-4">

                                        <button type="button" class="btn btn-outline-primary w-100"
                                            id="btnAssociarEquipamento">

                                            <i class="fa-solid fa-plus me-2"></i>
                                            Associar

                                        </button>

                                    </div>

                                </div>

                                <!-- Equipamentos associados -->
                                <div id="listaEquipamentosAssociados" class="mt-4 d-flex flex-wrap gap-2">

                                </div>
                            </div>

                        </div>


                        <!-- OBSERVAÇÕES -->
                        <hr class="my-5">

                        <h4 class="fw-bold mb-4">

                            <i class="fa-solid fa-note-sticky me-2 text-primary"></i>
                            Observações

                        </h4>

                        <div class="mb-4">

                            <textarea class="form-control" rows="5"
                                placeholder="Observações adicionais sobre o fornecedor..."></textarea>

                        </div>


                        <!-- BOTÕES -->
                        <div class="d-flex justify-content-end gap-3 mt-5">

                            <a href="fornecedores.php" class="btn btn-outline-secondary">

                                Cancelar

                            </a>

                            <button type="submit" class="btn btn-primary-custom">

                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Guardar Fornecedor

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </main>
    </div>
    <?php include '../../includes/footer.php'; ?> 