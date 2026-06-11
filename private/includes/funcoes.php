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