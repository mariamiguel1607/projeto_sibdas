<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

$erros = [];
$erro_sistema = "";

try {
    $ligacao = ligar_bd();

    $categorias   = $ligacao->query("SELECT * FROM categorias ORDER BY nome_categoria")->fetchAll(PDO::FETCH_OBJ);
    $estados      = $ligacao->query("SELECT * FROM estados ORDER BY nome_estado")->fetchAll(PDO::FETCH_OBJ);
    $localizacoes = $ligacao->query("SELECT * FROM localizacoes ORDER BY codigo_localizacao")->fetchAll(PDO::FETCH_OBJ);
    $fornecedores = $ligacao->query("SELECT * FROM fornecedores ORDER BY nome_empresa")->fetchAll(PDO::FETCH_OBJ);
    $ultimo = $ligacao->query(" SELECT codigo_interno FROM equipamentos ORDER BY id DESC LIMIT 1 ")->fetchColumn();

    if ($ultimo) {
        // Extrai o número do último código (ex: EQ-0025 → 25)
        $numero = (int) preg_replace('/[^0-9]/', '', $ultimo);
        $proximo_numero = $numero + 1;
    } else {
        // Se não houver nenhum equipamento ainda
        $proximo_numero = 1;
    }

    // Formata com zeros à esquerda (ex: 1 → EQ-0001, 26 → EQ-0026)
    $proximo_codigo = 'EQ-' . str_pad($proximo_numero, 4, '0', STR_PAD_LEFT);
} catch (PDOException $err) {
    $categorias = $estados = $localizacoes = $fornecedores = [];
    $erro_sistema = "Erro ao carregar dados.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. RECOLHER DADOS
    $codigo_interno       = $_POST['codigo_interno']       ?? '';
    $designacao           = $_POST['designacao']           ?? '';
    $id_categoria         = $_POST['id_categoria']         ?? '';
    $fabricante           = $_POST['fabricante']           ?? '';
    $marca                = $_POST['marca']                ?? '';
    $modelo               = $_POST['modelo']               ?? '';
    $num_serie            = $_POST['num_serie']            ?? '';
    $ano_fabrico          = $_POST['ano_fabrico']          ?? '';
    $criticidade          = $_POST['criticidade']          ?? '';
    $data_aquisicao       = $_POST['data_aquisicao']       ?? '';
    $custo_aquisicao      = $_POST['custo_aquisicao']      ?? '';
    $tipo_entrada         = $_POST['tipo_entrada']         ?? '';
    $id_estado            = $_POST['id_estado']            ?? '';
    $acessorio_nome       = $_POST['acessorio_nome']       ?? [];
    $acessorio_quantidade = $_POST['acessorio_quantidade'] ?? [];
    $acessorio_estado     = $_POST['acessorio_estado']     ?? [];
    $consumivel_nome      = $_POST['consumivel_nome']       ?? [];
    $consumivel_quantidade = $_POST['consumivel_quantidade'] ?? [];
    $id_localizacao       = $_POST['id_localizacao']       ?? '';
    $ids_fornecedor = $_POST['id_fornecedor'] ?? [];
    $tipos_relacao  = $_POST['tipo_relacao']  ?? [];
    $tipo_contrato        = $_POST['tipo_contrato']        ?? '';
    $entidade_responsavel = $_POST['entidade_responsavel'] ?? '';
    $periodicidade        = $_POST['periodicidade']        ?? '';
    $observacoes          = $_POST['observacoes']          ?? '';
    $tem_garantia = $_POST['tem_garantia'] ?? '';
    $tem_contrato = $_POST['tem_contrato'] ?? '';

    // Documentos fixos — cada um tem nome, ficheiro, data e validade
    $documentos_fixos = [
        ['nome' => $_POST['nome_documento_manual_utilizacao']   ?? '', 'ficheiro' => $_FILES['manual_utilizacao']   ?? null, 'data' => $_POST['manual_utilizacao_data']   ?? '', 'validade' => $_POST['manual_utilizacao_validade']   ?? '', 'id_tipo' => 1],
        ['nome' => $_POST['nome_documento_manual_tecnico']      ?? '', 'ficheiro' => $_FILES['manual_tecnico']      ?? null, 'data' => $_POST['manual_tecnico_data']      ?? '', 'validade' => $_POST['manual_tecnico_validade']      ?? '', 'id_tipo' => 2],
        ['nome' => $_POST['nome_documento_fatura_aquisicao']    ?? '', 'ficheiro' => $_FILES['fatura_aquisicao']    ?? null, 'data' => $_POST['fatura_aquisicao_data']    ?? '', 'validade' => $_POST['fatura_aquisicao_validade']    ?? '', 'id_tipo' => 3],
        ['nome' => $_POST['nome_documento_contrato_aquisicao']  ?? '', 'ficheiro' => $_FILES['contrato_aquisicao']  ?? null, 'data' => $_POST['contrato_aquisicao_data']  ?? '', 'validade' => $_POST['contrato_aquisicao_validade']  ?? '', 'id_tipo' => 4],
        ['nome' => $_POST['nome_documento_certificado_garantia'] ?? '', 'ficheiro' => $_FILES['certificado_garantia'] ?? null, 'data' => $_POST['certificado_garantia_data'] ?? '', 'validade' => $_POST['certificado_garantia_validade'] ?? '', 'id_tipo' => 5],
        ['nome' => $_POST['nome_documento_contrato_manutencao'] ?? '', 'ficheiro' => $_FILES['contrato_manutencao'] ?? null, 'data' => $_POST['contrato_manutencao_data'] ?? '', 'validade' => $_POST['contrato_manutencao_validade'] ?? '', 'id_tipo' => 6],
        ['nome' => $_POST['nome_documento_certificado_calibracao'] ?? '', 'ficheiro' => $_FILES['certificado_calibracao'] ?? null, 'data' => $_POST['certificado_calibracao_data'] ?? '', 'validade' => $_POST['certificado_calibracao_validade'] ?? '', 'id_tipo' => 7],
        ['nome' => $_POST['nome_documento_relatorio_calibracao'] ?? '', 'ficheiro' => $_FILES['relatorio_calibracao'] ?? null, 'data' => $_POST['relatorio_calibracao_data'] ?? '', 'validade' => $_POST['relatorio_calibracao_validade'] ?? '', 'id_tipo' => 8],
    ];

    // Documentos adicionais
    $nomes_adicionais    = $_POST['nome_documento_adicional']      ?? [];
    $ficheiros_adicionais = $_FILES['ficheiro_documento_adicional'] ?? [];
    $datas_adicionais    = $_POST['data_documento_adicional']      ?? [];
    $validades_adicionais = $_POST['validade_documento_adicional'] ?? [];

    // 2. VALIDAR
    $designacao      = trim($designacao);
    $ano_fabrico     = trim($ano_fabrico);
    $data_aquisicao  = trim($data_aquisicao);
    $custo_aquisicao = trim($custo_aquisicao);


    if (empty($designacao))      $erros[] = "A designação é obrigatória.";
    if (empty($id_categoria))    $erros[] = "A categoria é obrigatória.";
    if (empty($id_estado))       $erros[] = "O estado é obrigatório.";
    if (empty($id_localizacao))  $erros[] = "A localização é obrigatória.";

    if (!empty($ano_fabrico)) {
        if (!preg_match('/^\d{4}$/', $ano_fabrico) || (int)$ano_fabrico < 1900 || (int)$ano_fabrico > (int)date('Y')) {
            $erros[] = "O ano de fabrico é inválido.";
        }
    }

    if (!empty($data_aquisicao)) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_aquisicao)) {
            $erros[] = "Formato de data de aquisição inválido.";
        } else {
            $partes = explode('-', $data_aquisicao);
            if (!checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0])) {
                $erros[] = "Data de aquisição inválida.";
            }
        }
    }

    if (!empty($custo_aquisicao) && !is_numeric($custo_aquisicao)) {
        $erros[] = "O custo de aquisição deve ser um valor numérico.";
    }

    $fornecedor_valido = false;
    foreach ($ids_fornecedor as $i => $id_forn) {
        if (!empty($id_forn) && !empty($tipos_relacao[$i])) {
            $fornecedor_valido = true;
            break;
        }
    }
    if (!$fornecedor_valido) {
        $erros[] = "É obrigatório associar pelo menos um fornecedor com tipo de relação.";
    }

    // Documentos sempre obrigatórios
    $docs_obrigatorios = [
        'manual_utilizacao'       => ['nome' => 'Manual de Utilização',      'id_tipo' => 1],
        'manual_tecnico'          => ['nome' => 'Manual Técnico',            'id_tipo' => 2],
        'fatura_aquisicao'        => ['nome' => 'Fatura de Aquisição',       'id_tipo' => 3],
        'contrato_aquisicao'      => ['nome' => 'Contrato de Aquisição',     'id_tipo' => 4],
        'certificado_calibracao'  => ['nome' => 'Certificado de Calibração', 'id_tipo' => 7],
        'relatorio_calibracao'    => ['nome' => 'Relatório de Calibração',   'id_tipo' => 8],
    ];

    foreach ($docs_obrigatorios as $chave => $info) {
        $doc = null;
        foreach ($documentos_fixos as $d) {
            if ($d['id_tipo'] === $info['id_tipo']) {
                $doc = $d;
                break;
            }
        }
        $sem_nome     = empty($doc['nome']);
        $sem_ficheiro = empty($doc['ficheiro']) || $doc['ficheiro']['error'] === UPLOAD_ERR_NO_FILE;
        if ($sem_nome && $sem_ficheiro) {
            $erros[] = $info['nome'] . " é obrigatório.";
        }
    }

    // Condicionais
    if ($tem_garantia === 'sim') {
        $doc_garantia = null;
        foreach ($documentos_fixos as $d) {
            if ($d['id_tipo'] === 5) {
                $doc_garantia = $d;
                break;
            }
        }
        if ((empty($doc_garantia['nome'])) && ($doc_garantia['ficheiro']['error'] === UPLOAD_ERR_NO_FILE)) {
            $erros[] = "O Certificado de Garantia é obrigatório quando tem garantia associada.";
        }
    }

    if ($tem_contrato === 'sim') {
        $doc_contrato = null;
        foreach ($documentos_fixos as $d) {
            if ($d['id_tipo'] === 6) {
                $doc_contrato = $d;
                break;
            }
        }
        if ((empty($doc_contrato['nome'])) && ($doc_contrato['ficheiro']['error'] === UPLOAD_ERR_NO_FILE)) {
            $erros[] = "O Contrato de Manutenção é obrigatório quando tem contrato associado.";
        }
    }

    // 3. NORMALIZAR
    $designacao           = ucwords(strtolower($designacao));
    $fabricante           = ucwords(strtolower(trim($fabricante)));
    $marca                = ucwords(strtolower(trim($marca)));
    $modelo               = trim($modelo);
    $num_serie            = trim($num_serie);
    $entidade_responsavel = ucwords(strtolower(trim($entidade_responsavel)));
    $observacoes          = trim($observacoes);

    // 4. GUARDAR NA BD
    if (empty($erros)) {
        try {
            $ligacao = ligar_bd();

            // INSERT equipamento
            $sql = "INSERT INTO equipamentos (
                    codigo_interno, designacao, id_categoria,
                    fabricante, marca, modelo, num_serie, ano_fabrico,
                    criticidade, data_aquisicao, custo_aquisicao,
                    tipo_entrada, id_estado, id_localizacao,
                    observacoes
                ) VALUES (
                    :codigo_interno, :designacao, :id_categoria,
                    :fabricante, :marca, :modelo, :num_serie, :ano_fabrico,
                    :criticidade, :data_aquisicao, :custo_aquisicao,
                    :tipo_entrada, :id_estado, :id_localizacao,
                    :observacoes
                )";
            $stmt = $ligacao->prepare($sql);
            $stmt->execute([
                ':codigo_interno'  => $codigo_interno,
                ':designacao'      => $designacao,
                ':id_categoria'    => $id_categoria    ?: null,
                ':fabricante'      => $fabricante      ?: null,
                ':marca'           => $marca           ?: null,
                ':modelo'          => $modelo          ?: null,
                ':num_serie'       => $num_serie       ?: null,
                ':ano_fabrico'     => $ano_fabrico     ?: null,
                ':criticidade'     => $criticidade     ?: null,
                ':data_aquisicao'  => $data_aquisicao  ?: null,
                ':custo_aquisicao' => $custo_aquisicao ?: null,
                ':tipo_entrada'    => $tipo_entrada    ?: null,
                ':id_estado'       => $id_estado       ?: null,
                ':id_localizacao'  => $id_localizacao  ?: null,
                ':observacoes'     => $observacoes     ?: null,
            ]);

            $id_equipamento = $ligacao->lastInsertId();

            // INSERT fornecedores
            $sql_forn = "INSERT INTO equipamentos_fornecedores (id_equipamento, id_fornecedor, tipo_relacao)
                     VALUES (:id_equipamento, :id_fornecedor, :tipo_relacao)";
            $stmt_forn = $ligacao->prepare($sql_forn);
            foreach ($ids_fornecedor as $i => $id_forn) {
                if (!empty($id_forn) && !empty($tipos_relacao[$i])) {
                    $stmt_forn->execute([
                        ':id_equipamento' => $id_equipamento,
                        ':id_fornecedor'  => $id_forn,
                        ':tipo_relacao'   => $tipos_relacao[$i],
                    ]);
                }
            }

            // Pasta de destino dos PDFs
            $pasta_uploads    = __DIR__ . '/../../../assets/uploads/documentos/';
            $caminho_relativo = BASE_URL . '/assets/uploads/documentos/';

            // Preparar query de documentos
            $sql_doc = "INSERT INTO documentacao (
                        nome_documento, data_documento, data_validade,
                        estado, caminho_ficheiro, id_tipo_documento, id_equipamento
                    ) VALUES (
                        :nome_documento, :data_documento, :data_validade,
                        :estado, :caminho_ficheiro, :id_tipo_documento, :id_equipamento
                    )";
            $stmt_doc = $ligacao->prepare($sql_doc);

            // variável para guardar o id do documento do contrato
            $id_doc_contrato = null;

            // Inserir documentos fixos
            foreach ($documentos_fixos as $doc) {
                if (empty($doc['nome']) && (empty($doc['ficheiro']) || $doc['ficheiro']['error'] === UPLOAD_ERR_NO_FILE)) {
                    continue;
                }

                $caminho = null;
                if (!empty($doc['ficheiro']) && $doc['ficheiro']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $resultado_upload = fazer_upload_pdf($doc['ficheiro'], $pasta_uploads);
                    if ($resultado_upload) {
                        $caminho = $caminho_relativo . $resultado_upload;
                    } elseif ($resultado_upload === false) {
                        $erro_sistema = "Erro no upload do ficheiro. Certifica-te que é um PDF.";
                        break;
                    }
                }

                $stmt_doc->execute([
                    ':nome_documento'    => $doc['nome']     ?: null,
                    ':data_documento'    => $doc['data']     ?: null,
                    ':data_validade'     => $doc['validade'] ?: null,
                    ':estado'            => 'Ativo',
                    ':caminho_ficheiro'  => $caminho,
                    ':id_tipo_documento' => $doc['id_tipo'],
                    ':id_equipamento'    => $id_equipamento,
                ]);

                // ← NOVO: se for o contrato de manutenção, guarda o id
                if ($doc['id_tipo'] === 6) {
                    $id_doc_contrato = $ligacao->lastInsertId();
                }
            }

            // Inserir documentos adicionais
            if (empty($erro_sistema)) {
                foreach ($nomes_adicionais as $i => $nome_add) {
                    if (empty($nome_add)) continue;

                    $caminho_add = null;
                    $ficheiro_add = [
                        'name'     => $ficheiros_adicionais['name'][$i]     ?? '',
                        'type'     => $ficheiros_adicionais['type'][$i]     ?? '',
                        'tmp_name' => $ficheiros_adicionais['tmp_name'][$i] ?? '',
                        'error'    => $ficheiros_adicionais['error'][$i]    ?? UPLOAD_ERR_NO_FILE,
                        'size'     => $ficheiros_adicionais['size'][$i]     ?? 0,
                    ];

                    if ($ficheiro_add['error'] !== UPLOAD_ERR_NO_FILE) {
                        $resultado_add = fazer_upload_pdf($ficheiro_add, $pasta_uploads);
                        if ($resultado_add) {
                            $caminho_add = $caminho_relativo . $resultado_add;
                        }
                    }

                    $stmt_doc->execute([
                        ':nome_documento'    => $nome_add,
                        ':data_documento'    => $datas_adicionais[$i]    ?: null,
                        ':data_validade'     => $validades_adicionais[$i] ?: null,
                        ':estado'            => 'Ativo',
                        ':caminho_ficheiro'  => $caminho_add,
                        ':id_tipo_documento' => 12,
                        ':id_equipamento'    => $id_equipamento,
                    ]);
                }
            }

            //  INSERT na tabela contratos (só se tiver contrato)
            if ($tem_contrato === 'sim' && !empty($id_doc_contrato)) {
                $sql_contrato = "INSERT INTO contratos (
                                tipo_contrato, periodicidade, entidade_responsavel,
                                observacoes, id_fornecedor, id_documento
                            ) VALUES (
                                :tipo_contrato, :periodicidade, :entidade_responsavel,
                                :observacoes, :id_fornecedor, :id_documento
                            )";
                $stmt_contrato = $ligacao->prepare($sql_contrato);
                $stmt_contrato->execute([
                    ':tipo_contrato'        => $tipo_contrato        ?: null,
                    ':periodicidade'        => $periodicidade         ?: null,
                    ':entidade_responsavel' => $entidade_responsavel  ?: null,
                    ':observacoes'          => $observacoes           ?: null,
                    ':id_fornecedor'        => $ids_fornecedor[0]     ?: null,
                    ':id_documento'         => $id_doc_contrato,
                ]);
            }

            // INSERT acessórios
            $sql_acessorio = "INSERT INTO acessorios (nome, quantidade, id_estado, id_equipamento)
                  VALUES (:nome, :quantidade, :id_estado, :id_equipamento)";
            $stmt_acessorio = $ligacao->prepare($sql_acessorio);

            foreach ($acessorio_nome as $i => $nome) {
                if (empty($nome)) continue;
                $stmt_acessorio->execute([
                    ':nome'          => $nome,
                    ':quantidade'    => $acessorio_quantidade[$i] ?: null,
                    ':id_estado'     => $acessorio_estado[$i]     ?: null,
                    ':id_equipamento' => $id_equipamento,
                ]);
            }

            // INSERT consumíveis
            $sql_consumivel = "INSERT INTO consumiveis (nome, quantidade, id_equipamento)
                   VALUES (:nome, :quantidade, :id_equipamento)";
            $stmt_consumivel = $ligacao->prepare($sql_consumivel);

            foreach ($consumivel_nome as $i => $nome) {
                if (empty($nome)) continue;
                $stmt_consumivel->execute([
                    ':nome'           => $nome,
                    ':quantidade'     => $consumivel_quantidade[$i] ?: null,
                    ':id_equipamento' => $id_equipamento,
                ]);
            }

            $ligacao = null;
            header('Location: equipamentos.php');
            exit;
        } catch (PDOException $err) {
            $erro_sistema = "Erro ao gravar os dados: " . $err->getMessage();
        }
    }
}
?>
<?php include '../../includes/header.php';
$paginaAtiva = 'equipamentos';
?>

