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
$paginaAtiva = 'gestao_conteudos';
?>

    <div class="private-layout">

        <?php include '../../includes/sidebar.php'; ?> 

        <!-- CONTEÚDO PRINCIPAL -->
        <main class="private-main">

            <!-- Cabeçalho da página -->
            <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                <div>
                    <h1 class="fw-bold mb-2">Editar Secção Sobre Nós</h1>
                    <p class="text-muted mb-0">
                        Gestão dos textos institucionais apresentados na área pública do website.
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="gestao_conteudos.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Voltar
                    </a>

                    <a href="../../../public/index.php#sobre" class="btn btn-outline-primary">
                        <i class="fa-solid fa-eye me-2"></i>
                        Ver na Página Pública
                    </a>
                </div>
            </div>

            <!-- CARD DE EDIÇÃO -->
            <section class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-lg-5">

                    <div class="mb-4">
                        <h2 class="fw-bold mb-2">Conteúdo da secção</h2>
                        <p class="text-muted mb-0">
                            Edita o texto apresentado na secção “Sobre Nós” da página pública.
                        </p>
                    </div>

                    <div class="row g-4">

                        <!-- Título -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Título da secção</label>
                            <input type="text" class="form-control" id="sobreTitulo" placeholder="Ex.: Sobre Nós">
                        </div>

                        <!-- Texto principal -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Texto principal</label>
                            <textarea class="form-control" id="sobreTexto" rows="5"
                                placeholder="Escreve o texto principal da secção Sobre Nós."></textarea>
                        </div>

                        <!-- Bloco 1 -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Título do bloco 1</label>
                            <input type="text" class="form-control mb-3" id="sobreBloco1Titulo"
                                placeholder="Ex.: Missão">

                            <label class="form-label fw-semibold">Texto do bloco 1</label>
                            <textarea class="form-control" id="sobreBloco1Texto" rows="4"
                                placeholder="Ex.: Apoiar instituições de saúde na gestão digital dos equipamentos médicos."></textarea>
                        </div>

                        <!-- Bloco 2 -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Título do bloco 2</label>
                            <input type="text" class="form-control mb-3" id="sobreBloco2Titulo"
                                placeholder="Ex.: Visão">

                            <label class="form-label fw-semibold">Texto do bloco 2</label>
                            <textarea class="form-control" id="sobreBloco2Texto" rows="4"
                                placeholder="Ex.: Tornar a gestão hospitalar mais organizada, segura e eficiente."></textarea>
                        </div>

                        <!-- Bloco 3 -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Título do bloco 3</label>
                            <input type="text" class="form-control mb-3" id="sobreBloco3Titulo"
                                placeholder="Ex.: Valores">

                            <label class="form-label fw-semibold">Texto do bloco 3</label>
                            <textarea class="form-control" id="sobreBloco3Texto" rows="4"
                                placeholder="Ex.: Segurança, organização, confiança, inovação e eficiência."></textarea>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado da secção</label>
                            <select class="form-select" id="sobreEstado">
                                <option value="Ativo">Ativo</option>
                                <option value="Inativo">Inativo</option>
                            </select>
                        </div>

                    </div>

                    <!-- Botão guardar -->
                    <div class="d-flex justify-content-center mt-5">
                        <button type="button" class="btn btn-primary-custom px-5" id="btnGuardarSobre">
                            <i class="fa-solid fa-floppy-disk me-2"></i>
                            Guardar Alterações
                        </button>
                    </div>

                </div>
            </section>

        </main>
    </div>
   <?php include '../../includes/footer.php'; ?> 