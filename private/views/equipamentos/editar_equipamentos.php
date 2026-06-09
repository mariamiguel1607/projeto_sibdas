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
                <h1 class="fw-bold mb-1">Editar Equipamento</h1>
                <p class="text-muted mb-0">
                    Atualização dos dados técnicos e administrativos do equipamento.
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="ficha_equipamento.php" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Voltar
                </a>
            </div>

        </div>

        <!-- FORMULÁRIO -->
        <form>

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
                            <button type="button" class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#aquisicao">
                                Aquisição
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#acessorios">
                                Acessórios e Consumíveis
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#localizacao">
                                Localização
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#fornecedor">
                                Fornecedor Associado
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#garantias">
                                Garantias e Contratos
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#documentacao">
                                Documentação
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#observacoes">
                                Observações
                            </button>
                        </li>

                    </ul>


                    <div class="tab-content">

                        <!-- DADOS GERAIS -->
                        <div class="tab-pane fade show active" id="dadosGerais">
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
                            <div class="row g-3">

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Código Interno</label>
                                    <input type="text" class="form-control" value="EQ-0001" name="codigo_interno">
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Designação</label>
                                    <input type="text" class="form-control" value="Ventilador Pulmonar" name="designacao">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Categoria</label>
                                    <select class="form-select" name="id_categoria">
                                        <option selected>Suporte de Vida</option>
                                        <option>Diagnóstico</option>
                                        <option>Monitorização</option>
                                        <option>Terapia</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Marca</label>
                                    <input type="text" class="form-control" name="marca">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Modelo</label>
                                    <input type="text" class="form-control" name="modelo">
                                </div>


                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Número de Série</label>
                                    <input type="text" class="form-control" value="SN-2026-001" name="numero_serie">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Ano Fabrico</label>
                                    <input type="number" class="form-control"  value="2023" name="ano_fabrico">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Criticidade</label>
                                    <select class="form-select" name="criticidade">
                                        <option>Baixa</option>
                                        <option>Média</option>
                                        <option>Alta</option>
                                        <option selected>Crítica</option>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Estado</label>
                                    <select class="form-select" name="estado">
                                        <option>Ativo</option>
                                        <option>Em manutenção</option>
                                        <option>Inativo</option>
                                        <option>Em calibração</option>
                                        <option>Em quarentena</option>
                                        <option>Abatido</option>
                                    </select>
                                </div>


                                <hr class="my-4">

                                <div class="d-flex justify-content-between align-items-center mb-3">

                                    <h5 class="fw-bold mb-0">
                                        Documentação Técnica
                                    </h5>

                                </div>

                                <div class="row g-3">

                                    <div class="col-md-6">

                                        <div class="border rounded-3 p-3">

                                            <h6 class="fw-bold">
                                                Manual de Utilização
                                            </h6>

                                            <p class="text-muted small mb-3">
                                                Documento técnico associado ao equipamento.
                                            </p>

                                            <button type="button" class="btn btn-outline-primary"
                                                data-bs-toggle="modal" data-bs-target="#modalManualUtilizacao">

                                                <i class="fa-solid fa-file-pdf me-2"></i>
                                                Editar Documento

                                            </button>

                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="border rounded-3 p-3">

                                            <h6 class="fw-bold">
                                                Manual Técnico
                                            </h6>

                                            <p class="text-muted small mb-3">
                                                Manual técnico disponibilizado pelo fabricante.
                                            </p>

                                            <button type="button" class="btn btn-outline-primary"
                                                data-bs-toggle="modal" data-bs-target="#modalManualTecnico">

                                                <i class="fa-solid fa-file-pdf me-2"></i>
                                                Editar Documento

                                            </button>

                                        </div>

                                    </div>

                                </div>


                            </div>


                            <!-- MODAL MANUAL DE UTILIZAÇÃO -->

                            <div class="modal fade" id="modalManualUtilizacao" tabindex="-1">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Editar Manual de Utilização
                                            </h5>

                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>

                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Nome do Documento
                                                </label>
                                                <input type="text" class="form-control"
                                                    name="nome_documento_manual_utilizacao"
                                                    placeholder="Ex: Manual de Utilização Dräger V2">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <input type="file" class="form-control"
                                                    name="manual_utilizacao" accept="application/pdf">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="manual_utilizacao_data">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="manual_utilizacao_validade">
                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                                                Cancelar

                                            </button>

                                            <button type="button" class="btn btn-primary">

                                                Guardar Alterações

                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- MODAL MANUAL TÉCNICO -->

                            <div class="modal fade" id="modalManualTecnico" tabindex="-1">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Editar Manual Técnico
                                            </h5>

                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>

                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Nome do Documento
                                                </label>
                                                <input type="text" class="form-control"
                                                    name="nome_documento_manual_tecnico"
                                                    placeholder="Ex: Manual Técnico Fabricante 2023">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <input type="file" class="form-control"
                                                    name="manual_tecnico" accept="application/pdf">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="manual_tecnico_data">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="manual_tecnico_validade">
                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                                                Cancelar

                                            </button>

                                            <button type="button" class="btn btn-primary">

                                                Guardar Alterações

                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>


                        </div>

                        <!-- Aquisição -->
                        <div class="tab-pane fade" id="aquisicao">
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

                                <div class="col-md-3">

                                    <label class="form-label fw-semibold">
                                        Data de Aquisição
                                    </label>

                                    <input type="date" class="form-control" value="2024-03-12" name="data_aquisicao">

                                </div>

                                <div class="col-md-3">

                                    <label class="form-label fw-semibold">
                                        Custo de Aquisição
                                    </label>

                                    <input type="number" class="form-control" placeholder="0.00" name="custo_aquisicao">

                                </div>

                                <div class="col-md-3">

                                    <label class="form-label fw-semibold">
                                        Tipo de Entrada
                                    </label>

                                    <select class="form-select" name="tipo_entrada">

                                        <option selected>
                                            Compra
                                        </option>

                                        <option>
                                            Doação
                                        </option>

                                        <option>
                                            Aluguer
                                        </option>

                                        <option>
                                            Empréstimo
                                        </option>

                                    </select>

                                </div>
                                <div class="col-md-3">

                                    <label class="form-label fw-bold">

                                        Estado

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

                                    </label>

                                    <select class="form-select" name="estado">

                                        <option selected>
                                            Ativo
                                        </option>

                                        <option>
                                            Inativo
                                        </option>

                                        <option>
                                            Em manutenção
                                        </option>

                                        <option>
                                            Em calibração
                                        </option>

                                    </select>

                                </div>

                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <h5 class="fw-bold mb-0">
                                    Documentação de Aquisição
                                </h5>

                            </div>

                            <div class="row g-3">

                                <!-- FATURA DE AQUISIÇÃO -->

                                <div class="col-md-6">

                                    <div class="border rounded-3 p-3">

                                        <h6 class="fw-bold">
                                            Fatura de Aquisição
                                        </h6>

                                        <p class="text-muted small mb-3">
                                            Documento comprovativo da aquisição.
                                        </p>

                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#modalFaturaAquisicao">

                                            <i class="fa-solid fa-file-pdf me-2"></i>
                                            Editar Documento

                                        </button>

                                    </div>

                                </div>

                                <!-- CONTRATO DE AQUISIÇÃO -->

                                <div class="col-md-6">

                                    <div class="border rounded-3 p-3">

                                        <h6 class="fw-bold">
                                            Contrato de Aquisição
                                        </h6>

                                        <p class="text-muted small mb-3">
                                            Contrato associado à compra do equipamento.
                                        </p>

                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#modalContratoAquisicao">

                                            <i class="fa-solid fa-file-pdf me-2"></i>
                                            Editar Documento

                                        </button>

                                    </div>

                                </div>

                            </div>


                            <!-- MODAL FATURA DE AQUISIÇÃO -->

                            <div class="modal fade" id="modalFaturaAquisicao" tabindex="-1">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Editar Fatura de Aquisição
                                            </h5>

                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>

                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Nome do Documento
                                                </label>
                                                <input type="text" class="form-control"
                                                    name="nome_documento_fatura_aquisicao"
                                                    placeholder="Ex: Fatura MedEquip 2024">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <input type="file" class="form-control"
                                                    name="fatura_aquisicao" accept="application/pdf">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="fatura_aquisicao_data">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="fatura_aquisicao_validade">
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- MODAL CONTRATO DE AQUISIÇÃO -->

                            <div class="modal fade" id="modalContratoAquisicao" tabindex="-1">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Editar Contrato de Aquisição
                                            </h5>

                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>

                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Nome do Documento
                                                </label>
                                                <input type="text" class="form-control"
                                                    name="nome_documento_contrato_aquisicao"
                                                    placeholder="Ex: Contrato Aquisição 2024">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <input type="file" class="form-control"
                                                    name="contrato_aquisicao" accept="application/pdf">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="contrato_aquisicao_data">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="contrato_aquisicao_validade">
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- ACESSÓRIOS E CONSUMÍVEIS -->
                        <div class="tab-pane fade" id="acessorios">

                            <!-- ALERTAS -->
                            <div id="alertasAcessoriosEditar" class="alert alert-danger mb-4" style="display:none;">

                                <h6 class="alert-heading mb-2">

                                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                                    Foram encontrados erros

                                </h6>

                                <ul class="mb-0"></ul>

                            </div>

                            <div class="row g-4">

                                <!-- EXISTEM ACESSÓRIOS -->

                                <div class="col-md-6">

                                    <label class="form-label fw-bold">
                                        Existem acessórios associados ao equipamento?
                                    </label>

                                    <select class="form-select" id="temAcessoriosEditar">

                                        <option value="sim" selected>
                                            Sim
                                        </option>

                                        <option value="nao">
                                            Não
                                        </option>

                                    </select>

                                </div>

                                <!-- EXISTEM CONSUMÍVEIS -->

                                <div class="col-md-6">

                                    <label class="form-label fw-bold">
                                        Existem consumíveis associados ao equipamento?
                                    </label>

                                    <select class="form-select" id="temConsumiveisEditar">

                                        <option value="sim" selected>
                                            Sim
                                        </option>

                                        <option value="nao">
                                            Não
                                        </option>

                                    </select>

                                </div>

                            </div>

                            <!-- ACESSÓRIOS -->

                            <div id="secaoAcessoriosEditar">

                                <hr class="my-4">

                                <h5 class="fw-bold mb-3">
                                    Acessórios Associados
                                </h5>

                                <div id="listaAcessoriosEditar">

                                    <!-- Acessório existente -->

                                    <div class="border rounded-3 p-3 mb-3">

                                        <div class="row g-3">

                                            <div class="col-md-5">

                                                <label class="form-label fw-bold">
                                                    Nome do Acessório
                                                </label>

                                                <input type="text" class="form-control"  value="Sensor de Fluxo" name="nome_acessorio[]">

                                            </div>

                                            <div class="col-md-3">

                                                <label class="form-label fw-bold">
                                                    Quantidade
                                                </label>

                                                <input type="number" class="form-control" value="2" name="quantidade_acessorio[]">

                                            </div>

                                            <div class="col-md-3">

                                                <label class="form-label fw-bold">

                                                    Estado

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

                                                </label>

                                                <select class="form-select" name="estado_acessorio[]">

                                                    <option selected>
                                                        Ativo
                                                    </option>

                                                    <option>
                                                        Inativo
                                                    </option>

                                                    <option>
                                                        Em manutenção
                                                    </option>

                                                    <option>
                                                        Em calibração
                                                    </option>

                                                </select>

                                            </div>

                                            <div class="col-md-1 d-flex align-items-end">

                                                <button type="button" class="btn btn-outline-danger">

                                                    <i class="fa-solid fa-trash"></i>

                                                </button>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <button type="button" class="btn btn-outline-primary"
                                    id="btnAdicionarAcessorioEditar">

                                    <i class="fa-solid fa-plus me-2"></i>
                                    Adicionar Acessório

                                </button>

                            </div>

                            <!-- CONSUMÍVEIS -->

                            <div id="secaoConsumiveisEditar">

                                <hr class="my-4">

                                <h5 class="fw-bold mb-3">
                                    Consumíveis Associados
                                </h5>

                                <div id="listaConsumiveisEditar">

                                    <!-- Consumível existente -->

                                    <div class="border rounded-3 p-3 mb-3">

                                        <div class="row g-3">

                                            <div class="col-md-5">

                                                <label class="form-label fw-bold">
                                                    Nome do Consumível
                                                </label>

                                                <input type="text" class="form-control" value="Filtro Bacteriano" name="nome_consumivel[]">

                                            </div>

                                            <div class="col-md-3">

                                                <label class="form-label fw-bold">
                                                    Quantidade
                                                </label>

                                                <input type="number" class="form-control" value="10" name="quantidade_consumivel[]">

                                            </div>

                                            <div class="col-md-1 d-flex align-items-end">

                                                <button type="button" class="btn btn-outline-danger">

                                                    <i class="fa-solid fa-trash"></i>

                                                </button>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <button type="button" class="btn btn-outline-primary"
                                    id="btnAdicionarConsumivelEditar">

                                    <i class="fa-solid fa-plus me-2"></i>
                                    Adicionar Consumível

                                </button>

                            </div>

                        </div>


                        <!-- LOCALIZAÇÃO -->
                        <div class="tab-pane fade" id="localizacao">
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

                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        Localização Associada
                                    </label>

                                    <select class="form-select" name="id_localizacao">

                                        <option selected>
                                            UCI - Piso 2 - Sala UCI02
                                        </option>

                                        <option>
                                            Urgência - Piso 1
                                        </option>

                                        <option>
                                            Bloco Operatório
                                        </option>

                                    </select>

                                </div>

                                <div class="col-md-6">

                                    <div class="card border bg-light">

                                        <div class="card-body">

                                            <h6 class="fw-bold">
                                                Resumo da Localização
                                            </h6>

                                            <p class="mb-1">
                                                Hospital São João
                                            </p>

                                            <p class="mb-1">
                                                Unidade de Cuidados Intensivos
                                            </p>

                                            <p class="mb-0">
                                                Piso 2 • Sala UCI02
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- FORNECEDOR -->
                        <div class="tab-pane fade" id="fornecedor">
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

                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        Fornecedor Associado
                                    </label>

                                    <select class="form-select" name="id_fornecedor">

                                        <option selected>
                                            MedEquip Portugal
                                        </option>

                                        <option>
                                            SaúdeTec Lda.
                                        </option>

                                        <option>
                                            HospitalCare Solutions
                                        </option>

                                    </select>

                                </div>

                                <div class="col-md-6">

                                    <div class="card border bg-light">

                                        <div class="card-body">

                                            <h6 class="fw-bold">
                                                Dados do Fornecedor
                                            </h6>

                                            <p class="mb-1">
                                                MedEquip Portugal
                                            </p>

                                            <p class="mb-1">
                                                geral@medequip.pt
                                            </p>

                                            <p class="mb-0">
                                                +351 220 000 000
                                            </p>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- GARANTIAS -->
                        <div class="tab-pane fade" id="garantias">
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

                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        Garantia Associada
                                    </label>

                                    <select class="form-select" id="temGarantiaEditar">

                                        <option value="sim" selected>
                                            Sim
                                        </option>

                                        <option value="nao">
                                            Não
                                        </option>

                                    </select>

                                </div>

                            </div>

                            <hr class="my-4">

                            <div class="row g-4">

                                <div class="col-md-6">

                                    <label class="form-label fw-semibold">
                                        Contrato de Manutenção Associado
                                    </label>

                                    <select class="form-select" id="temContratoEditar">

                                        <option value="sim" selected>
                                            Sim
                                        </option>

                                        <option value="nao">
                                            Não
                                        </option>

                                    </select>

                                </div>

                            </div>

                            <div id="secaoContratoEditar">

                                <div class="row g-4 mt-2">

                                    <div class="col-md-4">

                                        <label class="form-label fw-semibold">
                                            Tipo de Contrato
                                        </label>

                                        <select class="form-select" name="tipo_contrato">

                                            <option>
                                                Manutenção Preventiva
                                            </option>

                                            <option>
                                                Manutenção Corretiva
                                            </option>

                                            <option selected>
                                                Manutenção Preventiva e Corretiva
                                            </option>

                                        </select>

                                    </div>

                                    <div class="col-md-4">

                                        <label class="form-label fw-semibold">
                                            Entidade Responsável
                                        </label>

                                        <input type="text" class="form-control " value="Philips Healthcare" name="entidade_responsavel">

                                    </div>

                                    <div class="col-md-4">

                                        <label class="form-label fw-semibold">
                                            Periodicidade
                                        </label>

                                        <select class="form-select" name="periodicidade">

                                            <option>
                                                Mensal
                                            </option>

                                            <option>
                                                Trimestral
                                            </option>

                                            <option selected>
                                                Semestral
                                            </option>

                                            <option>
                                                Anual
                                            </option>

                                        </select>

                                    </div>

                                </div>

                            </div>

                            <hr class="my-4">

                            <h5 class="fw-bold mb-3">
                                Documentos Associados
                            </h5>

                            <div class="row g-3">

                                <!-- CERTIFICADO DE GARANTIA -->

                                <div class="col-md-6" id="cardGarantia" style="display:none;">

                                    <div class="border rounded-3 p-3">

                                        <h6 class="fw-bold">
                                            Certificado de Garantia
                                        </h6>

                                        <button type="button" class="btn btn-outline-primary mt-2"
                                            data-bs-toggle="modal" data-bs-target="#modalGarantia">

                                            <i class="fa-solid fa-file-pdf me-2"></i>
                                            Editar Documento

                                        </button>

                                    </div>

                                </div>

                                <!-- CONTRATO DE MANUTENÇÃO -->

                                <div class="col-md-6" id="cardContrato" style="display:none;">

                                    <div class="border rounded-3 p-3">

                                        <h6 class="fw-bold">
                                            Contrato de Manutenção
                                        </h6>

                                        <button type="button" class="btn btn-outline-primary mt-2"
                                            data-bs-toggle="modal" data-bs-target="#modalContratoManutencao">

                                            <i class="fa-solid fa-file-pdf me-2"></i>
                                            Editar Documento

                                        </button>

                                    </div>

                                </div>

                                <!-- CERTIFICADO DE CALIBRAÇÃO -->

                                <div class="col-md-6">

                                    <div class="border rounded-3 p-3">

                                        <h6 class="fw-bold">
                                            Certificado de Calibração
                                        </h6>

                                        <button type="button" class="btn btn-outline-primary mt-2"
                                            data-bs-toggle="modal" data-bs-target="#modalCertificadoCalibracao">

                                            <i class="fa-solid fa-file-pdf me-2"></i>
                                            Editar Documento

                                        </button>

                                    </div>

                                </div>

                                <!-- RELATÓRIO DE CALIBRAÇÃO -->

                                <div class="col-md-6">

                                    <div class="border rounded-3 p-3">

                                        <h6 class="fw-bold">
                                            Relatório de Calibração
                                        </h6>

                                        <button type="button" class="btn btn-outline-primary mt-2"
                                            data-bs-toggle="modal" data-bs-target="#modalRelatorioCalibracao">

                                            <i class="fa-solid fa-file-pdf me-2"></i>
                                            Editar Documento

                                        </button>

                                    </div>

                                </div>

                            </div>

                            <!-- MODAL CERTIFICADO DE GARANTIA -->

                            <div class="modal fade" id="modalGarantia" tabindex="-1">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Certificado de Garantia
                                            </h5>

                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>

                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Nome do Documento
                                                </label>
                                                <input type="text" class="form-control"
                                                    name="nome_documento_certificado_garantia"
                                                    placeholder="Ex: Certificado de Garantia 2024">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <input type="file" class="form-control"
                                                    name="certificado_garantia" accept="application/pdf">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="certificado_garantia_data">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="certificado_garantia_validade">
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- MODAL CONTRATO DE MANUTENÇÃO -->

                            <div class="modal fade" id="modalContratoManutencao" tabindex="-1">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Contrato de Manutenção
                                            </h5>

                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>

                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Nome do Documento
                                                </label>
                                                <input type="text" class="form-control"
                                                    name="nome_documento_contrato_manutencao"
                                                    placeholder="Ex: Contrato Manutenção Preventiva 2025">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <input type="file" class="form-control"
                                                    name="contrato_manutencao" accept="application/pdf">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="contrato_manutencao_data">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="contrato_manutencao_validade">
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- MODAL CERTIFICADO DE CALIBRAÇÃO -->

                            <div class="modal fade" id="modalCertificadoCalibracao" tabindex="-1">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Certificado de Calibração
                                            </h5>

                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>

                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Nome do Documento
                                                </label>
                                                <input type="text" class="form-control"
                                                    name="nome_documento_certificado_calibracao"
                                                    placeholder="Ex: Certificado Calibração IPQ 2025">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <input type="file" class="form-control"
                                                    name="certificado_calibracao" accept="application/pdf">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="certificado_calibracao_data">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="certificado_calibracao_validade">
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- MODAL RELATÓRIO DE CALIBRAÇÃO -->

                            <div class="modal fade" id="modalRelatorioCalibracao" tabindex="-1">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Relatório de Calibração
                                            </h5>

                                            <button type="button" class="btn-close"
                                                data-bs-dismiss="modal"></button>

                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Nome do Documento
                                                </label>
                                                <input type="text" class="form-control"
                                                    name="nome_documento_relatorio_calibracao"
                                                    placeholder="Ex: Relatório Calibração Anual 2025">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <input type="file" class="form-control"
                                                    name="relatorio_calibracao" accept="application/pdf">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="relatorio_calibracao_data">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="relatorio_calibracao_validade">
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>



                        </div>

                        <!-- DOCUMENTAÇÃO -->
                        <div class="tab-pane fade" id="documentacao">
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
                            <h5 class="fw-bold mb-3">
                                Resumo da Documentação Associada
                            </h5>

                            <div class="accordion" id="accordionResumoDocumentacaoEditar">

                                <!-- DOCUMENTAÇÃO TÉCNICA -->

                                <div class="accordion-item border rounded-3 mb-3">

                                    <h2 class="accordion-header">

                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseResumoTecnicaEditar">

                                            Documentação Técnica

                                        </button>

                                    </h2>

                                    <div id="collapseResumoTecnicaEditar" class="accordion-collapse collapse">

                                        <div class="accordion-body">

                                            <ul class="mb-0">

                                                <li>Manual de Utilização</li>
                                                <li>Manual Técnico</li>

                                            </ul>

                                        </div>

                                    </div>

                                </div>

                                <!-- AQUISIÇÃO -->

                                <div class="accordion-item border rounded-3 mb-3">

                                    <h2 class="accordion-header">

                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapseResumoAquisicaoEditar">

                                            Documentação de Aquisição

                                        </button>

                                    </h2>

                                    <div id="collapseResumoAquisicaoEditar" class="accordion-collapse collapse">

                                        <div class="accordion-body">

                                            <ul class="mb-0">

                                                <li>Fatura de Aquisição</li>
                                                <li>Contrato de Aquisição</li>

                                            </ul>

                                        </div>

                                    </div>

                                </div>

                                <!-- GARANTIAS -->

                                <div class="accordion-item border rounded-3 mb-3">

                                    <h2 class="accordion-header">

                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#collapseResumoGarantiasEditar">

                                            Garantias e Contratos

                                        </button>

                                    </h2>

                                    <div id="collapseResumoGarantiasEditar" class="accordion-collapse collapse">

                                        <div class="accordion-body">

                                            <ul class="mb-0">

                                                <li>Certificado de Garantia</li>
                                                <li>Contrato de Manutenção</li>
                                                <li>Certificado de Calibração</li>
                                                <li>Relatório de Calibração</li>

                                            </ul>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <hr class="my-4">

                            <div class="row g-4">

                                <div class="col-md-6">

                                    <label class="form-label fw-bold">
                                        Documentação Adicional Associada
                                    </label>

                                    <select class="form-select" id="temDocumentacaoAdicionalEditar">

                                        <option value="sim" selected>
                                            Sim
                                        </option>

                                        <option value="nao">
                                            Não
                                        </option>

                                    </select>

                                </div>

                            </div>

                            <div id="secaoDocumentacaoAdicionalEditar">

                                <hr class="my-4">

                                <div id="listaDocumentacaoAdicionalEditar">

                                    <!-- DOCUMENTO EXISTENTE -->

                                    <div class="border rounded-3 p-3 documento-adicional-item">

                                        <h6 class="fw-bold">
                                            Documento Adicional
                                        </h6>

                                        <div class="row g-3">

                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Nome do Documento
                                                </label>

                                                <input type="text" class="form-control" value="Certificado CE">

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    PDF Atual
                                                </label>

                                                <div class="border rounded p-2 bg-light">

                                                    CertificadoCE.pdf

                                                </div>

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Substituir PDF
                                                </label>

                                                <input type="file" class="form-control" accept="application/pdf">

                                            </div>

                                            <div class="col-md-3">

                                                <label class="form-label">
                                                    Data do Documento
                                                </label>

                                                <input type="date" class="form-control" value="2025-03-15">

                                            </div>

                                            <div class="col-md-3">

                                                <label class="form-label">
                                                    Data de Validade
                                                </label>

                                                <input type="date" class="form-control" value="2030-03-15">

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <button type="button" class="btn btn-outline-primary mt-3"
                                    id="btnAdicionarDocumentoAdicionalEditar">

                                    <i class="fa-solid fa-plus me-2"></i>
                                    Adicionar Documento

                                </button>

                            </div>

                        </div>

                        <!-- OBSERVAÇÕES -->
                        <div class="tab-pane fade" id="observacoes">
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

                            <label class="form-label fw-semibold">
                                Observações
                            </label>

                            <textarea class="form-control"  name="observacoes"
                                rows="6">Equipamento em funcionamento regular. Deve ser dada prioridade à manutenção preventiva devido à sua criticidade.</textarea>

                        </div>

                    </div>

                </div>

            </div>


            <!-- BOTÕES -->
            <div class="d-flex justify-content-end gap-2 mt-4">

                <a href="ficha_equipamento.php" class="btn btn-outline-secondary">

                    Cancelar

                </a>

                <button type="submit" class="btn btn-primary-custom">

                    <i class="fa-solid fa-floppy-disk me-2"></i>

                    Guardar Alterações

                </button>

            </div>

        </form>

    </main>

</div>

<?php include '../../includes/footer.php'; ?>