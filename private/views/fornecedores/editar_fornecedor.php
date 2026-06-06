<?php include '../../includes/header.php'; 
$paginaAtiva = 'fornecedores';
?>

    <div class="private-layout">
        <?php include '../../includes/sidebar.php'; ?> 
        <!-- Conteudo Principal -->
        <main class="private-main">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

                <div>
                    <h1 class="fw-bold mb-1">
                        Editar Fornecedor
                    </h1>
                    <p class="text-muted mb-0">
                        Modificar os dados do fornecedor e atualizar as associações de equipamentos médicos.
                    </p>
                </div>

                <a href="fornecedores.php" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Voltar
                </a>

            </div>


            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body p-4 p-lg-5">

                    <form>

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

                            <div class="col-md-4">
                                <label for="codigoFornecedor" class="form-label fw-bold">
                                    Código
                                </label>
                                <input type="text" id="codigoFornecedor" class="form-control" value="FOR-0005" disabled>
                                <small class="text-muted">O código não pode ser alterado.</small>
                            </div>

                            <div class="col-md-8">
                                <label for="nomeFornecedor" class="form-label fw-bold">
                                    Nome da empresa
                                </label>
                                <input type="text" id="nomeFornecedor" class="form-control"
                                    value="Dräger Portugal Lda.">
                            </div>

                            <div class="col-md-6">
                                <label for="tipoFornecedor" class="form-label fw-bold">
                                    Tipo de fornecedor
                                </label>
                                <select id="tipoFornecedor" class="form-select">
                                    <option disabled>Selecionar tipo</option>
                                    <option selected>Fabricante</option>
                                    <option>Distribuidor / Fornecedor Comercial</option>
                                    <option>Assistência Técnica</option>
                                    <option>Consumíveis / Acessórios</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="nifFornecedor" class="form-label fw-bold">
                                    NIF
                                </label>
                                <input type="text" id="nifFornecedor" class="form-control" value="509123456"
                                   inputmode="numeric">
                            </div>

                            <div class="col-md-4">
                                <label for="telefoneFornecedor" class="form-label fw-bold">
                                    Contacto telefónico
                                </label>
                                <input type="text" id="telefoneFornecedor" class="form-control" value="+351 220 123 456"
                                    inputmode="tel">
                            </div>

                            <div class="col-md-4">
                                <label for="emailFornecedor" class="form-label fw-bold">
                                    Email
                                </label>
                                <input type="email" id="emailFornecedor" class="form-control"
                                    value="contacto@draeger.pt">
                            </div>

                            <div class="col-md-8">
                                <label for="moradaFornecedor" class="form-label fw-bold">
                                    Morada
                                </label>
                                <input type="text" id="moradaFornecedor" class="form-control"
                                    value="Avenida da Tecnologia, n.º 45, 4000-000 Porto">
                            </div>

                            <div class="col-md-4">
                                <label for="websiteFornecedor" class="form-label fw-bold">
                                    Website
                                </label>
                                <input type="url" id="websiteFornecedor" class="form-control"
                                    value="https://www.draeger.com">
                            </div>

                        </div>


                        <hr class="my-5">

                        <h4 class="fw-bold mb-4">
                            <i class="fa-solid fa-user me-2 text-primary"></i>
                            Pessoa de contacto
                        </h4>

                        <div class="row g-4">

                            <div class="col-md-6">
                                <label for="pessoaContacto" class="form-label fw-bold">
                                    Nome da pessoa de contacto
                                </label>
                                <input type="text" id="pessoaContacto" class="form-control" value="Eng. Carlos Silva">
                            </div>

                            <div class="col-md-6">
                                <label for="telefoneContacto" class="form-label fw-bold">
                                    Telefone da pessoa de contacto
                                </label>
                                <input type="text" id="telefoneContacto" class="form-control" value="+351 912 345 678">
                            </div>

                        </div>

                        <hr class="my-5">

                        <div class="row g-4">

                            <div class="col-12">

                                <h4 class="fw-bold mb-4">
                                    <i class="fa-solid fa-laptop-medical text-primary me-3"></i>
                                    Selecionar equipamentos associados
                                </h4>

                                <div class="row g-3 align-items-end">

                                    <div class="col-md-8">
                                        <label for="equipamentoAssociado" class="form-label fw-bold">
                                            Código do equipamento
                                        </label>
                                        <input type="text" id="equipamentoAssociado" class="form-control"
                                            placeholder="Ex: EQ-0001">
                                    </div>

                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-outline-primary w-100"
                                            id="btnAssociarEquipamento">
                                            <i class="fa-solid fa-plus me-2"></i>
                                            Associar
                                        </button>
                                    </div>

                                </div>

                                <div id="listaEquipamentosAssociados" class="mt-4 d-flex flex-wrap gap-2">
                                    <span class="badge bg-light text-dark border p-2 d-flex align-items-center gap-2">
                                        EQ-0001 - Ventilador Pulmonar
                                        <button type="button" class="btn-close" style="font-size: 0.65rem;"
                                            aria-label="Remover"></button>
                                    </span>
                                    <span class="badge bg-light text-dark border p-2 d-flex align-items-center gap-2">
                                        EQ-0024 - Monitor Multiparamétrico
                                        <button type="button" class="btn-close" style="font-size: 0.65rem;"
                                            aria-label="Remover"></button>
                                    </span>
                                </div>
                            </div>

                        </div>


                        <hr class="my-5">

                        <h4 class="fw-bold mb-4">
                            <i class="fa-solid fa-note-sticky me-2 text-primary"></i>
                            Observações
                        </h4>

                        <div class="mb-4">
                            <textarea class="form-control" rows="5"
                                placeholder="Observações adicionais sobre o fornecedor...">Contrato de manutenção preventiva ativo válido até Dezembro de 2026. Tempo de resposta premium em menos de 24 horas.</textarea>
                        </div>


                        <div class="d-flex justify-content-end gap-3 mt-5">

                            <a href="fornecedores.php" class="btn btn-outline-secondary">
                                Cancelar
                            </a>

                            <button type="submit" class="btn btn-primary-custom">
                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Atualizar Fornecedor
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </main>
    </div>
    <?php include '../../includes/footer.php'; ?> 