<?php
// --------------------------------------------------------------------
// SEGURANÇA: Proteção de acesso à página de edição
// Este ficheiro deve ser acedido apenas por utilizadores autenticados.
// Caso não exista sessão iniciada, o utilizador será redirecionado para o login.
// --------------------------------------------------------------------
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged(); // Inicia a sessão (se necessário) e verifica se o utilizador está autenticado
// 1. Receber e desencriptar o ID vindo por GET
$idEncriptado = $_GET['id'] ?? null;
$id = aes_decrypt($idEncriptado);

// 2. Validar: tem de ser numérico
if (!$id || !is_numeric($id)) {
    header('Location: equipamentos.php');
    exit;
}

// 3. Ir buscar o equipamento à BD (com joins para categoria, estado, localização)
try {
    $ligacao = ligar_bd();

    $stmt = $ligacao->prepare("
        SELECT
            equipamentos.*,
            categorias.nome_categoria,
            estados.nome_estado,
            localizacoes.codigo_localizacao,
            localizacoes.edificio,
            localizacoes.piso,
            localizacoes.servico_departamento,
            localizacoes.sala_gabinete
        FROM equipamentos
        INNER JOIN categorias ON equipamentos.id_categoria = categorias.id
        INNER JOIN estados ON equipamentos.id_estado = estados.id
        INNER JOIN localizacoes ON equipamentos.id_localizacao = localizacoes.id
        WHERE equipamentos.id = :id
    ");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $equipamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$equipamento) {
        header('Location: equipamentos.php');
        exit;
    }

    // Buscar documentos deste equipamento (dentro do mesmo try)
    $stmtDocs = $ligacao->prepare("
        SELECT documentacao.*, tipos_documento.nome_tipo
        FROM documentacao
        INNER JOIN tipos_documento ON documentacao.id_tipo_documento = tipos_documento.id
        WHERE documentacao.id_equipamento = :id
    ");
    $stmtDocs->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtDocs->execute();
    $documentos = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);

    // Organizar por tipo para fácil acesso na view
    $docsPorTipo = [];
    foreach ($documentos as $doc) {
        $docsPorTipo[$doc['nome_tipo']] = $doc;
    }
    // Buscar acessórios do equipamento
    $stmtAcess = $ligacao->prepare("
    SELECT acessorios.*, estados.nome_estado
    FROM acessorios
    INNER JOIN estados ON acessorios.id_estado = estados.id
    WHERE acessorios.id_equipamento = :id
");
    $stmtAcess->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtAcess->execute();
    $acessorios = $stmtAcess->fetchAll(PDO::FETCH_ASSOC);

    // Buscar consumíveis do equipamento
    $stmtConsum = $ligacao->prepare("
    SELECT * FROM consumiveis
    WHERE id_equipamento = :id
");
    $stmtConsum->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtConsum->execute();
    $consumiveis = $stmtConsum->fetchAll(PDO::FETCH_ASSOC);

    // Buscar fornecedores do equipamento
    $stmtForn = $ligacao->prepare("
    SELECT fornecedores.*
    FROM fornecedores
    INNER JOIN equipamentos_fornecedores ON fornecedores.id = equipamentos_fornecedores.id_fornecedor
    WHERE equipamentos_fornecedores.id_equipamento = :id
");
    $stmtForn->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtForn->execute();
    $fornecedores = $stmtForn->fetchAll(PDO::FETCH_ASSOC);

    // Buscar contrato de manutenção do equipamento (ligado ao documento)
    $stmtContrato = $ligacao->prepare("
    SELECT contratos.*, fornecedores.nome_empresa AS nome_fornecedor
    FROM contratos
    INNER JOIN documentacao ON contratos.id_documento = documentacao.id
    LEFT JOIN fornecedores ON contratos.id_fornecedor = fornecedores.id
    WHERE documentacao.id_equipamento = :id
    LIMIT 1
");
    $stmtContrato->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtContrato->execute();
    $contrato = $stmtContrato->fetch(PDO::FETCH_ASSOC);

    $ligacao = null;
} catch (PDOException $e) {
    echo "<p class='text-danger'>Erro: " . $e->getMessage() . "</p>";
    exit;
}
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

                <a href="editar_equipamentos.php?id_equipamento=<?= aes_encrypt($equipamento['id']) ?>" class="btn btn-primary-custom">
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
                            <?= htmlspecialchars($equipamento['designacao']) ?>
                        </h3>

                        <p class="text-muted mb-0">
                            Código: <?= htmlspecialchars($equipamento['codigo_interno']) ?>
                        </p>

                    </div>

                    <div class="col-lg-4">

                        <div class="d-flex gap-2 justify-content-lg-end mt-3 mt-lg-0">

                            <?php
                            $nomeEstado = $equipamento['nome_estado'];
                            if ($nomeEstado == 'Ativo') echo '<span class="badge bg-success">Ativo</span>';
                            elseif ($nomeEstado == 'Em manutenção') echo '<span class="badge bg-warning text-dark">Em manutenção</span>';
                            elseif ($nomeEstado == 'Inativo') echo '<span class="badge bg-secondary">Inativo</span>';
                            elseif ($nomeEstado == 'Em calibração') echo '<span class="badge bg-info text-dark">Em calibração</span>';
                            ?>

                            <?php
                            $criticidade = $equipamento['criticidade'];
                            if ($criticidade == 'Suporte de Vida') echo '<span class="badge bg-danger">Suporte de Vida</span>';
                            elseif ($criticidade == 'Alta') echo '<span class="badge bg-warning text-dark">Alta</span>';
                            elseif ($criticidade == 'Média') echo '<span class="badge bg-primary">Média</span>';
                            elseif ($criticidade == 'Baixa') echo '<span class="badge bg-secondary">Baixa</span>';
                            ?>
                            <span class="badge bg-primary">
                                <?= htmlspecialchars($equipamento['servico_departamento']) ?>
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
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['codigo_interno']) ?></p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Designação</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['designacao']) ?></p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Categoria</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['nome_categoria']) ?></p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Fabricante</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['fabricante'] ?? '-') ?></p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Marca</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['marca']) ?></p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Modelo</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['modelo']) ?></p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Número de Série</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['num_serie']) ?></p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Ano de Fabrico</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['ano_fabrico'] ?? '-') ?></p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Criticidade</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['criticidade']) ?></p>
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

                                            <?php $doc = $docsPorTipo['Manual de Utilização']; ?>
                                            <div class="border rounded-3 p-3">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Manual de Utilização
                                                    </span>
                                                    <a href="<?= htmlspecialchars($doc['caminho_ficheiro']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-eye me-1"></i>
                                                        Abrir PDF
                                                    </a>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row">
                                                    <div class="col-12 mb-2">
                                                        <small class="text-muted d-block">Nome do Documento</small>
                                                        <span class="fw-semibold"><?= htmlspecialchars($doc['nome_documento']) ?></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">Data do Documento</small>
                                                        <span class="fw-semibold">
                                                            <?= $doc['data_documento'] ? date('d/m/Y', strtotime($doc['data_documento'])) : '-' ?>
                                                        </span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">Data de Validade</small>
                                                        <span class="fw-semibold">
                                                            <?= $doc['data_validade'] ? date('d/m/Y', strtotime($doc['data_validade'])) : 'Sem validade' ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Manual Técnico -->
                                            <?php $doc = $docsPorTipo['Manual Técnico']; ?>
                                            <div class="border rounded-3 p-3">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Manual Técnico
                                                    </span>
                                                    <a href="<?= htmlspecialchars($doc['caminho_ficheiro']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-eye me-1"></i>
                                                        Abrir PDF
                                                    </a>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row">
                                                    <div class="col-12 mb-2">
                                                        <small class="text-muted d-block">Nome do Documento</small>
                                                        <span class="fw-semibold"><?= htmlspecialchars($doc['nome_documento']) ?></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">Data do Documento</small>
                                                        <span class="fw-semibold">
                                                            <?= $doc['data_documento'] ? date('d/m/Y', strtotime($doc['data_documento'])) : '-' ?>
                                                        </span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">Data de Validade</small>
                                                        <span class="fw-semibold">
                                                            <?= $doc['data_validade'] ? date('d/m/Y', strtotime($doc['data_validade'])) : 'Sem validade' ?>
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
                                <p class="fw-semibold mb-0">
                                    <?= $equipamento['data_aquisicao'] ? date('d/m/Y', strtotime($equipamento['data_aquisicao'])) : '-' ?>
                                </p>
                            </div>

                            <div class="col-md-3">
                                <p class="text-muted mb-1">Custo de Aquisição (€)</p>
                                <p class="fw-semibold mb-0">
                                    <?= $equipamento['custo_aquisicao'] ? number_format($equipamento['custo_aquisicao'], 2, ',', ' ') . ' €' : '-' ?>
                                </p>
                            </div>

                            <div class="col-md-3">
                                <p class="text-muted mb-1">Tipo de Entrada</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['tipo_entrada'] ?? '-') ?></p>
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
                                <?php
                                $nomeEstado = $equipamento['nome_estado'];
                                if ($nomeEstado == 'Ativo') echo '<span class="badge bg-success">Ativo</span>';
                                elseif ($nomeEstado == 'Em manutenção') echo '<span class="badge bg-warning text-dark">Em manutenção</span>';
                                elseif ($nomeEstado == 'Inativo') echo '<span class="badge bg-secondary">Inativo</span>';
                                elseif ($nomeEstado == 'Em calibração') echo '<span class="badge bg-info text-dark">Em calibração</span>';
                                ?>
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

                                                <?php $doc = $docsPorTipo['Fatura de Aquisição']; ?>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Fatura de Aquisição
                                                    </span>
                                                    <a href="<?= htmlspecialchars($doc['caminho_ficheiro']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-eye me-1"></i>
                                                        Abrir PDF
                                                    </a>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row">
                                                    <div class="col-12 mb-2">
                                                        <small class="text-muted d-block">Nome do Documento</small>
                                                        <span class="fw-semibold"><?= htmlspecialchars($doc['nome_documento']) ?></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">Data do Documento</small>
                                                        <span class="fw-semibold">
                                                            <?= $doc['data_documento'] ? date('d/m/Y', strtotime($doc['data_documento'])) : '-' ?>
                                                        </span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">Data de Validade</small>
                                                        <span class="fw-semibold">
                                                            <?= $doc['data_validade'] ? date('d/m/Y', strtotime($doc['data_validade'])) : 'Sem validade' ?>
                                                        </span>
                                                    </div>
                                                </div>

                                            </div>

                                            <!-- CONTRATO DE AQUISIÇÃO -->

                                            <div class="border rounded-3 p-3">

                                                <?php $doc = $docsPorTipo['Contrato de Aquisição']; ?>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Contrato de Aquisição
                                                    </span>
                                                    <a href="<?= htmlspecialchars($doc['caminho_ficheiro']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-eye me-1"></i>
                                                        Abrir PDF
                                                    </a>
                                                </div>
                                                <hr class="my-2">
                                                <div class="row">
                                                    <div class="col-12 mb-2">
                                                        <small class="text-muted d-block">Nome do Documento</small>
                                                        <span class="fw-semibold"><?= htmlspecialchars($doc['nome_documento']) ?></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">Data do Documento</small>
                                                        <span class="fw-semibold">
                                                            <?= $doc['data_documento'] ? date('d/m/Y', strtotime($doc['data_documento'])) : '-' ?>
                                                        </span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <small class="text-muted d-block">Data de Validade</small>
                                                        <span class="fw-semibold">
                                                            <?= $doc['data_validade'] ? date('d/m/Y', strtotime($doc['data_validade'])) : 'Sem validade' ?>
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
                        <?php if (empty($acessorios) && empty($consumiveis)): ?>
                            <div class="alert alert-light border text-center">
                                <i class="fa-solid fa-circle-info me-2"></i>
                                Não existem acessórios nem consumíveis associados a este equipamento.
                            </div>
                        <?php endif; ?>

                        <div class="accordion" id="accordionAcessoriosConsumiveis">

                            <!-- ACESSÓRIOS -->
                            <div class="accordion-item border rounded-3 mb-3">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseAcessorios">
                                        Acessórios Associados (<?= count($acessorios) ?>)
                                    </button>
                                </h2>
                                <div id="collapseAcessorios" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionAcessoriosConsumiveis">
                                    <div class="accordion-body">
                                        <?php if (empty($acessorios)): ?>
                                            <p class="text-muted">Nenhum acessório associado.</p>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>Nome</th>
                                                            <th>Quantidade</th>
                                                            <th>Estado
                                                                <button type="button" class="btn btn-sm border-0 p-0 ms-1"
                                                                    data-bs-toggle="popover" data-bs-trigger="hover focus"
                                                                    data-bs-html="true" title="Estados dos Equipamentos"
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
                                                        <?php foreach ($acessorios as $acessorio): ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($acessorio['nome']) ?></td>
                                                                <td><?= htmlspecialchars($acessorio['quantidade']) ?></td>
                                                                <td>
                                                                    <?php
                                                                    $estado = $acessorio['nome_estado'];
                                                                    if ($estado == 'Ativo') echo '<span class="badge bg-success">Ativo</span>';
                                                                    elseif ($estado == 'Em manutenção') echo '<span class="badge bg-warning text-dark">Em manutenção</span>';
                                                                    elseif ($estado == 'Inativo') echo '<span class="badge bg-secondary">Inativo</span>';
                                                                    elseif ($estado == 'Em calibração') echo '<span class="badge bg-info text-dark">Em calibração</span>';
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- CONSUMÍVEIS -->
                            <div class="accordion-item border rounded-3">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseConsumiveis">
                                        Consumíveis Associados (<?= count($consumiveis) ?>)
                                    </button>
                                </h2>
                                <div id="collapseConsumiveis" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionAcessoriosConsumiveis">
                                    <div class="accordion-body">
                                        <?php if (empty($consumiveis)): ?>
                                            <p class="text-muted">Nenhum consumível associado.</p>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>Nome</th>
                                                            <th>Quantidade</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($consumiveis as $consumivel): ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($consumivel['nome']) ?></td>
                                                                <td><?= htmlspecialchars($consumivel['quantidade']) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php endif; ?>
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
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['codigo_localizacao'] ?? '-') ?></p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Edifício</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['edificio'] ?? '-') ?></p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Piso</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['piso'] ?? '-') ?></p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Serviço / Departamento</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['servico_departamento'] ?? '-') ?></p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Sala / Gabinete</p>
                                <p class="fw-semibold mb-0"><?= htmlspecialchars($equipamento['sala_gabinete'] ?? '-') ?></p>
                            </div>

                        </div>

                    </div>
                    <!-- FORNECEDOR -->
                    <div class="tab-pane fade" id="fornecedor">

                        <div class="accordion" id="accordionFornecedores">

                            <?php if (empty($fornecedores)): ?>
                                <div class="alert alert-light border text-center">
                                    <i class="fa-solid fa-circle-info me-2"></i>
                                    Não existem fornecedores associados a este equipamento.
                                </div>
                            <?php else: ?>
                                <?php foreach ($fornecedores as $i => $forn): ?>
                                    <div class="accordion-item border rounded-3 mb-3">

                                        <h2 class="accordion-header" id="fornecedorHeading<?= $i ?>">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#fornecedorCollapse<?= $i ?>">

                                                <div class="w-100 d-flex justify-content-between align-items-center pe-3">
                                                    <div>
                                                        <strong><?= htmlspecialchars($forn['nome_empresa']) ?></strong>
                                                    </div>
                                                    <div class="text-muted small">
                                                        <?= htmlspecialchars($forn['codigo_fornecedor']) ?> • <?= htmlspecialchars($forn['tipo_fornecedor']) ?>
                                                    </div>
                                                </div>

                                            </button>
                                        </h2>

                                        <div id="fornecedorCollapse<?= $i ?>" class="accordion-collapse collapse"
                                            data-bs-parent="#accordionFornecedores">

                                            <div class="accordion-body">
                                                <div class="row g-4">

                                                    <div class="col-md-4">
                                                        <p class="text-muted mb-1">Código Interno</p>
                                                        <p class="fw-semibold mb-0"><?= htmlspecialchars($forn['codigo_fornecedor']) ?></p>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <p class="text-muted mb-1">Tipo de Fornecedor</p>
                                                        <p class="fw-semibold mb-0"><?= htmlspecialchars($forn['tipo_fornecedor']) ?></p>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <p class="text-muted mb-1">NIF</p>
                                                        <p class="fw-semibold mb-0"><?= htmlspecialchars($forn['nif']) ?></p>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <p class="text-muted mb-1">Email</p>
                                                        <p class="fw-semibold mb-0"><?= htmlspecialchars($forn['email']) ?></p>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <p class="text-muted mb-1">Telefone</p>
                                                        <p class="fw-semibold mb-0"><?= htmlspecialchars($forn['telefone']) ?></p>
                                                    </div>

                                                    <div class="col-12">
                                                        <p class="text-muted mb-1">Morada</p>
                                                        <p class="fw-semibold mb-0"><?= htmlspecialchars($forn['morada']) ?></p>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <p class="text-muted mb-1">Pessoa de Contacto</p>
                                                        <p class="fw-semibold mb-0"><?= htmlspecialchars($forn['pessoa_contacto'] ?? '-') ?></p>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <p class="text-muted mb-1">Contacto da Pessoa</p>
                                                        <p class="fw-semibold mb-0"><?= htmlspecialchars($forn['telefone_contacto'] ?? '-') ?></p>
                                                    </div>

                                                    <div class="col-12">
                                                        <p class="text-muted mb-1">Website</p>
                                                        <a href="<?= htmlspecialchars($forn['website'] ?? '#') ?>"
                                                            target="_blank" class="fw-semibold">
                                                            <?= htmlspecialchars($forn['website'] ?? '-') ?>
                                                        </a>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </div>

                    </div>

                    <!-- GARANTIAS e CONTRATOS -->
                    <div class="tab-pane fade" id="garantias">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Garantia Associada</p>
                                <p class="fw-semibold mb-0">
                                    <?= $equipamento['tem_garantia'] ? 'Sim' : 'Não' ?>
                                </p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-muted mb-1">Contrato Associado</p>
                                <p class="fw-semibold mb-0">
                                    <?= $equipamento['tem_contrato'] ? 'Sim' : 'Não' ?>
                                </p>
                            </div>

                            <?php if ($contrato): ?>
                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Tipo de Contrato</p>
                                    <p class="fw-semibold mb-0"><?= htmlspecialchars($contrato['tipo_contrato']) ?></p>
                                </div>

                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Entidade Responsável</p>
                                    <p class="fw-semibold mb-0"><?= htmlspecialchars($contrato['nome_fornecedor'] ?? $contrato['entidade_responsavel'] ?? '-') ?></p>
                                </div>

                                <div class="col-md-6">
                                    <p class="text-muted mb-1">Periodicidade</p>
                                    <p class="fw-semibold mb-0"><?= htmlspecialchars($contrato['periodicidade'] ?? '-') ?></p>
                                </div>
                            <?php endif; ?>

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
                                            <?php if (!empty($docsPorTipo['Certificado de Garantia'])): ?>
                                                <?php $doc = $docsPorTipo['Certificado de Garantia']; ?>
                                                <div class="border rounded-3 p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span>
                                                            <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                            Certificado de Garantia
                                                        </span>
                                                        <a href="<?= htmlspecialchars($doc['caminho_ficheiro']) ?>"
                                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fa-solid fa-eye me-1"></i>
                                                            Abrir PDF
                                                        </a>
                                                    </div>
                                                    <hr class="my-2">
                                                    <div class="row">
                                                        <div class="col-12 mb-2">
                                                            <small class="text-muted d-block">Nome do Documento</small>
                                                            <span class="fw-semibold"><?= htmlspecialchars($doc['nome_documento']) ?></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block">Data do Documento</small>
                                                            <span class="fw-semibold">
                                                                <?= $doc['data_documento'] ? date('d/m/Y', strtotime($doc['data_documento'])) : '-' ?>
                                                            </span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block">Data de Validade</small>
                                                            <span class="fw-semibold">
                                                                <?= $doc['data_validade'] ? date('d/m/Y', strtotime($doc['data_validade'])) : 'Sem validade' ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- CONTRATO DE MANUTENÇÃO -->
                                            <?php if (!empty($docsPorTipo['Contrato de Manutenção'])): ?>
                                                <?php $doc = $docsPorTipo['Contrato de Manutenção']; ?>
                                                <div class="border rounded-3 p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span>
                                                            <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                            Contrato de Manutenção
                                                        </span>
                                                        <a href="<?= htmlspecialchars($doc['caminho_ficheiro']) ?>"
                                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fa-solid fa-eye me-1"></i>
                                                            Abrir PDF
                                                        </a>
                                                    </div>
                                                    <hr class="my-2">
                                                    <div class="row">
                                                        <div class="col-12 mb-2">
                                                            <small class="text-muted d-block">Nome do Documento</small>
                                                            <span class="fw-semibold"><?= htmlspecialchars($doc['nome_documento']) ?></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block">Data do Documento</small>
                                                            <span class="fw-semibold">
                                                                <?= $doc['data_documento'] ? date('d/m/Y', strtotime($doc['data_documento'])) : '-' ?>
                                                            </span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block">Data de Validade</small>
                                                            <span class="fw-semibold">
                                                                <?= $doc['data_validade'] ? date('d/m/Y', strtotime($doc['data_validade'])) : 'Sem validade' ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- CERTIFICADO DE CALIBRAÇÃO -->
                                            <?php if (!empty($docsPorTipo['Certificado de Calibração'])): ?>
                                                <?php $doc = $docsPorTipo['Certificado de Calibração']; ?>
                                                <div class="border rounded-3 p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span>
                                                            <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                            Certificado de Calibração
                                                        </span>
                                                        <a href="<?= htmlspecialchars($doc['caminho_ficheiro']) ?>"
                                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fa-solid fa-eye me-1"></i>
                                                            Abrir PDF
                                                        </a>
                                                    </div>
                                                    <hr class="my-2">
                                                    <div class="row">
                                                        <div class="col-12 mb-2">
                                                            <small class="text-muted d-block">Nome do Documento</small>
                                                            <span class="fw-semibold"><?= htmlspecialchars($doc['nome_documento']) ?></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block">Data do Documento</small>
                                                            <span class="fw-semibold">
                                                                <?= $doc['data_documento'] ? date('d/m/Y', strtotime($doc['data_documento'])) : '-' ?>
                                                            </span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block">Data de Validade</small>
                                                            <span class="fw-semibold">
                                                                <?= $doc['data_validade'] ? date('d/m/Y', strtotime($doc['data_validade'])) : 'Não aplicável' ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <!-- RELATÓRIO DE CALIBRAÇÃO -->
                                            <?php if (!empty($docsPorTipo['Relatório de Calibração'])): ?>
                                                <?php $doc = $docsPorTipo['Relatório de Calibração']; ?>
                                                <div class="border rounded-3 p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span>
                                                            <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                            Relatório de Calibração
                                                        </span>
                                                        <a href="<?= htmlspecialchars($doc['caminho_ficheiro']) ?>"
                                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fa-solid fa-eye me-1"></i>
                                                            Abrir PDF
                                                        </a>
                                                    </div>
                                                    <hr class="my-2">
                                                    <div class="row">
                                                        <div class="col-12 mb-2">
                                                            <small class="text-muted d-block">Nome do Documento</small>
                                                            <span class="fw-semibold"><?= htmlspecialchars($doc['nome_documento']) ?></span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block">Data do Documento</small>
                                                            <span class="fw-semibold">
                                                                <?= $doc['data_documento'] ? date('d/m/Y', strtotime($doc['data_documento'])) : '-' ?>
                                                            </span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <small class="text-muted d-block">Data de Validade</small>
                                                            <span class="fw-semibold">
                                                                <?= $doc['data_validade'] ? date('d/m/Y', strtotime($doc['data_validade'])) : 'Não aplicável' ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

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
                                        data-bs-target="#collapseDoc2Tecnica">
                                        Documentação Técnica
                                    </button>
                                </h2>
                                <div id="collapseDoc2Tecnica" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionDocumentacaoGeral">
                                    <div class="accordion-body">
                                        <div class="d-flex flex-column gap-2">

                                            <?php if (!empty($docsPorTipo['Manual de Utilização'])): ?>
                                                <div class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Manual de Utilização
                                                    </span>
                                                    <a href="<?= htmlspecialchars($docsPorTipo['Manual de Utilização']['caminho_ficheiro']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-eye me-1"></i> Abrir PDF
                                                    </a>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($docsPorTipo['Manual Técnico'])): ?>
                                                <div class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Manual Técnico
                                                    </span>
                                                    <a href="<?= htmlspecialchars($docsPorTipo['Manual Técnico']['caminho_ficheiro']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-eye me-1"></i> Abrir PDF
                                                    </a>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (empty($docsPorTipo['Manual de Utilização']) && empty($docsPorTipo['Manual Técnico'])): ?>
                                                <p class="text-muted mb-0">Nenhum documento técnico associado.</p>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DOCUMENTAÇÃO DE AQUISIÇÃO -->
                            <div class="accordion-item border rounded-3 mb-3">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseDoc2Aquisicao">
                                        Documentação de Aquisição
                                    </button>
                                </h2>
                                <div id="collapseDoc2Aquisicao" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionDocumentacaoGeral">
                                    <div class="accordion-body">
                                        <div class="d-flex flex-column gap-2">

                                            <?php if (!empty($docsPorTipo['Fatura de Aquisição'])): ?>
                                                <div class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Fatura de Aquisição
                                                    </span>
                                                    <a href="<?= htmlspecialchars($docsPorTipo['Fatura de Aquisição']['caminho_ficheiro']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-eye me-1"></i> Abrir PDF
                                                    </a>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($docsPorTipo['Contrato de Aquisição'])): ?>
                                                <div class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
                                                    <span>
                                                        <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                        Contrato de Aquisição
                                                    </span>
                                                    <a href="<?= htmlspecialchars($docsPorTipo['Contrato de Aquisição']['caminho_ficheiro']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-eye me-1"></i> Abrir PDF
                                                    </a>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (empty($docsPorTipo['Fatura de Aquisição']) && empty($docsPorTipo['Contrato de Aquisição'])): ?>
                                                <p class="text-muted mb-0">Nenhum documento de aquisição associado.</p>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- GARANTIAS E CONTRATOS -->
                            <div class="accordion-item border rounded-3 mb-3">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseDoc2Garantias">
                                        Garantias e Contratos
                                    </button>
                                </h2>
                                <div id="collapseDoc2Garantias" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionDocumentacaoGeral">
                                    <div class="accordion-body">
                                        <div class="d-flex flex-column gap-2">

                                            <?php if (!empty($docsPorTipo['Certificado de Garantia'])): ?>
                                                <div class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
                                                    <span><i class="fa-solid fa-file-pdf text-danger me-2"></i>Certificado de Garantia</span>
                                                    <a href="<?= htmlspecialchars($docsPorTipo['Certificado de Garantia']['caminho_ficheiro']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-eye me-1"></i> Abrir PDF
                                                    </a>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($docsPorTipo['Contrato de Manutenção'])): ?>
                                                <div class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
                                                    <span><i class="fa-solid fa-file-pdf text-danger me-2"></i>Contrato de Manutenção</span>
                                                    <a href="<?= htmlspecialchars($docsPorTipo['Contrato de Manutenção']['caminho_ficheiro']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-eye me-1"></i> Abrir PDF
                                                    </a>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($docsPorTipo['Certificado de Calibração'])): ?>
                                                <div class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
                                                    <span><i class="fa-solid fa-file-pdf text-danger me-2"></i>Certificado de Calibração</span>
                                                    <a href="<?= htmlspecialchars($docsPorTipo['Certificado de Calibração']['caminho_ficheiro']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-eye me-1"></i> Abrir PDF
                                                    </a>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($docsPorTipo['Relatório de Calibração'])): ?>
                                                <div class="d-flex justify-content-between align-items-center border rounded-3 px-3 py-2">
                                                    <span><i class="fa-solid fa-file-pdf text-danger me-2"></i>Relatório de Calibração</span>
                                                    <a href="<?= htmlspecialchars($docsPorTipo['Relatório de Calibração']['caminho_ficheiro']) ?>"
                                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fa-solid fa-eye me-1"></i> Abrir PDF
                                                    </a>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (empty($docsPorTipo['Certificado de Garantia']) && empty($docsPorTipo['Contrato de Manutenção']) && empty($docsPorTipo['Certificado de Calibração']) && empty($docsPorTipo['Relatório de Calibração'])): ?>
                                                <p class="text-muted mb-0">Nenhum documento de garantia ou contrato associado.</p>
                                            <?php endif; ?>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DOCUMENTAÇÃO ADICIONAL -->
                            <div class="accordion-item border rounded-3">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseDoc2Adicional">
                                        Documentação Adicional
                                    </button>
                                </h2>
                                <div id="collapseDoc2Adicional" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionDocumentacaoGeral">
                                    <div class="accordion-body">
                                        <?php
                                        $tiposAdicionais = ['Declaração de Conformidade', 'Relatório Técnico', 'Manual de Serviço', 'Outro'];
                                        $temAdicional = false;
                                        foreach ($tiposAdicionais as $tipo) {
                                            if (!empty($docsPorTipo[$tipo])) {
                                                $temAdicional = true;
                                                break;
                                            }
                                        }
                                        ?>
                                        <?php if (!$temAdicional): ?>
                                            <p class="text-muted mb-0">Nenhuma documentação adicional associada.</p>
                                        <?php else: ?>
                                            <div class="d-flex flex-column gap-3">
                                                <?php foreach ($tiposAdicionais as $tipo): ?>
                                                    <?php if (!empty($docsPorTipo[$tipo])): ?>
                                                        <?php $doc = $docsPorTipo[$tipo]; ?>
                                                        <div class="border rounded-3 p-3">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span>
                                                                    <i class="fa-solid fa-file-pdf text-danger me-2"></i>
                                                                    <?= htmlspecialchars($tipo) ?>
                                                                </span>
                                                                <a href="<?= htmlspecialchars($doc['caminho_ficheiro']) ?>"
                                                                    target="_blank" class="btn btn-sm btn-outline-primary">
                                                                    <i class="fa-solid fa-eye me-1"></i> Abrir PDF
                                                                </a>
                                                            </div>
                                                            <hr class="my-2">
                                                            <div class="row">
                                                                <div class="col-12 mb-2">
                                                                    <small class="text-muted d-block">Nome do Documento</small>
                                                                    <span class="fw-semibold"><?= htmlspecialchars($doc['nome_documento']) ?></span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <small class="text-muted d-block">Data do Documento</small>
                                                                    <span class="fw-semibold">
                                                                        <?= $doc['data_documento'] ? date('d/m/Y', strtotime($doc['data_documento'])) : '-' ?>
                                                                    </span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <small class="text-muted d-block">Data de Validade</small>
                                                                    <span class="fw-semibold">
                                                                        <?= $doc['data_validade'] ? date('d/m/Y', strtotime($doc['data_validade'])) : 'Sem validade' ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                   
                    <!-- OBSERVAÇÕES -->
                    <div class="tab-pane fade" id="observacoes">

                        <p class="text-muted mb-0">
                            <?= !empty($equipamento['observacoes'])
                                ? htmlspecialchars($equipamento['observacoes'])
                                : 'Sem observações registadas.' ?>
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

<?php include '../../includes/footer.php'; ?>