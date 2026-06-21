<?php
// Verifica se a sessão ainda não foi iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia a sessão
}
// Verifica se o utilizador está autenticado
if (!isset($_SESSION['utilizador'])) {
    // Se não estiver autenticado, redireciona para o formulário de login
    header('Location: ../public/login.php');
    exit; // Encerra o script
}
// A partir daqui, o utilizador está autenticado
// Podemos usar livremente os dados da sessão
$nome = $_SESSION['utilizador'];
?>
<!-- BOTÃO HAMBURGER (só visível em mobile) -->
<button id="btnHamburger" class="btn btn-sidebar-toggle position-fixed d-none"
    style="top: 8px; left: 8px; z-index: 1055; background: linear-gradient(135deg, #8E6CF1, #6C3FE0); color: white; border: none;"
    onclick="document.querySelector('.sidebar').classList.add('show'); document.querySelector('.sidebar-overlay').classList.add('show'); this.style.display='none';">
    <i class="fa-solid fa-bars"></i>
</button>

<!-- OVERLAY -->
<div class="sidebar-overlay"
    onclick="document.querySelector('.sidebar').classList.remove('show'); this.classList.remove('show'); document.getElementById('btnHamburger').style.display='';">
</div>
<!-- SIDEBAR -->

<aside class="sidebar d-flex flex-column p-3">

    <!-- BOTÃO FECHAR SIDEBAR (só visível em mobile) -->
    <button class="btn d-none btn-sidebar-toggle position-absolute text-white border-0"
        style="top: 10px; right: 10px; font-size: 1.3rem;"
        onclick="document.querySelector('.sidebar').classList.remove('show'); document.querySelector('.sidebar-overlay').classList.remove('show'); document.getElementById('btnHamburger').style.display='';">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <?php if (($_SESSION['perfil'] ?? '') === 'Administrador'): ?>
        <!-- ÍCONE MENSAGENS -->
        <div class="d-flex justify-content-start mb-2 px-2">
            <button class="btn position-relative rounded-circle d-flex align-items-center justify-content-center"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasMensagens"
                title="Mensagens recebidas"
                style="width: 42px; height: 42px; background-color: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25);">
                <i class="fa-solid fa-envelope text-white"></i>
                <?php
                try {
                    $ligacao_msg_count = ligar_bd();
                    $stmt_count = $ligacao_msg_count->query("SELECT COUNT(*) FROM mensagens_contacto WHERE lida = 0");
                    $nao_lidas = $stmt_count->fetchColumn();
                    $ligacao_msg_count = null;
                } catch (PDOException $e) {
                    $nao_lidas = 0;
                }
                ?>
                <?php if ($nao_lidas > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 0.6rem;">
                        <?= $nao_lidas ?>
                    </span>
                <?php endif; ?>
            </button>
        </div>
    <?php endif; ?>

    <div class="sidebar-logo text-center mb-4">
        <img src="<?= BASE_URL ?>/assets/images/imagem_logo1-semfundo.png" alt="Logo TechMed Solutions"
            class="img-fluid">
    </div>

    <!-- UTILIZADOR -->

    <div class="dropdown utilizador-sidebar mx-3 mb-4">

        <button class="btn w-100 border-0 rounded-3 text-start dropdown-toggle utilizador-btn"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false">

            <div class="d-flex align-items-center">

                <div class="avatar-utilizador">
                    <i class="fa-regular fa-user"></i>
                </div>

                <div class="ms-2">

                    <div class="nome-utilizador">
                        <?= htmlspecialchars($_SESSION['nome'] ?? 'Utilizador') ?>
                    </div>

                    <div class="cargo-utilizador">
                        <?= htmlspecialchars($_SESSION['perfil'] ?? '') ?>
                    </div>

                </div>

            </div>

        </button>

        <ul class="dropdown-menu w-100">

            <li>
                <h6 class="dropdown-header">
                    <?= htmlspecialchars($_SESSION['utilizador'] ?? '') ?>
                </h6>
            </li>

            <li>
                <hr class="dropdown-divider">
            </li>

            <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalAlterarPassword">
                    <i class="fa-solid fa-key me-2"></i>
                    Alterar Palavra-passe
                </a>
            </li>

            <li>
                <hr class="dropdown-divider">
            </li>

            <li>
                <a class="dropdown-item text-danger" href="<?= BASE_URL ?>/private/login/logout.php">
                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>
                    Sair
                </a>
            </li>

        </ul>

    </div>


    <nav class="sidebar-menu nav nav-pills flex-column gap-2">

        <!-- DASHBOARD — todos os perfis -->
        <a href="<?= BASE_URL ?>/private/views/dashboard/dashboard.php"
            class="nav-link <?= ($paginaAtiva == 'dashboard') ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-line"></i>
            Dashboard
        </a>

        <!-- EQUIPAMENTOS — todos os perfis -->
        <a href="<?= BASE_URL ?>/private/views/equipamentos/equipamentos.php"
            class="nav-link <?= ($paginaAtiva == 'equipamentos') ? 'active' : '' ?>">
            <i class="fa-solid fa-screwdriver-wrench"></i>
            Equipamentos
        </a>

        <!-- LOCALIZAÇÕES — todos os perfis -->
        <a href="<?= BASE_URL ?>/private/views/localizacao/localizacao.php"
            class="nav-link <?= ($paginaAtiva == 'localizacao') ? 'active' : '' ?>">
            <i class="fa-solid fa-location-dot"></i>
            Localizações
        </a>

        <!-- FORNECEDORES — todos os perfis -->
        <a href="<?= BASE_URL ?>/private/views/fornecedores/fornecedores.php"
            class="nav-link <?= ($paginaAtiva == 'fornecedores') ? 'active' : '' ?>">
            <i class="fa-solid fa-truck-medical"></i>
            Fornecedores
        </a>

        <!-- GESTÃO DE CONTEÚDOS — só Administrador -->
        <?php if (($_SESSION['perfil'] ?? '') === 'Administrador'): ?>
            <a href="<?= BASE_URL ?>/private/views/gestao_conteudos/gestao_conteudos.php"
                class="nav-link <?= ($paginaAtiva == 'gestao_conteudos') ? 'active' : '' ?>">
                <i class="fa-solid fa-pen-to-square"></i>
                Gestão de Conteúdos
            </a>
        <?php endif; ?>

    </nav>

    <div class="sidebar-footer mt-auto">

        <!-- HISTÓRICO — Administrador e Técnico -->
        <?php if (in_array($_SESSION['perfil'] ?? '', ['Administrador', 'Técnico'])): ?>
            <button class="nav-link w-100 text-start border-0 bg-transparent"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasHistorico">
                <i class="fa-solid fa-clock-rotate-left"></i>
                Histórico
            </button>
        <?php endif; ?>

    </div>

</aside>
<!-- OFFCANVAS HISTÓRICO -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasHistorico"
    aria-labelledby="offcanvasHistoricoLabel">

    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold" id="offcanvasHistoricoLabel">
            <i class="fa-solid fa-clock-rotate-left me-2"></i>
            Histórico de Atividade
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-0">
        <?php
        try {
            $ligacao_hist = ligar_bd();
            $stmtHist = $ligacao_hist->prepare("
                SELECT 
                    historico_equipamentos.*,
                    equipamentos.codigo_interno,
                    equipamentos.designacao
                FROM historico_equipamentos
                INNER JOIN equipamentos ON historico_equipamentos.id_equipamento = equipamentos.id
                ORDER BY historico_equipamentos.data_acao DESC
                LIMIT 50
            ");
            $stmtHist->execute();
            $historico = $stmtHist->fetchAll(PDO::FETCH_ASSOC);
            $ligacao_hist = null;
        } catch (PDOException $e) {
            $historico = [];
        }
        ?>

        <?php if (empty($historico)): ?>
            <div class="p-4 text-center text-muted">
                <i class="fa-solid fa-clock-rotate-left fa-2x mb-3"></i>
                <p>Nenhuma atividade registada.</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($historico as $evento): ?>
                    <div class="list-group-item px-4 py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fw-semibold">
                                    <?= htmlspecialchars($evento['codigo_interno']) ?>
                                    — <?= htmlspecialchars($evento['designacao']) ?>
                                </span>
                                <br>
                                <span class="text-muted small">
                                    <?= htmlspecialchars($evento['acao']) ?>
                                </span>
                                <?php if (!empty($evento['descricao'])): ?>
                                    <br>
                                    <span class="text-muted small">
                                        <?= htmlspecialchars($evento['descricao']) ?>
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($evento['utilizador'])): ?>
                                    <br>
                                    <span class="text-muted small">
                                        <i class="fa-solid fa-user me-1"></i>
                                        <?= htmlspecialchars($evento['utilizador']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <span class="text-muted small text-nowrap ms-3">
                                <?= (new DateTime($evento['data_acao'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Europe/Lisbon'))->format('d/m/Y H:i') ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>
<!-- OFFCANVAS MENSAGENS -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasMensagens"
    aria-labelledby="offcanvasMensagensLabel"
    style="width: 380px;">

    <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold" id="offcanvasMensagensLabel">
            <i class="fa-solid fa-envelope me-2"></i>
            Mensagens Recebidas
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-0">
        <?php
        try {
            $ligacao_msg = ligar_bd();
            $stmt_msg = $ligacao_msg->query("
                SELECT * FROM mensagens_contacto
                ORDER BY data_envio DESC
                LIMIT 30
            ");
            $mensagens = $stmt_msg->fetchAll(PDO::FETCH_ASSOC);

            // Marcar todas como lidas ao abrir
            $ligacao_msg = null;
        } catch (PDOException $e) {
            $mensagens = [];
        }
        ?>

        <?php if (empty($mensagens)): ?>
            <div class="p-4 text-center text-muted">
                <i class="fa-solid fa-envelope-open fa-2x mb-3"></i>
                <p>Nenhuma mensagem recebida.</p>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($mensagens as $msg): ?>
                    <div class="list-group-item px-3 py-3 <?= $msg['lida'] == 0 ? 'bg-light' : '' ?>">

                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-semibold small">
                                <?= htmlspecialchars($msg['nome']) ?>
                                <?php if ($msg['lida'] == 0): ?>
                                    <span class="badge text-bg-danger ms-1" style="font-size:0.6rem;">Nova</span>
                                <?php endif; ?>
                            </span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted" style="font-size: 0.7rem;">
                                    <?= (new DateTime($msg['data_envio'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Europe/Lisbon'))->format('d/m/Y H:i') ?>
                                </span>
                                <?php if ($msg['lida'] == 0): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/private/marcar_mensagens_lidas.php" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                                        <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-success p-0 px-1"
                                            title="Marcar como lida" style="font-size: 0.7rem;">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <i class="fa-solid fa-check text-success" style="font-size: 0.7rem;" title="Lida"></i>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="text-muted small mb-1">
                            <i class="fa-solid fa-envelope me-1"></i>
                            <?= htmlspecialchars($msg['email']) ?>
                        </div>

                        <?php if (!empty($msg['assunto'])): ?>
                            <div class="small fw-semibold mb-1">
                                <?= htmlspecialchars($msg['assunto']) ?>
                            </div>
                        <?php endif; ?>

                        <div class="small text-muted">
                            <?= htmlspecialchars(mb_substr($msg['mensagem'], 0, 100)) ?>
                            <?= mb_strlen($msg['mensagem']) > 100 ? '...' : '' ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

</div>
<!-- MODAL ALTERAR PALAVRA-PASSE -->
<div class="modal fade" id="modalAlterarPassword" tabindex="-1" aria-labelledby="modalAlterarPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAlterarPasswordLabel">
                    <i class="fa-solid fa-key me-2"></i>
                    Alterar Palavra-passe
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= BASE_URL ?>/private/alterar_password.php">
                <div class="modal-body">

                    <?php if (!empty($_SESSION['password_erro'])): ?>
                        <div class="alert alert-danger p-2 small">
                            <?= htmlspecialchars($_SESSION['password_erro']) ?>
                        </div>
                        <?php unset($_SESSION['password_erro']); ?>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['password_sucesso'])): ?>
                        <div class="alert alert-success p-2 small">
                            <?= htmlspecialchars($_SESSION['password_sucesso']) ?>
                        </div>
                        <?php unset($_SESSION['password_sucesso']); ?>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Palavra-passe atual</label>
                        <input type="password" class="form-control" name="password_atual" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nova palavra-passe</label>
                        <input type="password" class="form-control" name="password_nova" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Confirmar nova palavra-passe</label>
                        <input type="password" class="form-control" name="password_confirmar" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">Alterar</button>
                </div>
            </form>
        </div>
    </div>
</div>