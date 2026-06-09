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

                <a href="../private/login/login.html" class="btn-login">
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

                    <h1 id="inicioPublicoTitulo" class="display-4 fw-bold mb-4">

                        Gestão eficiente de equipamentos médicos

                    </h1>

                    <p id="inicioPublicoDescricao" class="lead mb-4">

                        A TechMed Solutions ajuda hospitais a gerir o inventário de equipamentos médicos de forma
                        centralizada, segura e eficiente.

                    </p>

                    <a href="#servicos" id="inicioPublicoBotao" class="btn btn-primary-custom">

                        Conhecer Serviços

                        <i class="fa-solid fa-arrow-right ms-2"></i>

                    </a>

                </div>

                <div class="col-lg-7 text-center">

                    <div class="home-visual position-relative">

                        <div class="circle-bg"></div>

                        <div class="dots-bg"></div>

                        <img id="inicioPublicoImagem" src="../assets/images/imagem_inicio.png" class="img-fluid"
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

            <div class="row g-4" id="servicosPublicos">
                <!-- Os serviços vão aparecer aqui através do JavaScript -->
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

                        <h2 id="sobrePublicoTitulo">Sobre Nós</h2>

                        <div class="section-line mb-4"></div>

                        <p id="sobrePublicoTexto">
                            A TechMed Solutions nasceu com o objetivo de apoiar instituições de saúde
                            na organização e digitalização dos seus processos internos, promovendo uma
                            gestão mais eficiente, segura e acessível da informação associada aos
                            equipamentos médicos.
                            <br><br>
                            Através de uma plataforma simples e intuitiva, procuramos reduzir a
                            dependência de registos manuais, facilitar o trabalho dos profissionais
                            responsáveis pela gestão hospitalar e contribuir para uma maior qualidade
                            e segurança nos serviços de saúde.
                        </p>

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
                                    <h3 id="sobrePublicoBloco1Titulo">Missão</h3>

                                    <p id="sobrePublicoBloco1Texto">
                                        Apoiar hospitais na transição para processos digitais mais
                                        organizados, seguros e eficientes.
                                    </p>
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
                                    <h3 id="sobrePublicoBloco2Titulo">Inovação</h3>

                                    <p id="sobrePublicoBloco2Texto">
                                        Desenvolver soluções tecnológicas simples, intuitivas e
                                        adaptadas às necessidades do contexto hospitalar.
                                    </p>
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
                                    <h3 id="sobrePublicoBloco3Titulo">Impacto</h3>

                                    <p id="sobrePublicoBloco3Texto">
                                        Contribuir para uma gestão hospitalar mais eficaz e para
                                        melhores condições de apoio aos profissionais de saúde.
                                    </p>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- Perguntas Frequentes -->
    <section id="perguntas-frequentes" class="faq-section py-5">

        <div class="container">

            <h2 class="section-title text-center mb-5">
                Perguntas Frequentes
            </h2>

            <div class="accordion mx-auto" id="faqAccordionPublico">

                <!-- FAQs aparecem aqui automaticamente -->

            </div>

        </div>

    </section>

    <!-- CONTACTOS -->
    <section id="contactos" class="contact-section">
        <div class="container">

            <!-- Título -->
            <div class="text-center mb-5">
                <h2 class="section-title" id="contactosPublicoTitulo">Contactos</h2>

                <p class="section-subtitle mx-auto" id="contactosPublicoTexto">
                    Entre em contacto connosco para obter mais informações sobre os nossos serviços.
                </p>
            </div>

            <!-- Formulário de contacto -->
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="contact-form-card">

                        <h3 class="fw-bold mb-4 text-center" id="contactosFormularioTituloPublico">
                            Envie-nos uma mensagem
                        </h3>

                        <form>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label for="nomeContacto" class="form-label fw-semibold">Nome</label>
                                    <input type="text" class="form-control" id="nomeContacto"
                                        placeholder="Insira o seu nome">
                                </div>

                                <div class="col-md-6">
                                    <label for="emailContacto" class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control" id="emailContacto"
                                        placeholder="Insira o seu email">
                                </div>

                                <div class="col-12">
                                    <label for="assuntoContacto" class="form-label fw-semibold">Assunto</label>
                                    <input type="text" class="form-control" id="assuntoContacto"
                                        placeholder="Indique o assunto da mensagem">
                                </div>

                                <div class="col-12">
                                    <label for="mensagemContacto" class="form-label fw-semibold">Mensagem</label>
                                    <textarea class="form-control" id="mensagemContacto" rows="5"
                                        placeholder="Escreva aqui a sua mensagem"></textarea>
                                </div>

                                <div class="col-12 text-center mt-4">
                                    <button type="button" class="btn btn-primary-custom px-5"
                                        id="contactosBotaoTextoPublico">
                                        Enviar Mensagem
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

                    <img src="../assets/images/imagem_logo2.png" alt="Logo TechMed Solutions" class="footer-logo mb-4"
                        id="footerLogo">
                    <p id="footerTexto">
                        Soluções digitais para gestão eficiente de equipamentos médicos.
                    </p>

                </div>

                <!-- Localização -->
                <div class="col-md-4 col-lg-2">

                    <h5 class="footer-title">
                        LOCALIZAÇÃO
                    </h5>

                    <p id="footerLocalizacao">
                        Rua Dr. António Bernardino de Almeida
                        <br>
                        4249-015 Porto
                        <br>
                        Portugal
                    </p>

                </div>

                <!-- Horário -->
                <div class="col-md-4 col-lg-3">

                    <h5 class="footer-title">
                        HORÁRIO
                    </h5>

                    <p id="footerHorario">
                        2ª a 6ª Feira: 9h — 18h
                        <br><br>
                        Sábado: Encerrado
                        <br><br>
                        Domingo: Encerrado
                    </p>

                </div>

                <!-- Contactos -->
                <div class="col-md-4 col-lg-3">

                    <h5 class="footer-title">
                        CONTACTOS
                    </h5>

                    <p>
                        <i class="fa-solid fa-phone me-2"></i>
                        <span id="footerTelefone">+351 917 654 321</span>
                    </p>

                    <p>
                        <i class="fa-solid fa-envelope me-2"></i>
                        <span id="footerEmail">geral@techmedsolutions.pt</span>
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