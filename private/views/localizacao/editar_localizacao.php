<?php
require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/validacoes.php';
redirect_if_not_logged();
if (!in_array($_SESSION['perfil'] ?? '', ['Administrador', 'Técnico'])) {
    header('Location: localizacao.php');
    exit;
}

$erros = [];

// Receber e desencriptar o ID
$idEncriptado = $_GET['id'] ?? $_POST['id'] ?? null;
$id = aes_decrypt($idEncriptado);

if (!$id || !is_numeric($id)) {
    header('Location: localizacao.php');
    exit;
}

// Carregar dados atuais da localização
try {
    $ligacao = ligar_bd();
    $stmt = $ligacao->prepare("SELECT * FROM localizacoes WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $localizacao = $stmt->fetch(PDO::FETCH_ASSOC);
    $ligacao = null;

    if (!$localizacao) {
        header('Location: localizacao.php');
        exit;
    }
} catch (PDOException $e) {
    header('Location: localizacao.php');
    exit;
}

// Pré-preencher $dados com os valores atuais
$dados = $localizacao;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dados = [
        'codigo_localizacao'   => trim($_POST['codigo_localizacao'] ?? ''),
        'edificio'             => trim($_POST['edificio'] ?? ''),
        'piso'                 => trim($_POST['piso'] ?? ''),
        'servico_departamento' => trim($_POST['servico_departamento'] ?? ''),
        'sala_gabinete'        => trim($_POST['sala_gabinete'] ?? ''),
    ];

    $erros = validar_inserir_localizacao($dados);

    if (empty($erros)) {
        try {
            $ligacao = ligar_bd();

            // Verificar combinação duplicada (excluindo a própria localização)
            $stmtVerif = $ligacao->prepare("
                SELECT id FROM localizacoes 
                WHERE edificio = :edificio 
                AND piso = :piso 
                AND servico_departamento = :servico 
                AND (sala_gabinete = :sala OR (sala_gabinete IS NULL AND :sala2 IS NULL))
                AND id != :id
            ");
            $stmtVerif->execute([
                ':edificio' => $dados['edificio'],
                ':piso'     => $dados['piso'],
                ':servico'  => $dados['servico_departamento'],
                ':sala'     => $dados['sala_gabinete'] ?: null,
                ':sala2'    => $dados['sala_gabinete'] ?: null,
                ':id'       => $id,
            ]);
            if ($stmtVerif->fetch()) {
                $erros[] = 'Já existe uma localização com essa combinação de edifício, piso, departamento e sala.';
            }

            if (empty($erros)) {
                $stmt = $ligacao->prepare("
        UPDATE localizacoes SET
            edificio             = :edificio,
            piso                 = :piso,
            servico_departamento = :servico,
            sala_gabinete        = :sala
        WHERE id = :id
    ");
                $stmt->execute([
                    ':edificio' => $dados['edificio'],
                    ':piso'     => $dados['piso'],
                    ':servico'  => $dados['servico_departamento'],
                    ':sala'     => $dados['sala_gabinete'] ?: null,
                    ':id'       => $id,
                ]);

                // Registar no histórico para todos os equipamentos desta localização
                $stmtEqs = $ligacao->prepare("SELECT id FROM equipamentos WHERE id_localizacao = :id");
                $stmtEqs->execute([':id' => $id]);
                $eqsNaLocalizacao = $stmtEqs->fetchAll(PDO::FETCH_ASSOC);

                foreach ($eqsNaLocalizacao as $eq) {
                    registar_historico(
                        $ligacao,
                        $eq['id'],
                        'Localização editada',
                        'Os dados da localização ' . $localizacao['codigo_localizacao'] . ' foram atualizados.'
                    );
                }

                $ligacao = null;
                header('Location: localizacao.php?sucesso=editado');
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

        <!-- CABEÇALHO -->
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

            <div>

                <h1 class="fw-bold mb-1">
                    Editar Localização
                </h1>

                <p class="text-muted mb-0">
                    Atualização da informação associada à localização física.
                </p>

            </div>

            <div class="d-flex gap-2">

                <a href="localizacao.php" class="btn btn-outline-secondary">

                    <i class="fa-solid fa-arrow-left me-2"></i>
                    Voltar

                </a>
            </div>

        </div>


        <!-- FORMULÁRIO -->
        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-body p-4 p-lg-5">

                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($idEncriptado) ?>">

                    <h4 class="fw-bold mb-4">
                        <i class="fa-solid fa-location-dot me-2 text-primary"></i>
                        Dados da localização
                    </h4>

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

                        <!-- Código (readonly) -->
                        <div class="col-md-6">
                            <label for="codigoLocalizacao" class="form-label fw-bold obrigatorio">Código da Localização</label>
                            <input type="text" id="codigoLocalizacao" class="form-control"
                                name="codigo_localizacao"
                                value="<?= htmlspecialchars($dados['codigo_localizacao']) ?>"
                                readonly>
                        </div>

                        <!-- Edifício -->
                        <div class="col-md-6">
                            <label for="edificio" class="form-label fw-bold obrigatorio">Edifício</label>
                            <input type="text" id="edificio" class="form-control"
                                name="edificio"
                                placeholder="Ex: Hospital Central"
                                value="<?= htmlspecialchars($dados['edificio'] ?? '') ?>">
                        </div>

                        <!-- Piso -->
                        <div class="col-md-6">
                            <label for="piso" class="form-label fw-bold obrigatorio">Piso</label>
                            <input type="text" id="piso" class="form-control"
                                name="piso"
                                placeholder="Ex: Piso 2"
                                value="<?= htmlspecialchars($dados['piso'] ?? '') ?>">
                        </div>

                        <!-- Departamento -->
                        <div class="col-md-6">
                            <label for="departamento" class="form-label fw-bold obrigatorio">Departamento / Serviço</label>
                            <input type="text" id="departamento" class="form-control"
                                name="servico_departamento"
                                placeholder="Ex: Cardiologia"
                                value="<?= htmlspecialchars($dados['servico_departamento'] ?? '') ?>">
                        </div>

                        <!-- Sala -->
                        <div class="col-md-6">
                            <label for="sala" class="form-label fw-bold">Sala / Gabinete</label>
                            <input type="text" id="sala" class="form-control"
                                name="sala_gabinete"
                                placeholder="Ex: Sala 2.14"
                                value="<?= htmlspecialchars($dados['sala_gabinete'] ?? '') ?>">
                        </div>

                    </div>

                    <!-- BOTÕES -->
                    <div class="d-flex justify-content-end gap-3 mt-5">
                        <a href="localizacao.php" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="fa-solid fa-floppy-disk me-2"></i>
                            Guardar Alterações
                        </button>
                    </div>

                </form>
            </div>

        </div>

    </main>
</div>
<?php include '../../includes/footer.php'; ?>