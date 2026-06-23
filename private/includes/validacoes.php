<?php

// ============================================================
// Validações genéricas reutilizáveis
// ============================================================

function validar_nome(string $nome): array
{
    $erros = [];
    if (empty(trim($nome))) {
        $erros[] = "O campo Nome é obrigatório.";
    } elseif (preg_match('/\d/', $nome)) {
        $erros[] = "O campo Nome não pode conter números.";
    }
    return $erros;
}

function validar_email(string $email): array
{
    $erros = [];
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "O email tem um formato inválido.";
    }
    return $erros;
}

function validar_nif(string $nif): array
{
    $erros = [];
    if (!empty($nif) && !preg_match('/^\d{9}$/', $nif)) {
        $erros[] = "O NIF deve conter exatamente 9 números.";
    }
    return $erros;
}

function validar_preco(string $valor, string $nome_campo = "O valor"): array
{
    $erros = [];
    if ($valor !== '') {
        if (!is_numeric($valor)) {
            $erros[] = "$nome_campo deve ser numérico.";
        } elseif ((float)$valor < 0) {
            $erros[] = "$nome_campo não pode ser negativo.";
        }
    }
    return $erros;
}

function validar_data(string $data, string $nome_campo = "A data"): array
{
    $erros = [];
    if (!empty($data)) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            $erros[] = "$nome_campo tem um formato inválido.";
        } else {
            $partes = explode('-', $data);
            if (!checkdate((int)$partes[1], (int)$partes[2], (int)$partes[0])) {
                $erros[] = "$nome_campo é inválida.";
            }
        }
    }
    return $erros;
}

function validar_ano(string $ano): array
{
    $erros = [];
    if (!empty($ano)) {
        if (!preg_match('/^\d{4}$/', $ano) || (int)$ano < 1900 || (int)$ano > (int)date('Y')) {
            $erros[] = "O ano de fabrico é inválido.";
        }
    }
    return $erros;
}


// ============================================================
// Validações de datas de documentos (independente do contexto)
// ============================================================

function validar_datas_documento_simples(string $data_documento, string $data_validade, string $nome_documento): array
{
    $erros = [];
    $hoje = date('Y-m-d');

    if (!empty($data_documento) && $data_documento > $hoje) {
        $erros[] = "A data do documento '{$nome_documento}' não pode ser futura.";
    }

    if (!empty($data_validade) && $data_validade < $hoje) {
        $erros[] = "A validade do documento '{$nome_documento}' já expirou.";
    }

    if (!empty($data_documento) && !empty($data_validade) && $data_validade < $data_documento) {
        $erros[] = "A validade do documento '{$nome_documento}' não pode ser anterior à data do documento.";
    }

    return $erros;
}


// ============================================================
// Validação completa de um documento: nome, data, validade e ficheiro
// (obriga TODOS os campos)
//
// $ficheiro            -> entrada de $_FILES correspondente (array com 'name', 'error', etc.) ou []
// $caminho_existente   -> caminho já guardado na sessão/BD (no editar); null no inserir
// $nome_documento      -> nome legível usado nas mensagens de erro
// ============================================================

