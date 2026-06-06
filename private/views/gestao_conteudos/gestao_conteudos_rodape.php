<?php include '../../includes/header.php'; 
$paginaAtiva = 'gestao_conteudos';
?>

    <div class="private-layout">

        <?php include '../../includes/sidebar.php'; ?> 

        <main class="private-main">

            <div class="content-card">

                <!-- Cabeçalho -->
                <div class="d-flex justify-content-between align-items-start mb-4">

                    <div>

                        <h1 class="section-title-private mb-2">
                            Gestão da Informação do Rodapé
                        </h1>

                        <p class="section-subtitle-private mb-0">
                            Edita o conteúdo apresentado no rodapé da página pública.
                        </p>

                    </div>
                    <div class="d-flex gap-2">

                        <a href="../gestao_conteudos/gestao_conteudos.php" class="btn btn-outline-secondary">

                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Voltar
                        </a>

                        <a href="../../../public/index.php#footer" class="btn btn-outline-primary">

                            <i class="fa-solid fa-eye me-2"></i>
                            Ver na Página Pública
                        </a>

                    </div>

                </div>

                <!-- Conteúdo -->
                <div class="row g-4">

                    <!-- Logo -->
                    <div class="col-12">

                        <label class="form-label fw-semibold">
                            Logo do rodapé
                        </label>

                        <input type="file" class="form-control" id="rodapeLogoInput" accept="image/*">

                    </div>


                    <!-- Texto -->
                    <div class="col-12">

                        <label class="form-label fw-semibold">
                            Texto descritivo
                        </label>

                        <textarea class="form-control" rows="4" id="rodapeTexto"></textarea>

                    </div>


                    <!-- Localização -->
                    <div class="col-12">

                        <label class="form-label fw-semibold">
                            Localização
                        </label>

                        <input type="text" class="form-control" id="rodapeLocalizacao">

                    </div>


                    <!-- Horário -->
                    <div class="col-12">

                        <label class="form-label fw-semibold">
                            Horário
                        </label>

                        <input type="text" class="form-control" id="rodapeHorario">

                    </div>


                    <!-- Contactos -->
                    <div class="col-12">

                        <label class="form-label fw-semibold">
                            Contactos
                        </label>

                        <input type="text" class="form-control" id="rodapeContactos">

                    </div>
                </div>
                <!-- Botão Guardar -->
                <div class="d-flex justify-content-center mt-5">

                    <button type="button" class="btn btn-primary-custom" id="btnGuardarRodape">

                        <i class="fa-solid fa-floppy-disk me-2"></i>
                        Guardar Alterações

                    </button>

                </div>
            </div>

        </main>
    </div>
   <?php include '../../includes/footer.php'; ?> 
