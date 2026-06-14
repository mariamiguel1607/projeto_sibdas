<?php
// --------------------------------------------------------------------
// SEGURANÇA: Proteção de acesso à página de edição
// Este ficheiro deve ser acedido apenas por utilizadores autenticados.
// Caso não exista sessão iniciada, o utilizador será redirecionado para o login.
// --------------------------------------------------------------------
require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/validacoes.php';
redirect_if_not_logged(); // Inicia a sessão (se necessário) e verifica se o utilizador está autenticado
if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/private/login/login.php');
    exit;
}

// Desencriptar o ID do equipamento
$idEquipamentoEncrypted = $_GET['id_equipamento'] ?? $_POST['id_equipamento'] ?? null;
$idEquipamento = aes_decrypt($idEquipamentoEncrypted);

if (!$idEquipamento || !is_numeric($idEquipamento)) {
    header('Location: ' . BASE_URL . '/private/views/equipamentos/equipamentos.php');
    exit;
}
// Sessão própria para edição
if (!isset($_SESSION['edit_step']) || ($_SESSION['edit_equip_id'] ?? null) != $idEquipamento) {
    $_SESSION['edit_step'] = 1;
    $_SESSION['edit_equip_id'] = $idEquipamento;
    $_SESSION['equipamento_edit'] = [];
}

// Sempre que abre via GET (botão "Editar"), recarrega dados frescos da BD
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $_SESSION['equipamento_edit'] = [];
}

$step_atual = $_SESSION['edit_step'];

$erros = [];
$erro_sistema = "";
$erros_dados_gerais = [];
$erros_aquisicao    = [];
$erros_localizacao  = [];
$erros_fornecedor   = [];
$erros_garantias    = [];
$erros_documentacao = [];
$erros_observacoes  = [];



if (isset($_POST['submeter_edit_step1'])) {

    $pasta_uploads = __DIR__ . '/../../../assets/uploads/documentos/';
    $caminho_relativo = BASE_URL . '/assets/uploads/documentos/';

    $caminho_mu = $_SESSION['equipamento_edit']['doc_manual_utilizacao']['caminho'] ?? null;
    if (!empty($_FILES['manual_utilizacao']['name'])) {
        $res = fazer_upload_pdf($_FILES['manual_utilizacao'], $pasta_uploads);
        if ($res) $caminho_mu = $caminho_relativo . $res;
    }

    $caminho_mt = $_SESSION['equipamento_edit']['doc_manual_tecnico']['caminho'] ?? null;
    if (!empty($_FILES['manual_tecnico']['name'])) {
        $res = fazer_upload_pdf($_FILES['manual_tecnico'], $pasta_uploads);
        if ($res) $caminho_mt = $caminho_relativo . $res;
    }

    $_SESSION['equipamento_edit']['designacao']   = ucwords(strtolower(trim($_POST['designacao'] ?? '')));
    $_SESSION['equipamento_edit']['id_categoria'] = $_POST['id_categoria'] ?? '';
    $_SESSION['equipamento_edit']['fabricante']   = ucwords(strtolower(trim($_POST['fabricante'] ?? '')));
    $_SESSION['equipamento_edit']['marca']        = ucwords(strtolower(trim($_POST['marca'] ?? '')));
    $_SESSION['equipamento_edit']['modelo']       = trim($_POST['modelo'] ?? '');
    $_SESSION['equipamento_edit']['num_serie']    = trim($_POST['num_serie'] ?? '');
    $_SESSION['equipamento_edit']['ano_fabrico']  = trim($_POST['ano_fabrico'] ?? '');
    $_SESSION['equipamento_edit']['criticidade']  = $_POST['criticidade'] ?? '';

    $_SESSION['equipamento_edit']['doc_manual_utilizacao'] = [
        'nome'     => trim($_POST['nome_documento_manual_utilizacao'] ?? ''),
        'caminho'  => $caminho_mu,
        'data'     => $_POST['manual_utilizacao_data'] ?? '',
        'validade' => $_POST['manual_utilizacao_validade'] ?? '',
    ];
    $_SESSION['equipamento_edit']['doc_manual_tecnico'] = [
        'nome'     => trim($_POST['nome_documento_manual_tecnico'] ?? ''),
        'caminho'  => $caminho_mt,
        'data'     => $_POST['manual_tecnico_data'] ?? '',
        'validade' => $_POST['manual_tecnico_validade'] ?? '',
    ];

    $erros_dados_gerais = validar_step_dados_gerais($_POST, $_FILES, $_SESSION['equipamento_edit']);

    if (empty($erros_dados_gerais)) {
        $_SESSION['edit_step'] = 2;
        header('Location: editar_equipamentos.php?id_equipamento=' . $idEquipamentoEncrypted);
        exit;
    }

    $step_atual = 1;
}

if (isset($_POST['submeter_edit_step2'])) {

    $pasta_uploads = __DIR__ . '/../../../assets/uploads/documentos/';
    $caminho_relativo = BASE_URL . '/assets/uploads/documentos/';

    $caminho_fatura = $_SESSION['equipamento_edit']['doc_fatura_aquisicao']['caminho'] ?? null;
    if (!empty($_FILES['fatura_aquisicao']['name'])) {
        $res = fazer_upload_pdf($_FILES['fatura_aquisicao'], $pasta_uploads);
        if ($res) $caminho_fatura = $caminho_relativo . $res;
    }

    $caminho_contrato_aq = $_SESSION['equipamento_edit']['doc_contrato_aquisicao']['caminho'] ?? null;
    if (!empty($_FILES['contrato_aquisicao']['name'])) {
        $res = fazer_upload_pdf($_FILES['contrato_aquisicao'], $pasta_uploads);
        if ($res) $caminho_contrato_aq = $caminho_relativo . $res;
    }

    $_SESSION['equipamento_edit']['data_aquisicao']  = trim($_POST['data_aquisicao'] ?? '');
    $_SESSION['equipamento_edit']['custo_aquisicao'] = trim($_POST['custo_aquisicao'] ?? '');
    $_SESSION['equipamento_edit']['tipo_entrada']    = $_POST['tipo_entrada'] ?? '';
    $_SESSION['equipamento_edit']['id_estado']       = $_POST['id_estado'] ?? '';

    $_SESSION['equipamento_edit']['doc_fatura_aquisicao'] = [
        'nome'     => trim($_POST['nome_documento_fatura_aquisicao'] ?? ''),
        'caminho'  => $caminho_fatura,
        'data'     => $_POST['fatura_aquisicao_data'] ?? '',
        'validade' => $_POST['fatura_aquisicao_validade'] ?? '',
    ];
    $_SESSION['equipamento_edit']['doc_contrato_aquisicao'] = [
        'nome'     => trim($_POST['nome_documento_contrato_aquisicao'] ?? ''),
        'caminho'  => $caminho_contrato_aq,
        'data'     => $_POST['contrato_aquisicao_data'] ?? '',
        'validade' => $_POST['contrato_aquisicao_validade'] ?? '',
    ];

    $erros_aquisicao    = validar_step_aquisicao($_POST, $_FILES, $_SESSION['equipamento_edit']);

    if (empty($erros_aquisicao)) {
        $_SESSION['edit_step'] = 3;
        header('Location: editar_equipamentos.php?id_equipamento=' . $idEquipamentoEncrypted);
        exit;
    }

    $step_atual = 2;
}

