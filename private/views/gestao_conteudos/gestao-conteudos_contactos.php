<?php include '../../includes/header.php'; 
$paginaAtiva = 'gestao_conteudos';
?>
    <div class="private-layout">

        <?php include '../../includes/sidebar.php'; ?> 

        <!-- CONTEÚDO PRINCIPAL -->
        <main class="private-main">

            <!-- Cabeçalho -->
            <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                <div>
                    <h1 class="fw-bold mb-2">Editar Contactos</h1>
                    <p class="text-muted mb-0">
                        Gestão dos contactos apresentados na área pública do website.
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="gestao_conteudos.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Voltar
                    </a>

                    <a href="../../../public/index.php#contactos" class="btn btn-outline-primary">
                        <i class="fa-solid fa-eye me-2"></i>
                        Ver na Página Pública
                    </a>
                </div>
            </div>

            <!-- Card principal -->
            <section class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">

                    <div class="mb-4">
                        <h2 class="fw-bold mb-2">Informações de contacto</h2>
                        <p class="text-muted mb-0">
                            Edita os dados de contacto, localização e horário apresentados ao público.
                        </p>
                    </div>

                    <div class="row g-4">

                        <!-- Título da secção -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Título da secção</label>
                            <input type="text" class="form-control" id="contactosTitulo" placeholder="Ex.: Contactos">
                        </div>

                        <!-- Texto introdutório -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Texto introdutório</label>
                            <textarea class="form-control" id="contactosTexto" rows="3"
                                placeholder="Ex.: Entre em contacto connosco para obter mais informações sobre os nossos serviços."></textarea>
                        </div>

                        <!-- Título do formulário -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Título do formulário</label>
                            <input type="text" class="form-control" id="contactosFormularioTitulo"
                                placeholder="Ex.: Envie-nos uma mensagem">
                        </div>

                        <!-- Texto do botão -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Texto do botão</label>
                            <input type="text" class="form-control" id="contactosBotaoTexto"
                                placeholder="Ex.: Enviar Mensagem">
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado da secção</label>
                            <select class="form-select" id="contactosEstado">
                                <option value="Ativo">Ativo</option>
                                <option value="Inativo">Inativo</option>
                            </select>
                        </div>

                    </div>

                    <!-- Botão guardar -->
                    <div class="d-flex justify-content-center mt-5">
                        <button type="button" class="btn btn-primary-custom px-5" id="btnGuardarContactos">
                            <i class="fa-solid fa-floppy-disk me-2"></i>
                            Guardar Alterações
                        </button>
                    </div>

                </div>
            </section>

        </main>

    </div>

    <?php include '../../includes/footer.php'; ?> 