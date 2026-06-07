<?php
// Inicia a sessão (necessário para usar $_SESSION)
session_start();
// Inicializa a variável que irá conter os erros de validação
$validation_errors = [];
// --------------------------------------------------------------------
// RECOLHA DE MENSAGENS TEMPORÁRIAS DA SESSÃO
// --------------------------------------------------------------------
// Verifica se existem erros de validação guardados na sessão
if (!empty($_SESSION['validation_errors'])) {
    // Se existirem, copia-os para a variável local
    $validation_errors = $_SESSION['validation_errors'];
    // Remove os erros da sessão para que não apareçam novamente numa recarga de página
    unset($_SESSION['validation_errors']);
}
// Inicializa a variável que irá conter erros de servidor
$server_error = [];
// Verifica se existe algum erro de servidor guardado na sessão
if (!empty($_SESSION['server_error'])) {
    // Se existir, copia-o para a variável local
    $server_error = $_SESSION['server_error'];
    // Remove o erro da sessão após ser lido
    unset($_SESSION['server_error']);
}
?>
<?php include '../includes/header.php'; ?>

<main class="login-page">

    <div class="login-card">

        <!-- Logo e título -->
        <div class="text-center mb-4">
            <a href="/projeto_sibdas/public/index.html">
                <img src="/projeto_sibdas/assets/images/imagem_logo1.png" alt="Logo TechMed Solutions" class="login-logo">
            </a>

            <h1 class="fw-bold mt-4 mb-2">Início de Sessão</h1>

            <p class="text-muted mb-0">
                Aceda à área reservada da TechMed Solutions.
            </p>
        </div>

        <!-- Formulário de login -->
        <form action="../processa_login.php" method="post" novalidate>

            <!-- Nome de utilizador -->
            <div class="mb-3">
                <label class="form-label fw-bold">
                    Nome
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa-solid fa-user"></i>
                    </span>

                    <input type="text" class="form-control" name="text_nome" id="" placeholder="Insira o seu nome">
                </div>
            </div>

            <!-- Email de utilizador -->
            <div class="mb-3">
                <label class="form-label fw-bold">
                    Email
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa-solid fa-envelope"></i>
                    </span>

                    <input type="email" class="form-control" name="text_username" id="" placeholder="Insira o email">
                </div>
            </div>

            <!-- Palavra-passe -->
            <div class="mb-3">
                <label for="password" class="form-label fw-bold">
                    Palavra-passe
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fa-solid fa-lock"></i>
                    </span>

                    <input type="password" class="form-control" name="text_password" id=""
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
            <!-- -------------------------------------------------------------------- -->
            <!-- APRESENTAÇÃO DE MENSAGENS DE ERRO (VALIDAÇÃO E SERVIDOR) -->
            <!-- -------------------------------------------------------------------- -->
            <!-- Verifica se existem erros de validação -->
            <?php if (!empty($validation_errors)) : ?>
                <!-- Se existirem, apresenta um alerta de erro (vermelho) usando as classes do Bootstrap -->
                <div class="alert alert-danger p-2 text-center">
                    <!-- Percorre todos os erros de validação -->
                    <?php foreach ($validation_errors as $error) : ?>
                        <!-- Mostra cada erro dentro de uma <div>, escapando caracteres especiais para segurança -->
                        <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <!-- Verifica se existe um erro de servidor -->
            <?php if (!empty($server_error)) : ?>
                <!-- Apresenta também num alerta de erro (vermelho) -->
                <div class="alert alert-danger p-2 text-center">
                    <!-- Mostra o erro do servidor, também escapado com htmlspecialchars -->
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
            <a href="../../public/index.html" class="back-public-link">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Voltar à página pública
            </a>
        </div>

    </div>

</main>


<?php include '../includes/footer.php'; ?>