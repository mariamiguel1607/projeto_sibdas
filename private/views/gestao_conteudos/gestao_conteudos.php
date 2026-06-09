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


    <main class="private-main">
        <!-- CABEÇALHO -->
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

            <div>
                <h1 class="fw-bold mb-1">Gestão de Conteúdos Públicos</h1>
                <p class="text-muted mb-0">
                    Atualização dos textos, contactos e informações apresentados na área pública do website.
                </p>
            </div>

            <a href="../../../public/index.php" class="btn btn-outline-secondary">
                <i class="fa-solid fa-eye me-2"></i>
                Ver Página Pública
            </a>

        </div>

        <!-- CARD PRINCIPAL -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <!-- TABS / LINKS DE GESTÃO -->
                <ul class="nav nav-pills conteudos-tabs mb-4" id="conteudosTabs" role="tablist">

                    <!-- INÍCIO - fica na própria página -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="inicio-tab" data-bs-toggle="pill"
                            data-bs-target="#inicio" type="button" role="tab" aria-controls="inicio"
                            aria-selected="true">
                            <i class="fa-solid fa-house me-2"></i>
                            Início
                        </button>
                    </li>

                    <!-- SERVIÇOS - abre outra página -->
                    <li class="nav-item">
                        <a class="nav-link" href="gestao_conteudos_servicos.php">
                            <i class="fa-solid fa-briefcase-medical me-2"></i>
                            Serviços
                        </a>
                    </li>

                    <!-- SOBRE NÓS - abre outra página -->
                    <li class="nav-item">
                        <a class="nav-link" href="gestao_conteudos_sobre.php">
                            <i class="fa-solid fa-users me-2"></i>
                            Sobre Nós
                        </a>
                    </li>

                    <!-- CONTACTOS - abre outra página -->
                    <li class="nav-item">
                        <a class="nav-link" href="gestao-conteudos_contactos.php">
                            <i class="fa-solid fa-address-book me-2"></i>
                            Contactos
                        </a>
                    </li>

                    <!-- FAQ - abre outra página -->
                    <li class="nav-item">
                        <a class="nav-link" href="gestão_conteudos_faq.php">
                            <i class="fa-solid fa-circle-question me-2"></i>
                            FAQ
                        </a>
                    </li>

                    <!-- RODAPÉ - abre outra página -->
                    <li class="nav-item">
                        <a class="nav-link" href="gestao_conteudos_rodape.php">
                            <i class="fa-solid fa-window-minimize me-2"></i>
                            Rodapé
                        </a>
                    </li>

                </ul>

                <!-- CONTEÚDO DAS TABS -->
                <div class="tab-content" id="conteudosTabsContent">

                    <!-- TAB INÍCIO -->
                    <div class="tab-pane fade show active" id="inicio" role="tabpanel" aria-labelledby="inicio-tab">

                        <h4 class="fw-bold mb-4">
                            Secção Inicial
                        </h4>

                        <div class="row g-4">

                            <!-- TÍTULO -->
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Título principal
                                </label>

                                <input type="text" id="inicioTitulo" class="form-control">

                            </div>

                            <!-- BOTÃO -->
                            <!-- BOTÃO -->
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Texto do botão
                                </label>

                                <input type="text" id="inicioBotao" class="form-control">

                            </div>

                            <!-- LINK DO BOTÃO -->
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Link do botão
                                </label>

                                <input type="url" id="inicioBotaoLink" class="form-control"
                                    placeholder="Ex: #servicos">

                            </div>
                            <!-- DESCRIÇÃO -->
                            <div class="col-12">

                                <label class="form-label fw-semibold">
                                    Descrição
                                </label>

                                <textarea id="inicioDescricao" class="form-control" rows="4"></textarea>

                            </div>

                            <!-- IMAGEM -->
                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Imagem principal
                                </label>

                                <input type="file" id="inicioImagem" class="form-control">

                            </div>

                        </div>

                        <!-- BOTÃO GUARDAR -->
                        <div class="d-flex justify-content-end mt-4">

                            <button type="button" id="btnGuardarInicio" class="btn btn-primary-custom">

                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Guardar alterações

                            </button>

                        </div>

                    </div>

                    <!-- TAB SERVIÇOS -->
                    <div class="tab-pane fade" id="servicos" role="tabpanel" aria-labelledby="servicos-tab">

                        <h4 class="fw-bold mb-4">Serviços Apresentados</h4>

                        <div class="row g-4">

                            <div class="col-lg-4">
                                <div class="card border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-body">
                                        <label class="form-label fw-semibold">Serviço 1</label>
                                        <input type="text" class="form-control mb-3" value="Gestão de Inventário">

                                        <label class="form-label fw-semibold">Descrição</label>
                                        <textarea class="form-control"
                                            rows="4">Registo e organização de equipamentos médicos por categoria, estado, localização e criticidade.</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="card border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-body">
                                        <label class="form-label fw-semibold">Serviço 2</label>
                                        <input type="text" class="form-control mb-3" value="Gestão Documental">

                                        <label class="form-label fw-semibold">Descrição</label>
                                        <textarea class="form-control"
                                            rows="4">Associação de manuais, certificados, contratos, relatórios técnicos e outros documentos aos equipamentos.</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="card border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-body">
                                        <label class="form-label fw-semibold">Serviço 3</label>
                                        <input type="text" class="form-control mb-3" value="Dashboard e Consulta">

                                        <label class="form-label fw-semibold">Descrição</label>
                                        <textarea class="form-control"
                                            rows="4">Visualização de indicadores, pesquisa e filtragem de equipamentos para apoiar a gestão hospitalar.</textarea>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="button" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-plus me-2"></i>
                                Adicionar serviço
                            </button>

                            <button type="button" class="btn btn-primary-custom">
                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Guardar alterações
                            </button>
                        </div>

                    </div>

                    <!-- TAB SOBRE NÓS -->
                    <div class="tab-pane fade" id="sobre" role="tabpanel" aria-labelledby="sobre-tab">

                        <h4 class="fw-bold mb-4">Sobre Nós</h4>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Texto principal</label>
                            <textarea class="form-control"
                                rows="5">A TechMed Solutions nasceu com o objetivo de apoiar instituições de saúde na organização e digitalização dos seus processos internos, promovendo uma gestão mais eficiente, segura e acessível da informação associada aos equipamentos médicos.</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Texto secundário</label>
                            <textarea class="form-control"
                                rows="5">Através de uma plataforma simples e intuitiva, procuramos reduzir a dependência de registos manuais, facilitar o trabalho dos profissionais responsáveis pela gestão hospitalar e contribuir para uma maior qualidade e segurança nos serviços de saúde.</textarea>
                        </div>

                        <div class="row g-4">

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Missão</label>
                                <textarea class="form-control"
                                    rows="3">Apoiar hospitais na transição para processos digitais mais organizados, seguros e eficientes.</textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Inovação</label>
                                <textarea class="form-control"
                                    rows="3">Desenvolver soluções tecnológicas simples, intuitivas e adaptadas às necessidades do contexto hospitalar.</textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Impacto</label>
                                <textarea class="form-control"
                                    rows="3">Contribuir para uma gestão hospitalar mais eficaz e para melhores condições de apoio aos profissionais de saúde.</textarea>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary-custom">
                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Guardar alterações
                            </button>
                        </div>

                    </div>

                    <!-- TAB CONTACTOS -->
                    <div class="tab-pane fade" id="contactos" role="tabpanel" aria-labelledby="contactos-tab">

                        <h4 class="fw-bold mb-4">Contactos e Horário</h4>

                        <div class="row g-4">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" value="geral@techmedsolutions.pt">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Telefone</label>
                                <input type="text" class="form-control" value="+351 917 654 321">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Morada</label>
                                <textarea class="form-control"
                                    rows="3">Rua Dr. António Bernardino de Almeida 4249-015 Porto Portugal</textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Horário semanal</label>
                                <input type="text" class="form-control" value="2ª a 6ª Feira: 9h — 18h">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Sábado</label>
                                <input type="text" class="form-control" value="Encerrado">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Domingo</label>
                                <input type="text" class="form-control" value="Encerrado">
                            </div>

                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary-custom">
                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Guardar alterações
                            </button>
                        </div>

                    </div>

                    <!-- TAB FAQ -->
                    <div class="tab-pane fade" id="faq" role="tabpanel" aria-labelledby="faq-tab">

                        <h4 class="fw-bold mb-4">Perguntas Frequentes</h4>

                        <div class="card border-0 shadow-sm rounded-4 mb-3">
                            <div class="card-body">
                                <label class="form-label fw-semibold">Pergunta 1</label>
                                <input type="text" class="form-control mb-3"
                                    value="A plataforma pode ser usada por diferentes serviços hospitalares?">

                                <label class="form-label fw-semibold">Resposta</label>
                                <textarea class="form-control"
                                    rows="3">Sim. A plataforma foi pensada para apoiar diferentes serviços na gestão e consulta de equipamentos médicos.</textarea>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 mb-3">
                            <div class="card-body">
                                <label class="form-label fw-semibold">Pergunta 2</label>
                                <input type="text" class="form-control mb-3"
                                    value="É possível associar documentos aos equipamentos?">

                                <label class="form-label fw-semibold">Resposta</label>
                                <textarea class="form-control"
                                    rows="3">Sim. O sistema permite associar manuais, certificados, contratos e relatórios técnicos aos equipamentos.</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="button" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-plus me-2"></i>
                                Adicionar pergunta
                            </button>

                            <button type="button" class="btn btn-primary-custom">
                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Guardar alterações
                            </button>
                        </div>

                    </div>

                    <!-- TAB RODAPÉ -->
                    <div class="tab-pane fade" id="rodape" role="tabpanel" aria-labelledby="rodape-tab">

                        <h4 class="fw-bold mb-4">Rodapé da Página Pública</h4>

                        <div class="row g-4">

                            <div class="col-12">
                                <label class="form-label fw-semibold">Texto institucional</label>
                                <textarea class="form-control"
                                    rows="3">Soluções digitais para gestão eficiente de equipamentos hospitalares.</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Título da coluna de localização</label>
                                <input type="text" class="form-control" value="LOCALIZAÇÃO">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Título da coluna de contactos</label>
                                <input type="text" class="form-control" value="CONTACTOS">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Título da coluna de horário</label>
                                <input type="text" class="form-control" value="HORÁRIO">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Logo do rodapé</label>
                                <input type="file" class="form-control">
                            </div>

                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-primary-custom">
                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Guardar alterações
                            </button>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </main>
</div>
<?php include '../../includes/footer.php'; ?>