function validar_documento_completo(
    string $nome,
    string $data,
    string $validade,
    array $ficheiro,
    ?string $caminho_existente,
    string $nome_documento
): array {
    $erros = [];

    $tem_ficheiro = !empty($ficheiro['name']) || !empty($caminho_existente);

    if (empty(trim($nome)))   $erros[] = "O nome do documento '{$nome_documento}' é obrigatório.";
    if (empty($data))         $erros[] = "A data do documento '{$nome_documento}' é obrigatória.";
    if (empty($validade))     $erros[] = "A validade do documento '{$nome_documento}' é obrigatória.";
    if (!$tem_ficheiro)       $erros[] = "O ficheiro PDF do documento '{$nome_documento}' é obrigatório.";

    if (!empty($data) && !empty($validade)) {
        $erros = array_merge($erros, validar_datas_documento_simples($data, $validade, $nome_documento));
    }

    return $erros;
}
function validar_num_serie_duplicado(string $num_serie, ?int $id_excluir = null): array
{
    $erros = [];

    if (empty($num_serie)) return $erros;

    try {
        $ligacao = ligar_bd();

        if ($id_excluir) {
            $stmt = $ligacao->prepare("
                SELECT COUNT(*) FROM equipamentos
                WHERE num_serie = ? AND id != ?
            ");
            $stmt->execute([$num_serie, $id_excluir]);
        } else {
            $stmt = $ligacao->prepare("
                SELECT COUNT(*) FROM equipamentos
                WHERE num_serie = ?
            ");
            $stmt->execute([$num_serie]);
        }

        if ($stmt->fetchColumn() > 0) {
            $erros[] = "Já existe um equipamento com este número de série.";
        }

        $ligacao = null;
    } catch (PDOException $e) {
        $erros[] = "Erro ao verificar número de série.";
    }

    return $erros;
}


// ============================================================
// Validações específicas dos steps de equipamentos
// (usadas no inserir_equipamentos.php e editar_equipamentos.php)
//
// $caminhos_existentes -> $_SESSION['equipamento'] ou $_SESSION['equipamento_edit']
//                          (passar [] no inserir caso ainda não existam documentos)
// ============================================================

function validar_step_dados_gerais(array $dados, array $files, array $caminhos_existentes = [], ?int $id_excluir = null): array
{
    $erros = [];

    if (empty(trim($dados['designacao'] ?? '')))   $erros[] = "A designação é obrigatória.";
    if (empty($dados['id_categoria'] ?? ''))       $erros[] = "A categoria é obrigatória.";
    if (empty(trim($dados['marca'] ?? '')))        $erros[] = "A marca é obrigatória.";
    if (empty(trim($dados['modelo'] ?? '')))       $erros[] = "O modelo é obrigatório.";
    if (empty($dados['ano_fabrico'] ?? ''))        $erros[] = "O ano de fabrico é obrigatório.";
    if (empty($dados['criticidade'] ?? ''))        $erros[] = "A criticidade é obrigatória.";

    $erros = array_merge($erros, validar_ano($dados['ano_fabrico'] ?? ''));

    // Verificação de número de série duplicado
    $erros = array_merge($erros, validar_num_serie_duplicado($dados['num_serie'] ?? '', $id_excluir));

    $erros = array_merge($erros, validar_documento_completo(
        $dados['nome_documento_manual_utilizacao'] ?? '',
        $dados['manual_utilizacao_data'] ?? '',
        $dados['manual_utilizacao_validade'] ?? '',
        $files['manual_utilizacao'] ?? [],
        $caminhos_existentes['doc_manual_utilizacao']['caminho'] ?? null,
        'Manual de Utilização'
    ));

    $erros = array_merge($erros, validar_documento_completo(
        $dados['nome_documento_manual_tecnico'] ?? '',
        $dados['manual_tecnico_data'] ?? '',
        $dados['manual_tecnico_validade'] ?? '',
        $files['manual_tecnico'] ?? [],
        $caminhos_existentes['doc_manual_tecnico']['caminho'] ?? null,
        'Manual Técnico'
    ));

    return $erros;
}

function validar_step_aquisicao(array $dados, array $files, array $caminhos_existentes = []): array
{
    $erros = [];

    if (empty($dados['id_estado'] ?? '')) $erros[] = "O estado é obrigatório.";
    if (empty($dados['data_aquisicao'] ?? '')) $erros[] = "A data de aquisição é obrigatória.";
    if (empty($dados['tipo_entrada'] ?? '')) $erros[] = "O tipo de entrada é obrigatório.";

    // Custo obrigatório apenas para Compra ou Aluguer
    if (in_array($dados['tipo_entrada'] ?? '', ['Compra', 'Aluguer'])) {
        if (empty($dados['custo_aquisicao'] ?? '')) {
            $erros[] = "O custo de aquisição é obrigatório para compra ou aluguer.";
        }
    }

    $erros = array_merge($erros, validar_data($dados['data_aquisicao'] ?? '', "A data de aquisição"));
    $erros = array_merge($erros, validar_preco($dados['custo_aquisicao'] ?? '', "O custo de aquisição"));

    $erros = array_merge($erros, validar_documento_completo(
        $dados['nome_documento_fatura_aquisicao'] ?? '',
        $dados['fatura_aquisicao_data'] ?? '',
        $dados['fatura_aquisicao_validade'] ?? '',
        $files['fatura_aquisicao'] ?? [],
        $caminhos_existentes['doc_fatura_aquisicao']['caminho'] ?? null,
        'Fatura de Aquisição'
    ));

    $erros = array_merge($erros, validar_documento_completo(
        $dados['nome_documento_contrato_aquisicao'] ?? '',
        $dados['contrato_aquisicao_data'] ?? '',
        $dados['contrato_aquisicao_validade'] ?? '',
        $files['contrato_aquisicao'] ?? [],
        $caminhos_existentes['doc_contrato_aquisicao']['caminho'] ?? null,
        'Contrato de Aquisição'
    ));

    if (!empty($dados['data_aquisicao'])) {
        if ($dados['data_aquisicao'] > date('Y-m-d')) {
            $erros[] = "A data de aquisição não pode ser futura.";
        }
    }

    return $erros;
}

function validar_step_acessorios_consumiveis(
    array $dados,
    array $acessorios,
    array $consumiveis
): array {

    $erros = [];


    // ==========================
    // ACESSÓRIOS
    // ==========================

    if (($dados['tem_acessorios'] ?? '') === 'sim') {

        $tem_acessorio_valido = false;

        foreach ($acessorios as $acessorio) {

            // Se todos os campos estão vazios, ignora esta linha
            if (empty(trim($acessorio['nome'] ?? '')) && empty($acessorio['quantidade']) && empty($acessorio['id_estado'])) {
                continue;
            }

            if (empty(trim($acessorio['nome'] ?? ''))) {
                $erros[] = "O nome do acessório é obrigatório.";
            }

            if (
                empty($acessorio['quantidade'])
                || !is_numeric($acessorio['quantidade'])
            ) {
                $erros[] = "A quantidade do acessório é obrigatória.";
            } elseif ($acessorio['quantidade'] < 0) {
                $erros[] = "A quantidade do acessório não pode ser negativa.";
            }

            if (empty($acessorio['id_estado'])) {
                $erros[] = "O estado do acessório é obrigatório.";
            }

            $tem_acessorio_valido = true;
        }

        if (!$tem_acessorio_valido) {
            $erros[] = "Deve adicionar pelo menos um acessório.";
        }
    }


    // ==========================
    // CONSUMÍVEIS
    // ==========================

    if (($dados['tem_consumiveis'] ?? '') === 'sim') {

        $tem_consumivel_valido = false;

        foreach ($consumiveis as $consumivel) {

            // Se todos os campos estão vazios, ignora esta linha
            if (empty(trim($consumivel['nome'] ?? '')) && empty($consumivel['quantidade'])) {
                continue;
            }

            if (empty(trim($consumivel['nome'] ?? ''))) {
                $erros[] = "O nome do consumível é obrigatório.";
            }

            if (
                empty($consumivel['quantidade'])
                || !is_numeric($consumivel['quantidade'])
            ) {
                $erros[] = "A quantidade do consumível é obrigatória.";
            } elseif ($consumivel['quantidade'] < 0) {
                $erros[] = "A quantidade do consumível não pode ser negativa.";
            }

            $tem_consumivel_valido = true;
        }

        if (!$tem_consumivel_valido) {
            $erros[] = "Deve adicionar pelo menos um consumível.";
        }
    }

    return $erros;
}

function validar_step_localizacao(array $dados): array
{
    $erros = [];
    if (empty($dados['id_localizacao'] ?? '')) $erros[] = "A localização é obrigatória.";
    return $erros;
}

function validar_step_fornecedor(array $ids_fornecedor, array $tipos_relacao, array $fornecedores_disponiveis = []): array
{
    $erros = [];
    $valido = false;

    // Criar mapa id => [tipo_fornecedor, nome] para validação rápida
    $mapaFornecedores = [];
    foreach ($fornecedores_disponiveis as $f) {
        $mapaFornecedores[$f['id']] = [
            'tipo' => $f['tipo_fornecedor'],
            'nome' => $f['nome_empresa'],
        ];
    }

    foreach ($ids_fornecedor as $i => $id_forn) {
        if (!empty($id_forn) && !empty($tipos_relacao[$i])) {
            $valido = true;

            // Validar compatibilidade do tipo
            if (!empty($mapaFornecedores) && isset($mapaFornecedores[$id_forn])) {
                if ($mapaFornecedores[$id_forn]['tipo'] !== $tipos_relacao[$i]) {
                    $nomeForn = $mapaFornecedores[$id_forn]['nome'];
                    $tipoForn = $mapaFornecedores[$id_forn]['tipo'];
                    $erros[] = 'O fornecedor "' . $nomeForn . '" é do tipo "' . $tipoForn . '" — não pode ser associado com o tipo de relação "' . $tipos_relacao[$i] . '".';
                }
            }
        }
    }

    if (!$valido) $erros[] = "É obrigatório associar pelo menos um fornecedor com tipo de relação.";

    return $erros;
}

function validar_step_garantias(array $dados, array $files, array $caminhos_existentes = []): array
{
    $erros = [];

    // Garantia e contrato — selecionar obrigatório
    if (empty($dados['tem_garantia'] ?? '')) {
        $erros[] = "Deve indicar se o equipamento tem garantia associada.";
    }
    if (empty($dados['tem_contrato'] ?? '')) {
        $erros[] = "Deve indicar se o equipamento tem contrato associado.";
    }

    $erros = array_merge($erros, validar_documento_completo(
        $dados['nome_documento_certificado_calibracao'] ?? '',
        $dados['certificado_calibracao_data'] ?? '',
        $dados['certificado_calibracao_validade'] ?? '',
        $files['certificado_calibracao'] ?? [],
        $caminhos_existentes['doc_certificado_calibracao']['caminho'] ?? null,
        'Certificado de Calibração'
    ));

    $erros = array_merge($erros, validar_documento_completo(
        $dados['nome_documento_relatorio_calibracao'] ?? '',
        $dados['relatorio_calibracao_data'] ?? '',
        $dados['relatorio_calibracao_validade'] ?? '',
        $files['relatorio_calibracao'] ?? [],
        $caminhos_existentes['doc_relatorio_calibracao']['caminho'] ?? null,
        'Relatório de Calibração'
    ));

    if (($dados['tem_garantia'] ?? '') === 'sim') {
        $erros = array_merge($erros, validar_documento_completo(
            $dados['nome_documento_certificado_garantia'] ?? '',
            $dados['certificado_garantia_data'] ?? '',
            $dados['certificado_garantia_validade'] ?? '',
            $files['certificado_garantia'] ?? [],
            $caminhos_existentes['doc_certificado_garantia']['caminho'] ?? null,
            'Certificado de Garantia'
        ));
    }

    if (($dados['tem_contrato'] ?? '') === 'sim') {
        if (empty(trim($dados['tipo_contrato'] ?? ''))) {
            $erros[] = "O tipo de contrato é obrigatório.";
        }
        if (empty(trim($dados['periodicidade'] ?? ''))) {
            $erros[] = "A periodicidade é obrigatória.";
        }
        if (empty(trim($dados['entidade_responsavel'] ?? ''))) {
            $erros[] = "A entidade responsável é obrigatória.";
        }

        $erros = array_merge($erros, validar_documento_completo(
            $dados['nome_documento_contrato_manutencao'] ?? '',
            $dados['contrato_manutencao_data'] ?? '',
            $dados['contrato_manutencao_validade'] ?? '',
            $files['contrato_manutencao'] ?? [],
            $caminhos_existentes['doc_contrato_manutencao']['caminho'] ?? null,
            'Contrato de Manutenção'
        ));
    }

    return $erros;
}
function validar_step_documentacao(
    array $dados,
    array $files
): array {

    $erros = [];

    if (($dados['tem_documentacao_adicional'] ?? '') !== 'sim') {
        return $erros;
    }

    $nomes      = $dados['nome_documento_adicional'] ?? [];
    $datas      = $dados['data_documento_adicional'] ?? [];
    $validades  = $dados['validade_documento_adicional'] ?? [];
    $ficheiros  = $files['ficheiro_documento_adicional'] ?? [];

    foreach ($nomes as $i => $nome) {

        $nome = trim($nome);

        if (empty($nome)) {
            continue;
        }

        $erros = array_merge(
            $erros,
            validar_datas_documento_simples(
                $datas[$i] ?? '',
                $validades[$i] ?? '',
                $nome
            )
        );

        if (empty($ficheiros['name'][$i])) {

            $erros[] =
                "O ficheiro PDF do documento '{$nome}' é obrigatório.";
        }
    }

    return $erros;
}

function validar_step_observacoes(string $observacoes): array
{
    $erros = [];
    if (strlen($observacoes) > 5000) {
        $erros[] = "As observações não podem exceder 5000 caracteres.";
    }
    return $erros;
}
function validar_inserir_localizacao(array $dados): array
{
    $erros = [];

    if (empty($dados['codigo_localizacao']))
        $erros[] = 'Código da localização é obrigatório.';

    if (empty($dados['edificio']))
        $erros[] = 'Edifício é obrigatório.';

    if (empty($dados['piso']))
        $erros[] = 'Piso é obrigatório.';

    if (empty($dados['servico_departamento']))
        $erros[] = 'Departamento / Serviço é obrigatório.';

    // sala_gabinete é opcional, não valida

    return $erros;
}
function validar_inserir_fornecedor(array $dados): array
{
    $erros = [];

    if (empty($dados['nome_empresa']))
        $erros[] = 'Nome da empresa é obrigatório.';

    if (empty($dados['tipo_fornecedor']))
        $erros[] = 'Tipo de fornecedor é obrigatório.';

    if (empty($dados['nif']))
        $erros[] = 'NIF é obrigatório.';
    elseif (!preg_match('/^\d{9}$/', $dados['nif']))
        $erros[] = 'NIF inválido — deve ter 9 dígitos.';

    if (empty($dados['telefone']))
        $erros[] = 'Contacto telefónico é obrigatório.';
    elseif (!preg_match('/^(\+351|00351)?[\s\-]?(2[0-9]{1}|9[1236])[0-9]{7}$/', str_replace(' ', '', $dados['telefone'])))
        $erros[] = 'Telefone inválido — deve ser um número português válido (ex: +351 912 345 678).';

    if (empty($dados['email']))
        $erros[] = 'Email é obrigatório.';
    elseif (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL))
        $erros[] = 'Email inválido.';

    if (empty($dados['pessoa_contacto']))
        $erros[] = 'Nome da pessoa de contacto é obrigatório.';

    if (empty($dados['telefone_contacto']))
        $erros[] = 'Telefone da pessoa de contacto é obrigatório.';
    elseif (!preg_match('/^(\+351|00351)?[\s\-]?(2[0-9]{1}|9[1236])[0-9]{7}$/', str_replace(' ', '', $dados['telefone_contacto'])))
        $erros[] = 'Telefone da pessoa de contacto inválido — deve ser um número português válido (ex: +351 912 345 678).';

    if (!empty($dados['website']) && !filter_var($dados['website'], FILTER_VALIDATE_URL))
        $erros[] = 'Website inválido — deve ser um URL válido (ex: https://www.empresa.pt).';
    return $erros;
}
