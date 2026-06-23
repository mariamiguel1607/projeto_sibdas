<?php
require_once 'includes/funcoes.php';
redirect_if_not_logged();
start_session();

$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>

<?php
$paginaAtiva = 'home';
include 'includes/header.php';
?>

<div class="private-layout">

    <?php include 'includes/sidebar.php'; ?>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="private-main">

        <?php if (!empty($success_message)) : ?>
            <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
                <div id="toastSuccess" class="toast align-items-center text-bg-success border-0 show" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <?= htmlspecialchars($success_message) ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- TOPO -->
        <header class="private-topbar">
            <div>
                <h1>Bem-vindo, <?= htmlspecialchars($_SESSION['nome'] ?? 'Utilizador') ?></h1>
                <p>Plataforma de gestão do inventário de equipamentos médicos.</p>
            </div>
        </header>

        <!-- CARDS INFORMATIVOS -->
        <div class="row g-4">

            <!-- EQUIPAMENTOS -->
            <div class="col-md-4">
                <div class="card private-card h-100 p-4" style="border-left: 4px solid #8E6CF1 !important;">
                    <div class="card-body d-flex align-items-start gap-3">
                        <div class="stats-icon mt-1" style="min-width: 55px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                        </div>
                        <div>
                            <span class="fw-bold d-block mb-1">Equipamentos</span>
                            <small class="text-muted">Registo, consulta e gestão de equipamentos médicos com documentação técnica, acessórios, consumíveis, fornecedores e contratos associados.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORNECEDORES -->
            <div class="col-md-4">
                <div class="card private-card h-100 p-4" style="border-left: 4px solid #20c997 !important;">
                    <div class="card-body d-flex align-items-start gap-3">
                        <div class="stats-icon mt-1" style="min-width: 55px; background-color: rgba(32,201,151,0.12); color: #20c997; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-truck-medical"></i>
                        </div>
                        <div>
                            <span class="fw-bold d-block mb-1">Fornecedores</span>
                            <small class="text-muted">Gestão de fornecedores, fabricantes e empresas de assistência técnica associados aos equipamentos médicos.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LOCALIZAÇÕES -->
            <div class="col-md-4">
                <div class="card private-card h-100 p-4" style="border-left: 4px solid #fd7e14 !important;">
                    <div class="card-body d-flex align-items-start gap-3">
                        <div class="stats-icon mt-1" style="min-width: 55px; background-color: rgba(253,126,20,0.12); color: #fd7e14; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <span class="fw-bold d-block mb-1">Localizações</span>
                            <small class="text-muted">Estrutura física do hospital — edifícios, pisos, serviços e salas onde os equipamentos estão instalados.</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

</div>

<?php include 'includes/footer.php'; ?>