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

        <!-- SIDEBAR -->
       <?php include '../../includes/sidebar.php'; ?> 

        <main class="private-main">
            <!-- CABEÇALHO -->
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

                <div>
                    <h1 class="fw-bold mb-1">Editar Serviços Públicos</h1>
                    <p class="text-muted mb-0">
                        Gestão dos serviços apresentados na área pública do website.
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="gestao_conteudos.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Voltar
                    </a>

                    <a href="../../../public/index.php#servicos" class="btn btn-outline-primary">
                        <i class="fa-solid fa-eye me-2"></i>
                        Ver na Página Pública
                    </a>
                </div>

            </div>

            <!-- CARD PRINCIPAL -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">

                    <div class="mb-4">
                        <h4 class="fw-bold mb-1">Serviços existentes</h4>
                        <p class="text-muted mb-0">
                            Edita os títulos, descrições e estado dos serviços visíveis na página pública.
                        </p>
                    </div>

                    <div id="listaServicosGestao">
                        <!-- Os serviços existentes vão aparecer aqui pelo JavaScript -->
                    </div>

                    <!-- ADICIONAR NOVO SERVIÇO -->
                    <div class="card border-0 shadow-sm rounded-4 mt-4">
                        <div class="card-body p-4">

                            <div
                                class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
                                <div>
                                    <h4 class="fw-bold mb-1">Adicionar Novo Serviço</h4>
                                    <p class="text-muted mb-0">
                                        Permite criar um novo serviço para apresentar na área pública do website.
                                    </p>
                                </div>

                                <span class="badge text-bg-secondary">
                                    Novo
                                </span>
                            </div>

                            <div class="row g-4">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Título do serviço</label>
                                    <input type="text" class="form-control" id="novoServicoTitulo"
                                        placeholder="Ex.: Manutenção Preventiva">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Ícone</label>
                                    <select class="form-select" id="novoServicoIcone">
                                        <option selected disabled value="">Selecionar ícone</option>
                                        <option value="fa-solid fa-clipboard-list">Clipboard List</option>
                                        <option value="fa-solid fa-folder-open">Folder Open</option>
                                        <option value="fa-solid fa-chart-simple">Chart Simple</option>
                                        <option value="fa-solid fa-briefcase-medical">Briefcase Medical</option>
                                        <option value="fa-solid fa-screwdriver-wrench">Screwdriver Wrench</option>
                                        <option value="fa-solid fa-file-lines">File Lines</option>
                                        <option value="fa-solid fa-hospital">Hospital</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Descrição do serviço</label>
                                    <textarea class="form-control" id="novoServicoDescricao" rows="3"
                                        placeholder="Escreve uma breve descrição do serviço que será apresentado na página pública."></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Estado</label>
                                    <select class="form-select" id="novoServicoEstado">
                                        <option selected>Ativo</option>
                                        <option>Inativo</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Ordem de apresentação</label>
                                    <input type="number" class="form-control" id="novoServicoOrdem"
                                        placeholder="Ex.: 4">
                                </div>

                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">

                                <button type="button" class="btn btn-outline-secondary" id="btnLimparServico">
                                    Limpar campos
                                </button>

                                <button type="button" class="btn btn-primary-custom" id="btnAdicionarServico">
                                    <i class="fa-solid fa-plus me-2"></i>
                                    Adicionar Serviço
                                </button>

                            </div>

                        </div>
                    </div>
                    <!-- GUARDAR ALTERAÇÕES -->
                    <div class="d-flex justify-content-center mt-4">
                        <button type="button" class="btn btn-primary-custom" id="btnGuardarServicos">
                            <i class="fa-solid fa-floppy-disk me-2"></i>
                            Guardar Alterações
                        </button>
                    </div>

                </div>
            </div>

        </main>
    </div>

    <?php include '../../includes/footer.php'; ?> 