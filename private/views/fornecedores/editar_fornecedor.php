<?php
require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/validacoes.php';
redirect_if_not_logged();
if (!in_array($_SESSION['perfil'] ?? '', ['Administrador', 'Técnico'])) {
    header('Location: fornecedores.php');
    exit;
}

$erros = [];

// Receber e desencriptar o ID
$idEncriptado = $_GET['id'] ?? $_POST['id'] ?? null;
$id = aes_decrypt($idEncriptado);

if (!$id || !is_numeric($id)) {
    header('Location: fornecedores.php');
    exit;
}

// Carregar dados atuais do fornecedor
try {
    $ligacao = ligar_bd();
    $stmt = $ligacao->prepare("SELECT * FROM fornecedores WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);
    $ligacao = null;

    if (!$fornecedor) {
        header('Location: fornecedores.php');
        exit;
    }
} catch (PDOException $e) {
    header('Location: fornecedores.php');
    exit;
}

// Pré-preencher $dados com os valores atuais
$dados = $fornecedor;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dados = [
        'nome_empresa'       => trim($_POST['nome_empresa'] ?? ''),
        'tipo_fornecedor'    => trim($_POST['tipo_fornecedor'] ?? ''),
        'nif'                => trim($_POST['nif'] ?? ''),
        'telefone'           => trim($_POST['telefone'] ?? ''),
        'email'              => trim($_POST['email'] ?? ''),
        'morada'             => trim($_POST['morada'] ?? ''),
        'website'            => trim($_POST['website'] ?? ''),
        'pessoa_contacto'    => trim($_POST['pessoa_contacto'] ?? ''),
        'telefone_contacto'  => trim($_POST['telefone_contacto'] ?? ''),
        'observacoes'        => trim($_POST['observacoes'] ?? ''),
    ];

    $erros = validar_inserir_fornecedor($dados);

    if (empty($erros)) {
        try {
            $ligacao = ligar_bd();

            // Verificar NIF duplicado (excluindo o próprio)
            $stmtNif = $ligacao->prepare("SELECT id FROM fornecedores WHERE nif = :nif AND id != :id");
            $stmtNif->execute([':nif' => $dados['nif'], ':id' => $id]);
            if ($stmtNif->fetch()) {
                $erros[] = 'Já existe um fornecedor com este NIF.';
            }

            if (empty($erros)) {
                $stmt = $ligacao->prepare("
                    UPDATE fornecedores SET
                        nome_empresa       = :nome,
                        tipo_fornecedor    = :tipo,
                        nif                = :nif,
                        website            = :website,
                        email              = :email,
                        telefone           = :telefone,
                        morada             = :morada,
                        pessoa_contacto    = :pessoa_contacto,
                        telefone_contacto  = :telefone_contacto,
                        observacoes        = :observacoes
                    WHERE id = :id
                ");
                $stmt->execute([
                    ':nome'               => $dados['nome_empresa'],
                    ':tipo'               => $dados['tipo_fornecedor'],
                    ':nif'                => $dados['nif'],
                    ':website'            => $dados['website'] ?: null,
                    ':email'              => $dados['email'] ?: null,
                    ':telefone'           => $dados['telefone'] ?: null,
                    ':morada'             => $dados['morada'] ?: null,
                    ':pessoa_contacto'    => $dados['pessoa_contacto'] ?: null,
                    ':telefone_contacto'  => $dados['telefone_contacto'] ?: null,
                    ':observacoes'        => $dados['observacoes'] ?: null,
                    ':id'                 => $id,
                ]);

                $ligacao = null;
                header('Location: fornecedores.php?sucesso=editado');
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
$paginaAtiva = 'fornecedores';
?>

<div class="private-layout">
    <?php include '../../includes/sidebar.php'; ?>

    <main class="private-main">

        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <h1 class="fw-bold mb-1">Editar Fornecedor</h1>
                <p class="text-muted mb-0">Modificar os dados do fornecedor.</p>
            </div>
            <a href="fornecedores.php" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>
                Voltar
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4 p-lg-5">

                <form method="POST" action="">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($idEncriptado) ?>">

                    <h4 class="fw-bold mb-4">
                        <i class="fa-solid fa-building me-2 text-primary"></i>
                        Dados do fornecedor
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

                        <div class="col-md-4">
                            <label class="form-label fw-bold obrigatorio">Código</label>
                            <input type="text" class="form-control"
                                value="<?= htmlspecialchars($fornecedor['codigo_fornecedor']) ?>"
                                disabled>
                            <small class="text-muted">O código não pode ser alterado.</small>
                        </div>

                        <div class="col-md-8">
                            <label for="nomeFornecedor" class="form-label fw-bold obrigatorio">Nome da empresa</label>
                            <input type="text" id="nomeFornecedor" class="form-control"
                                name="nome_empresa"
                                placeholder="Ex: Dräger"
                                value="<?= htmlspecialchars($dados['nome_empresa'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="tipoFornecedor" class="form-label fw-bold obrigatorio">Tipo de fornecedor</label>
                            <select id="tipoFornecedor" class="form-select" name="tipo_fornecedor">
                                <option value="" disabled <?= empty($dados['tipo_fornecedor']) ? 'selected' : '' ?>>Selecionar tipo</option>
                                <option value="Fabricante" <?= ($dados['tipo_fornecedor'] ?? '') == 'Fabricante' ? 'selected' : '' ?>>Fabricante</option>
                                <option value="Distribuidor" <?= ($dados['tipo_fornecedor'] ?? '') == 'Distribuidor' ? 'selected' : '' ?>>Distribuidor</option>
                                <option value="Assistência Técnica" <?= ($dados['tipo_fornecedor'] ?? '') == 'Assistência Técnica' ? 'selected' : '' ?>>Assistência Técnica</option>
                                <option value="Consumíveis / Acessórios" <?= ($dados['tipo_fornecedor'] ?? '') == 'Consumíveis / Acessórios' ? 'selected' : '' ?>>Consumíveis / Acessórios</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="nifFornecedor" class="form-label fw-bold obrigatorio">NIF</label>
                            <input type="text" id="nifFornecedor" class="form-control"
                                name="nif"
                                placeholder="Ex: 509123456"
                                inputmode="numeric"
                                value="<?= htmlspecialchars($dados['nif'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="telefoneFornecedor" class="form-label fw-bold obrigatorio">Contacto telefónico</label>
                            <input type="text" id="telefoneFornecedor" class="form-control"
                                name="telefone"
                                placeholder="Ex: +351 220 000 000"
                                value="<?= htmlspecialchars($dados['telefone'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="emailFornecedor" class="form-label fw-bold obrigatorio">Email</label>
                            <input type="text" id="emailFornecedor" class="form-control"
                                name="email"
                                placeholder="Ex: geral@empresa.pt"
                                value="<?= htmlspecialchars($dados['email'] ?? '') ?>">
                        </div>

                        <div class="col-md-8">
                            <label for="moradaFornecedor" class="form-label fw-bold">Morada</label>
                            <input type="text" id="moradaFornecedor" class="form-control"
                                name="morada"
                                placeholder="Ex: Rua da Tecnologia, Porto"
                                value="<?= htmlspecialchars($dados['morada'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="websiteFornecedor" class="form-label fw-bold">Website</label>
                            <input type="text" id="websiteFornecedor" class="form-control"
                                name="website"
                                placeholder="Ex: www.empresa.pt"
                                value="<?= htmlspecialchars($dados['website'] ?? '') ?>">
                        </div>

                    </div>

                    <hr class="my-5">

                    <h4 class="fw-bold mb-4">
                        <i class="fa-solid fa-user me-2 text-primary"></i>
                        Pessoa de contacto
                    </h4>

                    <div class="row g-4">

                        <div class="col-md-6">
                            <label for="pessoaContacto" class="form-label fw-bold obrigatorio">Nome da pessoa de contacto</label>
                            <input type="text" id="pessoaContacto" class="form-control"
                                name="pessoa_contacto"
                                placeholder="Ex: João Silva"
                                value="<?= htmlspecialchars($dados['pessoa_contacto'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="telefoneContacto" class="form-label fw-bold obrigatorio">Telefone da pessoa de contacto</label>
                            <input type="text" id="telefoneContacto" class="form-control"
                                name="telefone_contacto"
                                placeholder="Ex: +351 912 000 000"
                                value="<?= htmlspecialchars($dados['telefone_contacto'] ?? '') ?>">
                        </div>

                    </div>

                    <hr class="my-5">

                    <h4 class="fw-bold mb-4">
                        <i class="fa-solid fa-note-sticky me-2 text-primary"></i>
                        Observações
                    </h4>

                    <div class="mb-4">
                        <textarea class="form-control" rows="5" name="observacoes"
                            placeholder="Observações adicionais sobre o fornecedor..."><?= htmlspecialchars($dados['observacoes'] ?? '') ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5">
                        <a href="fornecedores.php" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="fa-solid fa-floppy-disk me-2"></i>
                            Atualizar Fornecedor
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </main>
</div>

<?php include '../../includes/footer.php'; ?>