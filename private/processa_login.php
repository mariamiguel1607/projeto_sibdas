<?php
require_once 'includes/funcoes.php';
start_session();

// --------------------------------------------------------------------
// SEGURANÇA
// --------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: login/login.php');
    return;
}

// --------------------------------------------------------------------
// RECOLHA DE DADOS DO FORMULÁRIO
// --------------------------------------------------------------------
$nome_utilizador = isset($_POST['text_nome']) ? $_POST['text_nome'] : '';
$username = isset($_POST['text_username']) ? $_POST['text_username'] : '';
$password = isset($_POST['text_password']) ? $_POST['text_password'] : '';

// --------------------------------------------------------------------
// VALIDAÇÃO DOS DADOS
// --------------------------------------------------------------------
$validation_errors = [];

if (empty($nome_utilizador)) {
    $validation_errors[] = 'O nome é obrigatório.';
}
if (strlen($nome_utilizador) < 3 || strlen($nome_utilizador) > 50) {
    $validation_errors[] = 'O nome deve ter entre 3 e 50 caracteres.';
}
if (!preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $nome_utilizador)) {
    $validation_errors[] = 'O nome deve conter apenas letras e espaços.';
}
if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    $validation_errors[] = 'O email tem que ser um email válido.';
}
if (strlen($username) < 5 || strlen($username) > 50) {
    $validation_errors[] = 'O email deve ter entre 5 e 50 caracteres.';
}
if (strlen($password) < 6 || strlen($password) > 12) {
    $validation_errors[] = 'A password deve ter entre 6 e 12 caracteres.';
}
if (!preg_match('/[A-Z]/', $password)) {
    $validation_errors[] = 'A password deve ter pelo menos uma letra maiúscula.';
}
if (!preg_match('/[0-9]/', $password)) {
    $validation_errors[] = 'A password deve ter pelo menos um número.';
}
if (!preg_match('/[\W]/', $password)) {
    $validation_errors[] = 'A password deve ter pelo menos um caractere especial (ex: @, !, #).';
}

if (!empty($validation_errors)) {
    $_SESSION['validation_errors'] = $validation_errors;
    header('Location: login/login.php');
    return;
}

// --------------------------------------------------------------------
// SIMULAÇÃO DE RESULTADO DE LOGIN
// --------------------------------------------------------------------
$result['status'] = 1;

if (!$result['status']) {
    $_SESSION['server_error'] = 'Login inválido';
    header('Location: login/login.php');
    return;
}

// --------------------------------------------------------------------
// LOGIN BEM-SUCEDIDO: Guardar o utilizador na sessão
// --------------------------------------------------------------------
$_SESSION['utilizador'] = $username;
$_SESSION['nome'] = $nome_utilizador;

header('Location: home.php');
exit;