<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

if (!isset($_SESSION['equip_step'])) $_SESSION['equip_step'] = 1;
$step_atual = $_SESSION['equip_step'];

if (!isset($_SESSION['equipamento'])) $_SESSION['equipamento'] = [];

$erros = [];
$erro_sistema = "";
$erros_dados_gerais = [];
$erros_aquisicao    = [];
$erros_localizacao  = [];
$erros_fornecedor   = [];
$erros_garantias    = [];
$erros_documentacao = [];

try {
    $ligacao = ligar_bd();

    $categorias   = $ligacao->query("SELECT * FROM categorias ORDER BY nome_categoria")->fetchAll(PDO::FETCH_OBJ);
    $estados      = $ligacao->query("SELECT * FROM estados ORDER BY nome_estado")->fetchAll(PDO::FETCH_OBJ);
    $localizacoes = $ligacao->query("SELECT * FROM localizacoes ORDER BY codigo_localizacao")->fetchAll(PDO::FETCH_OBJ);
    $fornecedores = $ligacao->query("SELECT * FROM fornecedores ORDER BY codigo_fornecedor")->fetchAll(PDO::FETCH_OBJ);
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

if (isset($_POST['anterior'])) {
    $_SESSION['equip_step'] = max(1, $_SESSION['equip_step'] - 1);
    header('Location: inserir_equipamentos.php');
    exit;
}

if (isset($_POST['submeter_step1'])) {

    // 1. RECOLHER
    $designacao  = trim($_POST['designacao'] ?? '');
    $id_categoria = $_POST['id_categoria'] ?? '';
    $fabricante  = trim($_POST['fabricante'] ?? '');
    $marca       = trim($_POST['marca'] ?? '');
    $modelo      = trim($_POST['modelo'] ?? '');
    $num_serie   = trim($_POST['num_serie'] ?? '');
    $ano_fabrico = trim($_POST['ano_fabrico'] ?? '');
    $criticidade = $_POST['criticidade'] ?? '';

    $nome_manual_utilizacao = trim($_POST['nome_documento_manual_utilizacao'] ?? '');
    $nome_manual_tecnico    = trim($_POST['nome_documento_manual_tecnico'] ?? '');
    $manual_utilizacao_data = $_POST['manual_utilizacao_data'] ?? '';
    $manual_utilizacao_validade = $_POST['manual_utilizacao_validade'] ?? '';
    $manual_tecnico_data = $_POST['manual_tecnico_data'] ?? '';
    $manual_tecnico_validade = $_POST['manual_tecnico_validade'] ?? '';

    // 2. VALIDAR
    if (empty($designacao))   $erros_dados_gerais[] = "A designação é obrigatória.";
    if (empty($id_categoria)) $erros_dados_gerais[] = "A categoria é obrigatória.";

    if (!empty($ano_fabrico)) {
        if (!preg_match('/^\d{4}$/', $ano_fabrico) || (int)$ano_fabrico < 1900 || (int)$ano_fabrico > (int)date('Y')) {
            $erros_dados_gerais[] = "O ano de fabrico é inválido.";
        }
    }

    $sem_ficheiro_mu = empty($_FILES['manual_utilizacao']['name']);
    if (empty($nome_manual_utilizacao) && $sem_ficheiro_mu) {
        $erros_dados_gerais[] = "O Manual de Utilização é obrigatório.";
    }

    $sem_ficheiro_mt = empty($_FILES['manual_tecnico']['name']);
    if (empty($nome_manual_tecnico) && $sem_ficheiro_mt) {
        $erros_dados_gerais[] = "O Manual Técnico é obrigatório.";
    }
    validar_datas_documento(
        $manual_utilizacao_data,
        $manual_utilizacao_validade,
        $erros_dados_gerais,
        'Manual de Utilização'
    );

    validar_datas_documento(
        $manual_tecnico_data,
        $manual_tecnico_validade,
        $erros_dados_gerais,
        'Manual Técnico'
    );

    // 3. SE OK: NORMALIZAR, UPLOAD, GUARDAR EM SESSÃO, AVANÇAR
    if (empty($erros_dados_gerais)) {

        $pasta_uploads = __DIR__ . '/../../../assets/uploads/documentos/';
        $caminho_relativo = BASE_URL . '/assets/uploads/documentos/';

        $caminho_mu = null;
        if (!$sem_ficheiro_mu) {
            $res = fazer_upload_pdf($_FILES['manual_utilizacao'], $pasta_uploads);
            if ($res) $caminho_mu = $caminho_relativo . $res;
        }

        $caminho_mt = null;
        if (!$sem_ficheiro_mt) {
            $res = fazer_upload_pdf($_FILES['manual_tecnico'], $pasta_uploads);
            if ($res) $caminho_mt = $caminho_relativo . $res;
        }

        $_SESSION['equipamento']['designacao']   = ucwords(strtolower($designacao));
        $_SESSION['equipamento']['id_categoria'] = $id_categoria;
        $_SESSION['equipamento']['fabricante']   = ucwords(strtolower($fabricante));
        $_SESSION['equipamento']['marca']        = ucwords(strtolower($marca));
        $_SESSION['equipamento']['modelo']       = $modelo;
        $_SESSION['equipamento']['num_serie']    = $num_serie;
        $_SESSION['equipamento']['ano_fabrico']  = $ano_fabrico;
        $_SESSION['equipamento']['criticidade']  = $criticidade;

        $_SESSION['equipamento']['doc_manual_utilizacao'] = [
            'nome' => $nome_manual_utilizacao,
            'caminho' => $caminho_mu,
            'data' => $manual_utilizacao_data,
            'validade' => $manual_utilizacao_validade,
        ];
        $_SESSION['equipamento']['doc_manual_tecnico'] = [
            'nome' => $nome_manual_tecnico,
            'caminho' => $caminho_mt,
            'data' => $manual_tecnico_data,
            'validade' => $manual_tecnico_validade,
        ];

        $_SESSION['equip_step'] = 2;
        header('Location: inserir_equipamentos.php');
        exit;
    }

    // Se há erros, fica no step 1
    $step_atual = 1;
}
if (isset($_POST['submeter_step2'])) {

    // 1. RECOLHER
    $data_aquisicao  = trim($_POST['data_aquisicao'] ?? '');
    $custo_aquisicao = trim($_POST['custo_aquisicao'] ?? '');
    $tipo_entrada    = $_POST['tipo_entrada'] ?? '';
    $id_estado       = $_POST['id_estado'] ?? '';

    $nome_fatura   = trim($_POST['nome_documento_fatura_aquisicao'] ?? '');
    $nome_contrato_aq = trim($_POST['nome_documento_contrato_aquisicao'] ?? '');
    $fatura_data     = $_POST['fatura_aquisicao_data'] ?? '';
    $fatura_validade = $_POST['fatura_aquisicao_validade'] ?? '';
    $contrato_aq_data     = $_POST['contrato_aquisicao_data'] ?? '';
    $contrato_aq_validade = $_POST['contrato_aquisicao_validade'] ?? '';

    // 2. VALIDAR
    if (empty($id_estado)) $erros_aquisicao[] = "O estado é obrigatório.";

    if (!empty($data_aquisicao)) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_aquisicao)) {
            $erros_aquisicao[] = "Formato de data de aquisição inválido.";
        } else {
            $partes = explode('-', $data_aquisicao);
            if (!checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0])) {
                $erros_aquisicao[] = "Data de aquisição inválida.";
            }
        }
    }

    if (!empty($custo_aquisicao)) {
        if (!is_numeric($custo_aquisicao)) {
            $erros_aquisicao[] = "O custo de aquisição deve ser um valor numérico.";
        } elseif ((float)$custo_aquisicao < 0) {
            $erros_aquisicao[] = "O custo de aquisição não pode ser negativo.";
        }
    }

    $sem_ficheiro_fatura = empty($_FILES['fatura_aquisicao']['name']);
    if (empty($nome_fatura) && $sem_ficheiro_fatura) {
        $erros_aquisicao[] = "A Fatura de Aquisição é obrigatória.";
    }

    $sem_ficheiro_contrato_aq = empty($_FILES['contrato_aquisicao']['name']);
    if (empty($nome_contrato_aq) && $sem_ficheiro_contrato_aq) {
        $erros_aquisicao[] = "O Contrato de Aquisição é obrigatório.";
    }
    validar_datas_documento(
        $fatura_data,
        $fatura_validade,
        $erros_aquisicao,
        'Fatura de Aquisição'
    );

    validar_datas_documento(
        $contrato_aq_data,
        $contrato_aq_validade,
        $erros_aquisicao,
        'Contrato de Aquisição'
    );

    // 3. SE OK: UPLOAD, GUARDAR, AVANÇAR
    if (empty($erros_aquisicao)) {

        $pasta_uploads = __DIR__ . '/../../../assets/uploads/documentos/';
        $caminho_relativo = BASE_URL . '/assets/uploads/documentos/';

        $caminho_fatura = null;
        if (!$sem_ficheiro_fatura) {
            $res = fazer_upload_pdf($_FILES['fatura_aquisicao'], $pasta_uploads);
            if ($res) $caminho_fatura = $caminho_relativo . $res;
        }

        $caminho_contrato_aq = null;
        if (!$sem_ficheiro_contrato_aq) {
            $res = fazer_upload_pdf($_FILES['contrato_aquisicao'], $pasta_uploads);
            if ($res) $caminho_contrato_aq = $caminho_relativo . $res;
        }

        $_SESSION['equipamento']['data_aquisicao']  = $data_aquisicao;
        $_SESSION['equipamento']['custo_aquisicao'] = $custo_aquisicao;
        $_SESSION['equipamento']['tipo_entrada']    = $tipo_entrada;
        $_SESSION['equipamento']['id_estado']       = $id_estado;

        $_SESSION['equipamento']['doc_fatura_aquisicao'] = [
            'nome' => $nome_fatura,
            'caminho' => $caminho_fatura,
            'data' => $fatura_data,
            'validade' => $fatura_validade,
        ];
        $_SESSION['equipamento']['doc_contrato_aquisicao'] = [
            'nome' => $nome_contrato_aq,
            'caminho' => $caminho_contrato_aq,
            'data' => $contrato_aq_data,
            'validade' => $contrato_aq_validade,
        ];

        $_SESSION['equip_step'] = 3;
        header('Location: inserir_equipamentos.php');
        exit;
    }

    $step_atual = 2;
}

