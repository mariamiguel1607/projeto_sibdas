<?php
// --------------------------------------------------------------------
// SEGURANÇA
// --------------------------------------------------------------------
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

// --------------------------------------------------------------------
// RECOLHA E VALIDAÇÃO DOS PARÂMETROS
// --------------------------------------------------------------------
$id_encriptado = $_GET['id'] ?? '';
$formato = $_GET['formato'] ?? '';

if (empty($id_encriptado) || empty($formato)) {
    header('Location: equipamentos.php');
    exit;
}

$id = aes_decrypt($id_encriptado);

if (!$id) {
    header('Location: equipamentos.php');
    exit;
}

// --------------------------------------------------------------------
// RECOLHA DE DADOS DO EQUIPAMENTO (completa para todos os formatos)
// --------------------------------------------------------------------
try {
    $ligacao = ligar_bd();

    // Dados gerais do equipamento
    $stmt = $ligacao->prepare("
        SELECT
            equipamentos.*,
            categorias.nome_categoria,
            estados.nome_estado,
            localizacoes.edificio,
            localizacoes.piso,
            localizacoes.servico_departamento,
            localizacoes.sala_gabinete
        FROM equipamentos
        INNER JOIN categorias ON equipamentos.id_categoria = categorias.id
        INNER JOIN estados ON equipamentos.id_estado = estados.id
        INNER JOIN localizacoes ON equipamentos.id_localizacao = localizacoes.id
        WHERE equipamentos.id = ?
    ");
    $stmt->execute([$id]);
    $equipamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$equipamento) {
        header('Location: equipamentos.php');
        exit;
    }

    // Acessórios
    $stmtAcess = $ligacao->prepare("
        SELECT acessorios.*, estados.nome_estado
        FROM acessorios
        INNER JOIN estados ON acessorios.id_estado = estados.id
        WHERE acessorios.id_equipamento = ?
    ");
    $stmtAcess->execute([$id]);
    $acessorios = $stmtAcess->fetchAll(PDO::FETCH_ASSOC);

    // Consumíveis
    $stmtConsum = $ligacao->prepare("SELECT * FROM consumiveis WHERE id_equipamento = ?");
    $stmtConsum->execute([$id]);
    $consumiveis = $stmtConsum->fetchAll(PDO::FETCH_ASSOC);

    // Fornecedores
    $stmtForn = $ligacao->prepare("
        SELECT fornecedores.*
        FROM fornecedores
        INNER JOIN equipamentos_fornecedores ON fornecedores.id = equipamentos_fornecedores.id_fornecedor
        WHERE equipamentos_fornecedores.id_equipamento = ?
    ");
    $stmtForn->execute([$id]);
    $fornecedores = $stmtForn->fetchAll(PDO::FETCH_ASSOC);

    // Documentos
    $stmtDocs = $ligacao->prepare("
        SELECT documentacao.*, tipos_documento.nome_tipo
        FROM documentacao
        INNER JOIN tipos_documento ON documentacao.id_tipo_documento = tipos_documento.id
        WHERE documentacao.id_equipamento = ?
    ");
    $stmtDocs->execute([$id]);
    $documentos = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);

    // Contrato
    $stmtContrato = $ligacao->prepare("
        SELECT contratos.*, fornecedores.nome_empresa AS nome_fornecedor
        FROM contratos
        INNER JOIN documentacao ON contratos.id_documento = documentacao.id
        LEFT JOIN fornecedores ON contratos.id_fornecedor = fornecedores.id
        WHERE documentacao.id_equipamento = ?
        LIMIT 1
    ");
    $stmtContrato->execute([$id]);
    $contrato = $stmtContrato->fetch(PDO::FETCH_ASSOC);

    $ligacao = null;
} catch (PDOException $e) {
    header('Location: equipamentos.php');
    exit;
}

// --------------------------------------------------------------------
// EXPORTAÇÃO CONFORME O FORMATO
// --------------------------------------------------------------------
$nomeDesignacao = preg_replace('/[^a-zA-Z0-9_-]/', '_', $equipamento['designacao']);
$nomeFicheiro = 'equipamento_' . $equipamento['codigo_interno'] . '_' . $nomeDesignacao;

if ($formato === 'csv') {

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $nomeFicheiro . '.csv"');

    $output = fopen('php://output', 'w');

    // BOM para UTF-8 — garante acentos corretos no Excel
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // ---- DADOS GERAIS ----
    fputcsv($output, ['=== DADOS GERAIS ==='], ';');
    fputcsv($output, [
        'Código Interno',
        'Designação',
        'Fabricante',
        'Marca',
        'Modelo',
        'Número de Série',
        'Ano de Fabrico',
        'Data de Aquisição',
        'Custo de Aquisição (€)',
        'Tipo de Entrada',
        'Categoria',
        'Estado',
        'Criticidade',
        'Edifício',
        'Piso',
        'Serviço/Departamento',
        'Sala/Gabinete',
        'Observações'
    ], ';');
    fputcsv($output, [
        $equipamento['codigo_interno'],
        $equipamento['designacao'],
        $equipamento['fabricante'],
        $equipamento['marca'],
        $equipamento['modelo'],
        $equipamento['num_serie'],
        $equipamento['ano_fabrico'],
        $equipamento['data_aquisicao'],
        $equipamento['custo_aquisicao'],
        $equipamento['tipo_entrada'],
        $equipamento['nome_categoria'],
        $equipamento['nome_estado'],
        $equipamento['criticidade'],
        $equipamento['edificio'],
        $equipamento['piso'],
        $equipamento['servico_departamento'],
        $equipamento['sala_gabinete'],
        $equipamento['observacoes']
    ], ';');

    // ---- ACESSÓRIOS ----
    fputcsv($output, [], ';');
    fputcsv($output, ['=== ACESSÓRIOS ==='], ';');
    if (!empty($acessorios)) {
        fputcsv($output, ['Nome', 'Quantidade', 'Estado'], ';');
        foreach ($acessorios as $ac) {
            fputcsv($output, [
                $ac['nome'],
                $ac['quantidade'] ?? '-',
                $ac['nome_estado']
            ], ';');
        }
    } else {
        fputcsv($output, ['Sem acessórios registados'], ';');
    }

    // ---- CONSUMÍVEIS ----
    fputcsv($output, [], ';');
    fputcsv($output, ['=== CONSUMÍVEIS ==='], ';');
    if (!empty($consumiveis)) {
        fputcsv($output, ['Nome', 'Quantidade'], ';');
        foreach ($consumiveis as $co) {
            fputcsv($output, [
                $co['nome'],
                $co['quantidade'] ?? '-'
            ], ';');
        }
    } else {
        fputcsv($output, ['Sem consumíveis registados'], ';');
    }

    // ---- FORNECEDORES ----
    fputcsv($output, [], ';');
    fputcsv($output, ['=== FORNECEDORES ==='], ';');
    if (!empty($fornecedores)) {
        fputcsv($output, [
            'Empresa',
            'Código',
            'Tipo',
            'NIF',
            'Telefone',
            'Email',
            'Website',
            'Morada',
            'Pessoa de Contacto',
            'Telefone Pessoa de Contacto'
        ], ';');
        foreach ($fornecedores as $fo) {
            fputcsv($output, [
                $fo['nome_empresa'],
                $fo['codigo_fornecedor'] ?? '-',
                $fo['tipo_fornecedor'] ?? '-',
                $fo['nif'] ?? '-',
                $fo['telefone'] ?? '-',
                $fo['email'] ?? '-',
                $fo['website'] ?? '-',
                $fo['morada'] ?? '-',
                $fo['pessoa_contacto'] ?? '-',
                $fo['telefone_pessoa_contacto'] ?? '-'
            ], ';');
        }
    } else {
        fputcsv($output, ['Sem fornecedores associados'], ';');
    }

    // ---- GARANTIAS E CONTRATOS ----
    fputcsv($output, [], ';');
    fputcsv($output, ['=== GARANTIAS E CONTRATOS ==='], ';');
    fputcsv($output, ['Garantia Associada', $equipamento['tem_garantia'] ? 'Sim' : 'Não'], ';');
    fputcsv($output, ['Contrato Associado', $equipamento['tem_contrato'] ? 'Sim' : 'Não'], ';');
    if ($contrato) {
        fputcsv($output, ['Tipo de Contrato', $contrato['tipo_contrato'] ?? '-'], ';');
        fputcsv($output, ['Entidade Responsável', $contrato['nome_fornecedor'] ?? '-'], ';');
        fputcsv($output, ['Periodicidade', $contrato['periodicidade'] ?? '-'], ';');
    }

    // ---- DOCUMENTAÇÃO ----
    fputcsv($output, [], ';');
    fputcsv($output, ['=== DOCUMENTAÇÃO ==='], ';');
    if (!empty($documentos)) {
        fputcsv($output, ['Tipo', 'Nome', 'Data', 'Validade', 'Estado'], ';');
        foreach ($documentos as $doc) {
            fputcsv($output, [
                $doc['nome_tipo'],
                $doc['nome_documento'] ?? '-',
                !empty($doc['data_documento']) ? date('d/m/Y', strtotime($doc['data_documento'])) : '-',
                !empty($doc['data_validade']) ? date('d/m/Y', strtotime($doc['data_validade'])) : '-',
                $doc['estado'] ?? '-'
            ], ';');
        }
    } else {
        fputcsv($output, ['Sem documentação associada'], ';');
    }

    fclose($output);
    exit;
} elseif ($formato === 'json') {

    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $nomeFicheiro . '.json"');

    $dados = [
        'dados_gerais' => [
            'codigo_interno'   => $equipamento['codigo_interno'],
            'designacao'       => $equipamento['designacao'],
            'fabricante'       => $equipamento['fabricante'],
            'marca'            => $equipamento['marca'],
            'modelo'           => $equipamento['modelo'],
            'numero_serie'     => $equipamento['num_serie'],
            'ano_fabrico'      => $equipamento['ano_fabrico'],
            'categoria'        => $equipamento['nome_categoria'],
            'estado'           => $equipamento['nome_estado'],
            'criticidade'      => $equipamento['criticidade'],
        ],
        'aquisicao' => [
            'data_aquisicao'   => $equipamento['data_aquisicao'],
            'custo_aquisicao'  => $equipamento['custo_aquisicao'],
            'tipo_entrada'     => $equipamento['tipo_entrada'],
        ],
        'localizacao' => [
            'edificio'             => $equipamento['edificio'],
            'piso'                 => $equipamento['piso'],
            'servico_departamento' => $equipamento['servico_departamento'],
            'sala_gabinete'        => $equipamento['sala_gabinete'],
        ],
        'acessorios' => array_map(function ($ac) {
            return [
                'nome'       => $ac['nome'],
                'quantidade' => $ac['quantidade'],
                'estado'     => $ac['nome_estado'],
            ];
        }, $acessorios),
        'consumiveis' => array_map(function ($co) {
            return [
                'nome'       => $co['nome'],
                'quantidade' => $co['quantidade'],
            ];
        }, $consumiveis),
        'fornecedores' => array_map(function ($fo) {
            return [
                'nome_empresa'              => $fo['nome_empresa'],
                'codigo_fornecedor'         => $fo['codigo_fornecedor'] ?? null,
                'tipo_fornecedor'           => $fo['tipo_fornecedor'] ?? null,
                'nif'                       => $fo['nif'] ?? null,
                'telefone'                  => $fo['telefone'] ?? null,
                'email'                     => $fo['email'] ?? null,
                'website'                   => $fo['website'] ?? null,
                'morada'                    => $fo['morada'] ?? null,
                'pessoa_contacto'           => $fo['pessoa_contacto'] ?? null,
                'telefone_pessoa_contacto'  => $fo['telefone_pessoa_contacto'] ?? null,
            ];
        }, $fornecedores),
        'garantias_contratos' => [
            'tem_garantia'  => (bool) $equipamento['tem_garantia'],
            'tem_contrato'  => (bool) $equipamento['tem_contrato'],
            'contrato'      => $contrato ? [
                'tipo_contrato'       => $contrato['tipo_contrato'] ?? null,
                'entidade_responsavel' => $contrato['nome_fornecedor'] ?? null,
                'periodicidade'       => $contrato['periodicidade'] ?? null,
            ] : null,
        ],
        'documentacao' => array_map(function ($doc) {
            return [
                'tipo'          => $doc['nome_tipo'],
                'nome'          => $doc['nome_documento'] ?? null,
                'data'          => $doc['data_documento'] ?? null,
                'validade'      => $doc['data_validade'] ?? null,
                'estado'        => $doc['estado'] ?? null,
            ];
        }, $documentos),
        'observacoes' => $equipamento['observacoes'],
    ];

    echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
} elseif ($formato === 'pdf') {

    // --------------------------------------------------------------------
    // Gerar página HTML limpa para impressão
    // --------------------------------------------------------------------
?>
    <!DOCTYPE html>
    <html lang="pt">

    <head>
        <meta charset="UTF-8">
        <title>Ficha do Equipamento — <?= htmlspecialchars($equipamento['codigo_interno']) ?></title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', Tahoma, sans-serif;
                font-size: 12px;
                color: #333;
                padding: 30px;
                line-height: 1.5;
            }

            .header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 3px solid #6f42c1;
                padding-bottom: 15px;
                margin-bottom: 25px;
            }

            .header h1 {
                font-size: 20px;
                color: #6f42c1;
            }

            .header .info-right {
                text-align: right;
                font-size: 11px;
                color: #666;
            }

            .section {
                margin-bottom: 20px;
                page-break-inside: avoid;
            }

            .section-title {
                background-color: #6f42c1;
                color: white;
                padding: 6px 12px;
                font-size: 13px;
                font-weight: bold;
                margin-bottom: 10px;
                border-radius: 4px;
            }

            .grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px 30px;
            }

            .field label {
                display: block;
                font-size: 10px;
                color: #888;
                text-transform: uppercase;
                margin-bottom: 2px;
            }

            .field value {
                display: block;
                font-weight: 600;
                font-size: 12px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 11px;
            }

            table th {
                background-color: #f3f0f7;
                text-align: left;
                padding: 6px 8px;
                border: 1px solid #ddd;
                font-size: 10px;
                text-transform: uppercase;
                color: #555;
            }

            table td {
                padding: 6px 8px;
                border: 1px solid #ddd;
            }

            .badge {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 10px;
                font-size: 10px;
                font-weight: bold;
                color: white;
            }

            .badge-success {
                background-color: #198754;
            }

            .badge-warning {
                background-color: #ffc107;
                color: #333;
            }

            .badge-danger {
                background-color: #dc3545;
            }

            .badge-secondary {
                background-color: #6c757d;
            }

            .badge-info {
                background-color: #0dcaf0;
                color: #333;
            }

            .badge-primary {
                background-color: #6f42c1;
            }

            .fornecedor-card {
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 10px;
                margin-bottom: 10px;
                page-break-inside: avoid;
            }

            .fornecedor-card .titulo {
                font-weight: bold;
                font-size: 13px;
                margin-bottom: 6px;
            }

            .fornecedor-card .subtitulo {
                color: #888;
                font-weight: normal;
                font-size: 11px;
                margin-left: 10px;
            }

            .footer {
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #ddd;
                font-size: 10px;
                color: #999;
                text-align: center;
            }

            @media print {
                body {
                    padding: 15px;
                }

                .no-print {
                    display: none;
                }
            }
        </style>
    </head>

    <body>

        <!-- BOTÃO IMPRIMIR -->
        <div class="no-print" style="margin-bottom: 20px;">
            <button onclick="window.print()" style="padding: 8px 20px; background: #6f42c1; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 13px;">
                <strong>Imprimir / Guardar PDF</strong>
            </button>
            <button onclick="window.close()" style="padding: 8px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 13px; margin-left: 10px;">
                Fechar
            </button>
        </div>

        <!-- CABEÇALHO -->
        <div class="header">
            <div>
                <h1><?= htmlspecialchars($equipamento['designacao']) ?></h1>
                <span style="color: #666;">Código: <strong><?= htmlspecialchars($equipamento['codigo_interno']) ?></strong></span>
            </div>
            <div class="info-right">
                <div>TechMed Solutions</div>
                <div>Data: <?= date('d/m/Y H:i') ?></div>
            </div>
        </div>

        <!-- DADOS GERAIS -->
        <div class="section">
            <div class="section-title">Dados Gerais</div>
            <div class="grid">
                <div class="field"><label>Código Interno</label>
                    <value><?= htmlspecialchars($equipamento['codigo_interno']) ?></value>
                </div>
                <div class="field"><label>Designação</label>
                    <value><?= htmlspecialchars($equipamento['designacao']) ?></value>
                </div>
                <div class="field"><label>Categoria</label>
                    <value><?= htmlspecialchars($equipamento['nome_categoria']) ?></value>
                </div>
                <div class="field"><label>Fabricante</label>
                    <value><?= htmlspecialchars($equipamento['fabricante'] ?? '-') ?></value>
                </div>
                <div class="field"><label>Marca</label>
                    <value><?= htmlspecialchars($equipamento['marca'] ?? '-') ?></value>
                </div>
                <div class="field"><label>Modelo</label>
                    <value><?= htmlspecialchars($equipamento['modelo'] ?? '-') ?></value>
                </div>
                <div class="field"><label>Número de Série</label>
                    <value><?= htmlspecialchars($equipamento['num_serie'] ?? '-') ?></value>
                </div>
                <div class="field"><label>Ano de Fabrico</label>
                    <value><?= htmlspecialchars($equipamento['ano_fabrico'] ?? '-') ?></value>
                </div>
                <div class="field">
                    <label>Estado</label>
                    <value>
                        <?php
                        $est = $equipamento['nome_estado'];
                        if ($est == 'Ativo') echo '<span class="badge badge-success">Ativo</span>';
                        elseif ($est == 'Em manutenção') echo '<span class="badge badge-warning">Em manutenção</span>';
                        elseif ($est == 'Inativo') echo '<span class="badge badge-secondary">Inativo</span>';
                        elseif ($est == 'Em calibração') echo '<span class="badge badge-info">Em calibração</span>';
                        ?>
                    </value>
                </div>
                <div class="field">
                    <label>Criticidade</label>
                    <value>
                        <?php
                        $crit = $equipamento['criticidade'];
                        if ($crit == 'Suporte de Vida') echo '<span class="badge badge-danger">Suporte de Vida</span>';
                        elseif ($crit == 'Alta') echo '<span class="badge badge-warning">Alta</span>';
                        elseif ($crit == 'Média') echo '<span class="badge badge-primary">Média</span>';
                        elseif ($crit == 'Baixa') echo '<span class="badge badge-secondary">Baixa</span>';
                        ?>
                    </value>
                </div>
            </div>
        </div>

        <!-- AQUISIÇÃO -->
        <div class="section">
            <div class="section-title">Aquisição</div>
            <div class="grid">
                <div class="field"><label>Data de Aquisição</label>
                    <value><?= !empty($equipamento['data_aquisicao']) ? date('d/m/Y', strtotime($equipamento['data_aquisicao'])) : '-' ?></value>
                </div>
                <div class="field"><label>Custo de Aquisição</label>
                    <value><?= !empty($equipamento['custo_aquisicao']) ? number_format($equipamento['custo_aquisicao'], 2, ',', '.') . ' €' : '-' ?></value>
                </div>
                <div class="field"><label>Tipo de Entrada</label>
                    <value><?= htmlspecialchars($equipamento['tipo_entrada'] ?? '-') ?></value>
                </div>
            </div>
        </div>

        <!-- LOCALIZAÇÃO -->
        <div class="section">
            <div class="section-title">Localização</div>
            <div class="grid">
                <div class="field"><label>Edifício</label>
                    <value><?= htmlspecialchars($equipamento['edificio'] ?? '-') ?></value>
                </div>
                <div class="field"><label>Piso</label>
                    <value><?= htmlspecialchars($equipamento['piso'] ?? '-') ?></value>
                </div>
                <div class="field"><label>Serviço / Departamento</label>
                    <value><?= htmlspecialchars($equipamento['servico_departamento'] ?? '-') ?></value>
                </div>
                <div class="field"><label>Sala / Gabinete</label>
                    <value><?= htmlspecialchars($equipamento['sala_gabinete'] ?? '-') ?></value>
                </div>
            </div>
        </div>

        <!-- ACESSÓRIOS -->
        <?php if (!empty($acessorios)): ?>
            <div class="section">
                <div class="section-title">Acessórios (<?= count($acessorios) ?>)</div>
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Quantidade</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($acessorios as $ac): ?>
                            <tr>
                                <td><?= htmlspecialchars($ac['nome']) ?></td>
                                <td><?= htmlspecialchars($ac['quantidade'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($ac['nome_estado']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- CONSUMÍVEIS -->
        <?php if (!empty($consumiveis)): ?>
            <div class="section">
                <div class="section-title">Consumíveis (<?= count($consumiveis) ?>)</div>
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($consumiveis as $co): ?>
                            <tr>
                                <td><?= htmlspecialchars($co['nome']) ?></td>
                                <td><?= htmlspecialchars($co['quantidade'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- FORNECEDORES -->
        <?php if (!empty($fornecedores)): ?>
            <div class="section">
                <div class="section-title">Fornecedores Associados (<?= count($fornecedores) ?>)</div>
                <?php foreach ($fornecedores as $fo): ?>
                    <div class="fornecedor-card">
                        <div class="titulo">
                            <?= htmlspecialchars($fo['nome_empresa']) ?>
                            <span class="subtitulo">
                                <?= htmlspecialchars($fo['codigo_fornecedor'] ?? '') ?> • <?= htmlspecialchars($fo['tipo_fornecedor'] ?? '') ?>
                            </span>
                        </div>
                        <div class="grid">
                            <div class="field"><label>NIF</label>
                                <value><?= htmlspecialchars($fo['nif'] ?? '-') ?></value>
                            </div>
                            <div class="field"><label>Telefone</label>
                                <value><?= htmlspecialchars($fo['telefone'] ?? '-') ?></value>
                            </div>
                            <div class="field"><label>Email</label>
                                <value><?= htmlspecialchars($fo['email'] ?? '-') ?></value>
                            </div>
                            <div class="field"><label>Website</label>
                                <value><?= htmlspecialchars($fo['website'] ?? '-') ?></value>
                            </div>
                            <div class="field"><label>Morada</label>
                                <value><?= htmlspecialchars($fo['morada'] ?? '-') ?></value>
                            </div>
                            <div class="field"><label>Pessoa de Contacto</label>
                                <value><?= htmlspecialchars($fo['pessoa_contacto'] ?? '-') ?></value>
                            </div>
                            <div class="field"><label>Telefone Pessoa de Contacto</label>
                                <value><?= htmlspecialchars($fo['telefone_pessoa_contacto'] ?? '-') ?></value>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- GARANTIAS E CONTRATOS -->
        <div class="section">
            <div class="section-title">Garantias e Contratos</div>
            <div class="grid">
                <div class="field"><label>Garantia Associada</label>
                    <value><?= $equipamento['tem_garantia'] ? 'Sim' : 'Não' ?></value>
                </div>
                <div class="field"><label>Contrato Associado</label>
                    <value><?= $equipamento['tem_contrato'] ? 'Sim' : 'Não' ?></value>
                </div>
                <?php if ($contrato): ?>
                    <div class="field"><label>Tipo de Contrato</label>
                        <value><?= htmlspecialchars($contrato['tipo_contrato'] ?? '-') ?></value>
                    </div>
                    <div class="field"><label>Entidade Responsável</label>
                        <value><?= htmlspecialchars($contrato['nome_fornecedor'] ?? '-') ?></value>
                    </div>
                    <div class="field"><label>Periodicidade</label>
                        <value><?= htmlspecialchars($contrato['periodicidade'] ?? '-') ?></value>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- DOCUMENTAÇÃO -->
        <?php if (!empty($documentos)): ?>
            <div class="section">
                <div class="section-title">Documentação (<?= count($documentos) ?>)</div>
                <table>
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Nome</th>
                            <th>Data</th>
                            <th>Validade</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documentos as $doc): ?>
                            <tr>
                                <td><?= htmlspecialchars($doc['nome_tipo']) ?></td>
                                <td><?= htmlspecialchars($doc['nome_documento'] ?? '-') ?></td>
                                <td><?= !empty($doc['data_documento']) ? date('d/m/Y', strtotime($doc['data_documento'])) : '-' ?></td>
                                <td><?= !empty($doc['data_validade']) ? date('d/m/Y', strtotime($doc['data_validade'])) : '-' ?></td>
                                <td><?= htmlspecialchars($doc['estado'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- OBSERVAÇÕES -->
        <?php if (!empty($equipamento['observacoes'])): ?>
            <div class="section">
                <div class="section-title">Observações</div>
                <p><?= nl2br(htmlspecialchars($equipamento['observacoes'])) ?></p>
            </div>
        <?php endif; ?>

        <!-- RODAPÉ -->
        <div class="footer">
            TechMed Solutions — Ficha do Equipamento <?= htmlspecialchars($equipamento['codigo_interno']) ?> — Gerado em <?= date('d/m/Y H:i') ?>
        </div>

        <script>
            window.onload = function() {
                window.print();
            };
        </script>

    </body>

    </html>
<?php
    exit;
} else {
    header('Location: equipamentos.php');
    exit;
}
