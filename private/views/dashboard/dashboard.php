<?php
// --------------------------------------------------------------------
// SEGURANÇA: Proteção de acesso à página
// Apenas utilizadores autenticados podem aceder a esta página.
// --------------------------------------------------------------------
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();
try {
    $ligacao = ligar_bd();

    // Total equipamentos
    $total = $ligacao->query("SELECT COUNT(*) FROM equipamentos WHERE ativo = 1")->fetchColumn();

    // Por estado
    $stmtEstados = $ligacao->query("
        SELECT estados.nome_estado, COUNT(equipamentos.id) as total
        FROM equipamentos
        INNER JOIN estados ON equipamentos.id_estado = estados.id
        WHERE equipamentos.ativo = 1
        GROUP BY estados.nome_estado
    ")->fetchAll(PDO::FETCH_KEY_PAIR);

    $ativos      = $stmtEstados['Ativo'] ?? 0;
    $manutencao  = $stmtEstados['Em manutenção'] ?? 0;
    $inativos    = $stmtEstados['Inativo'] ?? 0;

    // Garantias expiradas
    $garantiasExpiradas = $ligacao->query("
        SELECT COUNT(*) FROM documentacao
        WHERE id_tipo_documento = 5
        AND data_validade < CURDATE()
    ")->fetchColumn();

    // Sem documentação (sem nenhum documento associado)
    $semDocumentacao = $ligacao->query("
        SELECT COUNT(*) FROM equipamentos e
        WHERE e.ativo = 1
        AND NOT EXISTS (
            SELECT 1 FROM documentacao d WHERE d.id_equipamento = e.id
        )
    ")->fetchColumn();

    // Equipamentos por serviço (top 5)
    $equipamentosPorServico = $ligacao->query("
    SELECT localizacoes.servico_departamento, COUNT(equipamentos.id) as total
    FROM equipamentos
    INNER JOIN localizacoes ON equipamentos.id_localizacao = localizacoes.id
    WHERE equipamentos.ativo = 1
    GROUP BY localizacoes.servico_departamento
    ORDER BY total DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

    // Distribuição por categoria
    $equipamentosPorCategoria = $ligacao->query("
    SELECT categorias.nome_categoria, COUNT(equipamentos.id) as total
    FROM equipamentos
    INNER JOIN categorias ON equipamentos.id_categoria = categorias.id
    WHERE equipamentos.ativo = 1
    GROUP BY categorias.nome_categoria
    ORDER BY total DESC
")->fetchAll(PDO::FETCH_ASSOC);

    // Garantias a expirar nos próximos 4 meses
    $garantiasProximos = $ligacao->query("
    SELECT 
        DATE_FORMAT(data_validade, '%Y-%m') as mes,
        DATE_FORMAT(data_validade, '%M %Y') as mes_nome,
        COUNT(*) as total
    FROM documentacao
    WHERE id_tipo_documento = 5
    AND data_validade >= CURDATE()
    AND data_validade <= DATE_ADD(CURDATE(), INTERVAL 4 MONTH)
    GROUP BY mes, mes_nome
    ORDER BY mes ASC
")->fetchAll(PDO::FETCH_ASSOC);

    // Suporte de vida por serviço
    $suporteVidaPorServico = $ligacao->query("
    SELECT localizacoes.servico_departamento, COUNT(equipamentos.id) as total
    FROM equipamentos
    INNER JOIN localizacoes ON equipamentos.id_localizacao = localizacoes.id
    WHERE equipamentos.ativo = 1
    AND equipamentos.criticidade = 'Suporte de Vida'
    GROUP BY localizacoes.servico_departamento
    ORDER BY total DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

    $ligacao = null;
} catch (PDOException $e) {
    $total = $ativos = $manutencao = $inativos = $garantiasExpiradas = $semDocumentacao = 0;
}

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
                        <h2 class="fw-bold mb-1"><?= $total ?></h2>
                        <small class="text-muted">Total de Equipamentos</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-4">
                        <h2 class="fw-bold mb-1"><?= $ativos ?></h2>
                        <small class="text-muted">Equipamentos Ativos</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-4">
                        <h2 class="fw-bold mb-1"><?= $manutencao ?></h2>
                        <small class="text-muted">Em Manutenção</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-4">
                        <h2 class="fw-bold mb-1"><?= $inativos ?></h2>
                        <small class="text-muted">Inativos</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-4">
                        <h2 class="fw-bold mb-1"><?= $garantiasExpiradas ?></h2>
                        <small class="text-muted">Garantias Expiradas</small>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body text-center py-4">
                        <h2 class="fw-bold mb-1"><?= $semDocumentacao ?></h2>
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
                            <?php
                            $maxServico = !empty($equipamentosPorServico) ? $equipamentosPorServico[0]['total'] : 1;
                            foreach ($equipamentosPorServico as $i => $servico):
                                $percentagem = round(($servico['total'] / $maxServico) * 100);
                            ?>
                                <div class="<?= $i < count($equipamentosPorServico) - 1 ? 'mb-4' : '' ?>">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span><?= htmlspecialchars($servico['servico_departamento']) ?></span>
                                        <strong><?= $servico['total'] ?></strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?= $percentagem ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
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
                            <?php
                            $cores = ['bg-primary', 'bg-success', 'bg-warning text-dark', 'bg-danger', 'bg-info'];
                            foreach ($equipamentosPorCategoria as $i => $cat):
                                $cor = $cores[$i % count($cores)];
                            ?>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span><?= htmlspecialchars($cat['nome_categoria']) ?></span>
                                    <span class="badge <?= $cor ?> px-3 py-2"><?= $cat['total'] ?></span>
                                </div>
                            <?php endforeach; ?>
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
                                    <?php if (empty($garantiasProximos)): ?>
                                        <tr>
                                            <td colspan="2" class="text-muted">Nenhuma garantia a expirar nos próximos 4 meses.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php
                                        $coresMes = ['bg-danger', 'bg-warning text-dark', 'bg-info', 'bg-success'];
                                        foreach ($garantiasProximos as $i => $g):
                                            $cor = $coresMes[$i % count($coresMes)];
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($g['mes_nome']) ?></td>
                                                <td><span class="badge <?= $cor ?>"><?= $g['total'] ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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
                            <?php
                            $maxSuporte = !empty($suporteVidaPorServico) ? $suporteVidaPorServico[0]['total'] : 1;
                            $coresSuporte = ['bg-danger', 'bg-warning', 'bg-success', 'bg-info', 'bg-primary'];
                            foreach ($suporteVidaPorServico as $i => $sv):
                                $percentagem = round(($sv['total'] / $maxSuporte) * 100);
                                $cor = $coresSuporte[$i % count($coresSuporte)];
                            ?>
                                <div class="<?= $i < count($suporteVidaPorServico) - 1 ? 'mb-4' : '' ?>">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span><?= htmlspecialchars($sv['servico_departamento']) ?></span>
                                        <strong><?= $sv['total'] ?></strong>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar <?= $cor ?>" style="width: <?= $percentagem ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>
</div>

<?php include '../../includes/footer.php'; ?>