if (isset($_POST['submeter_step3'])) {

    $tem_acessorios  = $_POST['tem_acessorios'] ?? '';
    $tem_consumiveis = $_POST['tem_consumiveis'] ?? '';

    $acessorio_nome       = $_POST['acessorio_nome'] ?? [];
    $acessorio_quantidade = $_POST['acessorio_quantidade'] ?? [];
    $acessorio_estado     = $_POST['acessorio_estado'] ?? [];

    $consumivel_nome       = $_POST['consumivel_nome'] ?? [];
    $consumivel_quantidade = $_POST['consumivel_quantidade'] ?? [];

    // Não há campos obrigatórios nesta secção

    $_SESSION['equipamento']['tem_acessorios']  = $tem_acessorios;
    $_SESSION['equipamento']['tem_consumiveis'] = $tem_consumiveis;

    $_SESSION['equipamento']['acessorios'] = [];
    foreach ($acessorio_nome as $i => $nome) {
        $nome = trim($nome);
        if (empty($nome)) continue;
        $_SESSION['equipamento']['acessorios'][] = [
            'nome'       => $nome,
            'quantidade' => $acessorio_quantidade[$i] ?? '',
            'id_estado'  => $acessorio_estado[$i] ?? '',
        ];
    }

    $_SESSION['equipamento']['consumiveis'] = [];
    foreach ($consumivel_nome as $i => $nome) {
        $nome = trim($nome);
        if (empty($nome)) continue;
        $_SESSION['equipamento']['consumiveis'][] = [
            'nome'       => $nome,
            'quantidade' => $consumivel_quantidade[$i] ?? '',
        ];
    }

    $_SESSION['equip_step'] = 4;
    header('Location: inserir_equipamentos.php');
    exit;
}

