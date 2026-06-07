
<?php
require_once '../../includes/funcoes.php';
redirect_if_not_logged();
start_session();

$paginaAtiva = 'dashboard';
include '../../includes/header.php';
?>

<div class="private-layout">
    
    <?php include '../../includes/sidebar.php'; ?> 
    <!-- CONTEÚDO PRINCIPAL -->
    <main class="private-main">

        <!-- TÍTULO -->
        <div class="mb-4">

            <h1 class="fw-bold mb-2">
                Dashboard
            </h1>

            <p class="text-muted mb-0">
                Visão geral do parque tecnológico hospitalar.
            </p>

        </div>


        <!-- KPI'S -->
        <div class="row g-4 mb-5">

            <div class="col-md-6 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-4">
                        <h2 class="fw-bold mb-1">152</h2>
                        <small class="text-muted">Total de Equipamentos</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-4">
                        <h2 class="fw-bold mb-1">127</h2>
                        <small class="text-muted">Equipamentos Ativos</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-4">
                        <h2 class="fw-bold mb-1">12</h2>
                        <small class="text-muted">Em Manutenção</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-4">
                        <h2 class="fw-bold mb-1">13</h2>
                        <small class="text-muted">Inativos</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-4">
                        <h2 class="fw-bold mb-1">8</h2>
                        <small class="text-muted">Garantias Expiradas</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-4">
                        <h2 class="fw-bold mb-1">5</h2>
                        <small class="text-muted">Sem Documentação</small>
                    </div>
                </div>
            </div>

        </div>


        <!-- LINHA 1 -->
        <div class="row g-4 mb-4">

            <div class="col-lg-8">

                <div class="card border-0 shadow-sm rounded-4">

                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-4 text-primary">

                            <i class="fa-solid fa-chart-column me-2"></i>
                            Equipamentos por Serviço

                        </h5>

                        <div class="equipamentos-servico">

                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Urgência</span>
                                    <strong>34</strong>
                                </div>

                                <div class="progress">
                                    <div class="progress-bar" style="width: 85%"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Bloco Operatório</span>
                                    <strong>30</strong>
                                </div>

                                <div class="progress">
                                    <div class="progress-bar" style="width: 75%"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>UCI</span>
                                    <strong>25</strong>
                                </div>

                                <div class="progress">
                                    <div class="progress-bar" style="width: 62%"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Imagiologia</span>
                                    <strong>22</strong>
                                </div>

                                <div class="progress">
                                    <div class="progress-bar" style="width: 55%"></div>
                                </div>
                            </div>

                            <div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Cardiologia</span>
                                    <strong>18</strong>
                                </div>

                                <div class="progress">
                                    <div class="progress-bar" style="width: 45%"></div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="card border-0 shadow-sm rounded-4">

                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-4 text-primary">

                            <i class="fa-solid fa-chart-pie me-2"></i>
                            Distribuição por Categoria

                        </h5>

                        <div class="categorias-dashboard">

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <span>Monitorização</span>

                                <span class="badge bg-primary px-3 py-2">
                                    45
                                </span>

                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <span>Diagnóstico</span>

                                <span class="badge bg-success px-3 py-2">
                                    38
                                </span>

                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <span>Terapia</span>

                                <span class="badge bg-warning text-dark px-3 py-2">
                                    29
                                </span>

                            </div>

                            <div class="d-flex justify-content-between align-items-center">

                                <span>Suporte de Vida</span>

                                <span class="badge bg-danger px-3 py-2">
                                    18
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- LINHA 2 -->
        <div class="row g-4">

            <div class="col-lg-6">

                <div class="card border-0 shadow-sm rounded-4">

                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-4 text-primary">

                            <i class="fa-solid fa-calendar-days me-2"></i>
                            Garantias a Expirar (Próximos Meses)

                        </h5>

                        <div class="table-responsive">

                            <table class="table align-middle">

                                <thead>

                                    <tr>
                                        <th>Mês</th>
                                        <th>Garantias</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <tr>
                                        <td>Junho</td>
                                        <td>
                                            <span class="badge bg-danger">
                                                8
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Julho</td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                5
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Agosto</td>
                                        <td>
                                            <span class="badge bg-info">
                                                3
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Setembro</td>
                                        <td>
                                            <span class="badge bg-success">
                                                2
                                            </span>
                                        </td>
                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-lg-6">

                <div class="card border-0 shadow-sm rounded-4">

                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-4 text-primary">

                            <i class="fa-solid fa-heart-pulse me-2"></i>
                            Equipamentos de Suporte de Vida por Serviço

                        </h5>

                        <div class="suporte-vida-dashboard">

                            <div class="mb-4">

                                <div class="d-flex justify-content-between mb-1">
                                    <span>UCI</span>
                                    <strong>12</strong>
                                </div>

                                <div class="progress">
                                    <div class="progress-bar bg-danger" style="width:100%"></div>
                                </div>

                            </div>

                            <div class="mb-4">

                                <div class="d-flex justify-content-between mb-1">
                                    <span>Urgência</span>
                                    <strong>4</strong>
                                </div>

                                <div class="progress">
                                    <div class="progress-bar bg-warning" style="width:33%"></div>
                                </div>

                            </div>

                            <div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span>Bloco Operatório</span>
                                    <strong>2</strong>
                                </div>

                                <div class="progress">
                                    <div class="progress-bar bg-success" style="width:16%"></div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>
</div>

<?php include '../../includes/footer.php'; ?> 