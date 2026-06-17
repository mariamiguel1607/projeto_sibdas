<?php
require_once __DIR__ . '/../../config/config.php';
// Inicia a sessão se ainda não estiver iniciada
function start_session()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Verifica se a sessão do utilizador está ativa
function check_session()
{
    return isset($_SESSION['utilizador']);
}

// Redireciona automaticamente se não houver sessão iniciada
function redirect_if_not_logged($redirect_to = '../../login/login.php')
{
    start_session();
    if (!check_session()) {
        header("Location: $redirect_to");
        exit;
    }
}

function logout_and_redirect($redirect_to = '../../login/login.php')
{
    start_session();
    session_unset();
    session_destroy();
    header("Location: $redirect_to");
    exit;
}

function ligar_bd()
{
    $ligacao = new PDO(
        "mysql:host=" . MYSQL_HOST .
            ";port=" . MYSQL_PORT .
            ";dbname=" . MYSQL_DATABASE .
            ";charset=utf8",
        MYSQL_USERNAME,
        MYSQL_PASSWORD
    );

    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $ligacao;
}

function fazer_upload_pdf($ficheiro, $pasta_destino)
{
    // Se não foi enviado nenhum ficheiro, retorna null
    if (!isset($ficheiro) || $ficheiro['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    // Verificar se é PDF
    if ($ficheiro['type'] !== 'application/pdf') {
        return false; // não é PDF
    }

    // Criar pasta se não existir
    if (!is_dir($pasta_destino)) {
        mkdir($pasta_destino, 0755, true);
    }

    // Gerar nome único para evitar conflitos
    $nome_unico = uniqid('doc_', true) . '.pdf';
    $caminho_destino = $pasta_destino . $nome_unico;

    if (move_uploaded_file($ficheiro['tmp_name'], $caminho_destino)) {
        return $nome_unico; // devolve o nome do ficheiro guardado
    }

    return false; // falhou o upload
}

function inserirDocumento($ligacao, $idEquipamento, $idTipoDocumento, $documento)
{
    if (empty($documento['nome']) && empty($documento['caminho'])) {
        return null;
    }

    $stmt = $ligacao->prepare("
        INSERT INTO documentacao
        (
            nome_documento,
            data_documento,
            data_validade,
            estado,
            caminho_ficheiro,
            observacoes,
            id_tipo_documento,
            id_equipamento
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?
        )
    ");

    $stmt->execute([
        $documento['nome'],
        $documento['data'] ?: null,
        $documento['validade'] ?: null,
        'Ativo',
        $documento['caminho'],
        null,
        $idTipoDocumento,
        $idEquipamento
    ]);

    return $ligacao->lastInsertId();
}
function validar_datas_documento(
    $data_documento,
    $data_validade,
    &$erros,
    $nome_documento
) {

    $hoje = date('Y-m-d');

    if (!empty($data_documento) && $data_documento > $hoje) {
        $erros[] =
            "A data do documento '{$nome_documento}' não pode ser futura.";
    }

    if (!empty($data_validade) && $data_validade < $hoje) {
        $erros[] =
            "A validade do documento '{$nome_documento}' já expirou.";
    }

    if (
        !empty($data_documento)
        && !empty($data_validade)
        && $data_validade < $data_documento
    ) {
        $erros[] =
            "A validade do documento '{$nome_documento}' não pode ser anterior à data do documento.";
    }
}
// ============================================================
// Encriptação e desencriptação de valores com OpenSSL
// ============================================================
function aes_encrypt($value)
{
    return bin2hex(openssl_encrypt(
        $value,
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    ));
}

function aes_decrypt($value)
{
    if (!is_string($value) || strlen($value) % 2 !== 0) return false;

    return openssl_decrypt(
        hex2bin($value),
        OPENSSL_METHOD,
        OPENSSL_KEY,
        OPENSSL_RAW_DATA,
        OPENSSL_IV
    );
}
function registar_historico($ligacao, $id_equipamento, $acao, $descricao = null, $id_localizacao_anterior = null)
{
    $utilizador = $_SESSION['utilizador'] ?? 'Sistema';
    $stmt = $ligacao->prepare("
        INSERT INTO historico_equipamentos (id_equipamento, acao, descricao, utilizador, id_localizacao_anterior)
        VALUES (:id_equipamento, :acao, :descricao, :utilizador, :id_localizacao_anterior)
    ");
    $stmt->execute([
        ':id_equipamento'        => $id_equipamento,
        ':acao'                  => $acao,
        ':descricao'             => $descricao,
        ':utilizador'            => $utilizador,
        ':id_localizacao_anterior' => $id_localizacao_anterior,
    ]);
}
