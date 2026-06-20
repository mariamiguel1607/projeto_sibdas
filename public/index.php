<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../private/includes/funcoes.php';

$ligacao = ligar_bd();

// Secção Início
$inicio = $ligacao->query("SELECT * FROM gestao_conteudos WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

// Serviços
$servicos = $ligacao->query("
    SELECT * FROM gestao_conteudos_servicos
    ORDER BY id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$sobre = $ligacao->query("SELECT * FROM gestao_conteudos_sobre WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

$contactos = $ligacao->query("SELECT * FROM gestao_conteudos_contactos WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

$faqs = $ligacao->query("
    SELECT * FROM gestao_conteudos_faq
    ORDER BY ordem_apresentacao ASC, id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$rodape = $ligacao->query("SELECT * FROM gestao_conteudos_rodape WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

// ============================================================
// PROCESSAR — MENSAGEM DE CONTACTO
// ============================================================
$sucesso_mensagem = false;
$erros_mensagem   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'enviar_mensagem') {
    $nome     = trim($_POST['contacto_nome']     ?? '');
    $email    = trim($_POST['contacto_email']    ?? '');
    $assunto  = trim($_POST['contacto_assunto']  ?? '');
    $mensagem = trim($_POST['contacto_mensagem'] ?? '');

    if (empty($nome))     $erros_mensagem[] = 'O nome é obrigatório.';
    if (empty($email)) {
        $erros_mensagem[] = 'O email é obrigatório.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros_mensagem[] = 'O email introduzido não é válido.';
    }
    if (empty($mensagem)) $erros_mensagem[] = 'A mensagem é obrigatória.';

    if (empty($erros_mensagem)) {
        $stmt = $ligacao->prepare("
            INSERT INTO mensagens_contacto (nome, email, assunto, mensagem)
            VALUES (:nome, :email, :assunto, :mensagem)
        ");
        $stmt->execute([
            ':nome'     => $nome,
            ':email'    => $email,
            ':assunto'  => $assunto,
            ':mensagem' => $mensagem,
        ]);
        $sucesso_mensagem = true;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechMed Solutions</title>
    <!--favicon-->
    <link rel="shortcut icon" href="../assets/images/imagem_logo1-semfundo.png" type="image/png">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:ital,wght@0,300;0,700;1,400&display=swap"
        rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/bootstrap/bootstrap.min.css">
    <!-- Font Awesome (local) -->
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link rel="stylesheet" href="../assets/css/1241375.css">

</head>

<body>

    <!-- Barra de Navegação -->
    <nav class="bng-navbar navbar navbar-expand-lg fixed-top bg-white shadow-sm">
        <div class="container-fluid px-5">

            <!-- Logo -->
            <a class="navbar-brand" href="#inicio">
                <img src="../assets/images/imagem_logo1.png" alt="Logo TechMed Solutions">
            </a>

            <!-- Botão mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
                aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu -->
            <div class="collapse navbar-collapse" id="menuPrincipal">

                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-4">
                    <li class="nav-item">
                        <a class="nav-link fw-bold" href="#inicio">Início</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link fw-bold" href="#servicos">Serviços</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link fw-bold" href="#sobre">Sobre Nós</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link fw-bold" href="#perguntas-frequentes">FAQ</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link fw-bold" href="#contactos">Contacto</a>
                    </li>
                </ul>

                <a href="../private/login/login.php" class="btn-login">
                    Início de Sessão
                </a>

            </div>
        </div>
    </nav>

    <!-- Início -->
    <section id="inicio" class="home-section d-flex align-items-center">

        <div class="container">

            <div class="row align-items-center g-5">

                <div class="col-lg-5 text-center text-lg-start">

                    <h1 class="display-4 fw-bold mb-4">
                        <?= htmlspecialchars($inicio['titulo_principal']) ?>
                    </h1>

                    <p class="lead mb-4">
                        <?= htmlspecialchars($inicio['descricao']) ?>
                    </p>

                    <a href="<?= htmlspecialchars($inicio['link_botao'] ?: '#servicos') ?>"
                        class="btn btn-primary-custom">
                        <?= htmlspecialchars($inicio['texto_botao']) ?>
                        <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>

                </div>

                <div class="col-lg-7 text-center">

                    <div class="home-visual position-relative">

                        <div class="circle-bg"></div>

                        <div class="dots-bg"></div>

                        <img src="<?= htmlspecialchars($inicio['imagem_principal']) ?>"
                            class="img-fluid"
                            alt="Dashboard de gestão hospitalar">

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- Serviços -->
    <section id="servicos" class="services-section pt-2 pb-5">
        <div class="container">

            <h2 class="section-title text-center mb-5">
                Os Nossos Serviços
            </h2>

            <div class="row g-4">

                <?php if (empty($servicos)): ?>
                    <div class="col-12 text-center text-muted">
                        <p>Nenhum serviço disponível de momento.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($servicos as $servico): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="service-card h-100 text-center">
                                <div class="service-icon mb-3">
                                    <i class="<?= htmlspecialchars($servico['icone']) ?>"></i>
                                </div>
                                <h3><?= htmlspecialchars($servico['titulo']) ?></h3>
                                <p><?= htmlspecialchars($servico['descricao']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

        </div>
    </section>

    <!-- SOBRE NÓS -->
    <section id="sobre" class="about-section">
        <div class="container">

            <div class="row align-items-center g-5">

                <!-- CARD PRINCIPAL -->
                <div class="col-lg-6">
                    <div class="about-card">

                        <h2><?= htmlspecialchars($sobre['titulo_secao']) ?></h2>

                        <div class="section-line mb-4"></div>

                        <p><?= nl2br(htmlspecialchars($sobre['texto_principal'])) ?></p>

                    </div>
                </div>

                <!-- CARDS LATERAIS -->
                <div class="col-lg-6">
                    <div class="row g-4">

                        <!-- BLOCO 1 -->
                        <div class="col-12">
                            <div class="about-info-card d-flex align-items-center gap-4">
                                <div class="about-icon">
                                    <i class="fa-solid fa-bullseye"></i>
                                </div>
                                <div>
                                    <h3><?= htmlspecialchars($sobre['bloco1_titulo']) ?></h3>
                                    <p><?= htmlspecialchars($sobre['bloco1_texto']) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- BLOCO 2 -->
                        <div class="col-12">
                            <div class="about-info-card d-flex align-items-center gap-4">
                                <div class="about-icon">
                                    <i class="fa-solid fa-lightbulb"></i>
                                </div>
                                <div>
                                    <h3><?= htmlspecialchars($sobre['bloco2_titulo']) ?></h3>
                                    <p><?= htmlspecialchars($sobre['bloco2_texto']) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- BLOCO 3 -->
                        <div class="col-12">
                            <div class="about-info-card d-flex align-items-center gap-4">
                                <div class="about-icon">
                                    <i class="fa-solid fa-hand-holding-heart"></i>
                                </div>
                                <div>
                                    <h3><?= htmlspecialchars($sobre['bloco3_titulo']) ?></h3>
                                    <p><?= htmlspecialchars($sobre['bloco3_texto']) ?></p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- FAQ -->
    <section id="perguntas-frequentes" class="faq-section py-5">

        <div class="container">

            <h2 class="section-title text-center mb-5">
                Perguntas Frequentes
            </h2>

            <div class="accordion mx-auto" id="faqAccordionPublico">
                <?php foreach ($faqs as $i => $faq): ?>
                    <div class="accordion-item mb-3 border-0 shadow-sm rounded-4 overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?> fw-bold"
                                type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq<?= $i ?>">
                                <?= htmlspecialchars($faq['pergunta']) ?>
                            </button>
                        </h2>
                        <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>"
                            data-bs-parent="#faqAccordionPublico">
                            <div class="accordion-body">
                                <?= nl2br(htmlspecialchars($faq['resposta'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>

    </section>

    <!-- CONTACTOS -->
    <section id="contactos" class="contact-section">
        <div class="container">

            <div class="text-center mb-5">
                <h2 class="section-title"><?= htmlspecialchars($contactos['titulo_secao']) ?></h2>
                <p class="section-subtitle mx-auto"><?= htmlspecialchars($contactos['texto_introdutorio']) ?></p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="contact-form-card">

                        <h3 class="fw-bold mb-4 text-center">
                            <?= htmlspecialchars($contactos['titulo_formulario']) ?>
                        </h3>

                        <?php if ($sucesso_mensagem): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa-solid fa-circle-check me-2"></i>
                                Mensagem enviada com sucesso! Entraremos em contacto brevemente.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($erros_mensagem)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Corrige os seguintes erros:</strong>
                                <ul class="mb-0 mt-1">
                                    <?php foreach ($erros_mensagem as $erro): ?>
                                        <li><?= htmlspecialchars($erro) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="#contactos">
                            <input type="hidden" name="acao" value="enviar_mensagem">

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nome</label>
                                    <input type="text" name="contacto_nome" class="form-control"
                                        placeholder="Insira o seu nome"
                                        value="<?= $sucesso_mensagem ? '' : htmlspecialchars($_POST['contacto_nome'] ?? '') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="text" name="contacto_email" class="form-control"
                                        placeholder="Insira o seu email"
                                        value="<?= $sucesso_mensagem ? '' : htmlspecialchars($_POST['contacto_email'] ?? '') ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Assunto</label>
                                    <input type="text" name="contacto_assunto" class="form-control"
                                        placeholder="Indique o assunto da mensagem"
                                        value="<?= $sucesso_mensagem ? '' : htmlspecialchars($_POST['contacto_assunto'] ?? '') ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Mensagem</label>
                                    <textarea name="contacto_mensagem" class="form-control" rows="5"
                                        placeholder="Escreva aqui a sua mensagem"><?= $sucesso_mensagem ? '' : htmlspecialchars($_POST['contacto_mensagem'] ?? '') ?></textarea>
                                </div>

                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary-custom px-5">
                                        <?= htmlspecialchars($contactos['texto_botao']) ?>
                                    </button>
                                </div>

                            </div>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- RODAPÉ -->
    <footer id="footer" class="footer-section">
        <div class="container">
            <div class="row gy-5">

                <!-- Logo -->
                <div class="col-lg-4">
                    <img src="<?= htmlspecialchars($rodape['logo']) ?>"
                        alt="Logo TechMed Solutions" class="footer-logo mb-4">
                    <p><?= htmlspecialchars($rodape['texto_descritivo']) ?></p>
                </div>

                <!-- Localização -->
                <div class="col-md-4 col-lg-2">
                    <h5 class="footer-title">LOCALIZAÇÃO</h5>
                    <p><?= nl2br(htmlspecialchars($rodape['localizacao'])) ?></p>
                </div>

                <!-- Horário -->
                <div class="col-md-4 col-lg-3">
                    <h5 class="footer-title">HORÁRIO</h5>
                    <p><?= nl2br(htmlspecialchars($rodape['horario'])) ?></p>
                </div>

                <!-- Contactos -->
                <div class="col-md-4 col-lg-3">
                    <h5 class="footer-title">CONTACTOS</h5>
                    <p>
                        <i class="fa-solid fa-phone me-2"></i>
                        <?= htmlspecialchars($rodape['telefone']) ?>
                    </p>
                    <p>
                        <i class="fa-solid fa-envelope me-2"></i>
                        <?= htmlspecialchars($rodape['email']) ?>
                    </p>
                </div>

            </div>
        </div>
    </footer>
    <!-- Bootstrap JS -->
    <script src="../assets/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/1241375.js"></script>

</body>

</html>