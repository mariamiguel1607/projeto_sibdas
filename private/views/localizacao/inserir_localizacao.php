<?php
require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/validacoes.php';
redirect_if_not_logged();
if (!in_array($_SESSION['perfil'] ?? '', ['Administrador', 'Técnico'])) {
    header('Location: localizacao.php'); 
    exit;
}

$erros = [];

// Gerar próximo código automaticamente
$proximo_codigo = 'LOC-001';
try {
    $ligacao = ligar_bd();
    $stmt = $ligacao->query("SELECT codigo_localizacao FROM localizacoes ORDER BY id DESC LIMIT 1");
    $ultimo = $stmt->fetchColumn();
    if ($ultimo) {
        $numero = intval(substr($ultimo, 4)) + 1;
        $proximo_codigo = 'LOC-' . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }
    $ligacao = null;
} catch (PDOException $e) {
    $proximo_codigo = 'LOC-001';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recolher dados do formulário
    $dados = [
        'codigo_localizacao'   => trim($_POST['codigo_localizacao'] ?? ''),
        'edificio'             => trim($_POST['edificio'] ?? ''),
        'piso'                 => trim($_POST['piso'] ?? ''),
        'servico_departamento' => trim($_POST['servico_departamento'] ?? ''),
        'sala_gabinete'        => trim($_POST['sala_gabinete'] ?? ''),
    ];

    // Validar campos obrigatórios
    $erros = validar_inserir_localizacao($dados);

    // Se passou a validação básica, verificar duplicados na BD
    if (empty($erros)) {
        try {
            $ligacao = ligar_bd();

            // 1. Verificar se o código já existe
            $stmtVerif = $ligacao->prepare("SELECT id FROM localizacoes WHERE codigo_localizacao = :codigo");
            $stmtVerif->execute([':codigo' => $dados['codigo_localizacao']]);
            if ($stmtVerif->fetch()) {
                $erros[] = 'Já existe uma localização com esse código.';
            }

            // 2. Verificar combinação duplicada
            $stmtVerif2 = $ligacao->prepare("
                SELECT id FROM localizacoes 
                WHERE edificio = :edificio 
                AND piso = :piso 
                AND servico_departamento = :servico 
                AND (sala_gabinete = :sala OR (sala_gabinete IS NULL AND :sala2 IS NULL))
            ");
            $stmtVerif2->execute([
                ':edificio' => $dados['edificio'],
                ':piso'     => $dados['piso'],
                ':servico'  => $dados['servico_departamento'],
                ':sala'     => $dados['sala_gabinete'] ?: null,
                ':sala2'    => $dados['sala_gabinete'] ?: null,
            ]);
            if ($stmtVerif2->fetch()) {
                $erros[] = 'Já existe uma localização com essa combinação de edifício, piso, departamento e sala.';
            }

            // 3. Se não há erros, inserir
            if (empty($erros)) {
                $stmt = $ligacao->prepare("
                    INSERT INTO localizacoes (codigo_localizacao, edificio, piso, servico_departamento, sala_gabinete)
                    VALUES (:codigo, :edificio, :piso, :servico, :sala)
                ");
                $stmt->execute([
                    ':codigo'   => $dados['codigo_localizacao'],
                    ':edificio' => $dados['edificio'],
                    ':piso'     => $dados['piso'],
                    ':servico'  => $dados['servico_departamento'],
                    ':sala'     => $dados['sala_gabinete'] ?: null,
                ]);

                $ligacao = null;
                header('Location: localizacao.php?sucesso=inserido');
                exit;
            }

            $ligacao = null;
        } catch (PDOException $e) {
            $erros[] = 'Erro ao guardar: ' . $e->getMessage();
        }
    }
}
?>

<?php include '../../includes/header.php';
$paginaAtiva = 'localizacao';
?>

<div class="private-layout">

    <?php include '../../includes/sidebar.php'; ?>
    <!-- CONTEÚDO PRINCIPAL -->
    <main class="private-main">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h1 class="fw-bold mb-1">
                    Adicionar Localização
                </h1>

                <p class="text-muted mb-0">
                    Registo de uma nova localização física na plataforma.
                </p>

            </div>

            <a href="localizacao.php" class="btn btn-outline-secondary">

                <i class="fa-solid fa-arrow-left me-2"></i>
                Voltar

            </a>

        </div>

        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body p-4 p-lg-5">

                <form method="POST" action="">

                    <!-- DADOS PRINCIPAIS -->
                    <h4 class="fw-bold mb-4">
                        Dados principais
                    </h4>
                    <!-- ALERTAS -->
                    <?php if (!empty($erros)): ?>
                        <div class="alert alert-danger mb-4">
                            <h6 class="alert-heading mb-2">
                                <i class="fa-solid fa-circle-exclamation me-2"></i>
                                Foram encontrados erros
                            </h6>
                            <ul class="mb-0">
                                <?php foreach ($erros as $erro): ?>
                                    <li><?= htmlspecialchars($erro) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="row g-4">

                        <!-- Código -->
                        <div class="col-md-4">

                            <label for="codigo" class="form-label fw-bold obrigatorio">
                                Código da localização
                            </label>

                            <input type="text" class="form-control" id="codigo" name="codigo_localizacao"
                                placeholder="Ex: LOC-0006"
                                value="<?= htmlspecialchars($dados['codigo_localizacao'] ?? $proximo_codigo) ?>"
                                readonly>

                        </div>

                        <!-- Edifício -->
                        <div class="col-md-8">

                            <label for="edificio" class="form-label fw-bold obrigatorio">
                                Edifício
                            </label>

                            <input type="text" class="form-control" id="edificio" name="edificio"
                                placeholder="Ex: Hospital Central" value="<?= htmlspecialchars($dados['edificio'] ?? '') ?>">

                        </div>

                        <!-- Piso -->
                        <div class=" col-md-4">

                            <label for="piso" class="form-label fw-bold obrigatorio">
                                Piso
                            </label>

                            <input type="text" class="form-control" id="piso" name="piso" placeholder="Ex: Piso 2" value="<?= htmlspecialchars($dados['piso'] ?? '') ?>">

                        </div>

                        <!-- Departamento -->
                        <div class="col-md-4">

                            <label for="departamento" class="form-label fw-bold obrigatorio">
                                Departamento / Serviço
                            </label>

                            <input type="text" class="form-control" id="departamento" name="servico_departamento" placeholder="Ex: Cardiologia" value="<?= htmlspecialchars($dados['servico_departamento'] ?? '') ?>">

                        </div>

                        <!-- Sala -->
                        <div class="col-md-4">

                            <label for="sala" class="form-label fw-bold">
                                Sala / Gabinete
                            </label>

                            <input type="text" class="form-control" id="sala" name="sala_gabinete" placeholder="Ex: Sala 2.14" value="<?= htmlspecialchars($dados['sala_gabinete'] ?? '') ?>">

                        </div>

                    </div>



                    <!-- BOTÕES -->
                    <div class="d-flex justify-content-end gap-3 mt-5">

                        <a href="localizacao.php" class="btn btn-outline-secondary">

                            Cancelar

                        </a>

                        <button type="submit" class="btn btn-primary-custom">

                            <i class="fa-solid fa-floppy-disk me-2"></i>
                            Guardar Localização

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </main>
</div>
<?php include '../../includes/footer.php'; ?>