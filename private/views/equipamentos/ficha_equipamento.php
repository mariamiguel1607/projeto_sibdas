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
$paginaAtiva = 'equipamentos';
?>

<div class="private-layout">

    <?php include '../../includes/sidebar.php'; ?>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="private-main">

        <!-- CABEÇALHO -->
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

            <div>
                <h1 class="fw-bold mb-1">Ficha do Equipamento</h1>
                <p class="text-muted mb-0">
                    Consulta detalhada da informação técnica e administrativa do equipamento.
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="equipamentos.php" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Voltar
                </a>

                <a href="editar_equipamentos.php" class="btn btn-primary-custom">
                    <i class="fa-solid fa-pen-to-square me-2"></i>
                    Editar
                </a>
            </div>

        </div>

        <!-- RESUMO DO EQUIPAMENTO -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">

            <div class="card-body p-4">

                <div class="row align-items-center">

                    <div class="col-lg-8">

                        <h3 class="fw-bold mb-1">
                            Ventilador Pulmonar
                        </h3>

                        <p class="text-muted mb-0">
                            Código: EQ-0001
                        </p>

                    </div>

                    <div class="col-lg-4">

                        <div class="d-flex gap-2 justify-content-lg-end mt-3 mt-lg-0">

                            <span class="badge bg-success">
                                Ativo
                            </span>

                            <span class="badge bg-danger">
                                Crítico
                            </span>

                            <span class="badge bg-primary">
                                UCI
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- TABS -->
        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body p-4">

                <ul class="nav nav-pills conteudos-tabs mb-4">

                    <li class="nav-item">
                        <button type="button" class="nav-link active" data-bs-toggle="pill"
                            data-bs-target="#dadosGerais">
                            Dados Gerais
                        </button>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="nav-link" data-bs-toggle="pill" data-bs-target="#aquisicao">
                            Aquisição
                        </button>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="nav-link" data-bs-toggle="pill" data-bs-target="#acessorios">
                            Acessórios e Consumíveis
                        </button>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="nav-link" data-bs-toggle="pill" data-bs-target="#localizacao">
                            Localização
                        </button>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="nav-link" data-bs-toggle="pill" data-bs-target="#fornecedor">
                            Fornecedor Associado
                        </button>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="nav-link" data-bs-toggle="pill" data-bs-target="#garantias">
                            Garantias e Contratos
                        </button>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="nav-link" data-bs-toggle="pill" data-bs-target="#documentacao">
                            Documentação
                        </button>
                    </li>

                    <li class="nav-item">
                        <button type="button" class="nav-link" data-bs-toggle="pill" data-bs-target="#observacoes">
                            Observações
                        </button>
                    </li>

                </ul>

                <div class="tab-content">

                    <!-- DADOS GERAIS -->
                    <div class="tab-pane fade show active" id="dadosGerais">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Código Interno</p>
                                <p class="fw-semibold mb-0">EQ-0001</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Designação</p>
                                <p class="fw-semibold mb-0">Ventilador Pulmonar</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Categoria</p>
                                <p class="fw-semibold mb-0">Suporte de Vida</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Fabricante</p>
                                <p class="fw-semibold mb-0">Philips</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Marca</p>
                                <p class="fw-semibold mb-0">Philips</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Modelo</p>
                                <p class="fw-semibold mb-0">VX-200</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Número de Série</p>
                                <p class="fw-semibold mb-0">PH-VX200-2021-001</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Ano de Fabrico</p>
                                <p class="fw-semibold mb-0">2021</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Criticidade</p>
                                <p class="fw-semibold mb-0">Suporte de Vida</p>
                            </div>

                        </div>

                        <hr class="my-4">

                        <div class="accordion" id="accordionDocumentacaoTecnica">

                            <div class="accordion-item border rounded-3">

                                <h2 class="accordion-header">

                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseDocumentacaoTecnica">

                                        Documentação Técnica

                                    </button>

                                </h2>

                                <div id="collapseDocumentacaoTecnica" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionDocumentacaoTecnica">

                                    <div class="accordion-body">

                                        <div class="d-flex flex-column gap-2">

                                            <!-- Manual de Utilização -->

                                            <div class="border rounded-3 p-3">

                                                <div class="d-flex align-items-center justify-content-between">

                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Manual de Utilização
                                                    </span>

                                                    <a href="#" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">

                                                        <i class="fa-solid fa-eye me-1"></i>
                                                        Abrir PDF

                                                    </a>

                                                </div>

                                                <hr class="my-2">

                                                <div class="row">

                                                    <div class="col-12 mb-2">
                                                        <small class="text-muted d-block">
                                                            Nome do Documento
                                                        </small>
                                                        <span class="fw-semibold">
                                                            Manual Dräger V2
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data do Documento
                                                        </small>

                                                        <span class="fw-semibold">
                                                            15/03/2021
                                                        </span>

                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data de Validade
                                                        </small>

                                                        <span class="fw-semibold">
                                                            Sem validade
                                                        </span>

                                                    </div>

                                                </div>

                                            </div>

                                            <!-- Manual Técnico -->

                                            <div class="border rounded-3 p-3">

                                                <div class="d-flex align-items-center justify-content-between">

                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Manual Técnico
                                                    </span>

                                                    <a href="#" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">

                                                        <i class="fa-solid fa-eye me-1"></i>
                                                        Abrir PDF

                                                    </a>

                                                </div>

                                                <hr class="my-2">

                                                <div class="row">
                                                    <div class="col-12 mb-2">
                                                        <small class="text-muted d-block">
                                                            Nome do Documento
                                                        </small>
                                                        <span class="fw-semibold">
                                                            Manual Dräger V2
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data do Documento
                                                        </small>

                                                        <span class="fw-semibold">
                                                            20/03/2021
                                                        </span>

                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data de Validade
                                                        </small>

                                                        <span class="fw-semibold">
                                                            Sem validade
                                                        </span>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- AQUISIÇÃO -->
                    <div class="tab-pane fade" id="aquisicao">

                        <div class="row g-3">

                            <div class="col-md-3">
                                <p class="text-muted mb-1">Data de Aquisição</p>
                                <p class="fw-semibold mb-0">15/03/2021</p>
                            </div>

                            <div class="col-md-3">
                                <p class="text-muted mb-1">Custo de Aquisição (€)</p>
                                <p class="fw-semibold mb-0">28 500,00 €</p>
                            </div>

                            <div class="col-md-3">
                                <p class="text-muted mb-1">Tipo de Entrada</p>
                                <p class="fw-semibold mb-0">Compra</p>
                            </div>

                            <div class="col-md-3">
                                <p class="text-muted mb-1">
                                    Estado Atual
                                    <button
                                        type="button"
                                        class="btn btn-sm border-0 p-0 ms-1"
                                        data-bs-toggle="popover"
                                        data-bs-trigger="hover focus"
                                        data-bs-html="true"
                                        title="Estados dos Equipamentos"
                                        data-bs-content="
            <b>Ativo</b> - Disponível e operacional.<br>
            <b>Em manutenção</b> - Em intervenção técnica programada ou corretiva.<br>
            <b>Inativo</b> - Temporariamente indisponível para utilização.<br>
            <b>Em calibração</b> - Em processo de calibração ou validação metrológica.">

                                        <i class="fa-solid fa-circle-question text-primary"></i>

                                    </button>

                                </p>
                                </button>
                                <span class="badge bg-success">Ativo</span>
                            </div>

                        </div>
                        <hr class="my-4">

                        <div class="accordion" id="accordionDocumentacaoAquisicao">

                            <div class="accordion-item border rounded-3">

                                <h2 class="accordion-header">

                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseDocumentacaoAquisicao">

                                        Documentação de Aquisição

                                    </button>

                                </h2>

                                <div id="collapseDocumentacaoAquisicao" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionDocumentacaoAquisicao">

                                    <div class="accordion-body">

                                        <div class="d-flex flex-column gap-3">

                                            <!-- FATURA DE AQUISIÇÃO -->

                                            <div class="border rounded-3 p-3">

                                                <div class="d-flex align-items-center justify-content-between">

                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Fatura de Aquisição
                                                    </span>

                                                    <a href="#" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">

                                                        <i class="fa-solid fa-eye me-1"></i>
                                                        Abrir PDF

                                                    </a>

                                                </div>

                                                <hr class="my-2">

                                                <div class="row">
                                                    <div class="col-12 mb-2">
                                                        <small class="text-muted d-block">
                                                            Nome do Documento
                                                        </small>
                                                        <span class="fw-semibold">
                                                            Manual Dräger V2
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data do Documento
                                                        </small>

                                                        <span class="fw-semibold">
                                                            15/03/2021
                                                        </span>

                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data de Validade
                                                        </small>

                                                        <span class="fw-semibold">
                                                            Sem validade
                                                        </span>

                                                    </div>

                                                </div>

                                            </div>

                                            <!-- CONTRATO DE AQUISIÇÃO -->

                                            <div class="border rounded-3 p-3">

                                                <div class="d-flex align-items-center justify-content-between">

                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Contrato de Aquisição
                                                    </span>

                                                    <a href="#" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">

                                                        <i class="fa-solid fa-eye me-1"></i>
                                                        Abrir PDF

                                                    </a>

                                                </div>

                                                <hr class="my-2">

                                                <div class="row">

                                                    <div class="col-12 mb-2">
                                                        <small class="text-muted d-block">
                                                            Nome do Documento
                                                        </small>
                                                        <span class="fw-semibold">
                                                            Manual Dräger V2
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data do Documento
                                                        </small>

                                                        <span class="fw-semibold">
                                                            15/03/2021
                                                        </span>

                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data de Validade
                                                        </small>

                                                        <span class="fw-semibold">
                                                            15/03/2031
                                                        </span>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>


                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- ACESSÓRIOS E CONSUMÍVEIS -->
                    <div class="tab-pane fade" id="acessorios">


                        <!-- Mensagem quando não existem dados -->

                        <div class="alert alert-light border text-center">

                            <i class="fa-solid fa-circle-info me-2"></i>

                            Não existem acessórios nem consumíveis associados a este equipamento.

                        </div>


                        <div class="accordion" id="accordionAcessoriosConsumiveis">

                            <!-- ACESSÓRIOS -->

                            <div class="accordion-item border rounded-3 mb-3">

                                <h2 class="accordion-header">

                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseAcessorios">

                                        Acessórios Associados (3)

                                    </button>

                                </h2>

                                <div id="collapseAcessorios" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionAcessoriosConsumiveis">

                                    <div class="accordion-body">

                                        <div class="table-responsive">

                                            <table class="table align-middle">

                                                <thead>

                                                    <tr>

                                                        <th>Nome</th>
                                                        <th>Quantidade</th>
                                                        <th>Estado
                                                            <button
                                                                type="button"
                                                                class="btn btn-sm border-0 p-0 ms-1"
                                                                data-bs-toggle="popover"
                                                                data-bs-trigger="hover focus"
                                                                data-bs-html="true"
                                                                title="Estados dos Equipamentos"
                                                                data-bs-content="
            <b>Ativo</b> - Disponível e operacional.<br>
            <b>Em manutenção</b> - Em intervenção técnica programada ou corretiva.<br>
            <b>Inativo</b> - Temporariamente indisponível para utilização.<br>
            <b>Em calibração</b> - Em processo de calibração ou validação metrológica.">

                                                                <i class="fa-solid fa-circle-question text-primary"></i>

                                                            </button>
                                                        </th>

                                                    </tr>

                                                </thead>

                                                <tbody>

                                                    <tr>

                                                        <td>Cabo de Alimentação</td>
                                                        <td>1</td>
                                                        <td>
                                                            <span class="badge bg-success">
                                                                Ativo
                                                            </span>
                                                        </td>

                                                    </tr>

                                                    <tr>

                                                        <td>Sensor de Fluxo</td>
                                                        <td>2</td>
                                                        <td>
                                                            <span class="badge bg-success">
                                                                Ativo
                                                            </span>
                                                        </td>

                                                    </tr>

                                                    <tr>

                                                        <td>Suporte Móvel</td>
                                                        <td>1</td>
                                                        <td>
                                                            <span class="badge bg-success">
                                                                Ativo
                                                            </span>
                                                        </td>

                                                    </tr>

                                                </tbody>

                                            </table>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- CONSUMÍVEIS -->

                            <div class="accordion-item border rounded-3">

                                <h2 class="accordion-header">

                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseConsumiveis">

                                        Consumíveis Associados (3)

                                    </button>

                                </h2>

                                <div id="collapseConsumiveis" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionAcessoriosConsumiveis">

                                    <div class="accordion-body">

                                        <div class="table-responsive">

                                            <table class="table align-middle">

                                                <thead>

                                                    <tr>

                                                        <th>Nome</th>
                                                        <th>Quantidade</th>

                                                    </tr>

                                                </thead>

                                                <tbody>

                                                    <tr>

                                                        <td>Filtro Bacteriano</td>
                                                        <td>20</td>

                                                    </tr>

                                                    <tr>

                                                        <td>Circuito Respiratório</td>
                                                        <td>10</td>

                                                    </tr>

                                                    <tr>

                                                        <td>Máscara Ventilatória</td>
                                                        <td>15</td>

                                                    </tr>

                                                </tbody>

                                            </table>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- LOCALIZAÇÃO -->
                    <div class="tab-pane fade" id="localizacao">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Código da Localização</p>
                                <p class="fw-semibold mb-0">LOC-001</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Edifício</p>
                                <p class="fw-semibold mb-0">Hospital Central</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Piso</p>
                                <p class="fw-semibold mb-0">Piso 2</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Serviço / Departamento</p>
                                <p class="fw-semibold mb-0">UCI</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Sala / Gabinete</p>
                                <p class="fw-semibold mb-0">Sala UCI-A</p>
                            </div>

                        </div>

                    </div>

                    <!-- FORNECEDOR -->
                    <div class="tab-pane fade" id="fornecedor">

                        <div class="accordion" id="accordionFornecedores">

                            <!-- FORNECEDOR 1 -->

                            <div class="accordion-item border rounded-3 mb-3">

                                <h2 class="accordion-header" id="fornecedorHeading1">

                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#fornecedorCollapse1">

                                        <div class="w-100 d-flex justify-content-between align-items-center pe-3">

                                            <div>
                                                <strong>Dräger Portugal</strong>
                                            </div>

                                            <div class="text-muted small">

                                                FOR-001 • Fabricante

                                            </div>

                                        </div>

                                    </button>

                                </h2>

                                <div id="fornecedorCollapse1" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionFornecedores">

                                    <div class="accordion-body">

                                        <div class="row g-4">

                                            <div class="col-md-4">

                                                <p class="text-muted mb-1">Código Interno</p>
                                                <p class="fw-semibold mb-0">FOR-001</p>

                                            </div>

                                            <div class="col-md-4">

                                                <p class="text-muted mb-1">Tipo de Fornecedor</p>
                                                <p class="fw-semibold mb-0">Fabricante</p>

                                            </div>

                                            <div class="col-md-4">

                                                <p class="text-muted mb-1">NIF</p>
                                                <p class="fw-semibold mb-0">509999999</p>

                                            </div>

                                            <div class="col-md-6">

                                                <p class="text-muted mb-1">Email</p>
                                                <p class="fw-semibold mb-0">
                                                    contacto@draeger.pt
                                                </p>

                                            </div>

                                            <div class="col-md-6">

                                                <p class="text-muted mb-1">Telefone</p>
                                                <p class="fw-semibold mb-0">
                                                    +351 220 123 456
                                                </p>

                                            </div>

                                            <div class="col-12">

                                                <p class="text-muted mb-1">Morada</p>
                                                <p class="fw-semibold mb-0">
                                                    Rua Exemplo nº 100, Porto
                                                </p>

                                            </div>

                                            <div class="col-md-6">

                                                <p class="text-muted mb-1">
                                                    Pessoa de Contacto
                                                </p>

                                                <p class="fw-semibold mb-0">
                                                    João Silva
                                                </p>

                                            </div>

                                            <div class="col-md-6">

                                                <p class="text-muted mb-1">
                                                    Contacto da Pessoa
                                                </p>

                                                <p class="fw-semibold mb-0">
                                                    +351 912 345 678
                                                </p>

                                            </div>

                                            <div class="col-12">

                                                <p class="text-muted mb-1">Website</p>

                                                <a href="#" target="_blank" class="fw-semibold">

                                                    www.draeger.pt

                                                </a>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>
                            <!-- FORNECEDOR 2 -->
                            <div class="accordion-item border rounded-3 mb-3">

                                <h2 class="accordion-header" id="fornecedorHeading2">

                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#fornecedorCollapse2">

                                        <div class="w-100 d-flex justify-content-between align-items-center pe-3">

                                            <div>
                                                <strong>Philips Healthcare</strong>
                                            </div>

                                            <div class="text-muted small">

                                                FOR-002 • Distribuidor

                                            </div>

                                        </div>

                                    </button>

                                </h2>

                                <div id="fornecedorCollapse2" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionFornecedores">

                                    <div class="accordion-body">

                                        <div class="row g-4">

                                            <div class="col-md-4">
                                                <p class="text-muted mb-1">Código Interno</p>
                                                <p class="fw-semibold mb-0">FOR-002</p>
                                            </div>

                                            <div class="col-md-4">
                                                <p class="text-muted mb-1">Tipo de Fornecedor</p>
                                                <p class="fw-semibold mb-0">Distribuidor</p>
                                            </div>

                                            <div class="col-md-4">
                                                <p class="text-muted mb-1">NIF</p>
                                                <p class="fw-semibold mb-0">508888888</p>
                                            </div>

                                            <div class="col-md-6">
                                                <p class="text-muted mb-1">Email</p>
                                                <p class="fw-semibold mb-0">geral@philips.pt</p>
                                            </div>

                                            <div class="col-md-6">
                                                <p class="text-muted mb-1">Telefone</p>
                                                <p class="fw-semibold mb-0">+351 220 555 555</p>
                                            </div>

                                            <div class="col-12">
                                                <p class="text-muted mb-1">Morada</p>
                                                <p class="fw-semibold mb-0">Avenida Philips, Lisboa</p>
                                            </div>
                                            <div class="col-md-6">

                                                <p class="text-muted mb-1">
                                                    Pessoa de Contacto
                                                </p>

                                                <p class="fw-semibold mb-0">
                                                    Ana Martins
                                                </p>

                                            </div>

                                            <div class="col-md-6">

                                                <p class="text-muted mb-1">
                                                    Contacto da Pessoa
                                                </p>

                                                <p class="fw-semibold mb-0">
                                                    +351 915 555 555
                                                </p>

                                            </div>

                                            <div class="col-12">

                                                <p class="text-muted mb-1">
                                                    Website
                                                </p>

                                                <a href="#" target="_blank" class="fw-semibold">

                                                    www.philips.pt

                                                </a>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- GARANTIAS e CONTRATOS -->
                    <div class="tab-pane fade" id="garantias">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Garantia Associada</p>
                                <p class="fw-semibold mb-0">Sim</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Contrato Associado</p>
                                <p class="fw-semibold mb-0">Sim</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Tipo de Contrato</p>
                                <p class="fw-semibold mb-0">Manutenção Preventiva e Corretiva</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Entidade Responsável</p>
                                <p class="fw-semibold mb-0">SaúdeTec Lda.</p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Periodicidade</p>
                                <p class="fw-semibold mb-0">Semestral</p>
                            </div>

                        </div>
                        <hr class="my-4">

                        <div class="accordion" id="accordionGarantiasDocumentos">

                            <div class="accordion-item border rounded-3">

                                <h2 class="accordion-header">

                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseGarantiasDocumentos">

                                        Documentos Associados

                                    </button>

                                </h2>

                                <div id="collapseGarantiasDocumentos" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionGarantiasDocumentos">


                                    <div class="accordion-body">

                                        <div class="d-flex flex-column gap-3">

                                            <!-- CERTIFICADO DE GARANTIA -->

                                            <div class="border rounded-3 p-3">

                                                <div class="d-flex align-items-center justify-content-between">

                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Certificado de Garantia
                                                    </span>

                                                    <a href="#" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">

                                                        <i class="fa-solid fa-eye me-1"></i>
                                                        Abrir PDF

                                                    </a>

                                                </div>

                                                <hr class="my-2">

                                                <div class="row">
                                                    <div class="col-12 mb-2">
                                                        <small class="text-muted d-block">
                                                            Nome do Documento
                                                        </small>
                                                        <span class="fw-semibold">
                                                            Manual Dräger V2
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data do Documento
                                                        </small>

                                                        <span class="fw-semibold">
                                                            15/03/2021
                                                        </span>

                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data de Validade
                                                        </small>

                                                        <span class="fw-semibold">
                                                            15/03/2024
                                                        </span>

                                                    </div>

                                                </div>

                                            </div>

                                            <!-- CONTRATO DE MANUTENÇÃO -->

                                            <div class="border rounded-3 p-3">

                                                <div class="d-flex align-items-center justify-content-between">

                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Contrato de Manutenção
                                                    </span>

                                                    <a href="#" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">

                                                        <i class="fa-solid fa-eye me-1"></i>
                                                        Abrir PDF

                                                    </a>

                                                </div>

                                                <hr class="my-2">

                                                <div class="row">

                                                    <div class="col-12 mb-2">
                                                        <small class="text-muted d-block">
                                                            Nome do Documento
                                                        </small>
                                                        <span class="fw-semibold">
                                                            Manual Dräger V2
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data do Documento
                                                        </small>

                                                        <span class="fw-semibold">
                                                            15/03/2021
                                                        </span>

                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data de Validade
                                                        </small>

                                                        <span class="fw-semibold">
                                                            15/03/2026
                                                        </span>

                                                    </div>

                                                </div>

                                            </div>

                                            <!-- CERTIFICADO DE CALIBRAÇÃO -->

                                            <div class="border rounded-3 p-3">

                                                <div class="d-flex align-items-center justify-content-between">

                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Certificado de Calibração
                                                    </span>

                                                    <a href="#" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">

                                                        <i class="fa-solid fa-eye me-1"></i>
                                                        Abrir PDF

                                                    </a>

                                                </div>

                                                <hr class="my-2">

                                                <div class="row">

                                                    <div class="col-12 mb-2">
                                                        <small class="text-muted d-block">
                                                            Nome do Documento
                                                        </small>
                                                        <span class="fw-semibold">
                                                            Manual Dräger V2
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data do Documento
                                                        </small>

                                                        <span class="fw-semibold">
                                                            12/01/2025
                                                        </span>

                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data de Validade
                                                        </small>

                                                        <span class="fw-semibold">
                                                            12/01/2026
                                                        </span>

                                                    </div>

                                                </div>

                                            </div>

                                            <!-- RELATÓRIO DE CALIBRAÇÃO -->

                                            <div class="border rounded-3 p-3">

                                                <div class="d-flex align-items-center justify-content-between">

                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Relatório de Calibração
                                                    </span>

                                                    <a href="#" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">

                                                        <i class="fa-solid fa-eye me-1"></i>
                                                        Abrir PDF

                                                    </a>

                                                </div>

                                                <hr class="my-2">

                                                <div class="row">
                                                    <div class="col-12 mb-2">
                                                        <small class="text-muted d-block">
                                                            Nome do Documento
                                                        </small>
                                                        <span class="fw-semibold">
                                                            Manual Dräger V2
                                                        </span>
                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data do Documento
                                                        </small>

                                                        <span class="fw-semibold">
                                                            12/01/2025
                                                        </span>

                                                    </div>

                                                    <div class="col-md-6">

                                                        <small class="text-muted d-block">
                                                            Data de Validade
                                                        </small>

                                                        <span class="fw-semibold">
                                                            Não aplicável
                                                        </span>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- DOCUMENTAÇÃO -->
                <div class="tab-pane fade" id="documentacao">

                    <div class="accordion" id="accordionDocumentacaoGeral">

                        <!-- DOCUMENTAÇÃO TÉCNICA -->

                        <div class="accordion-item border rounded-3 mb-3">

                            <h2 class="accordion-header">

                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseDocTecnica">

                                    Documentação Técnica

                                </button>

                            </h2>

                            <div id="collapseDocTecnica" class="accordion-collapse collapse"
                                data-bs-parent="#accordionDocumentacaoGeral">

                                <div class="accordion-body">

                                    <div class="d-flex flex-column gap-2">

                                        <div
                                            class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">

                                            <span>
                                                <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                Manual de Utilização
                                            </span>

                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye me-1"></i>
                                                Abrir PDF
                                            </a>

                                        </div>

                                        <div
                                            class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">

                                            <span>
                                                <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                Manual Técnico
                                            </span>

                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye me-1"></i>
                                                Abrir PDF
                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- DOCUMENTAÇÃO DE AQUISIÇÃO -->

                        <div class="accordion-item border rounded-3 mb-3">

                            <h2 class="accordion-header">

                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseDocAquisicao">

                                    Documentação de Aquisição

                                </button>

                            </h2>

                            <div id="collapseDocAquisicao" class="accordion-collapse collapse"
                                data-bs-parent="#accordionDocumentacaoGeral">

                                <div class="accordion-body">

                                    <div class="d-flex flex-column gap-2">

                                        <div
                                            class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">

                                            <span>
                                                <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                Fatura de Aquisição
                                            </span>

                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye me-1"></i>
                                                Abrir PDF
                                            </a>

                                        </div>

                                        <div
                                            class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">

                                            <span>
                                                <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                Contrato de Aquisição
                                            </span>

                                            <a href="#" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye me-1"></i>
                                                Abrir PDF
                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- GARANTIAS E CONTRATOS -->

                        <div class="accordion-item border rounded-3 mb-3">

                            <h2 class="accordion-header">

                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseDocGarantias">

                                    Garantias e Contratos

                                </button>

                            </h2>

                            <div id="collapseDocGarantias" class="accordion-collapse collapse"
                                data-bs-parent="#accordionDocumentacaoGeral">

                                <div class="accordion-body">

                                    <div class="d-flex flex-column gap-2">

                                        <div
                                            class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
                                            <span><i class="fa-solid fa-file-pdf text-danger me-2"></i>Certificado
                                                de Garantia</span>
                                            <a href="#" class="btn btn-sm btn-outline-primary">Abrir PDF</a>
                                        </div>

                                        <div
                                            class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
                                            <span><i class="fa-solid fa-file-pdf text-danger me-2"></i>Contrato
                                                de Manutenção</span>
                                            <a href="#" class="btn btn-sm btn-outline-primary">Abrir PDF</a>
                                        </div>

                                        <div
                                            class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
                                            <span><i class="fa-solid fa-file-pdf text-danger me-2"></i>Certificado
                                                de Calibração</span>
                                            <a href="#" class="btn btn-sm btn-outline-primary">Abrir PDF</a>
                                        </div>

                                        <div
                                            class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
                                            <span><i class="fa-solid fa-file-pdf text-danger me-2"></i>Relatório
                                                de Calibração</span>
                                            <a href="#" class="btn btn-sm btn-outline-primary">Abrir PDF</a>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- DOCUMENTAÇÃO ADICIONAL -->

                        <div class="accordion-item border rounded-3">

                            <h2 class="accordion-header">

                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseDocAdicional">

                                    Documentação Adicional

                                </button>

                            </h2>

                            <div id="collapseDocAdicional" class="accordion-collapse collapse"
                                data-bs-parent="#accordionDocumentacaoGeral">

                                <div class="accordion-body">

                                    <div class="border rounded-3 p-3">

                                        <div class="d-flex justify-content-between align-items-center">

                                            <span>
                                                <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                Certificado CE
                                            </span>

                                            <a href="#" class="btn btn-sm btn-outline-primary">

                                                <i class="fa-solid fa-eye me-1"></i>
                                                Abrir PDF

                                            </a>

                                        </div>

                                        <hr class="my-2">

                                        <div class="row">

                                            <div class="col-12 mb-2">
                                                <small class="text-muted d-block">
                                                    Nome do Documento
                                                </small>
                                                <span class="fw-semibold">
                                                    Manual Dräger V2
                                                </span>
                                            </div>

                                            <div class="col-md-6">

                                                <small class="text-muted d-block">
                                                    Data do Documento
                                                </small>

                                                <span class="fw-semibold">
                                                    15/03/2025
                                                </span>

                                            </div>

                                            <div class="col-md-6">

                                                <small class="text-muted d-block">
                                                    Data de Validade
                                                </small>

                                                <span class="fw-semibold">
                                                    15/03/2030
                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

            <!-- OBSERVAÇÕES -->
            <div class="tab-pane fade" id="observacoes">

                <p class="text-muted mb-0">
                    Equipamento em funcionamento regular. Deve ser dada prioridade à manutenção
                    preventiva
                    devido à sua criticidade no serviço onde se encontra instalado.
                </p>

            </div>

        </div>

</div>

</div>

</main>

</div>

<?php include '../../includes/footer.php'; ?>