<div class="private-layout">

    <?php include '../../includes/sidebar.php'; ?>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="private-main">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-1">Adicionar Equipamento</h1>
                <p class="text-muted mb-0">
                    Registo de um novo equipamento médico na plataforma.
                </p>
            </div>

            <a href="equipamentos.php" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Voltar
            </a>
        </div>

        <form action="#" method="post" novalidate enctype="multipart/form-data">
            <!-- TABS -->
            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-body p-4">

                    <ul class="nav nav-pills conteudos-tabs mb-4">

                        <li class="nav-item">
                            <button type="button" class="nav-link active" data-bs-toggle="pill"
                                data-bs-target="#dadosGeraisNovo">
                                Dados Gerais
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#aquisicaoNovo">
                                Aquisição
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#acessoriosNovo">
                                Acessórios e Consumíveis
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#localizacaoNovo">
                                Localização
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#fornecedorNovo">
                                Fornecedor Associado
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#garantiasNovo">
                                Garantias e Contratos
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#documentacaoNovo">
                                Documentação
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link" data-bs-toggle="pill"
                                data-bs-target="#observacoesNovo">
                                Observações
                            </button>
                        </li>

                    </ul>
                    <!-- Dados Gerais -->
                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="dadosGeraisNovo">
                            <!-- ALERTAS -->
                            <?php if (!empty($erros)): ?>
                                <div class="alert alert-danger mb-4">
                                    <h6 class="alert-heading mb-2">
                                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                                        Foram encontrados erros
                                    </h6>
                                    <ul class="mb-0">
                                        <?php foreach ($erros as $erro): ?>
                                            <li><?= htmlspecialchars($erro) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($erro_sistema)): ?>
                                <div class="alert alert-danger mb-4">
                                    <strong>Erro do sistema:</strong>
                                    <p><?= htmlspecialchars($erro_sistema) ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="row g-4">

                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Código Interno</label>
                                    <input type="text" class="form-control" name="codigo_interno"
                                        value="<?= htmlspecialchars($proximo_codigo ?? '') ?>"
                                        readonly>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Designação</label>
                                    <input type="text" class="form-control" name="designacao" value="<?= $_POST['designacao'] ?? '' ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Categoria</label>
                                    <select class="form-select" name="id_categoria">
                                        <option value="">Selecione uma categoria</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria->id ?>"
                                                <?= (($_POST['id_categoria'] ?? '') == $categoria->id) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($categoria->nome_categoria) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Fabricante</label>
                                    <input type="text" class="form-control" name="fabricante" value="<?= $_POST['fabricante'] ?? '' ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Marca</label>
                                    <input type="text" class="form-control" name="marca" value="<?= $_POST['marca'] ?? '' ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Modelo</label>
                                    <input type="text" class="form-control" name="modelo" value="<?= $_POST['modelo'] ?? '' ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Número de Série</label>
                                    <input type="text" class="form-control" name="num_serie" value="<?= $_POST['num_serie'] ?? '' ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Ano de Fabrico</label>
                                    <input type="number" class="form-control" name="ano_fabrico" value="<?= $_POST['ano_fabrico'] ?? '' ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Criticidade</label>
                                    <select class="form-select" name="criticidade">

                                        <option value="">Selecione a criticidade</option>
                                        <option value="Baixa"
                                            <?= (($_POST['criticidade'] ?? '') == 'Baixa') ? 'selected' : '' ?>>
                                            Baixa
                                        </option>
                                        <option value="Média"
                                            <?= (($_POST['criticidade'] ?? '') == 'Média') ? 'selected' : '' ?>>
                                            Média
                                        </option>
                                        <option value="Alta"
                                            <?= (($_POST['criticidade'] ?? '') == 'Alta') ? 'selected' : '' ?>>
                                            Alta
                                        </option>
                                        <option value="Suporte de Vida"
                                            <?= (($_POST['criticidade'] ?? '') == 'Suporte de Vida') ? 'selected' : '' ?>>
                                            Suporte de Vida
                                        </option>

                                    </select>
                                </div>

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

                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#modalManualUtilizacao">

                                            <i class="fa-solid fa-file-pdf me-2"></i>
                                            Adicionar Documento

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

                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#modalManualTecnico">

                                            <i class="fa-solid fa-file-pdf me-2"></i>
                                            Adicionar Documento

                                        </button>

                                    </div>

                                </div>

                            </div>


                            <!-- MODAL MANUAL DE UTILIZAÇÃO -->

                            <div class="modal fade" id="modalManualUtilizacao" tabindex="-1">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Manual de Utilização
                                            </h5>

                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

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

                                    </div>

                                </div>

                            </div>


                            <!-- MODAL MANUAL TÉCNICO -->

                            <div class="modal fade" id="modalManualTecnico" tabindex="-1">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <h5 class="modal-title">
                                                Manual Técnico
                                            </h5>

                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

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

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- AQUISIÇÃO -->
                        <div class="tab-pane fade" id="aquisicaoNovo">
                            <!-- ALERTAS -->
                            <?php if (!empty($erros)): ?>
                                <div class="alert alert-danger mb-4">
                                    <h6 class="alert-heading mb-2">
                                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                                        Foram encontrados erros
                                    </h6>
                                    <ul class="mb-0">
                                        <?php foreach ($erros as $erro): ?>
                                            <li><?= htmlspecialchars($erro) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($erro_sistema)): ?>
                                <div class="alert alert-danger mb-4">
                                    <strong>Erro do sistema:</strong>
                                    <p><?= htmlspecialchars($erro_sistema) ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="row g-4">

                                <div class="col-md-3">

                                    <label class="form-label fw-bold">
                                        Data de Aquisição
                                    </label>

                                    <input type="date" class="form-control" name="data_aquisicao" value="<?= $_POST['data_aquisicao'] ?? '' ?>">

                                </div>

                                <div class="col-md-3">

                                    <label class="form-label fw-bold">
                                        Custo de Aquisição (€)
                                    </label>

                                    <input type="number" class="form-control" name="custo_aquisicao" placeholder="0.00" value="<?= $_POST['custo_aquisicao'] ?? '' ?>">

                                </div>

                                <div class="col-md-3">

                                    <label class="form-label fw-bold">
                                        Tipo de Entrada
                                    </label>

                                    <select class="form-select" name="tipo_entrada">

                                        <option selected disabled>
                                            Selecionar tipo
                                        </option>

                                        <option>
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

                                <div class="col-md-6">
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
                                    <select class="form-select" name="id_estado">
                                        <option value="">Selecione uma estado</option>
                                        <?php foreach ($estados as $estado): ?>
                                            <option value="<?= $estado->id ?>"
                                                <?= (($_POST['id_estado'] ?? '') == $estado->id) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($estado->nome_estado) ?>
                                            </option>
                                        <?php endforeach; ?>
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
                                            Adicionar Documento

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
                                            Contrato associado à compra ou aquisição do equipamento.
                                        </p>

                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#modalContratoAquisicao">

                                            <i class="fa-solid fa-file-pdf me-2"></i>
                                            Adicionar Documento

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
                                                Fatura de Aquisição
                                            </h5>

                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

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
                                                Contrato de Aquisição
                                            </h5>

                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

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
                        <div class="tab-pane fade" id="acessoriosNovo">
                            <!-- ALERTAS -->
                            <?php if (!empty($erros)): ?>
                                <div class="alert alert-danger mb-4">
                                    <h6 class="alert-heading mb-2">
                                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                                        Foram encontrados erros
                                    </h6>
                                    <ul class="mb-0">
                                        <?php foreach ($erros as $erro): ?>
                                            <li><?= htmlspecialchars($erro) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($erro_sistema)): ?>
                                <div class="alert alert-danger mb-4">
                                    <strong>Erro do sistema:</strong>
                                    <p><?= htmlspecialchars($erro_sistema) ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="row g-4">

                                <!-- EXISTEM ACESSÓRIOS -->

                                <div class="col-md-6">

                                    <label class="form-label fw-bold">
                                        Existem acessórios associados ao equipamento?
                                    </label>

                                    <select class="form-select" id="temAcessorios">

                                        <option selected disabled>
                                            Selecionar opção
                                        </option>

                                        <option value="sim">
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

                                    <select class="form-select" id="temConsumiveis">

                                        <option selected disabled>
                                            Selecionar opção
                                        </option>

                                        <option value="sim">
                                            Sim
                                        </option>

                                        <option value="nao">
                                            Não
                                        </option>

                                    </select>

                                </div>

                            </div>

                            <!-- SECÇÃO ACESSÓRIOS -->

                            <div id="secaoAcessorios" style="display:none;">

                                <hr class="my-4">

                                <h5 class="fw-bold mb-3">
                                    Acessórios Associados
                                </h5>

                                <div id="listaAcessorios">

                                    <div class="row g-4 acessorio-item">

                                        <div class="col-md-6">

                                            <label class="form-label fw-bold">
                                                Nome do Acessório
                                            </label>

                                            <input type="text" class="form-control" name="acessorio_nome[]" value="<?= $_POST['acessorio_nome[]'] ?? '' ?>" placeholder="Ex: Sensor de Fluxo">

                                        </div>

                                        <div class="col-md-3">

                                            <label class="form-label fw-bold">
                                                Quantidade
                                            </label>

                                            <input type="number" class="form-control" name="acessorio_quantidade[]" value="<?= $_POST['acessorio_quantidade[]'] ?? '' ?>">

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
                                            <select class="form-select" name="acessorio_estado[]">
                                                <option value="">Selecione um estado</option>
                                                <?php foreach ($estados as $estado): ?>
                                                    <option value="<?= $estado->id ?>">
                                                        <?= htmlspecialchars($estado->nome_estado) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                        </div>

                                    </div>

                                </div>

                                <div class="mt-3">

                                    <button type="button" id="btnAdicionarAcessorio" class="btn btn-outline-primary">

                                        <i class="fa-solid fa-plus me-2"></i>
                                        Adicionar Acessório

                                    </button>

                                </div>

                            </div>

                            <!-- SECÇÃO CONSUMÍVEIS -->

                            <div id="secaoConsumiveis" style="display:none;">

                                <hr class="my-4">

                                <h5 class="fw-bold mb-3">
                                    Consumíveis Associados
                                </h5>

                                <div id="listaConsumiveis">

                                    <div class="row g-4 consumivel-item">

                                        <div class="col-md-8">

                                            <label class="form-label fw-bold">
                                                Nome do Consumível
                                            </label>

                                            <input type="text" class="form-control" name="consumivel_nome[]" value="<?= $_POST['consumivel_nome[]'] ?? '' ?>" placeholder="Ex: Filtro Bacteriano">

                                        </div>

                                        <div class="col-md-4">

                                            <label class="form-label fw-bold">
                                                Quantidade
                                            </label>

                                            <input type="number" class="form-control" name="consumivel_quantidade[]" value="<?= $_POST['consumivel_quantidade[]'] ?? '' ?>">

                                        </div>

                                    </div>

                                </div>

                                <div class="mt-3">

                                    <button type="button" id="btnAdicionarConsumivel" class="btn btn-outline-primary">

                                        <i class="fa-solid fa-plus me-2"></i>
                                        Adicionar Consumível

                                    </button>

                                </div>

                            </div>

                        </div>

                        <!-- Localização -->
                        <div class="tab-pane fade" id="localizacaoNovo">

                            <!-- ALERTAS -->
                            <?php if (!empty($erros)): ?>
                                <div class="alert alert-danger mb-4">
                                    <h6 class="alert-heading mb-2">
                                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                                        Foram encontrados erros
                                    </h6>
                                    <ul class="mb-0">
                                        <?php foreach ($erros as $erro): ?>
                                            <li><?= htmlspecialchars($erro) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($erro_sistema)): ?>
                                <div class="alert alert-danger mb-4">
                                    <strong>Erro do sistema:</strong>
                                    <p><?= htmlspecialchars($erro_sistema) ?></p>
                                </div>
                            <?php endif; ?>

                            <label class="form-label fw-bold">
                                Localização Associada
                            </label>

                            <select class="form-select" name="id_localizacao">
                                <option value="">Selecionar localização</option>
                                <?php foreach ($localizacoes as $loc): ?>
                                    <option value="<?= $loc->id ?>"
                                        <?= (($_POST['id_localizacao'] ?? '') == $loc->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc->edificio . ' - Piso ' . $loc->piso . ' - ' . $loc->sala_gabinete) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                        </div>

                        <!-- Fornecedor -->
                        <div class="tab-pane fade" id="fornecedorNovo">

                            <!-- Alertas -->
                            <?php if (!empty($erros)): ?>
                                <div class="alert alert-danger mb-4">
                                    <h6 class="alert-heading mb-2">
                                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                                        Foram encontrados erros
                                    </h6>
                                    <ul class="mb-0">
                                        <?php foreach ($erros as $erro): ?>
                                            <li><?= htmlspecialchars($erro) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">Fornecedores Associados</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAdicionarFornecedor">
                                    <i class="fa-solid fa-plus me-1"></i>
                                    Adicionar Fornecedor
                                </button>
                            </div>

                            <div id="lista-fornecedores">

                                <div class="row g-2 mb-2 linha-fornecedor">
                                    <div class="col-md-7">
                                        <select class="form-select" name="id_fornecedor[]">
                                            <option value="">Selecionar fornecedor</option>
                                            <?php foreach ($fornecedores as $f): ?>
                                                <option value="<?= $f->id ?>">
                                                    <?= htmlspecialchars($f->nome_empresa) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <select class="form-select" name="tipo_relacao[]">
                                            <option value="">Tipo de relação</option>
                                            <option value="Fabricante">Fabricante</option>
                                            <option value="Distribuidor">Distribuidor</option>
                                            <option value="Assistência Técnica">Assistência Técnica</option>
                                            <option value="Consumíveis / Acessórios">Consumíveis / Acessórios</option>
                                        </select>
                                    </div>
                                </div>

                            </div>

                            <!-- JSON com fornecedores para o JS usar -->
                            <script>
                                const fornecedoresDisponiveis = <?= json_encode(
                                                                    array_map(fn($f) => ['id' => $f->id, 'nome' => $f->nome_empresa], $fornecedores)
                                                                ) ?>;
                            </script>

                        </div>

                        <!-- Garantias e Contratos -->
                        <div class="tab-pane fade" id="garantiasNovo">
                            <!-- ALERTAS -->
                            <?php if (!empty($erros)): ?>
                                <div class="alert alert-danger mb-4">
                                    <h6 class="alert-heading mb-2">
                                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                                        Foram encontrados erros
                                    </h6>
                                    <ul class="mb-0">
                                        <?php foreach ($erros as $erro): ?>
                                            <li><?= htmlspecialchars($erro) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($erro_sistema)): ?>
                                <div class="alert alert-danger mb-4">
                                    <strong>Erro do sistema:</strong>
                                    <p><?= htmlspecialchars($erro_sistema) ?></p>
                                </div>
                            <?php endif; ?>

                            <!-- GARANTIA -->

                            <div class="row g-4">

                                <div class="col-md-6">

                                    <label class="form-label fw-bold">
                                        Garantia Associada
                                    </label>

                                    <select class="form-select" id="temGarantia" name="tem_garantia">

                                        <option selected disabled>
                                            Selecionar opção
                                        </option>

                                        <option value="sim">
                                            Sim
                                        </option>

                                        <option value="nao">
                                            Não
                                        </option>

                                    </select>

                                </div>

                            </div>

                            <hr class="my-4">

                            <!-- CONTRATO -->

                            <div class="row g-4">

                                <div class="col-md-6">

                                    <label class="form-label fw-bold">
                                        Contrato de Manutenção Associado
                                    </label>

                                    <select class="form-select" id="temContrato" name="tem_contrato">

                                        <option selected disabled>
                                            Selecionar opção
                                        </option>

                                        <option value="sim">
                                            Sim
                                        </option>

                                        <option value="nao">
                                            Não
                                        </option>

                                    </select>

                                </div>

                            </div>

                            <!-- SECÇÃO CONTRATO -->

                            <div id="secaoContrato" style="display:none;">

                                <div class="row g-4 mt-2">

                                    <div class="col-md-4">

                                        <label class="form-label fw-bold">
                                            Tipo de Contrato
                                        </label>

                                        <select class="form-select" name="tipo_contrato">

                                            <option>
                                                Manutenção Preventiva
                                            </option>

                                            <option>
                                                Manutenção Corretiva
                                            </option>

                                            <option>
                                                Manutenção Preventiva e Corretiva
                                            </option>

                                        </select>

                                    </div>

                                    <div class="col-md-4">

                                        <label class="form-label fw-bold">
                                            Entidade Responsável
                                        </label>

                                        <input type="text" class="form-control" name="entidade_responsavel">

                                    </div>

                                    <div class="col-md-4">

                                        <label class="form-label fw-bold">
                                            Periodicidade
                                        </label>

                                        <select class="form-select" name="periodicidade">

                                            <option>
                                                Mensal
                                            </option>

                                            <option>
                                                Trimestral
                                            </option>

                                            <option>
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
                                            Adicionar Documento

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
                                            Adicionar Documento

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
                                            Adicionar Documento

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
                                            Adicionar Documento

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

                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

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

                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

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

                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

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

                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

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
                        <div class="tab-pane fade" id="documentacaoNovo">

                            <!-- ALERTAS -->
                            <?php if (!empty($erros)): ?>
                                <div class="alert alert-danger mb-4">
                                    <h6 class="alert-heading mb-2">
                                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                                        Foram encontrados erros
                                    </h6>
                                    <ul class="mb-0">
                                        <?php foreach ($erros as $erro): ?>
                                            <li><?= htmlspecialchars($erro) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($erro_sistema)): ?>
                                <div class="alert alert-danger mb-4">
                                    <strong>Erro do sistema:</strong>
                                    <p><?= htmlspecialchars($erro_sistema) ?></p>
                                </div>
                            <?php endif; ?>

                            <h5 class="fw-bold mb-3">
                                Resumo da Documentação Associada
                            </h5>

                            <div class="accordion" id="accordionResumoDocumentacao">

                                <!-- DOCUMENTAÇÃO TÉCNICA -->

                                <div class="accordion-item border rounded-3 mb-3">

                                    <h2 class="accordion-header">

                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseResumoTecnica">

                                            Documentação Técnica

                                        </button>

                                    </h2>

                                    <div id="collapseResumoTecnica" class="accordion-collapse collapse">

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
                                            data-bs-toggle="collapse" data-bs-target="#collapseResumoAquisicao">

                                            Documentação de Aquisição

                                        </button>

                                    </h2>

                                    <div id="collapseResumoAquisicao" class="accordion-collapse collapse">

                                        <div class="accordion-body">

                                            <ul class="mb-0">

                                                <li>Fatura de Aquisição</li>
                                                <li>Contrato de Aquisição</li>

                                            </ul>

                                        </div>

                                    </div>

                                </div>

                                <!-- GARANTIAS -->

                                <div class="accordion-item border rounded-3">

                                    <h2 class="accordion-header">

                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseResumoGarantias">

                                            Garantias e Contratos

                                        </button>

                                    </h2>

                                    <div id="collapseResumoGarantias" class="accordion-collapse collapse">

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

                                    <select class="form-select" id="temDocumentacaoAdicional">

                                        <option selected disabled>
                                            Selecionar opção
                                        </option>

                                        <option value="sim">
                                            Sim
                                        </option>

                                        <option value="nao">
                                            Não
                                        </option>

                                    </select>

                                </div>

                            </div>

                            <div id="secaoDocumentacaoAdicional" style="display:none;">

                                <hr class="my-4">

                                <div id="listaDocumentacaoAdicional">

                                    <!-- DOCUMENTO 1 -->

                                    <div class="border rounded-3 p-3 documento-adicional-item">

                                        <h6 class="fw-bold">
                                            Documento Adicional
                                        </h6>

                                        <div class="row g-3">

                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Nome do Documento
                                                </label>

                                                <input type="text" class="form-control"
                                                    name="nome_documento_adicional[]">

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Ficheiro PDF
                                                </label>

                                                <input type="file" class="form-control"
                                                    name="ficheiro_documento_adicional[]" accept="application/pdf">

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Data do Documento
                                                </label>

                                                <input type="date" class="form-control"
                                                    name="data_documento_adicional[]">

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Data de Validade
                                                </label>

                                                <input type="date" class="form-control"
                                                    name="validade_documento_adicional[]">

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <button type="button" class="btn btn-outline-primary mt-3"
                                    id="btnAdicionarDocumentoAdicional">

                                    <i class="fa-solid fa-plus me-2"></i>
                                    Adicionar Documento

                                </button>

                            </div>
                        </div>

                        <div class="tab-pane fade" id="observacoesNovo">
                            <!-- ALERTAS -->
                            <?php if (!empty($erros)): ?>
                                <div class="alert alert-danger mb-4">
                                    <h6 class="alert-heading mb-2">
                                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                                        Foram encontrados erros
                                    </h6>
                                    <ul class="mb-0">
                                        <?php foreach ($erros as $erro): ?>
                                            <li><?= htmlspecialchars($erro) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($erro_sistema)): ?>
                                <div class="alert alert-danger mb-4">
                                    <strong>Erro do sistema:</strong>
                                    <p><?= htmlspecialchars($erro_sistema) ?></p>
                                </div>
                            <?php endif; ?>

                            <label class="form-label fw-bold">
                                Observações
                            </label>

                            <textarea class="form-control" name="observacoes" rows="6"></textarea>

                        </div>


                        <div class="d-flex justify-content-end gap-3 mt-4">

                            <a href="equipamentos.php" class="btn btn-outline-secondary">
                                Cancelar
                            </a>

                            <button type="submit" class="btn btn-primary-custom">

                                <i class="fa-solid fa-floppy-disk me-2"></i>
                                Guardar Equipamento

                            </button>

                        </div>

                    </div>
                </div>

            </div>
        </form>
    </main>
</div>

<?php include '../../includes/footer.php'; ?>