if (isset($_POST['submeter_edit_step3'])) {

    $tem_acessorios  = $_POST['tem_acessorios'] ?? '';
    $tem_consumiveis = $_POST['tem_consumiveis'] ?? '';

    $acessorio_nome       = $_POST['acessorio_nome'] ?? [];
    $acessorio_quantidade = $_POST['acessorio_quantidade'] ?? [];
    $acessorio_estado     = $_POST['acessorio_estado'] ?? [];

    $consumivel_nome       = $_POST['consumivel_nome'] ?? [];
    $consumivel_quantidade = $_POST['consumivel_quantidade'] ?? [];

    $_SESSION['equipamento_edit']['tem_acessorios']  = $tem_acessorios;
    $_SESSION['equipamento_edit']['tem_consumiveis'] = $tem_consumiveis;

    $_SESSION['equipamento_edit']['acessorios'] = [];
    foreach ($acessorio_nome as $i => $nome) {
        $nome = trim($nome);
        if (empty($nome)) continue;
        $_SESSION['equipamento_edit']['acessorios'][] = [
            'nome'       => $nome,
            'quantidade' => $acessorio_quantidade[$i] ?? '',
            'id_estado'  => $acessorio_estado[$i] ?? '',
        ];
    }

    $_SESSION['equipamento_edit']['consumiveis'] = [];
    foreach ($consumivel_nome as $i => $nome) {
        $nome = trim($nome);
        if (empty($nome)) continue;
        $_SESSION['equipamento_edit']['consumiveis'][] = [
            'nome'       => $nome,
            'quantidade' => $consumivel_quantidade[$i] ?? '',
        ];
    }

    $_SESSION['edit_step'] = 4;
    header('Location: editar_equipamentos.php?id_equipamento=' . $idEquipamentoEncrypted);
    exit;
}

