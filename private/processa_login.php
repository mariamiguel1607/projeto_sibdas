<?php
require_once 'includes/funcoes.php';
start_session();

// --------------------------------------------------------------------
// SEGURANÇA
// --------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: login/login.php');
    exit;
}

// --------------------------------------------------------------------
// RECOLHA DE DADOS DO FORMULÁRIO
// --------------------------------------------------------------------
$username = trim($_POST['text_username'] ?? '');
$password = trim($_POST['text_password'] ?? '');

// --------------------------------------------------------------------
// VALIDAÇÃO BÁSICA
// --------------------------------------------------------------------
$validation_errors = [];

if (empty($username)) {
    $validation_errors[] = 'O email é obrigatório.';
} elseif (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    $validation_errors[] = 'Introduz um email válido.';
}

if (empty($password)) {
    $validation_errors[] = 'A password é obrigatória.';
}

if (!empty($validation_errors)) {
    $_SESSION['validation_errors'] = $validation_errors;
    header('Location: login/login.php');
    exit;
}

// --------------------------------------------------------------------
// VERIFICAÇÃO NA BASE DE DADOS
// --------------------------------------------------------------------
try {
    $ligacao = ligar_bd();

    $stmt = $ligacao->prepare("
        SELECT id, nome, email, password, perfil
        FROM utilizadores
        WHERE email = :email
    ");
    $stmt->execute([':email' => $username]);
    $utilizador = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar se existe e se a password está correta
    if (!$utilizador || $utilizador['password'] !== $password) {
        $_SESSION['server_error'] = 'Email ou password incorretos.';
        header('Location: login/login.php');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['server_error'] = 'Erro ao ligar à base de dados.';
    header('Location: login/login.php');
    exit;
}

// --------------------------------------------------------------------
// LOGIN BEM-SUCEDIDO: Guardar na sessão
// --------------------------------------------------------------------
$_SESSION['utilizador'] = $utilizador['email'];
$_SESSION['nome']       = $utilizador['nome'];
$_SESSION['perfil']     = $utilizador['perfil'];
$_SESSION['id']         = $utilizador['id'];

header('Location: home.php');
exit;
