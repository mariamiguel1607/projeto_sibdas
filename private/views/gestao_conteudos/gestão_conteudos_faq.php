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
                            Gestão das FAQs
                        </h1>

                        <p class="section-subtitle-private mb-0">
                            Edita as perguntas frequentes da página pública.
                        </p>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="gestao_conteudos.php" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-2"></i>
                            Voltar
                        </a>

                        <a href="../../../public/index.php#perguntas-frequentes" class="btn btn-outline-primary">
                            <i class="fa-solid fa-eye me-2"></i>
                            Ver na Página Pública
                        </a>
                    </div>

                </div>

                <!-- Lista dinâmica -->
                <div id="listaFaqGestao" class="mx-auto w-100"></div>

                <!-- Botões -->
                <div class="d-flex justify-content-center gap-3 mt-4">

                    <button type="button" class="btn btn-outline-secondary" id="btnAdicionarFaq">

                        <i class="fa-solid fa-plus me-2"></i>
                        Adicionar FAQ
                    </button>

                    <button type="button" class="btn btn-primary-custom" id="btnGuardarFaq">

                        <i class="fa-solid fa-floppy-disk me-2"></i>
                        Guardar Alterações
                    </button>

                </div>

            </div>
        </main>
    </div>
<?php include '../../includes/footer.php'; ?> 