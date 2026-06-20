// =======================================================
// ATUALIZAR CARDS DO TOPO - ÁREA PRIVADA
// Esta parte só funciona na página equipamentos.html
// =======================================================

function atualizarCardsEquipamentos() {
    const linhasAtuais = document.querySelectorAll("tbody tr");
    const cards = document.querySelectorAll(".private-card h3");

    // Se não existirem os cards, não faz nada
    if (cards.length < 4 || linhasAtuais.length === 0) return;

    let total = 0;
    let ativos = 0;
    let manutencao = 0;
    let criticos = 0;

    linhasAtuais.forEach(function (linha) {
        total++;

        const estado = linha.dataset.estado;
        const criticidade = linha.children[5].innerText.trim();

        if (estado === "Ativo") {
            ativos++;
        }

        if (estado === "Em manutenção") {
            manutencao++;
        }

        if (criticidade === "Crítica") {
            criticos++;
        }
    });

    cards[0].innerText = total;
    cards[1].innerText = ativos;
    cards[2].innerText = manutencao;
    cards[3].innerText = criticos;
}

// =======================================================
// INICIAR JAVASCRIPT QUANDO A PÁGINA CARREGAR
// =======================================================

document.addEventListener("DOMContentLoaded", function () {

    // ===================================================
    // LOGIN
    // ===================================================

    try {
        iniciarValidacaoLogin();
    } catch (e) {
        console.log("Erro login:", e);
    }


    // ===================================================
    // EQUIPAMENTOS
    // ===================================================

    try {
        iniciarFiltrosEquipamentos();
    } catch (e) {
        console.log("Erro filtros equipamentos:", e);
    }

    try {
        iniciarArquivarEquipamentos();
    } catch (e) {
        console.log("Erro arquivar equipamentos:", e);
    }

    try {
        atualizarCardsEquipamentos();
    } catch (e) {
        console.log("Erro atualizar cards:", e);
    }

    // =======================================================
    // EQUIPAMENTOS ASSOCIADOS AO FORNECEDOR
    // =======================================================

    const btnAssociar =
        document.getElementById("btnAssociarEquipamento");

    const inputEquipamento =
        document.getElementById("equipamentoAssociado");

    const listaEquipamentos =
        document.getElementById("listaEquipamentosAssociados");


    if (
        btnAssociar &&
        inputEquipamento &&
        listaEquipamentos
    ) {

        btnAssociar.addEventListener("click", function () {

            const codigo =
                inputEquipamento.value.trim();

            // Não adicionar vazio
            if (codigo === "") {
                return;
            }

            // Criar badge
            const badge =
                document.createElement("span");

            badge.className =
                "badge rounded-pill bg-light text-dark border px-3 py-2";

            badge.innerHTML = `

            ${codigo}

            <i class="fa-solid fa-xmark ms-2 removerEquipamento"
                style="cursor:pointer;"></i>

        `;

            // Adicionar badge
            listaEquipamentos.appendChild(badge);

            // Limpar input
            inputEquipamento.value = "";

            // Remover badge
            badge
                .querySelector(".removerEquipamento")
                .addEventListener("click", function () {

                    badge.remove();

                });

        });

    }

    // MOSTRAR / ESCONDER ACESSÓRIOS

    const temAcessorios = document.getElementById("temAcessorios");

    if (temAcessorios) {

        const secaoAcessorios = document.getElementById("secaoAcessorios");

        function atualizarAcessorios() {
            secaoAcessorios.style.display = (temAcessorios.value === "sim") ? "block" : "none";
        }

        // Estado inicial — espera um pouco para garantir que o browser já aplicou o valor selecionado
        setTimeout(atualizarAcessorios, 0);

        temAcessorios.addEventListener("change", atualizarAcessorios);

    }

    // MOSTRAR / ESCONDER CONSUMÍVEIS

    const temConsumiveis = document.getElementById("temConsumiveis");

    if (temConsumiveis) {

        const secaoConsumiveis = document.getElementById("secaoConsumiveis");

        function atualizarConsumiveis() {
            secaoConsumiveis.style.display = (temConsumiveis.value === "sim") ? "block" : "none";
        }

        setTimeout(atualizarConsumiveis, 0);

        temConsumiveis.addEventListener("change", atualizarConsumiveis);

    }

    // ADICIONAR ACESSÓRIO

    const btnAdicionarAcessorio = document.getElementById("btnAdicionarAcessorio");

    if (btnAdicionarAcessorio) {

        btnAdicionarAcessorio.addEventListener("click", function () {

            document.getElementById("listaAcessorios")
                .insertAdjacentHTML(
                    "beforeend",
                    `
        <div class="row g-4 mt-3 acessorio-item">

            <div class="col-md-6">
                <input type="text" class="form-control" name="acessorio_nome[]" placeholder="Nome do Acessório">
            </div>

            <div class="col-md-3">
                <input type="number" class="form-control" name="acessorio_quantidade[]" min="1" placeholder="Quantidade">
            </div>

            <div class="col-md-3">
                <select class="form-select" name="acessorio_estado[]">
                    ${opcoesEstados}
                </select>
            </div>

        </div>
        `
                );

        });

    }

    // ADICIONAR CONSUMÍVEL

    const btnAdicionarConsumivel = document.getElementById("btnAdicionarConsumivel");

    if (btnAdicionarConsumivel) {

        btnAdicionarConsumivel.addEventListener("click", function () {

            document.getElementById("listaConsumiveis")
                .insertAdjacentHTML(
                    "beforeend",
                    `
        <div class="row g-4 mt-3 consumivel-item">

            <div class="col-md-8">
                <input type="text" class="form-control" name="consumivel_nome[]" placeholder="Nome do Consumível">
            </div>

            <div class="col-md-4">
                <input type="number" class="form-control" name="consumivel_quantidade[]" min="1" placeholder="Quantidade">
            </div>

        </div>
        `
                );

        });

    }

    // ======================================
    // EDITAR EQUIPAMENTO - ACESSÓRIOS
    // ======================================

    const btnAdicionarAcessorioEditar =
        document.getElementById("btnAdicionarAcessorioEditar");

    if (btnAdicionarAcessorioEditar) {

        btnAdicionarAcessorioEditar.addEventListener("click", function () {

            document.getElementById("listaAcessoriosEditar")
                .insertAdjacentHTML(
                    "beforeend",

                    `
                <div class="border rounded-3 p-3 mb-3 mt-3">

                    <div class="row g-3">

                        <div class="col-md-5">

                            <label class="form-label fw-bold">
                                Nome do Acessório
                            </label>

                            <input type="text"
                                class="form-control">

                        </div>

                        <div class="col-md-3">

                            <label class="form-label fw-bold">
                                Quantidade
                            </label>

                            <input type="number"
                                class="form-control"
                                min="1">

                        </div>

                        <div class="col-md-3">

                            <label class="form-label fw-bold">
                                Estado
                            </label>

                            <select class="form-select">

                                <option>
                                    Ativo
                                </option>

                                <option>
                                    Inativo
                                </option>

                                <option>
                                    Avariado
                                </option>

                            </select>

                        </div>

                        <div class="col-md-1 d-flex align-items-end">

                            <button type="button"
                                class="btn btn-outline-danger">

                                <i class="fa-solid fa-trash"></i>

                            </button>

                        </div>

                    </div>

                </div>
                `
                );

        });

    }

    // ======================================
    // EDITAR EQUIPAMENTO - CONSUMÍVEIS
    // ======================================

    const btnAdicionarConsumivelEditar =
        document.getElementById("btnAdicionarConsumivelEditar");

    if (btnAdicionarConsumivelEditar) {

        btnAdicionarConsumivelEditar.addEventListener("click", function () {

            document.getElementById("listaConsumiveisEditar")
                .insertAdjacentHTML(
                    "beforeend",

                    `
                <div class="border rounded-3 p-3 mb-3 mt-3">

                    <div class="row g-3">

                        <div class="col-md-5">

                            <label class="form-label fw-bold">
                                Nome do Consumível
                            </label>

                            <input type="text"
                                class="form-control">

                        </div>

                        <div class="col-md-3">

                            <label class="form-label fw-bold">
                                Quantidade
                            </label>

                            <input type="number"
                                class="form-control"
                                min="1">

                        </div>

                        <div class="col-md-3">

                            <label class="form-label fw-bold">
                                Stock Mínimo
                            </label>

                            <input type="number"
                                class="form-control"
                                min="0">

                        </div>

                        <div class="col-md-1 d-flex align-items-end">

                            <button type="button"
                                class="btn btn-outline-danger">

                                <i class="fa-solid fa-trash"></i>

                            </button>

                        </div>

                    </div>

                </div>
                `
                );

        });

    }


    // GARANTIAS (Inserir)

    const temGarantia = document.getElementById("temGarantia");

    if (temGarantia) {

        const cardGarantia = document.getElementById("cardGarantia");

        function atualizarGarantia() {
            cardGarantia.style.display = (temGarantia.value === "sim") ? "block" : "none";
        }

        setTimeout(atualizarGarantia, 0);

        temGarantia.addEventListener("change", atualizarGarantia);

    }


    // CONTRATOS (Inserir)

    const temContrato = document.getElementById("temContrato");

    if (temContrato) {

        const secaoContrato = document.getElementById("secaoContrato");
        const cardContrato = document.getElementById("cardContrato");

        function atualizarContrato() {
            const visivel = (temContrato.value === "sim") ? "block" : "none";
            secaoContrato.style.display = visivel;
            cardContrato.style.display = visivel;
        }

        setTimeout(atualizarContrato, 0);

        temContrato.addEventListener("change", atualizarContrato);

    }
    // ======================================
    // EDITAR EQUIPAMENTO - GARANTIAS
    // ======================================

    const temGarantiaEditar =
        document.getElementById("temGarantiaEditar");

    if (temGarantiaEditar) {

        const cardGarantia =
            document.getElementById("cardGarantia");

        // Estado inicial da página

        if (temGarantiaEditar.value === "sim") {

            cardGarantia.style.display = "block";

        } else {

            cardGarantia.style.display = "none";

        }

        // Quando altera

        temGarantiaEditar.addEventListener("change", function () {

            if (this.value === "sim") {

                cardGarantia.style.display = "block";

            } else {

                cardGarantia.style.display = "none";

            }

        });

    }


    // ======================================
    // EDITAR EQUIPAMENTO - CONTRATOS
    // ======================================

    const temContratoEditar =
        document.getElementById("temContratoEditar");

    if (temContratoEditar) {

        const secaoContratoEditar =
            document.getElementById("secaoContratoEditar");

        const cardContrato =
            document.getElementById("cardContrato");

        // Estado inicial

        if (temContratoEditar.value === "sim") {

            secaoContratoEditar.style.display = "block";
            cardContrato.style.display = "block";

        } else {

            secaoContratoEditar.style.display = "none";
            cardContrato.style.display = "none";

        }

        // Quando altera

        temContratoEditar.addEventListener("change", function () {

            if (this.value === "sim") {

                secaoContratoEditar.style.display = "block";
                cardContrato.style.display = "block";

            } else {

                secaoContratoEditar.style.display = "none";
                cardContrato.style.display = "none";

            }

        });

    }
    // ======================================
    // INSERIR EQUIPAMENTO - DOCUMENTAÇÃO ADICIONAL
    // ======================================

    const temDocumentacaoAdicional =
        document.getElementById("temDocumentacaoAdicional");

    if (temDocumentacaoAdicional) {

        const secao = document.getElementById("secaoDocumentacaoAdicional");

        function atualizarDocumentacaoAdicional() {
            secao.style.display = (temDocumentacaoAdicional.value === "sim") ? "block" : "none";
        }

        setTimeout(atualizarDocumentacaoAdicional, 0);

        temDocumentacaoAdicional.addEventListener("change", atualizarDocumentacaoAdicional);

    }

    const btnAdicionarDocumentoAdicional =
        document.getElementById("btnAdicionarDocumentoAdicional");

    if (btnAdicionarDocumentoAdicional) {

        btnAdicionarDocumentoAdicional.addEventListener("click", function () {

            document.getElementById("listaDocumentacaoAdicional")
                .insertAdjacentHTML(
                    "beforeend",

                    `
                <div class="border rounded-3 p-3 mt-3 documento-adicional-item">

                    <h6 class="fw-bold">
                        Documento Adicional
                    </h6>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">
                                Nome do Documento
                            </label>

                            <input type="text"
                                class="form-control"
                                name="nome_documento_adicional[]">

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Ficheiro PDF
                            </label>

                            <input type="file"
                                class="form-control"
                                name="ficheiro_documento_adicional[]"
                                accept="application/pdf">

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Data do Documento
                            </label>

                            <input type="date"
                                class="form-control"
                                name="data_documento_adicional[]">

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                Data de Validade
                            </label>

                            <input type="date"
                                class="form-control"
                                name="validade_documento_adicional[]">

                        </div>

                    </div>

                </div>
                `
                );

        });

    }


    // ======================================
    // EDITAR EQUIPAMENTO - DOCUMENTAÇÃO ADICIONAL
    // ======================================

    const temDocumentacaoAdicionalEditar =
        document.getElementById("temDocumentacaoAdicionalEditar");

    if (temDocumentacaoAdicionalEditar) {

        const secaoDocumentacaoAdicionalEditar =
            document.getElementById("secaoDocumentacaoAdicionalEditar");

        if (temDocumentacaoAdicionalEditar.value === "sim") {

            secaoDocumentacaoAdicionalEditar.style.display = "block";

        } else {

            secaoDocumentacaoAdicionalEditar.style.display = "none";

        }

        temDocumentacaoAdicionalEditar.addEventListener("change", function () {

            if (this.value === "sim") {

                secaoDocumentacaoAdicionalEditar.style.display = "block";

            } else {

                secaoDocumentacaoAdicionalEditar.style.display = "none";

            }

        });

    }


    // ======================================
    // EDITAR EQUIPAMENTO - ADICIONAR DOCUMENTO
    // ======================================

    const btnAdicionarDocumentoAdicionalEditar =
        document.getElementById("btnAdicionarDocumentoAdicionalEditar");

    if (btnAdicionarDocumentoAdicionalEditar) {

        btnAdicionarDocumentoAdicionalEditar.addEventListener("click", function () {

            document.getElementById("listaDocumentacaoAdicionalEditar")
                .insertAdjacentHTML(
                    "beforeend",

                    `
                <div class="border rounded-3 p-3 mt-3 documento-adicional-item">

                    <h6 class="fw-bold">
                        Documento Adicional
                    </h6>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">
                                Nome do Documento
                            </label>

                            <input type="text"
                                class="form-control">

                        </div>

                        <div class="col-md-6">

                            <label class="form-label">
                                PDF
                            </label>

                            <input type="file"
                                class="form-control"
                                accept="application/pdf">

                        </div>

                        <div class="col-md-3">

                            <label class="form-label">
                                Data do Documento
                            </label>

                            <input type="date"
                                class="form-control">

                        </div>

                        <div class="col-md-3">

                            <label class="form-label">
                                Data de Validade
                            </label>

                            <input type="date"
                                class="form-control">

                        </div>

                    </div>

                </div>
                `
                );

        });

    }

    const popoverTriggerList =
        document.querySelectorAll('[data-bs-toggle="popover"]');

    [...popoverTriggerList].forEach(
        popoverTriggerEl =>
            new bootstrap.Popover(popoverTriggerEl)
    );
});

