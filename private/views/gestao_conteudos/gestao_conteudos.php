<?php
require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

$ligacao = ligar_bd();

// ============================================================
// PROCESSAR — INÍCIO
// ============================================================
$erros_inicio   = [];
$sucesso_inicio = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'guardar_inicio') {

    $titulo_principal = trim($_POST['titulo_principal'] ?? '');
    $descricao        = trim($_POST['descricao']        ?? '');
    $texto_botao      = trim($_POST['texto_botao']      ?? '');
    $link_botao       = trim($_POST['link_botao']       ?? '');
    $imagem_atual     = trim($_POST['imagem_atual']     ?? '');
    $imagem_final     = $imagem_atual;

    if (!empty($_FILES['imagem_principal']['name'])) {
        $pasta = __DIR__ . '/../../../assets/uploads/conteudos/';
        $ext   = strtolower(pathinfo($_FILES['imagem_principal']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $erros_inicio[] = 'A imagem deve ser JPG, PNG, WEBP ou GIF.';
        } elseif ($_FILES['imagem_principal']['size'] > 5 * 1024 * 1024) {
            $erros_inicio[] = 'A imagem não pode ultrapassar 5 MB.';
        } else {
            $nome_unico = 'inicio_' . uniqid('', true) . '.' . $ext;
            if (move_uploaded_file($_FILES['imagem_principal']['tmp_name'], $pasta . $nome_unico)) {
                $imagem_final = '../assets/uploads/conteudos/' . $nome_unico;
            } else {
                $erros_inicio[] = 'Erro ao guardar a imagem.';
            }
        }
    }

    if (empty($titulo_principal)) $erros_inicio[] = 'O título principal é obrigatório.';

    if (empty($erros_inicio)) {
        $stmt = $ligacao->prepare("
            UPDATE gestao_conteudos
            SET titulo_principal = :titulo, descricao = :descricao,
                texto_botao = :texto_botao, link_botao = :link_botao,
                imagem_principal = :imagem
            WHERE id = 1
        ");
        $stmt->execute([
            ':titulo' => $titulo_principal,
            ':descricao' => $descricao,
            ':texto_botao' => $texto_botao,
            ':link_botao' => $link_botao,
            ':imagem' => $imagem_final,
        ]);
        $sucesso_inicio = true;
    }
}

// ============================================================
// PROCESSAR — ADICIONAR SERVIÇO
// ============================================================
$erros_servico   = [];
$sucesso_servico = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'adicionar_servico') {

    $titulo_s = trim($_POST['novo_titulo']    ?? '');
    $icone_s  = trim($_POST['novo_icone']     ?? '');
    $desc_s   = trim($_POST['novo_descricao'] ?? '');
    $estado_s = trim($_POST['novo_estado']    ?? 'Ativo');
    $ordem_s  = intval($_POST['novo_ordem']   ?? 0);

    if (empty($titulo_s)) $erros_servico[] = 'O título do serviço é obrigatório.';
    if (empty($icone_s))  $erros_servico[] = 'Seleciona um ícone.';
    if (empty($desc_s))   $erros_servico[] = 'A descrição é obrigatória.';

    if (empty($erros_servico)) {
        $stmt = $ligacao->prepare("
            INSERT INTO gestao_conteudos_servicos (titulo, descricao, icone, estado, ordem_apresentacao)
            VALUES (:titulo, :descricao, :icone, :estado, :ordem)
        ");
        $stmt->execute([
            ':titulo' => $titulo_s,
            ':descricao' => $desc_s,
            ':icone'  => $icone_s,
            ':estado'    => $estado_s,
            ':ordem'  => $ordem_s ?: null,
        ]);
        $sucesso_servico = true;
    }
}

// ============================================================
// PROCESSAR — GUARDAR ALTERAÇÕES SERVIÇOS
// ============================================================
$erros_guardar_servicos   = [];
$sucesso_guardar_servicos = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'guardar_servicos') {

    $ids        = $_POST['servico_id']        ?? [];
    $titulos    = $_POST['servico_titulo']    ?? [];
    $descricoes = $_POST['servico_descricao'] ?? [];
    $icones     = $_POST['servico_icone']     ?? [];
    $estados    = $_POST['servico_estado']    ?? [];
    $ordens     = $_POST['servico_ordem']     ?? [];

    foreach ($ids as $i => $id_s) {
        $id_s   = intval($id_s);
        $tit    = trim($titulos[$i]    ?? '');
        $desc   = trim($descricoes[$i] ?? '');
        $icone  = trim($icones[$i]     ?? '');
        $estado = trim($estados[$i]    ?? 'Ativo');
        $ordem  = intval($ordens[$i]   ?? 0);

        if (empty($tit) || empty($icone) || empty($desc)) {
            $erros_guardar_servicos[] = "Preenche todos os campos do serviço #" . ($i + 1) . ".";
            continue;
        }

        $stmt = $ligacao->prepare("
            UPDATE gestao_conteudos_servicos
            SET titulo = :titulo, descricao = :descricao, icone = :icone,
                estado = :estado, ordem_apresentacao = :ordem
            WHERE id = :id
        ");
        $stmt->execute([
            ':titulo' => $tit,
            ':descricao' => $desc,
            ':icone' => $icone,
            ':estado' => $estado,
            ':ordem' => $ordem ?: null,
            ':id' => $id_s,
        ]);
    }

    if (empty($erros_guardar_servicos)) $sucesso_guardar_servicos = true;
}

// ============================================================
// PROCESSAR — ELIMINAR SERVIÇO
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'eliminar_servico') {
    $id_eliminar = intval($_POST['id_servico'] ?? 0);
    if ($id_eliminar > 0) {
        $stmt = $ligacao->prepare("DELETE FROM gestao_conteudos_servicos WHERE id = :id");
        $stmt->execute([':id' => $id_eliminar]);
    }
    header('Location: gestao_conteudos.php?tab=servicos&sucesso=eliminado');
    exit;
}
// ============================================================
// PROCESSAR — SOBRE NÓS
// ============================================================
$erros_sobre   = [];
$sucesso_sobre = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'guardar_sobre') {

    $titulo_secao    = trim($_POST['titulo_secao']    ?? '');
    $texto_principal = trim($_POST['texto_principal'] ?? '');
    $bloco1_titulo   = trim($_POST['bloco1_titulo']   ?? '');
    $bloco1_texto    = trim($_POST['bloco1_texto']    ?? '');
    $bloco2_titulo   = trim($_POST['bloco2_titulo']   ?? '');
    $bloco2_texto    = trim($_POST['bloco2_texto']    ?? '');
    $bloco3_titulo   = trim($_POST['bloco3_titulo']   ?? '');
    $bloco3_texto    = trim($_POST['bloco3_texto']    ?? '');

    if (empty($titulo_secao))    $erros_sobre[] = 'O título da secção é obrigatório.';
    if (empty($texto_principal)) $erros_sobre[] = 'O texto principal é obrigatório.';

    if (empty($erros_sobre)) {
        $stmt = $ligacao->prepare("
            UPDATE gestao_conteudos_sobre
            SET titulo_secao    = :titulo,
                texto_principal = :texto,
                bloco1_titulo   = :b1t,  bloco1_texto = :b1tx,
                bloco2_titulo   = :b2t,  bloco2_texto = :b2tx,
                bloco3_titulo   = :b3t,  bloco3_texto = :b3tx
            WHERE id = 1
        ");
        $stmt->execute([
            ':titulo' => $titulo_secao,
            ':texto' => $texto_principal,
            ':b1t'    => $bloco1_titulo,
            ':b1tx' => $bloco1_texto,
            ':b2t'    => $bloco2_titulo,
            ':b2tx' => $bloco2_texto,
            ':b3t'    => $bloco3_titulo,
            ':b3tx' => $bloco3_texto,
        ]);
        $sucesso_sobre = true;
    }
}
// ============================================================
// PROCESSAR — CONTACTOS
// ============================================================
$erros_contactos   = [];
$sucesso_contactos = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'guardar_contactos') {

    $titulo_secao       = trim($_POST['contactos_titulo_secao']       ?? '');
    $texto_introdutorio = trim($_POST['contactos_texto_introdutorio'] ?? '');
    $titulo_formulario  = trim($_POST['contactos_titulo_formulario']  ?? '');
    $texto_botao        = trim($_POST['contactos_texto_botao']        ?? '');

    if (empty($titulo_secao)) $erros_contactos[] = 'O título da secção é obrigatório.';

    if (empty($erros_contactos)) {
        $stmt = $ligacao->prepare("
            UPDATE gestao_conteudos_contactos
            SET titulo_secao       = :titulo,
                texto_introdutorio = :texto,
                titulo_formulario  = :titulo_form,
                texto_botao        = :texto_botao
            WHERE id = 1
        ");
        $stmt->execute([
            ':titulo'      => $titulo_secao,
            ':texto'       => $texto_introdutorio,
            ':titulo_form' => $titulo_formulario,
            ':texto_botao' => $texto_botao,
        ]);
        $sucesso_contactos = true;
    }
}
// ============================================================
// PROCESSAR — ADICIONAR FAQ
// ============================================================
$erros_faq   = [];
$sucesso_faq = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'adicionar_faq') {

    $pergunta = trim($_POST['nova_pergunta'] ?? '');
    $resposta = trim($_POST['nova_resposta'] ?? '');
    $ordem    = intval($_POST['nova_ordem']  ?? 0);

    if (empty($pergunta)) $erros_faq[] = 'A pergunta é obrigatória.';
    if (empty($resposta)) $erros_faq[] = 'A resposta é obrigatória.';

    if ($ordem < 0) $erros_faq[] = 'A ordem não pode ser negativa.';

    if ($ordem > 0 && empty($erros_faq)) {
        $stmt_check = $ligacao->prepare("SELECT COUNT(*) FROM gestao_conteudos_faq WHERE ordem_apresentacao = :ordem");
        $stmt_check->execute([':ordem' => $ordem]);
        if ($stmt_check->fetchColumn() > 0) {
            $erros_faq[] = 'Já existe uma pergunta com a ordem ' . $ordem . '. Escolhe uma ordem diferente.';
        }
    }

    if (empty($erros_faq)) {
        $stmt = $ligacao->prepare("
            INSERT INTO gestao_conteudos_faq (pergunta, resposta, ordem_apresentacao)
            VALUES (:pergunta, :resposta, :ordem)
        ");
        $stmt->execute([
            ':pergunta' => $pergunta,
            ':resposta' => $resposta,
            ':ordem'    => $ordem ?: null,
        ]);
        $sucesso_faq = true;
    }
}

// ============================================================
// PROCESSAR — GUARDAR ALTERAÇÕES FAQ
// ============================================================
$erros_guardar_faq   = [];
$sucesso_guardar_faq = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'guardar_faq') {

    $ids       = $_POST['faq_id']       ?? [];
    $perguntas = $_POST['faq_pergunta'] ?? [];
    $respostas = $_POST['faq_resposta'] ?? [];
    $ordens    = $_POST['faq_ordem']    ?? [];

    $ordens_usadas = [];

    foreach ($ids as $i => $id_f) {
        $id_f     = intval($id_f);
        $pergunta = trim($perguntas[$i] ?? '');
        $resposta = trim($respostas[$i] ?? '');
        $ordem    = intval($ordens[$i]  ?? 0);

        if (empty($pergunta) || empty($resposta)) {
            $erros_guardar_faq[] = "Preenche a pergunta e resposta do item #" . ($i + 1) . ".";
            continue;
        }

        if ($ordem < 0) {
            $erros_guardar_faq[] = "A ordem do item #" . ($i + 1) . " não pode ser negativa.";
            continue;
        }

        if ($ordem > 0 && in_array($ordem, $ordens_usadas)) {
            $erros_guardar_faq[] = "A ordem " . $ordem . " está repetida. Cada pergunta deve ter uma ordem diferente.";
            continue;
        }

        if ($ordem > 0) $ordens_usadas[] = $ordem;

        $stmt = $ligacao->prepare("
            UPDATE gestao_conteudos_faq
            SET pergunta           = :pergunta,
                resposta           = :resposta,
                ordem_apresentacao = :ordem
            WHERE id = :id
        ");
        $stmt->execute([
            ':pergunta' => $pergunta,
            ':resposta' => $resposta,
            ':ordem'    => $ordem ?: null,
            ':id'       => $id_f,
        ]);
    }

    if (empty($erros_guardar_faq)) $sucesso_guardar_faq = true;
}

// ============================================================
// PROCESSAR — ELIMINAR FAQ
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'eliminar_faq') {
    $id_eliminar = intval($_POST['id_faq'] ?? 0);
    if ($id_eliminar > 0) {
        $stmt = $ligacao->prepare("DELETE FROM gestao_conteudos_faq WHERE id = :id");
        $stmt->execute([':id' => $id_eliminar]);
    }
    header('Location: gestao_conteudos.php?tab=faq&sucesso=eliminado');
    exit;
}
// ============================================================
// PROCESSAR — RODAPÉ
// ============================================================
$erros_rodape   = [];
$sucesso_rodape = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'guardar_rodape') {

    $texto_descritivo = trim($_POST['rodape_texto_descritivo'] ?? '');
    $localizacao      = trim($_POST['rodape_localizacao']      ?? '');
    $horario          = trim($_POST['rodape_horario']          ?? '');
    $telefone         = trim($_POST['rodape_telefone']         ?? '');
    $email            = trim($_POST['rodape_email']            ?? '');
    $logo_atual       = trim($_POST['logo_atual']              ?? '');
    $logo_final       = $logo_atual;

    // Upload do logo
    if (!empty($_FILES['rodape_logo']['name'])) {
        $pasta = __DIR__ . '/../../../assets/uploads/conteudos/';
        $ext   = strtolower(pathinfo($_FILES['rodape_logo']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
            $erros_rodape[] = 'O logo deve ser JPG, PNG, WEBP, GIF ou SVG.';
        } elseif ($_FILES['rodape_logo']['size'] > 5 * 1024 * 1024) {
            $erros_rodape[] = 'O logo não pode ultrapassar 5 MB.';
        } else {
            $nome_unico = 'logo_' . uniqid('', true) . '.' . $ext;
            if (move_uploaded_file($_FILES['rodape_logo']['tmp_name'], $pasta . $nome_unico)) {
                $logo_final = '../assets/uploads/conteudos/' . $nome_unico;
            } else {
                $erros_rodape[] = 'Erro ao guardar o logo.';
            }
        }
    }

    if (empty($erros_rodape)) {
        $stmt = $ligacao->prepare("
            UPDATE gestao_conteudos_rodape
            SET logo             = :logo,
                texto_descritivo = :texto,
                localizacao      = :localizacao,
                horario          = :horario,
                telefone         = :telefone,
                email            = :email
            WHERE id = 1
        ");
        $stmt->execute([
            ':logo'        => $logo_final,
            ':texto'       => $texto_descritivo,
            ':localizacao' => $localizacao,
            ':horario'     => $horario,
            ':telefone'    => $telefone,
            ':email'       => $email,
        ]);
        $sucesso_rodape = true;
    }
}
// ============================================================
// CARREGAR DADOS
// ============================================================
$inicio = $ligacao->query("SELECT * FROM gestao_conteudos WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

$servicos = $ligacao->query("
    SELECT * FROM gestao_conteudos_servicos
    ORDER BY ordem_apresentacao ASC, id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$sobre = $ligacao->query("SELECT * FROM gestao_conteudos_sobre WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

$contactos = $ligacao->query("SELECT * FROM gestao_conteudos_contactos WHERE id = 1")->fetch(PDO::FETCH_ASSOC);

$faqs = $ligacao->query("
    SELECT * FROM gestao_conteudos_faq
    ORDER BY ordem_apresentacao ASC, id ASC
")->fetchAll(PDO::FETCH_ASSOC);

$rodape = $ligacao->query("SELECT * FROM gestao_conteudos_rodape WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
// Tab ativa após POST
$tab_ativa = 'inicio';
if (
    $sucesso_servico || !empty($erros_servico)
    || $sucesso_guardar_servicos || !empty($erros_guardar_servicos)
) {
    $tab_ativa = 'servicos';
}
if ($sucesso_sobre || !empty($erros_sobre)) {
    $tab_ativa = 'sobre';
}
if ($sucesso_contactos || !empty($erros_contactos)) {
    $tab_ativa = 'contactos';
}
if (
    $sucesso_faq || !empty($erros_faq)
    || $sucesso_guardar_faq || !empty($erros_guardar_faq)
) {
    $tab_ativa = 'faq';
}
if ($sucesso_rodape || !empty($erros_rodape)) {
    $tab_ativa = 'rodape';
}
if (isset($_GET['tab'])) $tab_ativa = htmlspecialchars($_GET['tab']);


// Ícones disponíveis
$icones_disponiveis = [
    'fa-solid fa-clipboard-list'     => 'Clipboard List',
    'fa-solid fa-folder-open'        => 'Folder Open',
    'fa-solid fa-chart-simple'       => 'Chart Simple',
    'fa-solid fa-briefcase-medical'  => 'Briefcase Medical',
    'fa-solid fa-screwdriver-wrench' => 'Screwdriver Wrench',
    'fa-solid fa-file-lines'         => 'File Lines',
    'fa-solid fa-hospital'           => 'Hospital',
];
?>

<?php include '../../includes/header.php';
$paginaAtiva = 'gestao_conteudos';
?>

<div class="private-layout">

    <?php include '../../includes/sidebar.php'; ?>


    <main class="private-main">
        <!-- CABEÇALHO -->
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">

            <div>
                <h1 class="fw-bold mb-1">Gestão de Conteúdos Públicos</h1>
                <p class="text-muted mb-0">
                    Atualização dos textos, contactos e informações apresentados na área pública do website.
                </p>
            </div>

            <a href="../../../public/index.php" class="btn btn-outline-secondary">
                <i class="fa-solid fa-eye me-2"></i>
                Ver Página Pública
            </a>

        </div>

        <!-- CARD PRINCIPAL -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">

                <!-- TABS / LINKS DE GESTÃO -->
                <ul class="nav nav-pills conteudos-tabs mb-4" id="conteudosTabs" role="tablist">

                    <!-- INÍCIO - fica na própria página -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $tab_ativa === 'inicio' ? 'active' : '' ?>" id="inicio-tab" data-bs-toggle="pill"
                            data-bs-target="#inicio" type="button" role="tab" aria-controls="inicio"
                            aria-selected="true">
                            <i class="fa-solid fa-house me-2"></i>
                            Início
                        </button>
                    </li>

                    <!-- SERVIÇOS -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $tab_ativa === 'servicos' ? 'active' : '' ?>"
                            id="servicos-tab" data-bs-toggle="pill" data-bs-target="#servicos"
                            type="button" role="tab" aria-controls="servicos" aria-selected="false">
                            <i class="fa-solid fa-briefcase-medical me-2"></i>
                            Serviços
                        </button>
                    </li>

                    <!-- SOBRE NÓS -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $tab_ativa === 'sobre' ? 'active' : '' ?>"
                            id="sobre-tab" data-bs-toggle="pill" data-bs-target="#sobre"
                            type="button" role="tab">
                            <i class="fa-solid fa-users me-2"></i>
                            Sobre Nós
                        </button>
                    </li>

                    <!-- FAQ -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $tab_ativa === 'faq' ? 'active' : '' ?>"
                            id="faq-tab" data-bs-toggle="pill" data-bs-target="#faq"
                            type="button" role="tab">
                            <i class="fa-solid fa-circle-question me-2"></i>
                            FAQ
                        </button>
                    </li>

                    <!-- CONTACTOS -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $tab_ativa === 'contactos' ? 'active' : '' ?>"
                            id="contactos-tab" data-bs-toggle="pill" data-bs-target="#contactos"
                            type="button" role="tab">
                            <i class="fa-solid fa-address-book me-2"></i>
                            Contactos
                        </button>
                    </li>

                    <!-- RODAPÉ -->
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $tab_ativa === 'rodape' ? 'active' : '' ?>"
                            id="rodape-tab" data-bs-toggle="pill" data-bs-target="#rodape"
                            type="button" role="tab">
                            <i class="fa-solid fa-window-minimize me-2"></i>
                            Rodapé
                        </button>
                    </li>

                </ul>

                <!-- CONTEÚDO DAS TABS -->
                <div class="tab-content" id="conteudosTabsContent">

                    <!-- TAB INÍCIO -->
                    <div class="tab-pane fade <?= $tab_ativa === 'inicio' ? 'show active' : '' ?>"
                        id="inicio" role="tabpanel" aria-labelledby="inicio-tab">

                        <h4 class="fw-bold mb-4">Secção Inicial</h4>

                        <?php if ($sucesso_inicio): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fa-solid fa-circle-check me-2"></i>
                                Secção inicial atualizada com sucesso.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($erros_inicio)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Corrige os seguintes erros:</strong>
                                <ul class="mb-0 mt-1">
                                    <?php foreach ($erros_inicio as $erro): ?>
                                        <li><?= htmlspecialchars($erro) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="acao" value="guardar_inicio">
                            <input type="hidden" name="imagem_atual"
                                value="<?= htmlspecialchars($inicio['imagem_principal'] ?? '') ?>">

                            <div class="row g-4">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Título principal</label>
                                    <input type="text" name="titulo_principal" class="form-control"
                                        value="<?= htmlspecialchars($inicio['titulo_principal'] ?? '') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Texto do botão</label>
                                    <input type="text" name="texto_botao" class="form-control"
                                        value="<?= htmlspecialchars($inicio['texto_botao'] ?? '') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Link do botão</label>
                                    <input type="text" name="link_botao" class="form-control"
                                        placeholder="Ex: #servicos"
                                        value="<?= htmlspecialchars($inicio['link_botao'] ?? '') ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Descrição</label>
                                    <textarea name="descricao" class="form-control" rows="4"><?= htmlspecialchars($inicio['descricao'] ?? '') ?></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Imagem principal</label>
                                    <?php if (!empty($inicio['imagem_principal'])): ?>
                                        <p class="text-muted small mb-1">
                                            <i class="fa-solid fa-image me-1"></i>
                                            Imagem atual: <code><?= htmlspecialchars(basename($inicio['imagem_principal'])) ?></code>
                                        </p>
                                    <?php endif; ?>
                                    <input type="file" name="imagem_principal" class="form-control" accept="image/*">
                                    <div class="form-text">Deixa em branco para manter a imagem atual. Máx. 5 MB.</div>
                                </div>

                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="fa-solid fa-floppy-disk me-2"></i>
                                    Guardar alterações
                                </button>
                            </div>

                        </form>

                    </div>

                    <!-- TAB SERVIÇOS -->
                    <div class="tab-pane fade <?= $tab_ativa === 'servicos' ? 'show active' : '' ?>"
                        id="servicos" role="tabpanel" aria-labelledby="servicos-tab">

                        <h4 class="fw-bold mb-4">Serviços Apresentados</h4>

                        <?php if ($sucesso_guardar_servicos): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fa-solid fa-circle-check me-2"></i> Serviços atualizados com sucesso.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($erros_guardar_servicos)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong>Erros:</strong>
                                <ul class="mb-0 mt-1">
                                    <?php foreach ($erros_guardar_servicos as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($sucesso_servico): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fa-solid fa-circle-check me-2"></i> Novo serviço adicionado.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($erros_servico)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong>Erros ao adicionar:</strong>
                                <ul class="mb-0 mt-1">
                                    <?php foreach ($erros_servico as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'eliminado'): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fa-solid fa-circle-check me-2"></i> Serviço eliminado com sucesso.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <h5 class="fw-bold mb-1">Serviços existentes</h5>
                            <p class="text-muted mb-0">Edita os títulos, descrições e estado dos serviços visíveis na página pública.</p>
                        </div>

                        <?php if (empty($servicos)): ?>
                            <div class="alert alert-info">
                                <i class="fa-solid fa-circle-info me-2"></i>
                                Ainda não existem serviços. Adiciona o primeiro abaixo.
                            </div>
                        <?php else: ?>

                            <form method="POST">
                                <input type="hidden" name="acao" value="guardar_servicos">

                                <?php foreach ($servicos as $i => $servico): ?>
                                    <div class="border rounded-4 p-4 mb-4 bg-light">
                                        <input type="hidden" name="servico_id[]" value="<?= $servico['id'] ?>">

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="fw-bold mb-0">Serviço <?= $i + 1 ?></h6>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge <?= $servico['estado'] === 'Ativo' ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                                    <?= htmlspecialchars($servico['estado']) ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Título do serviço</label>
                                                <input type="text" name="servico_titulo[]" class="form-control"
                                                    value="<?= htmlspecialchars($servico['titulo']) ?>">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Ícone</label>
                                                <div class="dropdown">
                                                    <button class="btn btn-light border w-100 d-flex justify-content-between align-items-center dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown">
                                                        <span>
                                                            <i class="<?= htmlspecialchars($servico['icone']) ?> me-2"></i>
                                                            <?= htmlspecialchars($icones_disponiveis[$servico['icone']] ?? 'Selecionar ícone') ?>
                                                        </span>
                                                    </button>
                                                    <ul class="dropdown-menu w-100">
                                                        <?php foreach ($icones_disponiveis as $val => $label): ?>
                                                            <li>
                                                                <a class="dropdown-item opcao-icone" href="#"
                                                                    data-valor="<?= $val ?>" data-label="<?= $label ?>">
                                                                    <i class="<?= $val ?> me-2"></i> <?= $label ?>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                    <input type="hidden" name="servico_icone[]"
                                                        value="<?= htmlspecialchars($servico['icone']) ?>">
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Descrição</label>
                                                <textarea name="servico_descricao[]" class="form-control" rows="3"><?= htmlspecialchars($servico['descricao']) ?></textarea>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Estado</label>
                                                <select name="servico_estado[]" class="form-select">
                                                    <option value="Ativo" <?= $servico['estado'] === 'Ativo'   ? 'selected' : '' ?>>Ativo</option>
                                                    <option value="Inativo" <?= $servico['estado'] === 'Inativo' ? 'selected' : '' ?>>Inativo</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Ordem de apresentação</label>
                                                <input type="number" name="servico_ordem[]" class="form-control"
                                                    value="<?= htmlspecialchars($servico['ordem_apresentacao'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                                <div class="d-flex justify-content-end mb-4">
                                    <button type="submit" class="btn btn-primary-custom">
                                        <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Alterações
                                    </button>
                                </div>
                            </form>

                            <!-- Formulários de eliminar (fora do form de guardar para não aninhar) -->
                            <?php foreach ($servicos as $servico): ?>
                                <form method="POST" id="form-eliminar-<?= $servico['id'] ?>" class="d-none">
                                    <input type="hidden" name="acao" value="eliminar_servico">
                                    <input type="hidden" name="id_servico" value="<?= $servico['id'] ?>">
                                </form>
                            <?php endforeach; ?>

                        <?php endif; ?>

                        <!-- ADICIONAR NOVO SERVIÇO -->
                        <div class="card border-0 shadow-sm rounded-4 mt-2">
                            <div class="card-body p-4">

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h5 class="fw-bold mb-1">Adicionar Novo Serviço</h5>
                                        <p class="text-muted mb-0">Cria um novo serviço para apresentar na área pública.</p>
                                    </div>
                                    <span class="badge text-bg-secondary">Novo</span>
                                </div>

                                <form method="POST">
                                    <input type="hidden" name="acao" value="adicionar_servico">

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Título do serviço</label>
                                            <input type="text" name="novo_titulo" class="form-control"
                                                placeholder="Ex.: Manutenção Preventiva">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Ícone</label>
                                            <div class="dropdown">
                                                <button class="btn btn-light border w-100 d-flex justify-content-between align-items-center dropdown-toggle"
                                                    type="button" data-bs-toggle="dropdown">
                                                    <span class="texto-icone-novo">Selecionar ícone</span>
                                                </button>
                                                <ul class="dropdown-menu w-100">
                                                    <?php foreach ($icones_disponiveis as $val => $label): ?>
                                                        <li>
                                                            <a class="dropdown-item opcao-icone" href="#"
                                                                data-valor="<?= $val ?>" data-label="<?= $label ?>">
                                                                <i class="<?= $val ?> me-2"></i> <?= $label ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                                <input type="hidden" name="novo_icone" value="">
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Descrição do serviço</label>
                                            <textarea name="novo_descricao" class="form-control" rows="3"
                                                placeholder="Breve descrição."></textarea>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Estado</label>
                                            <select name="novo_estado" class="form-select">
                                                <option value="Ativo">Ativo</option>
                                                <option value="Inativo">Inativo</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Ordem de apresentação</label>
                                            <input type="number" name="novo_ordem" class="form-control"
                                                placeholder="Ex.: <?= count($servicos) + 1 ?>">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 mt-4">
                                        <button type="reset" class="btn btn-outline-secondary">Limpar campos</button>
                                        <button type="submit" class="btn btn-primary-custom">
                                            <i class="fa-solid fa-plus me-2"></i> Adicionar Serviço
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>

                    </div>

                    <!-- TAB SOBRE NÓS -->
                    <div class="tab-pane fade <?= $tab_ativa === 'sobre' ? 'show active' : '' ?>"
                        id="sobre" role="tabpanel" aria-labelledby="sobre-tab">

                        <h4 class="fw-bold mb-4">Sobre Nós</h4>

                        <?php if ($sucesso_sobre): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fa-solid fa-circle-check me-2"></i>
                                Secção "Sobre Nós" atualizada com sucesso.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($erros_sobre)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong>Corrige os seguintes erros:</strong>
                                <ul class="mb-0 mt-1">
                                    <?php foreach ($erros_sobre as $erro): ?>
                                        <li><?= htmlspecialchars($erro) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <input type="hidden" name="acao" value="guardar_sobre">

                            <div class="row g-4">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Título da secção</label>
                                    <input type="text" name="titulo_secao" class="form-control"
                                        value="<?= htmlspecialchars($sobre['titulo_secao'] ?? '') ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Texto principal</label>
                                    <textarea name="texto_principal" class="form-control" rows="5"><?= htmlspecialchars($sobre['texto_principal'] ?? '') ?></textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Título do bloco 1</label>
                                    <input type="text" name="bloco1_titulo" class="form-control mb-3"
                                        value="<?= htmlspecialchars($sobre['bloco1_titulo'] ?? '') ?>">
                                    <label class="form-label fw-semibold">Texto do bloco 1</label>
                                    <textarea name="bloco1_texto" class="form-control" rows="4"><?= htmlspecialchars($sobre['bloco1_texto'] ?? '') ?></textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Título do bloco 2</label>
                                    <input type="text" name="bloco2_titulo" class="form-control mb-3"
                                        value="<?= htmlspecialchars($sobre['bloco2_titulo'] ?? '') ?>">
                                    <label class="form-label fw-semibold">Texto do bloco 2</label>
                                    <textarea name="bloco2_texto" class="form-control" rows="4"><?= htmlspecialchars($sobre['bloco2_texto'] ?? '') ?></textarea>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Título do bloco 3</label>
                                    <input type="text" name="bloco3_titulo" class="form-control mb-3"
                                        value="<?= htmlspecialchars($sobre['bloco3_titulo'] ?? '') ?>">
                                    <label class="form-label fw-semibold">Texto do bloco 3</label>
                                    <textarea name="bloco3_texto" class="form-control" rows="4"><?= htmlspecialchars($sobre['bloco3_texto'] ?? '') ?></textarea>
                                </div>

                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="fa-solid fa-floppy-disk me-2"></i>
                                    Guardar alterações
                                </button>
                            </div>

                        </form>

                    </div>

                    <!-- TAB CONTACTOS -->
                    <div class="tab-pane fade <?= $tab_ativa === 'contactos' ? 'show active' : '' ?>"
                        id="contactos" role="tabpanel">

                        <h4 class="fw-bold mb-4">Contactos</h4>

                        <?php if ($sucesso_contactos): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fa-solid fa-circle-check me-2"></i>
                                Contactos atualizados com sucesso.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($erros_contactos)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong>Corrige os seguintes erros:</strong>
                                <ul class="mb-0 mt-1">
                                    <?php foreach ($erros_contactos as $erro): ?>
                                        <li><?= htmlspecialchars($erro) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <input type="hidden" name="acao" value="guardar_contactos">

                            <div class="row g-4">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Título da secção</label>
                                    <input type="text" name="contactos_titulo_secao" class="form-control"
                                        value="<?= htmlspecialchars($contactos['titulo_secao'] ?? '') ?>">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Texto introdutório</label>
                                    <textarea name="contactos_texto_introdutorio" class="form-control" rows="3"><?= htmlspecialchars($contactos['texto_introdutorio'] ?? '') ?></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Título do formulário</label>
                                    <input type="text" name="contactos_titulo_formulario" class="form-control"
                                        value="<?= htmlspecialchars($contactos['titulo_formulario'] ?? '') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Texto do botão</label>
                                    <input type="text" name="contactos_texto_botao" class="form-control"
                                        value="<?= htmlspecialchars($contactos['texto_botao'] ?? '') ?>">
                                </div>

                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="fa-solid fa-floppy-disk me-2"></i>
                                    Guardar alterações
                                </button>
                            </div>

                        </form>
                    </div>

                    <!-- TAB FAQ -->
                    <div class="tab-pane fade <?= $tab_ativa === 'faq' ? 'show active' : '' ?>"
                        id="faq" role="tabpanel">

                        <h4 class="fw-bold mb-4">Perguntas Frequentes</h4>

                        <?php if ($sucesso_guardar_faq): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fa-solid fa-circle-check me-2"></i> FAQ atualizada com sucesso.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($erros_guardar_faq)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong>Erros:</strong>
                                <ul class="mb-0 mt-1">
                                    <?php foreach ($erros_guardar_faq as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($sucesso_faq): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fa-solid fa-circle-check me-2"></i> Nova pergunta adicionada.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($erros_faq)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong>Erros ao adicionar:</strong>
                                <ul class="mb-0 mt-1">
                                    <?php foreach ($erros_faq as $err): ?>
                                        <li><?= htmlspecialchars($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] === 'eliminado' && $tab_ativa === 'faq'): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fa-solid fa-circle-check me-2"></i> Pergunta eliminada com sucesso.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($faqs)): ?>
                            <div class="alert alert-info mb-4">
                                <i class="fa-solid fa-circle-info me-2"></i>
                                Ainda não existem perguntas. Adiciona a primeira abaixo.
                            </div>
                        <?php else: ?>

                            <!-- FORM DE GUARDAR (sem botão aqui) -->
                            <form method="POST" id="form-guardar-faq">
                                <input type="hidden" name="acao" value="guardar_faq">

                                <?php foreach ($faqs as $i => $faq): ?>
                                    <div class="border rounded-4 p-4 mb-4 bg-light">
                                        <input type="hidden" name="faq_id[]" value="<?= $faq['id'] ?>">

                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="fw-bold mb-0">Pergunta <?= $i + 1 ?></h6>
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="if(confirm('Tens a certeza que queres eliminar esta pergunta?')) document.getElementById('form-eliminar-faq-<?= $faq['id'] ?>').submit();">
                                                <i class="fa-solid fa-trash me-1"></i> Eliminar
                                            </button>
                                        </div>

                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Pergunta</label>
                                                <input type="text" name="faq_pergunta[]" class="form-control"
                                                    value="<?= htmlspecialchars($faq['pergunta']) ?>">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Resposta</label>
                                                <textarea name="faq_resposta[]" class="form-control"
                                                    rows="3"><?= htmlspecialchars($faq['resposta']) ?></textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold">Ordem de apresentação</label>
                                                <input type="number" name="faq_ordem[]" class="form-control" min="1"
                                                    value="<?= htmlspecialchars($faq['ordem_apresentacao'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                            </form>

                            <!-- FORMS DE ELIMINAR (fora do form principal) -->
                            <?php foreach ($faqs as $faq): ?>
                                <form method="POST" id="form-eliminar-faq-<?= $faq['id'] ?>" class="d-none">
                                    <input type="hidden" name="acao" value="eliminar_faq">
                                    <input type="hidden" name="id_faq" value="<?= $faq['id'] ?>">
                                </form>
                            <?php endforeach; ?>

                        <?php endif; ?>

                        <!-- ADICIONAR NOVA PERGUNTA -->
                        <div class="card border-0 shadow-sm rounded-4 mt-2">
                            <div class="card-body p-4">

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h5 class="fw-bold mb-1">Adicionar Nova Pergunta</h5>
                                        <p class="text-muted mb-0">Cria uma nova pergunta para apresentar na área pública.</p>
                                    </div>
                                    <span class="badge text-bg-secondary">Nova</span>
                                </div>

                                <form method="POST">
                                    <input type="hidden" name="acao" value="adicionar_faq">

                                    <div class="row g-4">
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Pergunta</label>
                                            <input type="text" name="nova_pergunta" class="form-control"
                                                placeholder="Ex.: Como posso aceder à plataforma?">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-semibold">Resposta</label>
                                            <textarea name="nova_resposta" class="form-control" rows="3"
                                                placeholder="Escreve a resposta à pergunta."></textarea>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Ordem de apresentação</label>
                                            <input type="number" name="nova_ordem" class="form-control" min="1"
                                                placeholder="Ex.: <?= count($faqs) + 1 ?>">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 mt-4">
                                        <button type="reset" class="btn btn-outline-secondary">Limpar campos</button>
                                        <button type="submit" class="btn btn-primary-custom">
                                            <i class="fa-solid fa-plus me-2"></i> Adicionar Pergunta
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>

                        <!-- BOTÃO GUARDAR ALTERAÇÕES (associado ao form-guardar-faq via atributo form=) -->
                        <?php if (!empty($faqs)): ?>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" form="form-guardar-faq" class="btn btn-primary-custom">
                                    <i class="fa-solid fa-floppy-disk me-2"></i> Guardar Alterações
                                </button>
                            </div>
                        <?php endif; ?>

                    </div>


                    <!-- TAB RODAPÉ -->
                    <div class="tab-pane fade <?= $tab_ativa === 'rodape' ? 'show active' : '' ?>"
                        id="rodape" role="tabpanel">

                        <h4 class="fw-bold mb-4">Rodapé da Página Pública</h4>

                        <?php if ($sucesso_rodape): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fa-solid fa-circle-check me-2"></i>
                                Rodapé atualizado com sucesso.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($erros_rodape)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong>Corrige os seguintes erros:</strong>
                                <ul class="mb-0 mt-1">
                                    <?php foreach ($erros_rodape as $erro): ?>
                                        <li><?= htmlspecialchars($erro) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="acao" value="guardar_rodape">
                            <input type="hidden" name="logo_atual"
                                value="<?= htmlspecialchars($rodape['logo'] ?? '') ?>">

                            <div class="row g-4">

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Logo do rodapé</label>
                                    <?php if (!empty($rodape['logo'])): ?>
                                        <p class="text-muted small mb-1">
                                            <i class="fa-solid fa-image me-1"></i>
                                            Logo atual: <code><?= htmlspecialchars(basename($rodape['logo'])) ?></code>
                                        </p>
                                    <?php endif; ?>
                                    <input type="file" name="rodape_logo" class="form-control" accept="image/*">
                                    <div class="form-text">Deixa em branco para manter o logo atual. Máx. 5 MB.</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Texto descritivo</label>
                                    <textarea name="rodape_texto_descritivo" class="form-control" rows="3"><?= htmlspecialchars($rodape['texto_descritivo'] ?? '') ?></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Localização</label>
                                    <textarea name="rodape_localizacao" class="form-control" rows="3"><?= htmlspecialchars($rodape['localizacao'] ?? '') ?></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Horário</label>
                                    <textarea name="rodape_horario" class="form-control" rows="3"><?= htmlspecialchars($rodape['horario'] ?? '') ?></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Telefone</label>
                                    <input type="text" name="rodape_telefone" class="form-control"
                                        value="<?= htmlspecialchars($rodape['telefone'] ?? '') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="text" name="rodape_email" class="form-control"
                                        value="<?= htmlspecialchars($rodape['email'] ?? '') ?>">
                                </div>

                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary-custom">
                                    <i class="fa-solid fa-floppy-disk me-2"></i>
                                    Guardar alterações
                                </button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    // Quando se clica numa opção de ícone, atualiza o botão e o input escondido
    document.querySelectorAll(".opcao-icone").forEach(function(opcao) {
        opcao.addEventListener("click", function(e) {
            e.preventDefault();

            const valor = this.dataset.valor;
            const label = this.dataset.label;

            // O botão do dropdown é o "primo" anterior do <ul> que contém esta opção
            const dropdown = this.closest(".dropdown");
            const botao = dropdown.querySelector("button span");
            const inputEscondido = dropdown.querySelector("input[type='hidden']");

            // Atualizar o texto e ícone do botão
            botao.innerHTML = '<i class="' + valor + ' me-2"></i> ' + label;

            // Atualizar o valor escondido
            inputEscondido.value = valor;
        });
    });
</script>
<?php include '../../includes/footer.php'; ?>