if (isset($_POST['submeter_edit_step4'])) {

    $_SESSION['equipamento_edit']['id_localizacao'] = $_POST['id_localizacao'] ?? '';

    $erros_localizacao = validar_step_localizacao($_POST);

    if (empty($erros_localizacao)) {
        $_SESSION['edit_step'] = 5;
        header('Location: editar_equipamentos.php?id_equipamento=' . $idEquipamentoEncrypted);
        exit;
    }

    $step_atual = 4;
}
if (isset($_POST['submeter_edit_step5'])) {

    $ids_fornecedor = $_POST['id_fornecedor'] ?? [];
    $tipos_relacao  = $_POST['tipo_relacao']  ?? [];

    $fornecedores_validos = [];
    foreach ($ids_fornecedor as $i => $id_forn) {
        if (!empty($id_forn) && !empty($tipos_relacao[$i])) {
            $fornecedores_validos[] = [
                'id_fornecedor' => $id_forn,
                'tipo_relacao'  => $tipos_relacao[$i],
            ];
        }
    }

    $_SESSION['equipamento_edit']['fornecedores'] = $fornecedores_validos;

    $erros_fornecedor = validar_step_fornecedor($ids_fornecedor, $tipos_relacao);

    if (empty($erros_fornecedor)) {
        $_SESSION['edit_step'] = 6;
        header('Location: editar_equipamentos.php?id_equipamento=' . $idEquipamentoEncrypted);
        exit;
    }

    $step_atual = 5;
}
if (isset($_POST['submeter_edit_step6'])) {

    $tem_garantia = $_POST['tem_garantia'] ?? '';
    $tem_contrato = $_POST['tem_contrato'] ?? '';
    $tipo_contrato = $_POST['tipo_contrato'] ?? '';
    $entidade_responsavel = trim($_POST['entidade_responsavel'] ?? '');
    $periodicidade = $_POST['periodicidade'] ?? '';

    $pasta_uploads = __DIR__ . '/../../../assets/uploads/documentos/';
    $caminho_relativo = BASE_URL . '/assets/uploads/documentos/';

    // Mantém caminho antigo, ou faz upload se enviado novo ficheiro
    $caminho_cert_garantia = $_SESSION['equipamento_edit']['doc_certificado_garantia']['caminho'] ?? null;
    if (!empty($_FILES['certificado_garantia']['name'])) {
        $res = fazer_upload_pdf($_FILES['certificado_garantia'], $pasta_uploads);
        if ($res) $caminho_cert_garantia = $caminho_relativo . $res;
    }

    $caminho_contrato_manut = $_SESSION['equipamento_edit']['doc_contrato_manutencao']['caminho'] ?? null;
    if (!empty($_FILES['contrato_manutencao']['name'])) {
        $res = fazer_upload_pdf($_FILES['contrato_manutencao'], $pasta_uploads);
        if ($res) $caminho_contrato_manut = $caminho_relativo . $res;
    }

    $caminho_cert_calib = $_SESSION['equipamento_edit']['doc_certificado_calibracao']['caminho'] ?? null;
    if (!empty($_FILES['certificado_calibracao']['name'])) {
        $res = fazer_upload_pdf($_FILES['certificado_calibracao'], $pasta_uploads);
        if ($res) $caminho_cert_calib = $caminho_relativo . $res;
    }

    $caminho_relat_calib = $_SESSION['equipamento_edit']['doc_relatorio_calibracao']['caminho'] ?? null;
    if (!empty($_FILES['relatorio_calibracao']['name'])) {
        $res = fazer_upload_pdf($_FILES['relatorio_calibracao'], $pasta_uploads);
        if ($res) $caminho_relat_calib = $caminho_relativo . $res;
    }

    // Atualiza a sessão SEMPRE (mesmo se houver erro a seguir)
    $_SESSION['equipamento_edit']['tem_garantia'] = $tem_garantia;
    $_SESSION['equipamento_edit']['tem_contrato'] = $tem_contrato;
    $_SESSION['equipamento_edit']['tipo_contrato'] = $tipo_contrato;
    $_SESSION['equipamento_edit']['entidade_responsavel'] = $entidade_responsavel;
    $_SESSION['equipamento_edit']['periodicidade'] = $periodicidade;

    $_SESSION['equipamento_edit']['doc_certificado_garantia'] = [
        'nome' => trim($_POST['nome_documento_certificado_garantia'] ?? ''),
        'caminho' => $caminho_cert_garantia,
        'data' => $_POST['certificado_garantia_data'] ?? '',
        'validade' => $_POST['certificado_garantia_validade'] ?? '',
    ];
    $_SESSION['equipamento_edit']['doc_contrato_manutencao'] = [
        'nome' => trim($_POST['nome_documento_contrato_manutencao'] ?? ''),
        'caminho' => $caminho_contrato_manut,
        'data' => $_POST['contrato_manutencao_data'] ?? '',
        'validade' => $_POST['contrato_manutencao_validade'] ?? '',
    ];
    $_SESSION['equipamento_edit']['doc_certificado_calibracao'] = [
        'nome' => trim($_POST['nome_documento_certificado_calibracao'] ?? ''),
        'caminho' => $caminho_cert_calib,
        'data' => $_POST['certificado_calibracao_data'] ?? '',
        'validade' => $_POST['certificado_calibracao_validade'] ?? '',
    ];
    $_SESSION['equipamento_edit']['doc_relatorio_calibracao'] = [
        'nome' => trim($_POST['nome_documento_relatorio_calibracao'] ?? ''),
        'caminho' => $caminho_relat_calib,
        'data' => $_POST['relatorio_calibracao_data'] ?? '',
        'validade' => $_POST['relatorio_calibracao_validade'] ?? '',
    ];

    // Agora valida (usa os dados já atualizados na sessão)
    $erros_garantias    = validar_step_garantias($_POST, $_FILES, $_SESSION['equipamento_edit']);

    if (empty($erros_garantias)) {
        $_SESSION['edit_step'] = 7;
        header('Location: editar_equipamentos.php?id_equipamento=' . $idEquipamentoEncrypted);
        exit;
    }

    $step_atual = 6;
}
if (isset($_POST['submeter_edit_step7'])) {

    $pasta_uploads = __DIR__ . '/../../../assets/uploads/documentos/';
    $caminho_relativo = BASE_URL . '/assets/uploads/documentos/';

    $tem_documentacao_adicional = $_POST['tem_documentacao_adicional'] ?? '';

    $nomes_doc_adicional = $_POST['nome_documento_adicional'] ?? [];
    $datas_doc_adicional = $_POST['data_documento_adicional'] ?? [];
    $validades_doc_adicional = $_POST['validade_documento_adicional'] ?? [];
    $ficheiros_doc_adicional = $_FILES['ficheiro_documento_adicional'] ?? [];

    $_SESSION['equipamento_edit']['tem_documentacao_adicional'] = $tem_documentacao_adicional;

    $documentos_existentes = $_SESSION['equipamento_edit']['documentos_adicionais'] ?? [];
    $novos_documentos = [];

    foreach ($nomes_doc_adicional as $i => $nome) {
        $nome = trim($nome);
        if (empty($nome)) continue;

        $caminho = $documentos_existentes[$i]['caminho'] ?? null;

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

        $novos_documentos[] = [
            'nome' => $nome,
            'caminho' => $caminho,
            'data' => $datas_doc_adicional[$i] ?? '',
            'validade' => $validades_doc_adicional[$i] ?? '',
        ];
    }

    $_SESSION['equipamento_edit']['documentos_adicionais'] = $novos_documentos;

    $_SESSION['edit_step'] = 8;
    header('Location: editar_equipamentos.php?id_equipamento=' . $idEquipamentoEncrypted);
    exit;
}
if (isset($_POST['submeter_edit_step8'])) {

    $observacoes = trim($_POST['observacoes'] ?? '');

    $erros_observacoes = validar_step_observacoes($observacoes);

    if (empty($erros_observacoes)) {

        $_SESSION['equipamento_edit']['observacoes'] = $observacoes;
        try {
            $ligacao = ligar_bd();
            $ligacao->beginTransaction();

            $eq = $_SESSION['equipamento_edit'];

            // 1. UPDATE equipamentos
            $stmt = $ligacao->prepare("
                UPDATE equipamentos SET
                    designacao      = :designacao,
                    id_categoria    = :id_categoria,
                    fabricante      = :fabricante,
                    marca           = :marca,
                    modelo          = :modelo,
                    num_serie       = :num_serie,
                    ano_fabrico     = :ano_fabrico,
                    criticidade     = :criticidade,
                    data_aquisicao  = :data_aquisicao,
                    custo_aquisicao = :custo_aquisicao,
                    tipo_entrada    = :tipo_entrada,
                    id_estado       = :id_estado,
                    id_localizacao  = :id_localizacao,
                    observacoes     = :observacoes
                WHERE id = :id
            ");
            $stmt->execute([
                ':designacao'      => $eq['designacao'],
                ':id_categoria'    => $eq['id_categoria'],
                ':fabricante'      => $eq['fabricante'],
                ':marca'           => $eq['marca'],
                ':modelo'          => $eq['modelo'],
                ':num_serie'       => $eq['num_serie'],
                ':ano_fabrico'     => $eq['ano_fabrico'] ?: null,
                ':criticidade'     => $eq['criticidade'],
                ':data_aquisicao'  => $eq['data_aquisicao'] ?: null,
                ':custo_aquisicao' => $eq['custo_aquisicao'] ?: null,
                ':tipo_entrada'    => $eq['tipo_entrada'],
                ':id_estado'       => $eq['id_estado'],
                ':id_localizacao'  => $eq['id_localizacao'],
                ':observacoes'     => $eq['observacoes'],
                ':id'              => $idEquipamento,
            ]);

            // 2. Fornecedores — DELETE + INSERT
            $ligacao->prepare("DELETE FROM equipamentos_fornecedores WHERE id_equipamento = :id")
                ->execute([':id' => $idEquipamento]);

            foreach ($eq['fornecedores'] as $forn) {
                $stmt = $ligacao->prepare("
                    INSERT INTO equipamentos_fornecedores (id_equipamento, id_fornecedor, tipo_relacao)
                    VALUES (:id_equipamento, :id_fornecedor, :tipo_relacao)
                ");
                $stmt->execute([
                    ':id_equipamento' => $idEquipamento,
                    ':id_fornecedor'  => $forn['id_fornecedor'],
                    ':tipo_relacao'   => $forn['tipo_relacao'],
                ]);
            }

            // 3. Acessórios — DELETE + INSERT
            $ligacao->prepare("DELETE FROM acessorios WHERE id_equipamento = :id")
                ->execute([':id' => $idEquipamento]);

            if (!empty($eq['acessorios'])) {
                foreach ($eq['acessorios'] as $ac) {
                    $stmt = $ligacao->prepare("
                        INSERT INTO acessorios (id_equipamento, nome, quantidade, id_estado)
                        VALUES (:id_equipamento, :nome, :quantidade, :id_estado)
                    ");
                    $stmt->execute([
                        ':id_equipamento' => $idEquipamento,
                        ':nome'           => $ac['nome'],
                        ':quantidade'     => $ac['quantidade'] ?: null,
                        ':id_estado'      => $ac['id_estado'] ?: null,
                    ]);
                }
            }

            // 4. Consumíveis — DELETE + INSERT
            $ligacao->prepare("DELETE FROM consumiveis WHERE id_equipamento = :id")
                ->execute([':id' => $idEquipamento]);

            if (!empty($eq['consumiveis'])) {
                foreach ($eq['consumiveis'] as $con) {
                    $stmt = $ligacao->prepare("
                        INSERT INTO consumiveis (id_equipamento, nome, quantidade)
                        VALUES (:id_equipamento, :nome, :quantidade)
                    ");
                    $stmt->execute([
                        ':id_equipamento' => $idEquipamento,
                        ':nome'           => $con['nome'],
                        ':quantidade'     => $con['quantidade'] ?: null,
                    ]);
                }
            }

            // 5. Documentos — DELETE + INSERT (só tipos 1-8)
            $ligacao->prepare("
                DELETE FROM documentacao 
                WHERE id_equipamento = :id AND id_tipo_documento BETWEEN 1 AND 8
            ")->execute([':id' => $idEquipamento]);

            $mapa_docs = [
                'doc_manual_utilizacao'    => 1,
                'doc_manual_tecnico'       => 2,
                'doc_fatura_aquisicao'     => 3,
                'doc_contrato_aquisicao'   => 4,
                'doc_certificado_garantia' => 5,
                'doc_contrato_manutencao'  => 6,
                'doc_certificado_calibracao' => 7,
                'doc_relatorio_calibracao'   => 8,
            ];

            foreach ($mapa_docs as $chave => $idTipo) {
                if (!empty($eq[$chave]['nome'])) {
                    $idDoc = inserirDocumento($ligacao, $idEquipamento, $idTipo, $eq[$chave]);
                }
            }

            // 6. Documentos adicionais — DELETE tipo > 8 + INSERT
            $ligacao->prepare("
                DELETE FROM documentacao 
                WHERE id_equipamento = :id AND id_tipo_documento NOT BETWEEN 1 AND 8
            ")->execute([':id' => $idEquipamento]);

            if (!empty($eq['documentos_adicionais'])) {
                foreach ($eq['documentos_adicionais'] as $doc) {
                    if (!empty($doc['nome'])) {
                        inserirDocumento($ligacao, $idEquipamento, 12, $doc);
                    }
                }
            }

            // 7. Contrato de manutenção — DELETE + INSERT se existir
            $ligacao->prepare("DELETE FROM contratos WHERE id_documento IN (
                SELECT id FROM documentacao WHERE id_equipamento = :id AND id_tipo_documento = 6
            )")->execute([':id' => $idEquipamento]);

            // O contrato já foi inserido acima (tipo 6), precisamos do seu ID
            if (($eq['tem_contrato'] ?? '') === 'sim' && !empty($eq['doc_contrato_manutencao']['nome'])) {
                $stmt = $ligacao->prepare("
                    SELECT id FROM documentacao 
                    WHERE id_equipamento = :id AND id_tipo_documento = 6 
                    ORDER BY id DESC LIMIT 1
                ");
                $stmt->execute([':id' => $idEquipamento]);
                $idDocContrato = $stmt->fetchColumn();

                if ($idDocContrato) {
                    $stmt = $ligacao->prepare("
                        INSERT INTO contratos (id_documento, tipo_contrato, periodicidade, entidade_responsavel)
                        VALUES (:id_documento, :tipo_contrato, :periodicidade, :entidade_responsavel)
                    ");
                    $stmt->execute([
                        ':id_documento'        => $idDocContrato,
                        ':tipo_contrato'       => $eq['tipo_contrato'],
                        ':periodicidade'       => $eq['periodicidade'],
                        ':entidade_responsavel' => $eq['entidade_responsavel'] ?: null,
                    ]);
                }
            }

            $ligacao->commit();
            $ligacao = null;

            // Limpar sessão de edição
            unset($_SESSION['equipamento_edit']);
            unset($_SESSION['edit_step']);
            unset($_SESSION['edit_equip_id']);

            header('Location: ' . BASE_URL . '/private/views/equipamentos/equipamentos.php?sucesso=editado');
            exit;
        } catch (PDOException $err) {
            if ($ligacao) $ligacao->rollBack();
            $erros_observacoes[] = "Erro ao guardar: " . $err->getMessage();
        }
    }

    $step_atual = 8;
}


// Se a sessão de edição ainda está vazia, carregar dados da BD e pré-preencher
if (empty($_SESSION['equipamento_edit'])) {

    try {
        $ligacao = ligar_bd();

        $stmt = $ligacao->prepare("SELECT * FROM equipamentos WHERE id = :id");
        $stmt->bindParam(':id', $idEquipamento, PDO::PARAM_INT);
        $stmt->execute();
        $equipamento = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$equipamento) {
            header('Location: ' . BASE_URL . '/private/views/equipamentos/equipamentos.php');
            exit;
        }

        // Pré-popula a sessão com os dados atuais do equipamento
        $_SESSION['equipamento_edit']['codigo_interno'] = $equipamento->codigo_interno;
        $_SESSION['equipamento_edit']['designacao']   = $equipamento->designacao;
        $_SESSION['equipamento_edit']['id_categoria'] = $equipamento->id_categoria;
        $_SESSION['equipamento_edit']['fabricante']   = $equipamento->fabricante;
        $_SESSION['equipamento_edit']['marca']        = $equipamento->marca;
        $_SESSION['equipamento_edit']['modelo']       = $equipamento->modelo;
        $_SESSION['equipamento_edit']['num_serie']    = $equipamento->num_serie;
        $_SESSION['equipamento_edit']['ano_fabrico']  = $equipamento->ano_fabrico;
        $_SESSION['equipamento_edit']['criticidade']  = $equipamento->criticidade;
        $_SESSION['equipamento_edit']['data_aquisicao']  = $equipamento->data_aquisicao;
        $_SESSION['equipamento_edit']['custo_aquisicao'] = $equipamento->custo_aquisicao;
        $_SESSION['equipamento_edit']['tipo_entrada']    = $equipamento->tipo_entrada;
        $_SESSION['equipamento_edit']['id_estado']       = $equipamento->id_estado;
        $_SESSION['equipamento_edit']['id_localizacao']  = $equipamento->id_localizacao;
        $_SESSION['equipamento_edit']['observacoes']     = $equipamento->observacoes;



        // Fornecedores associados
        $stmt = $ligacao->prepare("SELECT id_fornecedor, tipo_relacao FROM equipamentos_fornecedores WHERE id_equipamento = :id");
        $stmt->bindParam(':id', $idEquipamento, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['equipamento_edit']['fornecedores'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Acessórios
        $stmt = $ligacao->prepare("SELECT nome, quantidade, id_estado FROM acessorios WHERE id_equipamento = :id");
        $stmt->bindParam(':id', $idEquipamento, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['equipamento_edit']['acessorios'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $_SESSION['equipamento_edit']['tem_acessorios'] = !empty($_SESSION['equipamento_edit']['acessorios']) ? 'sim' : 'nao';

        // Consumíveis
        $stmt = $ligacao->prepare("SELECT nome, quantidade FROM consumiveis WHERE id_equipamento = :id");
        $stmt->bindParam(':id', $idEquipamento, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['equipamento_edit']['consumiveis'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $_SESSION['equipamento_edit']['tem_consumiveis'] = !empty($_SESSION['equipamento_edit']['consumiveis']) ? 'sim' : 'nao';

        // Documentos (por tipo)
        $stmt = $ligacao->prepare("SELECT * FROM documentacao WHERE id_equipamento = :id");
        $stmt->bindParam(':id', $idEquipamento, PDO::PARAM_INT);
        $stmt->execute();
        $documentos = $stmt->fetchAll(PDO::FETCH_OBJ);

        $mapa_docs = [
            1 => 'doc_manual_utilizacao',
            2 => 'doc_manual_tecnico',
            3 => 'doc_fatura_aquisicao',
            4 => 'doc_contrato_aquisicao',
            5 => 'doc_certificado_garantia',
            6 => 'doc_contrato_manutencao',
            7 => 'doc_certificado_calibracao',
            8 => 'doc_relatorio_calibracao',
        ];

        $_SESSION['equipamento_edit']['documentos_adicionais'] = [];

        foreach ($documentos as $doc) {
            $info = [
                'nome'     => $doc->nome_documento,
                'caminho'  => $doc->caminho_ficheiro,
                'data'     => $doc->data_documento,
                'validade' => $doc->data_validade,
            ];

            if (isset($mapa_docs[$doc->id_tipo_documento])) {
                $_SESSION['equipamento_edit'][$mapa_docs[$doc->id_tipo_documento]] = $info;
            } else {
                // tipo 12 = "Outro" → documento adicional
                $_SESSION['equipamento_edit']['documentos_adicionais'][] = $info;
            }
        }

        $_SESSION['equipamento_edit']['tem_documentacao_adicional'] =
            !empty($_SESSION['equipamento_edit']['documentos_adicionais']) ? 'sim' : 'nao';

        // Contrato de manutenção (se existir)
        $stmt = $ligacao->prepare("
            SELECT c.*, d.id as id_documento
            FROM contratos c
            JOIN documentacao d ON d.id = c.id_documento
            WHERE d.id_equipamento = :id AND d.id_tipo_documento = 6
        ");
        $stmt->bindParam(':id', $idEquipamento, PDO::PARAM_INT);
        $stmt->execute();
        $contrato = $stmt->fetch(PDO::FETCH_OBJ);

        if ($contrato) {
            $_SESSION['equipamento_edit']['tem_contrato'] = 'sim';
            $_SESSION['equipamento_edit']['tipo_contrato'] = $contrato->tipo_contrato;
            $_SESSION['equipamento_edit']['periodicidade'] = $contrato->periodicidade;
            $_SESSION['equipamento_edit']['entidade_responsavel'] = $contrato->entidade_responsavel;
        } else {
            $_SESSION['equipamento_edit']['tem_contrato'] = 'nao';
        }

        // Garantia (existe se houver documento tipo 5)
        $_SESSION['equipamento_edit']['tem_garantia'] =
            !empty($_SESSION['equipamento_edit']['doc_certificado_garantia']) ? 'sim' : 'nao';

        $ligacao = null;
    } catch (PDOException $err) {
        $erro_sistema = "Erro ao carregar dados do equipamento.";
    }
}



try {
    $ligacao = ligar_bd();

    $categorias    = $ligacao->query("SELECT * FROM categorias ORDER BY nome_categoria")->fetchAll(PDO::FETCH_OBJ);
    $estados       = $ligacao->query("SELECT * FROM estados")->fetchAll(PDO::FETCH_OBJ);
    $localizacoes  = $ligacao->query("SELECT * FROM localizacoes")->fetchAll(PDO::FETCH_OBJ);
    $fornecedores  = $ligacao->query("SELECT * FROM fornecedores ORDER BY codigo_fornecedor")->fetchAll(PDO::FETCH_OBJ);

    $ligacao = null;
} catch (PDOException $err) {
    $erro_sistema = "Erro ao carregar dados auxiliares.";
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
                <h1 class="fw-bold mb-1">Editar Equipamento</h1>
                <p class="text-muted mb-0">
                    Atualização dos dados técnicos e administrativos do equipamento.
                </p>
            </div>

            <div class="d-flex gap-2">
                <a href="equipamentos.php" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Voltar
                </a>
            </div>

        </div>

        <!-- FORMULÁRIO -->
        <form action="editar_equipamentos.php?id_equipamento=<?= htmlspecialchars($idEquipamentoEncrypted) ?>" method="post" novalidate enctype="multipart/form-data">
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
                                        value="<?= htmlspecialchars($_SESSION['equipamento_edit']['codigo_interno'] ?? '') ?>"
                                        readonly>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Designação</label>
                                    <input type="text" class="form-control" name="designacao" value="<?= htmlspecialchars($_SESSION['equipamento_edit']['designacao'] ?? '') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Categoria</label>
                                    <select class="form-select" name="id_categoria">
                                        <option value="">Selecione uma categoria</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?= $categoria->id ?>"
                                                <?= (($_SESSION['equipamento_edit']['id_categoria'] ?? '') == $categoria->id) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($categoria->nome_categoria) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Fabricante</label>
                                    <input type="text" class="form-control" name="fabricante" value="<?= htmlspecialchars($_SESSION['equipamento_edit']['fabricante'] ?? '') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Marca</label>
                                    <input type="text" class="form-control" name="marca" value="<?= htmlspecialchars($_SESSION['equipamento_edit']['marca'] ?? '') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Modelo</label>
                                    <input type="text" class="form-control" name="modelo" value="<?= htmlspecialchars($_SESSION['equipamento_edit']['modelo'] ?? '') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Número de Série</label>
                                    <input type="text" class="form-control" name="num_serie" value="<?= htmlspecialchars($_SESSION['equipamento_edit']['num_serie'] ?? '') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Ano de Fabrico</label>
                                    <input type="number" class="form-control" min="1900"
                                        max="<?= date('Y') ?>" name="ano_fabrico" value="<?= htmlspecialchars($_SESSION['equipamento_edit']['ano_fabrico'] ?? '') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Criticidade</label>
                                    <select class="form-select" name="criticidade">

                                        <option value="">Selecione a criticidade</option>
                                        <option value="Baixa"
                                            <?= (($_SESSION['equipamento_edit']['criticidade'] ?? '') == 'Baixa') ? 'selected' : '' ?>>
                                            Baixa
                                        </option>
                                        <option value="Média"
                                            <?= (($_SESSION['equipamento_edit']['criticidade'] ?? '') == 'Média') ? 'selected' : '' ?>>
                                            Média
                                        </option>
                                        <option value="Alta"
                                            <?= (($_SESSION['equipamento_edit']['criticidade'] ?? '') == 'Alta') ? 'selected' : '' ?>>
                                            Alta
                                        </option>
                                        <option value="Suporte de Vida"
                                            <?= (($_SESSION['equipamento_edit']['criticidade'] ?? '') == 'Suporte de Vida') ? 'selected' : '' ?>>
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

                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#modalManualTecnico">

                                            <i class="fa-solid fa-file-pdf me-2"></i>
                                            Editar Documento

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
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_manual_utilizacao']['nome'] ?? '') ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <?php if (!empty($_SESSION['equipamento_edit']['doc_manual_utilizacao']['caminho'])): ?>
                                                    <div class="mb-2">
                                                        <a href="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_manual_utilizacao']['caminho']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fa-solid fa-file-pdf me-1"></i> Ver ficheiro atual
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control"
                                                    name="manual_utilizacao" accept="application/pdf">
                                                <small class="text-muted">Deixe vazio para manter o ficheiro atual.</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="manual_utilizacao_data"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_manual_utilizacao']['data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="manual_utilizacao_validade"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_manual_utilizacao']['validade'] ?? '') ?>">
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
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_manual_tecnico']['nome'] ?? '') ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <?php if (!empty($_SESSION['equipamento_edit']['doc_manual_tecnico']['caminho'])): ?>
                                                    <div class="mb-2">
                                                        <a href="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_manual_tecnico']['caminho']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fa-solid fa-file-pdf me-1"></i> Ver ficheiro atual
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control"
                                                    name="manual_tecnico" accept="application/pdf">
                                                <small class="text-muted">Deixe vazio para manter o ficheiro atual.</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="manual_tecnico_data"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_manual_tecnico']['data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="manual_tecnico_validade"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_manual_tecnico']['validade'] ?? '') ?>">
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" name="submeter_edit_step1" class="btn btn-primary-custom">Seguinte</button>
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

                                    <input type="date" class="form-control" name="data_aquisicao" value="<?= htmlspecialchars($_SESSION['equipamento_edit']['data_aquisicao'] ?? '') ?>">

                                </div>

                                <div class="col-md-3">

                                    <label class="form-label fw-bold">
                                        Custo de Aquisição (€)
                                    </label>

                                    <input type="number" class="form-control" name="custo_aquisicao" placeholder="0.00" value="<?= htmlspecialchars($_SESSION['equipamento_edit']['custo_aquisicao'] ?? '') ?>">

                                </div>

                                <div class="col-md-3">

                                    <label class="form-label fw-bold">
                                        Tipo de Entrada
                                    </label>

                                    <select class="form-select" name="tipo_entrada">

                                        <option value="Compra" <?= (($_SESSION['equipamento_edit']['tipo_entrada'] ?? '') == 'Compra') ? 'selected' : '' ?>>
                                            Compra
                                        </option>

                                        <option value="Doação" <?= (($_SESSION['equipamento_edit']['tipo_entrada'] ?? '') == 'Doação') ? 'selected' : '' ?>>
                                            Doação
                                        </option>

                                        <option value="Aluguer" <?= (($_SESSION['equipamento_edit']['tipo_entrada'] ?? '') == 'Aluguer') ? 'selected' : '' ?>>
                                            Aluguer
                                        </option>

                                        <option value="Empréstimo" <?= (($_SESSION['equipamento_edit']['tipo_entrada'] ?? '') == 'Empréstimo') ? 'selected' : '' ?>>
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
                                                <?= (($_SESSION['equipamento_edit']['id_estado'] ?? '') == $estado->id) ? 'selected' : '' ?>>
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
                                            Contrato associado à compra ou aquisição do equipamento.
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
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_fatura_aquisicao']['nome'] ?? '') ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <?php if (!empty($_SESSION['equipamento_edit']['doc_fatura_aquisicao']['caminho'])): ?>
                                                    <div class="mb-2">
                                                        <a href="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_fatura_aquisicao']['caminho']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fa-solid fa-file-pdf me-1"></i> Ver ficheiro atual
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control"
                                                    name="fatura_aquisicao" accept="application/pdf">
                                                <small class="text-muted">Deixe vazio para manter o ficheiro atual.</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="fatura_aquisicao_data"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_fatura_aquisicao']['data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="fatura_aquisicao_validade"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_fatura_aquisicao']['validade'] ?? '') ?>">
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
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_contrato_aquisicao']['nome'] ?? '') ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <?php if (!empty($_SESSION['equipamento_edit']['doc_contrato_aquisicao']['caminho'])): ?>
                                                    <div class="mb-2">
                                                        <a href="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_contrato_aquisicao']['caminho']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fa-solid fa-file-pdf me-1"></i> Ver ficheiro atual
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control"
                                                    name="contrato_aquisicao" accept="application/pdf">
                                                <small class="text-muted">Deixe vazio para manter o ficheiro atual.</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="contrato_aquisicao_data"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_contrato_aquisicao']['data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="contrato_aquisicao_validade"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_contrato_aquisicao']['validade'] ?? '') ?>">
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" name="submeter_edit_step2" class="btn btn-primary-custom">Seguinte</button>
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

                                        <option disabled <?= empty($_SESSION['equipamento_edit']['tem_acessorios'] ?? '') ? 'selected' : '' ?>>
                                            Selecionar opção
                                        </option>

                                        <option value="sim" <?= (($_SESSION['equipamento_edit']['tem_acessorios'] ?? '') == 'sim') ? 'selected' : '' ?>>
                                            Sim
                                        </option>

                                        <option value="nao" <?= (($_SESSION['equipamento_edit']['tem_acessorios'] ?? '') == 'nao') ? 'selected' : '' ?>>
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

                                        <option disabled <?= empty($_SESSION['equipamento_edit']['tem_consumiveis'] ?? '') ? 'selected' : '' ?>>
                                            Selecionar opção
                                        </option>

                                        <option value="sim" <?= (($_SESSION['equipamento_edit']['tem_consumiveis'] ?? '') == 'sim') ? 'selected' : '' ?>>
                                            Sim
                                        </option>

                                        <option value="nao" <?= (($_SESSION['equipamento_edit']['tem_consumiveis'] ?? '') == 'nao') ? 'selected' : '' ?>>
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
                                                value="<?= htmlspecialchars($_SESSION['equipamento_edit']['acessorios'][0]['nome'] ?? '') ?>"
                                                placeholder="Ex: Sensor de Fluxo">

                                        </div>

                                        <div class="col-md-3">

                                            <label class="form-label fw-bold">
                                                Quantidade
                                            </label>

                                            <input type="number" class="form-control" name="acessorio_quantidade[]"
                                                value="<?= htmlspecialchars($_SESSION['equipamento_edit']['acessorios'][0]['quantidade'] ?? '') ?>">

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
                                                        <?= (($_SESSION['equipamento_edit']['acessorios'][0]['id_estado'] ?? '') == $estado->id) ? 'selected' : '' ?>>
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
                                                value="<?= htmlspecialchars($_SESSION['equipamento_edit']['consumiveis'][0]['nome'] ?? '') ?>"
                                                placeholder="Ex: Filtro Bacteriano">

                                        </div>

                                        <div class="col-md-4">

                                            <label class="form-label fw-bold">
                                                Quantidade
                                            </label>

                                            <input type="number" class="form-control" name="consumivel_quantidade[]"
                                                value="<?= htmlspecialchars($_SESSION['equipamento_edit']['consumiveis'][0]['quantidade'] ?? '') ?>">
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
                                <button type="submit" name="submeter_edit_step3" class="btn btn-primary-custom">Seguinte</button>
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
                                        <?= (($_SESSION['equipamento_edit']['id_localizacao'] ?? '') == $loc->id) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc->edificio . ' - ' . $loc->piso . ' - ' . $loc->sala_gabinete) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" name="submeter_edit_step4" class="btn btn-primary-custom">Seguinte</button>
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
                                                    <?= (($_SESSION['equipamento_edit']['fornecedores'][0]['id_fornecedor'] ?? '') == $f->id) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($f->codigo_fornecedor . ' - ' . $f->nome_empresa) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <select class="form-select" name="tipo_relacao[]">
                                            <option value="">Tipo de relação</option>
                                            <option value="Fabricante" <?= (($_SESSION['equipamento_edit']['fornecedores'][0]['tipo_relacao'] ?? '') == 'Fabricante') ? 'selected' : '' ?>>Fabricante</option>
                                            <option value="Distribuidor" <?= (($_SESSION['equipamento_edit']['fornecedores'][0]['tipo_relacao'] ?? '') == 'Distribuidor') ? 'selected' : '' ?>>Distribuidor</option>
                                            <option value="Assistência Técnica" <?= (($_SESSION['equipamento_edit']['fornecedores'][0]['tipo_relacao'] ?? '') == 'Assistência Técnica') ? 'selected' : '' ?>>Assistência Técnica</option>
                                            <option value="Consumíveis / Acessórios" <?= (($_SESSION['equipamento_edit']['fornecedores'][0]['tipo_relacao'] ?? '') == 'Consumíveis/ Acessórios') ? 'selected' : '' ?>>Consumíveis / Acessórios</option>
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
                                const fornecedoresGuardados = <?= json_encode($_SESSION['equipamento_edit']['fornecedores'] ?? []) ?>;
                            </script>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" name="submeter_edit_step5" class="btn btn-primary-custom">Seguinte</button>
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

                                        <option disabled <?= empty($_SESSION['equipamento_edit']['tem_garantia'] ?? '') ? 'selected' : '' ?>>
                                            Selecionar opção
                                        </option>

                                        <option value="sim" <?= (($_SESSION['equipamento_edit']['tem_garantia'] ?? '') == 'sim') ?>>
                                            Sim
                                        </option>

                                        <option value="nao" <?= (($_SESSION['equipamento_edit']['tem_garantia'] ?? '') == 'nao') ?>>
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

                                        <option disabled <?= empty($_SESSION['equipamento_edit']['tem_contrato'] ?? '') ? 'selected' : '' ?>>
                                            Selecionar opção
                                        </option>

                                        <option value="sim" <?= (($_SESSION['equipamento_edit']['tem_garantia'] ?? '') == 'sim') ?>>
                                            Sim
                                        </option>

                                        <option value="nao" <?= (($_SESSION['equipamento_edit']['tem_garantia'] ?? '') == 'nao') ?>>
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
                                            <option value="Manutenção Preventiva" <?= (($_SESSION['equipamento_edit']['tipo_contrato'] ?? '') == 'Manutenção Preventiva') ? 'selected' : '' ?>>Manutenção Preventiva</option>
                                            <option value="Manutenção Corretiva" <?= (($_SESSION['equipamento_edit']['tipo_contrato'] ?? '') == 'Manutenção Corretiva') ? 'selected' : '' ?>>Manutenção Corretiva</option>
                                            <option value="Manutenção Preventiva e Corretiva" <?= (($_SESSION['equipamento_edit']['tipo_contrato'] ?? '') == 'Manutenção Preventiva e Corretiva') ? 'selected' : '' ?>>Manutenção Preventiva e Corretiva</option>
                                        </select>

                                    </div>

                                    <div class="col-md-4">

                                        <label class="form-label fw-bold">
                                            Entidade Responsável
                                        </label>

                                        <input type="text" class="form-control" name="entidade_responsavel"
                                            value="<?= htmlspecialchars($_SESSION['equipamento_edit']['entidade_responsavel'] ?? '') ?>">

                                    </div>

                                    <div class=" col-md-4">

                                        <label class="form-label fw-bold">
                                            Periodicidade
                                        </label>

                                        <select class="form-select" name="periodicidade">
                                            <option value="Mensal" <?= (($_SESSION['equipamento_edit']['periodicidade'] ?? '') == 'Mensal') ? 'selected' : '' ?>>Mensal</option>
                                            <option value="Trimestral" <?= (($_SESSION['equipamento_edit']['periodicidade'] ?? '') == 'Trimestral') ? 'selected' : '' ?>>Trimestral</option>
                                            <option value="Semestral" <?= (($_SESSION['equipamento_edit']['periodicidade'] ?? '') == 'Semestral') ? 'selected' : '' ?>>Semestral</option>
                                            <option value="Anual" <?= (($_SESSION['equipamento_edit']['periodicidade'] ?? '') == 'Anual') ? 'selected' : '' ?>>Anual</option>
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
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_certificado_garantia']['nome'] ?? '') ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <?php if (!empty($_SESSION['equipamento_edit']['doc_certificado_garantia']['caminho'])): ?>
                                                    <div class="mb-2">
                                                        <a href="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_certificado_garantiao']['caminho']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fa-solid fa-file-pdf me-1"></i> Ver ficheiro atual
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control"
                                                    name="certificado_garantia" accept="application/pdf">
                                                <small class="text-muted">Deixe vazio para manter o ficheiro atual.</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="certificado_garantia_data"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_certificado_garantia']['data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="certificado_garantia_validade"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_certificado_garantia']['validade'] ?? '') ?>">
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
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_contrato_manutencao']['nome'] ?? '') ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <?php if (!empty($_SESSION['equipamento_edit']['doc_contrato_manutencao']['caminho'])): ?>
                                                    <div class="mb-2">
                                                        <a href="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_contrato_manutencao']['caminho']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fa-solid fa-file-pdf me-1"></i> Ver ficheiro atual
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control"
                                                    name="contrato_manutencao" accept="application/pdf">
                                                <small class="text-muted">Deixe vazio para manter o ficheiro atual.</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="contrato_manutencao_data"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_contrato_manutencao']['data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="contrato_manutencao_validade"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_contrato_manutencao']['validade'] ?? '') ?>">
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
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_certificado_calibracao']['nome'] ?? '') ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <?php if (!empty($_SESSION['equipamento_edit']['doc_certificado_calibracao']['caminho'])): ?>
                                                    <div class="mb-2">
                                                        <a href="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_certificado_calibracao']['caminho']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fa-solid fa-file-pdf me-1"></i> Ver ficheiro atual
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control"
                                                    name="certificado_calibracao" accept="application/pdf">
                                                <small class="text-muted">Deixe vazio para manter o ficheiro atual.</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="certificado_calibracao_data"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_certificado_calibracao']['data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="certificado_calibracao_validade"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_certificado_calibracao']['validade'] ?? '') ?>">
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
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_relatorio_calibracao']['nome'] ?? '') ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Ficheiro PDF
                                                </label>
                                                <?php if (!empty($_SESSION['equipamento_edit']['doc_relatorio_calibracao']['caminho'])): ?>
                                                    <div class="mb-2">
                                                        <a href="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_relatorio_calibracao']['caminho']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fa-solid fa-file-pdf me-1"></i> Ver ficheiro atual
                                                        </a>
                                                    </div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control"
                                                    name="relatorio_calibracao" accept="application/pdf">
                                                <small class="text-muted">Deixe vazio para manter o ficheiro atual.</small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    Data do Documento
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="relatorio_calibracao_data"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_relatorio_calibracao']['data'] ?? '') ?>">
                                            </div>

                                            <div>
                                                <label class="form-label fw-bold">
                                                    Data de Validade
                                                </label>
                                                <input type="date" class="form-control"
                                                    name="relatorio_calibracao_validade"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['doc_relatorio_calibracao']['validade'] ?? '') ?>">
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" name="submeter_edit_step6" class="btn btn-primary-custom">Seguinte</button>
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
                                                <?php if (!empty($_SESSION['equipamento_edit']['doc_manual_utilizacao']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento_edit']['doc_manual_utilizacao']['nome']) ?></li>
                                                <?php endif; ?>
                                                <?php if (!empty($_SESSION['equipamento_edit']['doc_manual_tecnico']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento_edit']['doc_manual_tecnico']['nome']) ?></li>
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
                                                <?php if (!empty($_SESSION['equipamento_edit']['doc_fatura_aquisicao']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento_edit']['doc_fatura_aquisicao']['nome']) ?></li>
                                                <?php endif; ?>
                                                <?php if (!empty($_SESSION['equipamento_edit']['doc_contrato_aquisicao']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento_edit']['doc_contrato_aquisicao']['nome']) ?></li>
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
                                                <?php if (($_SESSION['equipamento_edit']['tem_garantia'] ?? '') === 'sim' && !empty($_SESSION['equipamento_edit']['doc_certificado_garantia']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento_edit']['doc_certificado_garantia']['nome']) ?></li>
                                                <?php endif; ?>
                                                <?php if (($_SESSION['equipamento_edit']['tem_contrato'] ?? '') === 'sim' && !empty($_SESSION['equipamento_edit']['doc_contrato_manutencao']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento_edit']['doc_contrato_manutencao']['nome']) ?></li>
                                                <?php endif; ?>
                                                <?php if (!empty($_SESSION['equipamento_edit']['doc_certificado_calibracao']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento_edit']['doc_certificado_calibracao']['nome']) ?></li>
                                                <?php endif; ?>
                                                <?php if (!empty($_SESSION['equipamento_edit']['doc_relatorio_calibracao']['nome'])): ?>
                                                    <li><?= htmlspecialchars($_SESSION['equipamento_edit']['doc_relatorio_calibracao']['nome']) ?></li>
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

                                        <option disabled <?= empty($_SESSION['equipamento_edit']['tem_documentacao_adicional'] ?? '') ? 'selected' : '' ?>>
                                            Selecionar opção
                                        </option>

                                        <option value="sim" <?= (($_SESSION['equipamento_edit']['tem_documentacao_adicional'] ?? '') == 'sim') ? 'selected' : '' ?>>
                                            Sim
                                        </option>

                                        <option value="nao" <?= (($_SESSION['equipamento_edit']['tem_documentacao_adicional'] ?? '') == 'nao') ? 'selected' : '' ?>>
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
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['documentos_adicionais'][0]['nome'] ?? '') ?>">

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Ficheiro PDF
                                                </label>
                                                <?php if (!empty($_SESSION['equipamento_edit']['documentos_adicionais'][0]['caminho'])): ?>
                                                    <div class="mb-2">
                                                        <a href="<?= htmlspecialchars($_SESSION['equipamento_edit']['documentos_adicionais'][0]['caminho']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fa-solid fa-file-pdf me-1"></i> Ver ficheiro atual
                                                        </a>
                                                    </div>
                                                <?php endif; ?>

                                                <input type="file" class="form-control"
                                                    name="ficheiro_documento_adicional[]" accept="application/pdf">
                                                <small class="text-muted">Deixe vazio para manter o ficheiro atual.</small>

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Data do Documento
                                                </label>

                                                <input type="date" class="form-control"
                                                    name="data_documento_adicional[]"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['documentos_adicionais'][0]['data'] ?? '') ?>">

                                            </div>

                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Data de Validade
                                                </label>

                                                <input type="date" class="form-control"
                                                    name="validade_documento_adicional[]"
                                                    value="<?= htmlspecialchars($_SESSION['equipamento_edit']['documentos_adicionais'][0]['validade'] ?? '') ?>">

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <button type="button" class="btn btn-outline-primary mt-3"
                                    id="btnAdicionarDocumentoAdicional">

                                    <i class="fa-solid fa-plus me-2"></i>
                                    Editar Documento

                                </button>

                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" name="submeter_edit_step7" class="btn btn-primary-custom">Seguinte</button>
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
                                rows="6"><?= htmlspecialchars($_SESSION['equipamento_edit']['observacoes'] ?? '') ?></textarea>
                            <div class="d-flex justify-content-between mt-4">
                                <div class="d-flex gap-3">
                                    <a href="equipamentos.php" class="btn btn-outline-secondary">Cancelar</a>
                                    <button type="submit"
                                        name="submeter_edit_step8"
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
    const modoEdicao = true;
</script>
<?php include '../../includes/footer.php'; ?>