if (isset($_POST['submeter_step4'])) {

    $id_localizacao = $_POST['id_localizacao'] ?? '';

    if (empty($id_localizacao)) $erros_localizacao[] = "A localização é obrigatória.";

    if (empty($erros_localizacao)) {
        $_SESSION['equipamento']['id_localizacao'] = $id_localizacao;
        $_SESSION['equip_step'] = 5;
        header('Location: inserir_equipamentos.php');
        exit;
    }

    $step_atual = 4;
}
if (isset($_POST['submeter_step5'])) {

    $ids_fornecedor = $_POST['id_fornecedor'] ?? [];
    $tipos_relacao  = $_POST['tipo_relacao']  ?? [];

    $fornecedor_valido = false;
    $fornecedores_validos = [];

    foreach ($ids_fornecedor as $i => $id_forn) {
        if (!empty($id_forn) && !empty($tipos_relacao[$i])) {
            $fornecedor_valido = true;
            $fornecedores_validos[] = [
                'id_fornecedor' => $id_forn,
                'tipo_relacao'  => $tipos_relacao[$i],
            ];
        }
    }

    if (!$fornecedor_valido) {
        $erros_fornecedor[] = "É obrigatório associar pelo menos um fornecedor com tipo de relação.";
    }

    if (empty($erros_fornecedor)) {
        $_SESSION['equipamento']['fornecedores'] = $fornecedores_validos;
        $_SESSION['equip_step'] = 6;
        header('Location: inserir_equipamentos.php');
        exit;
    }

    $step_atual = 5;
}
if (isset($_POST['submeter_step6'])) {

    $tem_garantia = $_POST['tem_garantia'] ?? '';
    $tem_contrato = $_POST['tem_contrato'] ?? '';
    $tipo_contrato = $_POST['tipo_contrato'] ?? '';
    $entidade_responsavel = trim($_POST['entidade_responsavel'] ?? '');
    $periodicidade = $_POST['periodicidade'] ?? '';

    $nome_certificado_garantia = trim($_POST['nome_documento_certificado_garantia'] ?? '');
    $nome_contrato_manutencao  = trim($_POST['nome_documento_contrato_manutencao'] ?? '');
    $nome_certificado_calibracao = trim($_POST['nome_documento_certificado_calibracao'] ?? '');
    $nome_relatorio_calibracao   = trim($_POST['nome_documento_relatorio_calibracao'] ?? '');

    $certificado_garantia_data     = $_POST['certificado_garantia_data'] ?? '';
    $certificado_garantia_validade = $_POST['certificado_garantia_validade'] ?? '';
    $contrato_manutencao_data      = $_POST['contrato_manutencao_data'] ?? '';
    $contrato_manutencao_validade  = $_POST['contrato_manutencao_validade'] ?? '';
    $certificado_calibracao_data     = $_POST['certificado_calibracao_data'] ?? '';
    $certificado_calibracao_validade = $_POST['certificado_calibracao_validade'] ?? '';
    $relatorio_calibracao_data     = $_POST['relatorio_calibracao_data'] ?? '';
    $relatorio_calibracao_validade = $_POST['relatorio_calibracao_validade'] ?? '';

    // Calibração - sempre obrigatórios
    $sem_ficheiro_cert_calib = empty($_FILES['certificado_calibracao']['name']);
    if (empty($nome_certificado_calibracao) && $sem_ficheiro_cert_calib) {
        $erros_garantias[] = "O Certificado de Calibração é obrigatório.";
    }

    $sem_ficheiro_relat_calib = empty($_FILES['relatorio_calibracao']['name']);
    if (empty($nome_relatorio_calibracao) && $sem_ficheiro_relat_calib) {
        $erros_garantias[] = "O Relatório de Calibração é obrigatório.";
    }

    // Garantia - condicional
    $sem_ficheiro_cert_garantia = empty($_FILES['certificado_garantia']['name']);
    if ($tem_garantia === 'sim') {
        if (empty($nome_certificado_garantia) && $sem_ficheiro_cert_garantia) {
            $erros_garantias[] = "O Certificado de Garantia é obrigatório quando tem garantia associada.";
        }
    }

    // Contrato - condicional
    $sem_ficheiro_contrato_manut = empty($_FILES['contrato_manutencao']['name']);
    if ($tem_contrato === 'sim') {
        if (empty($nome_contrato_manutencao) && $sem_ficheiro_contrato_manut) {
            $erros_garantias[] = "O Contrato de Manutenção é obrigatório quando tem contrato associado.";
        }
    }
    validar_datas_documento(
        $certificado_garantia_data,
        $certificado_garantia_validade,
        $erros_garantias,
        'Certificado de Garantia'
    );

    validar_datas_documento(
        $contrato_manutencao_data,
        $contrato_manutencao_validade,
        $erros_garantias,
        'Contrato de Manutenção'
    );

    validar_datas_documento(
        $certificado_calibracao_data,
        $certificado_calibracao_validade,
        $erros_garantias,
        'Certificado de Calibração'
    );

    validar_datas_documento(
        $relatorio_calibracao_data,
        $relatorio_calibracao_validade,
        $erros_garantias,
        'Relatório de Calibração'
    );

    if (empty($erros_garantias)) {

        $pasta_uploads = __DIR__ . '/../../../assets/uploads/documentos/';
        $caminho_relativo = BASE_URL . '/assets/uploads/documentos/';

        $caminho_cert_garantia = null;
        if (!$sem_ficheiro_cert_garantia) {
            $res = fazer_upload_pdf($_FILES['certificado_garantia'], $pasta_uploads);
            if ($res) $caminho_cert_garantia = $caminho_relativo . $res;
        }

        $caminho_contrato_manut = null;
        if (!$sem_ficheiro_contrato_manut) {
            $res = fazer_upload_pdf($_FILES['contrato_manutencao'], $pasta_uploads);
            if ($res) $caminho_contrato_manut = $caminho_relativo . $res;
        }

        $caminho_cert_calib = null;
        if (!$sem_ficheiro_cert_calib) {
            $res = fazer_upload_pdf($_FILES['certificado_calibracao'], $pasta_uploads);
            if ($res) $caminho_cert_calib = $caminho_relativo . $res;
        }

        $caminho_relat_calib = null;
        if (!$sem_ficheiro_relat_calib) {
            $res = fazer_upload_pdf($_FILES['relatorio_calibracao'], $pasta_uploads);
            if ($res) $caminho_relat_calib = $caminho_relativo . $res;
        }

        $_SESSION['equipamento']['tem_garantia'] = $tem_garantia;
        $_SESSION['equipamento']['tem_contrato'] = $tem_contrato;
        $_SESSION['equipamento']['tipo_contrato'] = $tipo_contrato;
        $_SESSION['equipamento']['entidade_responsavel'] = $entidade_responsavel;
        $_SESSION['equipamento']['periodicidade'] = $periodicidade;

        $_SESSION['equipamento']['doc_certificado_garantia'] = [
            'nome' => $nome_certificado_garantia,
            'caminho' => $caminho_cert_garantia,
            'data' => $certificado_garantia_data,
            'validade' => $certificado_garantia_validade,
        ];
        $_SESSION['equipamento']['doc_contrato_manutencao'] = [
            'nome' => $nome_contrato_manutencao,
            'caminho' => $caminho_contrato_manut,
            'data' => $contrato_manutencao_data,
            'validade' => $contrato_manutencao_validade,
        ];
        $_SESSION['equipamento']['doc_certificado_calibracao'] = [
            'nome' => $nome_certificado_calibracao,
            'caminho' => $caminho_cert_calib,
            'data' => $certificado_calibracao_data,
            'validade' => $certificado_calibracao_validade,
        ];
        $_SESSION['equipamento']['doc_relatorio_calibracao'] = [
            'nome' => $nome_relatorio_calibracao,
            'caminho' => $caminho_relat_calib,
            'data' => $relatorio_calibracao_data,
            'validade' => $relatorio_calibracao_validade,
        ];

        $_SESSION['equip_step'] = 7;
        header('Location: inserir_equipamentos.php');
        exit;
    }

    $step_atual = 6;
}
if (isset($_POST['submeter_step7'])) {

    $tem_documentacao_adicional = $_POST['tem_documentacao_adicional'] ?? '';

    $nomes_doc_adicional = $_POST['nome_documento_adicional'] ?? [];
    $datas_doc_adicional = $_POST['data_documento_adicional'] ?? [];
    $validades_doc_adicional = $_POST['validade_documento_adicional'] ?? [];
    $ficheiros_doc_adicional = $_FILES['ficheiro_documento_adicional'] ?? [];

    $pasta_uploads = __DIR__ . '/../../../assets/uploads/documentos/';
    $caminho_relativo = BASE_URL . '/assets/uploads/documentos/';

    $_SESSION['equipamento']['tem_documentacao_adicional'] = $tem_documentacao_adicional;
    $_SESSION['equipamento']['documentos_adicionais'] = [];

    foreach ($nomes_doc_adicional as $i => $nome) {
        $nome = trim($nome);
        if (empty($nome)) continue;

        $caminho = null;
        if (!empty($ficheiros_doc_adicional['name'][$i])) {
            $ficheiro_individual = [
                'name'     => $ficheiros_doc_adicional['name'][$i],
                'type'     => $ficheiros_doc_adicional['type'][$i],
                'tmp_name' => $ficheiros_doc_adicional['tmp_name'][$i],
                'error'    => $ficheiros_doc_adicional['error'][$i],
                'size'     => $ficheiros_doc_adicional['size'][$i],
            ];
            $res = fazer_upload_pdf($ficheiro_individual, $pasta_uploads);
            if ($res) $caminho = $caminho_relativo . $res;
        }

        $_SESSION['equipamento']['documentos_adicionais'][] = [
            'nome' => $nome,
            'caminho' => $caminho,
            'data' => $datas_doc_adicional[$i] ?? '',
            'validade' => $validades_doc_adicional[$i] ?? '',
        ];

        validar_datas_documento(
            $datas_doc_adicional[$i] ?? '',
            $validades_doc_adicional[$i] ?? '',
            $erros_documentacao,
            $nome
        );
    }

    $_SESSION['equip_step'] = 8;
    header('Location: inserir_equipamentos.php');
    exit;
}
if (isset($_POST['submeter_step8'])) {

    $observacoes = trim($_POST['observacoes'] ?? '');

    if (strlen($observacoes) > 5000) {
        $erros_observacoes[] = "As observações não podem exceder 5000 caracteres.";
    }

    if (empty($erros_observacoes)) {

        $_SESSION['equipamento']['observacoes'] = $observacoes;

        try {

            $ligacao = ligar_bd();
            $ligacao->beginTransaction();

            $stmt = $ligacao->prepare("
                INSERT INTO equipamentos (
                    codigo_interno,
                    designacao,
                    fabricante,
                    marca,
                    modelo,
                    num_serie,
                    ano_fabrico,
                    data_aquisicao,
                    custo_aquisicao,
                    tipo_entrada,
                    criticidade,
                    observacoes,
                    id_localizacao,
                    id_categoria,
                    id_estado
                )
                VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )
            ");

            $stmt->execute([
                $proximo_codigo,
                $_SESSION['equipamento']['designacao'],
                $_SESSION['equipamento']['fabricante'],
                $_SESSION['equipamento']['marca'],
                $_SESSION['equipamento']['modelo'],
                $_SESSION['equipamento']['num_serie'],
                $_SESSION['equipamento']['ano_fabrico'],
                $_SESSION['equipamento']['data_aquisicao'],
                $_SESSION['equipamento']['custo_aquisicao'],
                $_SESSION['equipamento']['tipo_entrada'],
                $_SESSION['equipamento']['criticidade'],
                $_SESSION['equipamento']['observacoes'],
                $_SESSION['equipamento']['id_localizacao'],
                $_SESSION['equipamento']['id_categoria'],
                $_SESSION['equipamento']['id_estado']
            ]);
            $idEquipamento = $ligacao->lastInsertId();

            foreach ($_SESSION['equipamento']['fornecedores'] as $fornecedor) {

                $stmt = $ligacao->prepare("
                    INSERT INTO equipamentos_fornecedores
                    (
                        id_equipamento,
                        id_fornecedor,
                        tipo_relacao
                    )
                    VALUES (?, ?, ?)
                ");

                $stmt->execute([
                    $idEquipamento,
                    $fornecedor['id_fornecedor'],
                    $fornecedor['tipo_relacao']
                ]);
            }

            foreach ($_SESSION['equipamento']['acessorios'] as $acessorio) {

                $stmt = $ligacao->prepare("
                    INSERT INTO acessorios
                    (
                        nome,
                        quantidade,
                        id_estado,
                        id_equipamento
                    )
                    VALUES (?, ?, ?, ?)
                ");

                $stmt->execute([
                    $acessorio['nome'],
                    $acessorio['quantidade'],
                    $acessorio['id_estado'],
                    $idEquipamento
                ]);
            }
            foreach ($_SESSION['equipamento']['consumiveis'] as $consumivel) {

                $stmt = $ligacao->prepare("
                    INSERT INTO consumiveis
                    (
                        nome,
                        quantidade,
                        id_equipamento
                    )
                    VALUES (?, ?, ?)
                ");

                $stmt->execute([
                    $consumivel['nome'],
                    $consumivel['quantidade'],
                    $idEquipamento
                ]);
            }
            $idManualUtilizacao = inserirDocumento(
                $ligacao,
                $idEquipamento,
                1,
                $_SESSION['equipamento']['doc_manual_utilizacao']
            );

            $idManualTecnico = inserirDocumento(
                $ligacao,
                $idEquipamento,
                2,
                $_SESSION['equipamento']['doc_manual_tecnico']
            );

            $idFatura = inserirDocumento(
                $ligacao,
                $idEquipamento,
                3,
                $_SESSION['equipamento']['doc_fatura_aquisicao']
            );

            $idContratoAquisicao = inserirDocumento(
                $ligacao,
                $idEquipamento,
                4,
                $_SESSION['equipamento']['doc_contrato_aquisicao']
            );

            $idCertificadoGarantia = null;

            if (
                ($_SESSION['equipamento']['tem_garantia'] ?? '') === 'sim'
            ) {

                $idCertificadoGarantia = inserirDocumento(
                    $ligacao,
                    $idEquipamento,
                    5,
                    $_SESSION['equipamento']['doc_certificado_garantia']
                );
            }

            $idContratoManutencao = null;

            if (
                ($_SESSION['equipamento']['tem_contrato'] ?? '') === 'sim'
            ) {

                $idContratoManutencao = inserirDocumento(
                    $ligacao,
                    $idEquipamento,
                    6,
                    $_SESSION['equipamento']['doc_contrato_manutencao']
                );
            }

            $idCertificadoCalibracao = inserirDocumento(
                $ligacao,
                $idEquipamento,
                7,
                $_SESSION['equipamento']['doc_certificado_calibracao']
            );

            $idRelatorioCalibracao = inserirDocumento(
                $ligacao,
                $idEquipamento,
                8,
                $_SESSION['equipamento']['doc_relatorio_calibracao']
            );

            foreach ($_SESSION['equipamento']['documentos_adicionais'] as $documento) {

                inserirDocumento(
                    $ligacao,
                    $idEquipamento,
                    12,
                    $documento
                );
            }

            if (
                ($_SESSION['equipamento']['tem_contrato'] ?? '') === 'sim'
                && $idContratoManutencao
            ) {

                $stmt = $ligacao->prepare("
                    INSERT INTO contratos
                    (
                        tipo_contrato,
                        periodicidade,
                        entidade_responsavel,
                        observacoes,
                        id_fornecedor,
                        id_documento
                    )
                    VALUES
                    (
                        ?, ?, ?, ?, ?, ?
                    )
                ");

                $stmt->execute([
                    $_SESSION['equipamento']['tipo_contrato'],
                    $_SESSION['equipamento']['periodicidade'],
                    $_SESSION['equipamento']['entidade_responsavel'],
                    null,
                    null,
                    $idContratoManutencao
                ]);
            }

            $ligacao->commit();

            unset($_SESSION['equipamento']);
            unset($_SESSION['equip_step']);

            $_SESSION['sucesso'] = "Equipamento registado com sucesso.";

            header('Location: equipamentos.php');
            exit;
        } catch (Exception $e) {

            if ($ligacao->inTransaction()) {
                $ligacao->rollBack();
            }

            $erros_observacoes[] = "Erro ao guardar equipamento: " . $e->getMessage();
            $step_atual = 8;
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
                            <button type="button" class="nav-link <?= ($step_atual == 1) ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#dadosGeraisNovo">

                                Dados Gerais
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link <?= ($step_atual == 2) ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#aquisicaoNovo">
                                Aquisição
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link <?= ($step_atual == 3) ? 'active' : '' ?>" data-bs-toggle="pill"
                                data-bs-target="#acessoriosNovo">
                                Acessórios e Consumíveis
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link <?= ($step_atual == 4) ? 'active' : '' ?>" data-bs-toggle="pill"
                                data-bs-target="#localizacaoNovo">
                                Localização
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link <?= ($step_atual == 5) ? 'active' : '' ?>" data-bs-toggle="pill"
                                data-bs-target="#fornecedorNovo">
                                Fornecedor Associado
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link <?= ($step_atual == 6) ? 'active' : '' ?>" data-bs-toggle="pill"
                                data-bs-target="#garantiasNovo">
                                Garantias e Contratos
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link <?= ($step_atual == 7) ? 'active' : '' ?>" data-bs-toggle="pill"
                                data-bs-target="#documentacaoNovo">
                                Documentação
                            </button>
                        </li>

                        <li class="nav-item">
                            <button type="button" class="nav-link <?= ($step_atual == 8) ? 'active' : '' ?>" data-bs-toggle="pill"
                                data-bs-target="#observacoesNovo">
                                Observações
                            </button>
                        </li>

                    </ul>
                    <!-- Dados Gerais -->
                    <div class="tab-content">

                        <div class="tab-pane fade <?= ($step_atual == 1) ? 'show active' : '' ?>" id="dadosGeraisNovo">
                            <!-- ALERTAS -->
                            <div class="alert alert-danger mb-4" id="alertas-dados-gerais" <?= empty($erros_dados_gerais) ? 'style="display:none;"' : '' ?>>
                                <h6 class="alert-heading mb-2"><i class="fa-solid fa-circle-exclamation me-2"></i>Foram encontrados erros</h6>
                                <ul class="mb-0">
                                    <?php foreach ($erros_dados_gerais as $erro): ?>
                                        <li><?= htmlspecialchars($erro) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

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
                                    <input type="text" class="form-control" name="designacao" value="<?= htmlspecialchars($_SESSION['equipamento']['designacao'] ?? '') ?>">
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
                                    <input type="number" class="form-control" min="1900"
                                        max="<?= date('Y') ?>" name="ano_fabrico" value="<?= $_POST['ano_fabrico'] ?? '' ?>">
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
                                                    placeholder="Ex: Manual de Utilização Dräger V2"
                                                    value="<?= htmlspecialchars($_POST['nome_documento_manual_utilizacao'] ?? '') ?>">
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
                                                    name="manual_utilizacao_data"
                                                    value="<?= htmlspecialchars($_POST['manual_utilizacao_data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="manual_utilizacao_validade"
                                                    value="<?= htmlspecialchars($_POST['manual_utilizacao_validade'] ?? '') ?>">
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
                                                    placeholder="Ex: Manual Técnico Fabricante 2023"
                                                    value="<?= htmlspecialchars($_POST['nome_documento_manual_tecnico'] ?? '') ?>">
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
                                                    name="manual_tecnico_data"
                                                    value="<?= htmlspecialchars($_POST['manual_tecnico_data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="manual_tecnico_validade"
                                                    value="<?= htmlspecialchars($_POST['manual_tecnico_validade'] ?? '') ?>">
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" name="submeter_step1" class="btn btn-primary-custom">Seguinte</button>
                            </div>
                        </div>

                        <!-- AQUISIÇÃO -->
                        <div class="tab-pane fade <?= ($step_atual == 2) ? 'show active' : '' ?>" id="aquisicaoNovo">
                            <!-- ALERTAS -->
                            <div class="alert alert-danger mb-4" id="alertas-aquisicao" <?= empty($erros_aquisicao) ? 'style="display:none;"' : '' ?>>
                                <h6 class="alert-heading mb-2"><i class="fa-solid fa-circle-exclamation me-2"></i>Foram encontrados erros</h6>
                                <ul class="mb-0">
                                    <?php foreach ($erros_aquisicao as $erro): ?>
                                        <li><?= htmlspecialchars($erro) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

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

                                        <option value="" disabled <?= empty($_POST['tipo_entrada'] ?? '') ? 'selected' : '' ?>>
                                            Selecionar tipo
                                        </option>

                                        <option <?= (($_POST['tipo_entrada'] ?? '') == 'Compra') ? 'selected' : '' ?>>
                                            Compra
                                        </option>

                                        <option <?= (($_POST['tipo_entrada'] ?? '') == 'Doação') ? 'selected' : '' ?>>
                                            Doação
                                        </option>

                                        <option <?= (($_POST['tipo_entrada'] ?? '') == 'Aluguer') ? 'selected' : '' ?>>
                                            Aluguer
                                        </option>

                                        <option <?= (($_POST['tipo_entrada'] ?? '') == 'Empréstimo') ? 'selected' : '' ?>>
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
                                                    placeholder="Ex: Fatura MedEquip 2024"
                                                    value="<?= htmlspecialchars($_POST['nome_documento_fatura_aquisicao'] ?? '') ?>">
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
                                                    name="fatura_aquisicao_data"
                                                    value="<?= htmlspecialchars($_POST['fatura_aquisicao_data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="fatura_aquisicao_validade"
                                                    value="<?= htmlspecialchars($_POST['fatura_aquisicao_validade'] ?? '') ?>">
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
                                                    placeholder="Ex: Contrato Aquisição 2024"
                                                    value="<?= htmlspecialchars($_POST['nome_documento_contrato_aquisicao'] ?? '') ?>">
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
                                                    name="contrato_aquisicao_data"
                                                    value="<?= htmlspecialchars($_POST['contrato_aquisicao_data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="contrato_aquisicao_validade"
                                                    value="<?= htmlspecialchars($_POST['contrato_aquisicao_validade'] ?? '') ?>">
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" name="submeter_step2" class="btn btn-primary-custom">Seguinte</button>
                            </div>

                        </div>

                        <!-- ACESSÓRIOS E CONSUMÍVEIS -->
                        <div class="tab-pane fade <?= ($step_atual == 3) ? 'show active' : '' ?>" id="acessoriosNovo">
                            <!-- ALERTAS -->
                            <div class="alert alert-danger mb-4" id="alertas-acessorios" style="display:none;">
                                <h6 class="alert-heading mb-2"><i class="fa-solid fa-circle-exclamation me-2"></i>Foram encontrados erros</h6>
                                <ul class="mb-0"></ul>
                            </div>

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

                                    <select class="form-select" id="temAcessorios" name="tem_acessorios">

                                        <option disabled <?= empty($_POST['tem_acessorios'] ?? '') ? 'selected' : '' ?>>
                                            Selecionar opção
                                        </option>

                                        <option value="sim" <?= (($_POST['tem_acessorios'] ?? '') == 'sim') ? 'selected' : '' ?>>
                                            Sim
                                        </option>

                                        <option value="nao" <?= (($_POST['tem_acessorios'] ?? '') == 'nao') ? 'selected' : '' ?>>
                                            Não
                                        </option>

                                    </select>

                                </div>

                                <!-- EXISTEM CONSUMÍVEIS -->

                                <div class="col-md-6">

                                    <label class="form-label fw-bold">
                                        Existem consumíveis associados ao equipamento?
                                    </label>

                                    <select class="form-select" id="temConsumiveis" name="tem_consumiveis">

                                        <option disabled <?= empty($_POST['tem_consumiveis'] ?? '') ? 'selected' : '' ?>>
                                            Selecionar opção
                                        </option>

                                        <option value="sim" <?= (($_POST['tem_consumiveis'] ?? '') == 'sim') ? 'selected' : '' ?>>
                                            Sim
                                        </option>

                                        <option value="nao" <?= (($_POST['tem_consumiveis'] ?? '') == 'nao') ? 'selected' : '' ?>>
                                            Não
                                        </option>

                                    </select>

                                </div>

                            </div>

                            <!-- SECÇÃO ACESSÓRIOS -->

                            <div id="secaoAcessorios" style="display:none;">

                                <script>
                                    let opcoesEstados = '<option value="">Selecione um estado</option>';
                                    <?php foreach ($estados as $estado): ?>
                                        opcoesEstados += '<option value="<?= $estado->id ?>"><?= htmlspecialchars($estado->nome_estado) ?></option>';
                                    <?php endforeach; ?>
                                </script>


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

                                            <input type="text" class="form-control" name="acessorio_nome[]"
                                                value="<?= htmlspecialchars($_POST['acessorio_nome'][0] ?? '') ?>"
                                                placeholder="Ex: Sensor de Fluxo">

                                        </div>

                                        <div class="col-md-3">

                                            <label class="form-label fw-bold">
                                                Quantidade
                                            </label>

                                            <input type="number" class="form-control" name="acessorio_quantidade[]"
                                                value="<?= htmlspecialchars($_POST['acessorio_quantidade'][0] ?? '') ?>">

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
                                                    <option value="<?= $estado->id ?>"
                                                        <?= (($_POST['acessorio_estado'][0] ?? '') == $estado->id) ? 'selected' : '' ?>>
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

                                            <input type="text" class="form-control" name="consumivel_nome[]"
                                                value="<?= htmlspecialchars($_POST['consumivel_nome'][0] ?? '') ?>"
                                                placeholder="Ex: Filtro Bacteriano">

                                        </div>

                                        <div class="col-md-4">

                                            <label class="form-label fw-bold">
                                                Quantidade
                                            </label>

                                            <input type="number" class="form-control" name="consumivel_quantidade[]"
                                                value="<?= htmlspecialchars($_POST['consumivel_quantidade'][0] ?? '') ?>">

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
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" name="submeter_step3" class="btn btn-primary-custom">Seguinte</button>
                            </div>
                        </div>

                        <!-- Localização -->
                        <div class="tab-pane fade <?= ($step_atual == 4) ? 'show active' : '' ?>" id="localizacaoNovo">

                            <!-- ALERTAS -->
                            <div class="alert alert-danger mb-4" id="alertas-localizacao" <?= empty($erros_localizacao) ? 'style="display:none;"' : '' ?>>
                                <h6 class="alert-heading mb-2"><i class="fa-solid fa-circle-exclamation me-2"></i>Foram encontrados erros</h6>
                                <ul class="mb-0">
                                    <?php foreach ($erros_localizacao as $erro): ?>
                                        <li><?= htmlspecialchars($erro) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <label class="form-label fw-bold">
                                Localização Associada
                            </label>

                            <select class="form-select" name="id_localizacao">
                                <option value="">Selecionar localização</option>
                                <?php foreach ($localizacoes as $loc): ?>
                                    <option value="<?= $loc->id ?>"
                                        <?= (($_SESSION['equipamento']['id_localizacao'] ?? '') == $loc->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc->edificio . ' - Piso ' . $loc->piso . ' - ' . $loc->sala_gabinete) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" name="submeter_step4" class="btn btn-primary-custom">Seguinte</button>
                            </div>
                        </div>

                        <!-- Fornecedor -->
                        <div class="tab-pane fade <?= ($step_atual == 5) ? 'show active' : '' ?>" id="fornecedorNovo">

                            <!-- Alertas -->
                            <div class="alert alert-danger mb-4" id="alertas-fornecedor" <?= empty($erros_fornecedor) ? 'style="display:none;"' : '' ?>>
                                <h6 class="alert-heading mb-2"><i class="fa-solid fa-circle-exclamation me-2"></i>Foram encontrados erros</h6>
                                <ul class="mb-0">
                                    <?php foreach ($erros_fornecedor as $erro): ?>
                                        <li><?= htmlspecialchars($erro) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

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
                                                <option value="<?= $f->id ?>"
                                                    <?= (($_SESSION['equipamento']['fornecedores'][0]['id_fornecedor'] ?? '') == $f->id) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($f->codigo_fornecedor . ' - ' . $f->nome_empresa) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <select class="form-select" name="tipo_relacao[]">
                                            <option value="">Tipo de relação</option>
                                            <option value="Fabricante" <?= (($_SESSION['equipamento']['fornecedores'][0]['tipo_relacao'] ?? '') == 'Fabricante') ? 'selected' : '' ?>>Fabricante</option>
                                            <option value="Distribuidor" <?= (($_SESSION['equipamento']['fornecedores'][0]['tipo_relacao'] ?? '') == 'Distribuidor') ? 'selected' : '' ?>>Distribuidor</option>
                                            <option value="Assistência Técnica" <?= (($_SESSION['equipamento']['fornecedores'][0]['tipo_relacao'] ?? '') == 'Assistência Técnica') ? 'selected' : '' ?>>Assistência Técnica</option>
                                            <option value="Consumíveis / Acessórios" <?= (($_SESSION['equipamento']['fornecedores'][0]['tipo_relacao'] ?? '') == 'Consumível/ Acessório') ? 'selected' : '' ?>>Consumíveis / Acessórios</option>
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
                            <script>
                                const fornecedoresGuardados = <?= json_encode($_SESSION['equipamento']['fornecedores'] ?? []) ?>;
                            </script>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" name="submeter_step5" class="btn btn-primary-custom">Seguinte</button>
                            </div>

                        </div>

                        <!-- Garantias e Contratos -->
                        <div class="tab-pane fade <?= ($step_atual == 6) ? 'show active' : '' ?>" id="garantiasNovo">
                            <!-- ALERTAS -->
                            <div class="alert alert-danger mb-4" id="alertas-garantias" <?= empty($erros_garantias) ? 'style="display:none;"' : '' ?>>
                                <h6 class="alert-heading mb-2"><i class="fa-solid fa-circle-exclamation me-2"></i>Foram encontrados erros</h6>
                                <ul class="mb-0">
                                    <?php foreach ($erros_garantias as $erro): ?>
                                        <li><?= htmlspecialchars($erro) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <!-- GARANTIA -->

                            <div class="row g-4">

                                <div class="col-md-6">

                                    <label class="form-label fw-bold">
                                        Garantia Associada
                                    </label>

                                    <select class="form-select" id="temGarantia" name="tem_garantia">

                                        <option disabled <?= (($_SESSION['equipamento']['tem_garantia'] ?? '') == '...') ? 'selected' : '' ?>>
                                            Selecionar opção
                                        </option>

                                        <option value="sim" <?= (($_POST['tem_garantia'] ?? '') == 'sim') ? 'selected' : '' ?>>
                                            Sim
                                        </option>

                                        <option value="nao" <?= (($_POST['tem_garantia'] ?? '') == 'nao') ? 'selected' : '' ?>>
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

                                        <option disabled <?= (($_SESSION['equipamento']['tem_contrato'] ?? '') == '...') ? 'selected' : '' ?>>
                                            Selecionar opção
                                        </option>

                                        <option value="sim" <?= (($_POST['tem_contrato'] ?? '') == 'sim') ? 'selected' : '' ?>>
                                            Sim
                                        </option>

                                        <option value="nao" <?= (($_POST['tem_contrato'] ?? '') == 'nao') ? 'selected' : '' ?>>
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
                                            <option <?= (($_SESSION['equipamento']['tipo_contrato'] ?? '') == 'Manutenção Preventiva') ? 'selected' : '' ?>>Manutenção Preventiva</option>
                                            <option <?= (($_SESSION['equipamento']['tipo_contrato'] ?? '') == 'Manutenção Corretiva') ? 'selected' : '' ?>>Manutenção Corretiva</option>
                                            <option <?= (($_SESSION['equipamento']['tipo_contrato'] ?? '') == 'Manutenção Preventiva e Corretiva') ? 'selected' : '' ?>>Manutenção Preventiva e Corretiva</option>
                                        </select>

                                    </div>

                                    <div class="col-md-4">

                                        <label class="form-label fw-bold">
                                            Entidade Responsável
                                        </label>

                                        <input type="text" class="form-control" name="entidade_responsavel"
                                            value="<?= htmlspecialchars($_SESSION['equipamento']['entidade_responsavel'] ?? '') ?>">

                                    </div>

                                    <div class=" col-md-4">

                                        <label class="form-label fw-bold">
                                            Periodicidade
                                        </label>

                                        <select class="form-select" name="periodicidade">
                                            <option <?= (($_SESSION['equipamento']['periodicidade'] ?? '') == 'Mensal') ? 'selected' : '' ?>>Mensal</option>
                                            <option <?= (($_SESSION['equipamento']['periodicidade'] ?? '') == 'Trimestral') ? 'selected' : '' ?>>Trimestral</option>
                                            <option <?= (($_SESSION['equipamento']['periodicidade'] ?? '') == 'Semestral') ? 'selected' : '' ?>>Semestral</option>
                                            <option <?= (($_SESSION['equipamento']['periodicidade'] ?? '') == 'Anual') ? 'selected' : '' ?>>Anual</option>
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
                                                    placeholder="Ex: Certificado de Garantia 2024"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['doc_certificado_garantia']['nome'] ?? '') ?>">
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
                                                    name="certificado_garantia_data"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['doc_certificado_garantia']['data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="certificado_garantia_validade"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['doc_certificado_garantia']['validade'] ?? '') ?>">
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
                                                    placeholder="Ex: Contrato Manutenção Preventiva 2025"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['doc_contrato_manutencao']['nome'] ?? '') ?>">
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
                                                    name="contrato_manutencao_data"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['doc_contrato_manutencao']['data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="contrato_manutencao_validade"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['doc_contrato_manutencao']['validade'] ?? '') ?>">
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
                                                    placeholder="Ex: Certificado Calibração IPQ 2025"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['doc_certificado_calibracao']['nome'] ?? '') ?>">
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
                                                    name="certificado_calibracao_data"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['doc_certificado_calibracao']['data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="certificado_calibracao_validade"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['doc_certificado_calibracao']['validade'] ?? '') ?>">
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
                                                    placeholder="Ex: Relatório Calibração Anual 2025"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['doc_relatorio_calibracao']['nome'] ?? '') ?>">
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
                                                    name="relatorio_calibracao_data"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['doc_relatorio_calibracao']['data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="relatorio_calibracao_validade"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['doc_relatorio_calibracao']['validade'] ?? '') ?>">
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" name="submeter_step6" class="btn btn-primary-custom">Seguinte</button>
                            </div>
                        </div>

                        <!-- DOCUMENTAÇÃO -->
                        <div class="tab-pane fade <?= ($step_atual == 7) ? 'show active' : '' ?>" id="documentacaoNovo">

                            <!-- ALERTAS -->
                            <div class="alert alert-danger mb-4" id="alertas-documentacao" <?= empty($erros_documentacao) ? 'style="display:none;"' : '' ?>>
                                <h6 class="alert-heading mb-2"><i class="fa-solid fa-circle-exclamation me-2"></i>Foram encontrados erros</h6>
                                <ul class="mb-0">
                                    <?php foreach ($erros_documentacao as $erro): ?>
                                        <li><?= htmlspecialchars($erro) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

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
                                                <?php if (!empty($_SESSION['equipamento']['doc_manual_utilizacao']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento']['doc_manual_utilizacao']['nome']) ?></li>
                                                <?php endif; ?>
                                                <?php if (!empty($_SESSION['equipamento']['doc_manual_tecnico']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento']['doc_manual_tecnico']['nome']) ?></li>
                                                <?php endif; ?>
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
                                                <?php if (!empty($_SESSION['equipamento']['doc_fatura_aquisicao']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento']['doc_fatura_aquisicao']['nome']) ?></li>
                                                <?php endif; ?>
                                                <?php if (!empty($_SESSION['equipamento']['doc_contrato_aquisicao']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento']['doc_contrato_aquisicao']['nome']) ?></li>
                                                <?php endif; ?>
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
                                                <?php if (($_SESSION['equipamento']['tem_garantia'] ?? '') === 'sim' && !empty($_SESSION['equipamento']['doc_certificado_garantia']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento']['doc_certificado_garantia']['nome']) ?></li>
                                                <?php endif; ?>
                                                <?php if (($_SESSION['equipamento']['tem_contrato'] ?? '') === 'sim' && !empty($_SESSION['equipamento']['doc_contrato_manutencao']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento']['doc_contrato_manutencao']['nome']) ?></li>
                                                <?php endif; ?>
                                                <?php if (!empty($_SESSION['equipamento']['doc_certificado_calibracao']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento']['doc_certificado_calibracao']['nome']) ?></li>
                                                <?php endif; ?>
                                                <?php if (!empty($_SESSION['equipamento']['doc_relatorio_calibracao']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento']['doc_relatorio_calibracao']['nome']) ?></li>
                                                <?php endif; ?>
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

                                    <select class="form-select" id="temDocumentacaoAdicional" name="tem_documentacao_adicional">

                                        <option disabled <?= (($_SESSION['equipamento']['tem_documento_adicional'] ?? '') == '...') ? 'selected' : '' ?>>
                                            Selecionar opção
                                        </option>

                                        <option value="sim" <?= (($_SESSION['equipamento']['tem_documentacao_adicional'] ?? '') == 'sim') ? 'selected' : '' ?>>
                                            Sim
                                        </option>

                                        <option value="nao" <?= (($_SESSION['equipamento']['tem_documentacao_adicional'] ?? '') == 'nao') ? 'selected' : '' ?>>
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
                                                    name="nome_documento_adicional[]"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['documentos_adicionais'][0]['nome'] ?? '') ?>">

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
                                                    name="data_documento_adicional[]"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['documentos_adicionais'][0]['data'] ?? '') ?>">

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Data de Validade
                                                </label>

                                                <input type="date" class="form-control"
                                                    name="validade_documento_adicional[]"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento']['documentos_adicionais'][0]['validade'] ?? '') ?>">

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
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" name="submeter_step7" class="btn btn-primary-custom">Seguinte</button>
                            </div>
                        </div>

                        <!-- Observações -->
                        <div class="tab-pane fade <?= ($step_atual == 8) ? 'show active' : '' ?>" id="observacoesNovo">
                            <!-- ALERTAS -->
                            <div class="alert alert-danger mb-4" id="alertas-observacoes" style="display:none;">
                                <h6 class="alert-heading mb-2"><i class="fa-solid fa-circle-exclamation me-2"></i>Foram encontrados erros</h6>
                                <ul class="mb-0"></ul>
                            </div>

                            <label class="form-label fw-bold">
                                Observações
                            </label>

                            <textarea class="form-control"
                                name="observacoes"
                                rows="6"><?= htmlspecialchars($_SESSION['equipamento']['observacoes'] ?? '') ?></textarea>
                            <div class="d-flex justify-content-between mt-4">
                                <div class="d-flex gap-3">
                                    <a href="equipamentos.php" class="btn btn-outline-secondary">Cancelar</a>
                                    <button type="submit"
                                        name="submeter_step8"
                                        class="btn btn-primary-custom">
                                        <i class="fa-solid fa-floppy-disk me-2"></i>
                                        Guardar Equipamento
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </form>
    </main>
</div>

<script>
    const errosPorAba = {
        dadosGeraisNovo: <?= json_encode(!empty($erros_dados_gerais)) ?>,
        aquisicaoNovo: <?= json_encode(!empty($erros_aquisicao)) ?>,
        acessoriosNovo: false,
        localizacaoNovo: <?= json_encode(!empty($erros_localizacao)) ?>,
        fornecedorNovo: <?= json_encode(!empty($erros_fornecedor)) ?>,
        garantiasNovo: <?= json_encode(!empty($erros_garantias)) ?>,
        documentacaoNovo: <?= json_encode(!empty($erros_documentacao)) ?>,
        observacoesNovo: false
    };
</script>

<?php include '../../includes/footer.php'; ?>