// -------------------------------------------------------
// FORNECEDORES ASSOCIADOS — Adicionar / Remover linhas
// -------------------------------------------------------

document.addEventListener('DOMContentLoaded', function () {

    const btnAdicionar = document.getElementById('btnAdicionarFornecedor');
    const lista = document.getElementById('lista-fornecedores');

    if (!btnAdicionar || !lista) return;

    const tiposRelacao = [
        'Fabricante',
        'Distribuidor',
        'Assistência Técnica',
        'Consumíveis / Acessórios'
    ];

    btnAdicionar.addEventListener('click', function () {

        // Construir opções de fornecedores
        let opcoesForncedor = '<option value="">Selecionar fornecedor</option>';
        fornecedoresDisponiveis.forEach(function (f) {
            opcoesForncedor += `<option value="${f.id}">${f.nome}</option>`;
        });

        // Construir opções de tipo
        let opcoesTipo = '<option value="">Tipo de relação</option>';
        tiposRelacao.forEach(function (t) {
            opcoesTipo += `<option value="${t}">${t}</option>`;
        });

        const novaLinha = document.createElement('div');
        novaLinha.className = 'row g-2 mb-2 linha-fornecedor';
        novaLinha.innerHTML = `
            <div class="col-md-7">
                <select class="form-select" name="id_fornecedor[]">
                    ${opcoesForncedor}
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" name="tipo_relacao[]">
                    ${opcoesTipo}
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-center">
                <button type="button" class="btn btn-sm btn-outline-danger remover-fornecedor">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;

        lista.appendChild(novaLinha);
    });

    // Remover linha
    lista.addEventListener('click', function (e) {
        const btn = e.target.closest('.remover-fornecedor');
        if (btn) {
            btn.closest('.linha-fornecedor').remove();
        }
    });

});

// ======================================
// NAVEGAÇÃO ENTRE ABAS - SEGUINTE / ANTERIOR
// ======================================

const ordemAbas = [
    "dadosGeraisNovo",
    "aquisicaoNovo",
    "acessoriosNovo",
    "localizacaoNovo",
    "fornecedorNovo",
    "garantiasNovo",
    "documentacaoNovo",
    "observacoesNovo"
];

function irParaAba(idAba) {
    const botaoAba = document.querySelector(`[data-bs-target="#${idAba}"]`);
    if (botaoAba) {
        const tab = new bootstrap.Tab(botaoAba);
        tab.show();
    }
}

function abaAtual() {
    const ativa = document.querySelector(".tab-pane.active");
    return ativa ? ativa.id : null;
}

// Botões "Anterior"
document.querySelectorAll(".btn-anterior").forEach(function (botao) {
    botao.addEventListener("click", function () {
        const atual = abaAtual();
        const index = ordemAbas.indexOf(atual);

        if (index > 0) {
            irParaAba(ordemAbas[index - 1]);
            window.scrollTo({ top: 0, behavior: "smooth" });
        }
    });
});

// ======================================
// VALIDAÇÃO POR ABA (antes de avançar)
// ======================================

function mostrarErrosAba(idDivAlertas, mensagens) {
    const div = document.getElementById(idDivAlertas);
    if (!div) return;

    const lista = div.querySelector("ul");
    lista.innerHTML = "";

    mensagens.forEach(function (msg) {
        const li = document.createElement("li");
        li.textContent = msg;
        lista.appendChild(li);
    });

    div.style.display = mensagens.length > 0 ? "block" : "none";
}

// Desativa todas as abas exceto a atual e as já validadas
function atualizarEstadoAbas() {

    if (typeof modoEdicao !== 'undefined' && modoEdicao === true) {
        // No modo edição, todas as abas ficam sempre disponíveis
        document.querySelectorAll('.conteudos-tabs .nav-link').forEach(function (botaoTab) {
            botaoTab.classList.remove('disabled');
        });
        return;
    }
    const atual = abaAtual();
    const indexAtual = ordemAbas.indexOf(atual);

    ordemAbas.forEach(function (idAba, index) {
        const botaoTab = document.querySelector(`[data-bs-target="#${idAba}"]`);
        if (!botaoTab) return;

        if (index > indexAtual) {
            botaoTab.classList.add("disabled");
        } else {
            botaoTab.classList.remove("disabled");
        }
    });
}

// Chamar no carregamento da página
atualizarEstadoAbas();

// ======================================
// AO CARREGAR: ABRIR A PRIMEIRA ABA COM ERROS PHP
// ======================================

if (typeof errosPorAba !== 'undefined') {
    for (const idAba of ordemAbas) {
        if (errosPorAba[idAba]) {
            irParaAba(idAba);
            atualizarEstadoAbas();
            break;
        }
    }
}

// ======================================
// FLATPICKR
// ======================================

document.querySelectorAll('input[type="date"]').forEach(function (campo) {
    flatpickr(campo, {
        dateFormat: "Y-m-d"
    });
});

// ======================================
// REPOPULAR FORNECEDORES GUARDADOS NA SESSÃO
// ======================================

if (typeof fornecedoresGuardados !== 'undefined' && fornecedoresGuardados.length > 0) {

    const lista = document.getElementById('lista-fornecedores');

    if (lista) {

        lista.innerHTML = '';

        const tiposRelacao = [
            'Fabricante',
            'Distribuidor',
            'Assistência Técnica',
            'Consumíveis / Acessórios'
        ];

        fornecedoresGuardados.forEach(function (forn) {

            let opcoesFornecedor = '<option value="">Selecionar fornecedor</option>';

            fornecedoresDisponiveis.forEach(function (f) {

                const sel = (f.id == forn.id_fornecedor)
                    ? 'selected'
                    : '';

                opcoesFornecedor += `
                    <option value="${f.id}" ${sel}>
                        ${f.nome}
                    </option>
                `;
            });

            let opcoesTipo = '<option value="">Tipo de relação</option>';

            tiposRelacao.forEach(function (t) {

                const sel = (t === forn.tipo_relacao)
                    ? 'selected'
                    : '';

                opcoesTipo += `
                    <option value="${t}" ${sel}>
                        ${t}
                    </option>
                `;
            });

            const novaLinha = document.createElement('div');

            novaLinha.className = 'row g-2 mb-2 linha-fornecedor';

            novaLinha.innerHTML = `
                <div class="col-md-7">
                    <select class="form-select" name="id_fornecedor[]">
                        ${opcoesFornecedor}
                    </select>
                </div>

                <div class="col-md-5">
                    <select class="form-select" name="tipo_relacao[]">
                        ${opcoesTipo}
                    </select>
                </div>
            `;

            lista.appendChild(novaLinha);
        });
    }
}