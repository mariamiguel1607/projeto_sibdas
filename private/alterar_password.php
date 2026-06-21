<?php
require_once 'includes/funcoes.php';
start_session();

if (!check_session()) {
    header('Location: login/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: home.php');
    exit;
}

$password_atual = trim($_POST['password_atual'] ?? '');
$password_nova = trim($_POST['password_nova'] ?? '');
$password_confirmar = trim($_POST['password_confirmar'] ?? '');

// Validações
if (empty($password_atual) || empty($password_nova) || empty($password_confirmar)) {
    $_SESSION['password_erro'] = 'Todos os campos são obrigatórios.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if (strlen($password_nova) < 6) {
    $_SESSION['password_erro'] = 'A nova palavra-passe deve ter pelo menos 6 caracteres.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if ($password_nova !== $password_confirmar) {
    $_SESSION['password_erro'] = 'A confirmação não coincide com a nova palavra-passe.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

try {
    $ligacao = ligar_bd();

    // Buscar password atual do utilizador
    $stmt = $ligacao->prepare("SELECT password FROM utilizadores WHERE id = ?");
    $stmt->execute([$_SESSION['id']]);
    $utilizador = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$utilizador || !password_verify($password_atual, $utilizador['password'])) {
        $_SESSION['password_erro'] = 'A palavra-passe atual está incorreta.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    // Atualizar para a nova password com hash
    $novaHash = password_hash($password_nova, PASSWORD_DEFAULT);
    $stmtUpdate = $ligacao->prepare("UPDATE utilizadores SET password = ? WHERE id = ?");
    $stmtUpdate->execute([$novaHash, $_SESSION['id']]);

    $ligacao = null;

    $_SESSION['password_sucesso'] = 'Palavra-passe alterada com sucesso.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;

} catch (PDOException $e) {
    $_SESSION['password_erro'] = 'Erro ao alterar a palavra-passe.';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}