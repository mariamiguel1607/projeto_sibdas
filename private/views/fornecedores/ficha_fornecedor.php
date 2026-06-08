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
$paginaAtiva = 'fornecedores';
?>

    <div class="private-layout">
       <?php include '../../includes/sidebar.php'; ?> 

        <!-- Conteudo Principal -->
        <main class="private-main">

            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

                <div>
                    <div class="d-flex align-items-center gap-3">
                        <h1 class="fw-bold mb-1">Dräger Portugal Lda.</h1>
                        <span
                            class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill">FOR-0005</span>
                    </div>
                    <p class="text-muted mb-0">
                        Perfil detalhado do fornecedor hospitalar e dispositivos médicos associados.
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="fornecedores.php" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left me-2"></i>
                        Voltar
                    </a>
                    <a href="editar_fornecedor.php" class="btn btn-primary-custom">
                        <i class="fa-solid fa-pen-to-square me-2"></i>
                        Editar Dados
                    </a>
                </div>

            </div>

            <div class="row g-4">

                <div class="col-lg-8">

                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4 text-primary">
                                <i class="fa-solid fa-building me-2"></i>Informação Institucional
                            </h5>

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <span class="text-muted d-block small">Tipo de Fornecedor</span>
                                    <span class="fw-bold text-dark">Fabricante</span>
                                </div>

                                <div class="col-sm-6">
                                    <span class="text-muted d-block small">NIF (Número de Identificação Fiscal)</span>
                                    <span class="fw-bold text-dark">509123456</span>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block small">Website</span>
                                    <a href="https://www.draeger.com" target="_blank"
                                        class="text-decoration-none fw-bold">
                                        www.draeger.com <i
                                            class="fa-solid fa-arrow-up-right-from-square ms-1 small"></i>
                                    </a>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted d-block small">Morada Principal</span>
                                    <span class="text-dark">Avenida da Tecnologia, n.º 45, 4000-000 Porto</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4 text-primary">
                                <i class="fa-solid fa-screwdriver-wrench me-2"></i>Equipamentos sob a Responsabilidade
                                deste Fornecedor
                            </h5>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle border-start-0 border-end-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Cód. Inventário</th>
                                            <th>Designação</th>
                                            <th>Marca / Modelo</th>
                                            <th>Criticidade</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="fw-bold">EQ-0001</td>
                                            <td>Ventilador Pulmonar</td>
                                            <td>Dräger / Evita V800</td>
                                            <td>
                                                <span
                                                    class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill">Suporte
                                                    de Vida</span>
                                            </td>
                                            <td>
                                                <a href="../equipamentos/ficha_equipamento.php"
                                                    class="btn btn-sm btn-outline-primary py-1">
                                                    <i class="fa-solid fa-eye"></i> Ver Ficha
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">EQ-0024</td>
                                            <td>Monitor Multiparamétrico</td>
                                            <td>Dräger / Vista 120</td>
                                            <td>
                                                <span
                                                    class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill text-dark">Média</span>
                                            </td>
                                            <td>
                                                <a href="../equipamentos/ficha_equipamento.php"
                                                    class="btn btn-sm btn-outline-primary py-1">
                                                    <i class="fa-solid fa-eye"></i> Ver Ficha
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fa-solid fa-info-circle me-1"></i> Esta listagem mostra os equipamentos
                                associados na base de dados relacional.
                            </small>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4">

                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4 text-primary">
                                <i class="fa-solid fa-address-book me-2"></i>Canais de Contacto
                            </h5>

                            <div class="mb-4">
                                <span class="text-muted d-block small mb-1"><i
                                        class="fa-solid fa-phone me-2 text-secondary"></i>Telefone Geral</span>
                                <span class="fw-bold text-dark">+351 220 123 456</span>
                            </div>

                            <div class="mb-4">
                                <span class="text-muted d-block small mb-1"><i
                                        class="fa-solid fa-envelope me-2 text-secondary"></i>Email Geral</span>
                                <span class="fw-bold text-dark">contacto@draeger.pt</span>
                            </div>

                            <hr class="my-3">

                            <div class="bg-light p-3 rounded-3 mt-3">
                                <h6 class="fw-bold mb-3 text-dark">
                                    <i class="fa-solid fa-user-tie me-2 text-primary"></i>Pessoa de Contacto
                                </h6>
                                <div class="mb-2">
                                    <span class="text-muted d-block small">Nome Comercial</span>
                                    <span class="fw-bold text-dark">Eng. Carlos Silva</span>
                                </div>
                                <div>
                                    <span class="text-muted d-block small">Telemóvel Direto</span>
                                    <span class="fw-bold text-dark">+351 912 345 678</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3 text-primary">
                                <i class="fa-solid fa-note-sticky me-2"></i>Observações Internas
                            </h5>
                            <p
                                class="text-dark bg-warning bg-opacity-10 border border-warning border-opacity-25 p-3 rounded-3 mb-0 small lh-base">
                                Contrato de manutenção preventiva ativo válido até Dezembro de 2026. Tempo de resposta
                                premium acordado em menos de 24 horas para equipamentos críticos da UCI e Bloco
                                Operatório.
                            </p>
                        </div>
                    </div>

                </div>

            </div>

        </main>
    </div>
    <?php include '../../includes/footer.php'; ?> 