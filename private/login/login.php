<?php
// Inicia a sessão (necessário para usar $_SESSION)
session_start();
// Inicializa a variável que irá conter os erros de validação
$validation_errors = [];
// --------------------------------------------------------------------
// RECOLHA DE MENSAGENS TEMPORÁRIAS DA SESSÃO
// --------------------------------------------------------------------
if (!empty($_SESSION['validation_errors'])) {
    $validation_errors = $_SESSION['validation_errors'];
    unset($_SESSION['validation_errors']);
}
$server_error = [];
if (!empty($_SESSION['server_error'])) {
    $server_error = $_SESSION['server_error'];
    unset($_SESSION['server_error']);
}
?>
<?php include '../includes/header.php'; ?>

<main class="login-page">

    <div class="login-card">

        <!-- Logo e título -->
        <div class="text-center mb-4">
            <a href="<?= BASE_URL ?>/public/index.php">
                <img src="<?= BASE_URL ?>/assets/images/imagem_logo1.png" alt="Logo TechMed Solutions" class="login-logo">
            </a>

            <h1 class="fw-bold mt-4 mb-2">Início de Sessão</h1>

            <p class="text-muted mb-0">
                Aceda à área reservada da TechMed Solutions.
            </p>
        </div>

        <!-- Formulário de login -->
        <form action="../processa_login.php" method="post" novalidate>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label fw-bold">
                    Email
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa-solid fa-envelope"></i>
                    </span>

                    <input type="text" class="form-control" name="text_username"
                        placeholder="Insira o seu email">
                </div>
            </div>

            <!-- Palavra-passe -->
            <div class="mb-3">
                <label class="form-label fw-bold">
                    Palavra-passe
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa-solid fa-lock"></i>
                    </span>

                    <input type="password" class="form-control" name="text_password"
                        placeholder="Insira a palavra-passe">
                </div>
            </div>

            <!-- Esqueceu-se da palavra-passe -->
            <div class="d-flex justify-content-end mb-4">
                <a href="#" class="forgot-link">
                    Esqueceu-se da palavra-passe?
                </a>
            </div>

            <!-- ALERTAS -->
            <?php if (!empty($validation_errors)) : ?>
                <div class="alert alert-danger p-2 text-center">
                    <?php foreach ($validation_errors as $error) : ?>
                        <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($server_error)) : ?>
                <div class="alert alert-danger p-2 text-center">
                    <div><?= htmlspecialchars($server_error) ?></div>
                </div>
            <?php endif; ?>

            <!-- Botão entrar -->
            <button type="submit" class="btn btn-primary-custom w-100">
                Entrar
            </button>

        </form>

        <!-- Voltar à página pública -->
        <div class="text-center mt-4">
            <a href="../../public/index.php" class="back-public-link">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Voltar à página pública
            </a>
        </div>

    </div>

</main>

<?php include '../includes/footer.php'; ?>