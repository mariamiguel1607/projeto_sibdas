// =======================================================
// GESTÃO DE SERVIÇOS PÚBLICOS - SIMULAÇÃO FRONTEND
// Esta parte permite editar os serviços da página pública
// através da área privada, usando localStorage.
// Mais tarde, esta lógica será substituída pela base de dados.
// =======================================================


// -------------------------------------------------------
// 1. Serviços iniciais
// -------------------------------------------------------

const servicosIniciais = [
    {
        titulo: "Gestão de Inventário",
        descricao: "Registo e organização de equipamentos médicos por categoria, estado, localização e criticidade.",
        icone: "fa-solid fa-clipboard-list",
        estado: "Ativo",
        ordem: 1
    },
    {
        titulo: "Gestão Documental",
        descricao: "Associação de manuais, certificados, contratos, relatórios técnicos e outros documentos aos equipamentos.",
        icone: "fa-solid fa-folder-open",
        estado: "Ativo",
        ordem: 2
    },
    {
        titulo: "Dashboard e Consulta",
        descricao: "Visualização de indicadores, pesquisa e filtragem de equipamentos para apoiar a gestão hospitalar.",
        icone: "fa-solid fa-chart-simple",
        estado: "Ativo",
        ordem: 3
    }
];


// -------------------------------------------------------
// 2. Variável temporária da página privada
// -------------------------------------------------------

let servicosTemporarios = [];


// -------------------------------------------------------
// 3. Criar serviços iniciais no localStorage
// -------------------------------------------------------

if (!localStorage.getItem("servicosPublicos")) {
    localStorage.setItem("servicosPublicos", JSON.stringify(servicosIniciais));
}


// -------------------------------------------------------
// 4. Funções para obter e guardar serviços
// -------------------------------------------------------

function obterServicos() {
    return JSON.parse(localStorage.getItem("servicosPublicos")) || [];
}

function guardarServicos(servicos) {
    localStorage.setItem("servicosPublicos", JSON.stringify(servicos));
}


// -------------------------------------------------------
// 5. Mostrar serviços na página pública
// -------------------------------------------------------

function mostrarServicosPublicos() {
    const container = document.getElementById("servicosPublicos");

    // Se não existir este id, significa que não estamos na página pública
    if (!container) return;

    const servicos = obterServicos()
        .filter(function (servico) {
            return servico.estado === "Ativo";
        })
        .sort(function (a, b) {
            return a.ordem - b.ordem;
        });

    container.innerHTML = "";

    servicos.forEach(function (servico) {
        container.innerHTML += `
            <div class="col-md-4 mb-4">
                <div class="service-card h-100 text-center">
                    <div class="service-icon mb-3">
                        <i class="${servico.icone}"></i>
                    </div>

                    <h3>${servico.titulo}</h3>

                    <p>${servico.descricao}</p>
                </div>
            </div>
        `;
    });
}


// -------------------------------------------------------
// 6. Criar card editável na página privada
// -------------------------------------------------------

function criarCardServico(servico, index) {
    return `
        <div class="border rounded-4 p-4 mb-4 bg-light servico-card" data-index="${index}">

            <div class="d-flex justify-content-between align-items-start mb-3">
                <h5 class="fw-bold mb-0">Serviço ${index + 1}</h5>

                <div class="d-flex align-items-center gap-2">
                    <span class="badge ${servico.estado === "Ativo" ? "text-bg-success" : "text-bg-secondary"}">
                        ${servico.estado}
                    </span>

                    <button type="button" class="btn btn-outline-danger btn-sm btn-eliminar-servico" data-index="${index}">
                        <i class="fa-solid fa-trash me-1"></i>
                        Eliminar
                    </button>
                </div>
            </div>

            <div class="row g-4">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Título do serviço</label>
                    <input 
                        type="text" 
                        class="form-control servico-titulo" 
                        value="${servico.titulo || ""}" 
                        data-index="${index}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ícone</label>
                    <select class="form-select servico-icone" data-index="${index}">
                        <option value="" ${servico.icone === "" ? "selected" : ""}>Selecionar ícone</option>
                        <option value="fa-solid fa-clipboard-list" ${servico.icone === "fa-solid fa-clipboard-list" ? "selected" : ""}>Clipboard List</option>
                        <option value="fa-solid fa-folder-open" ${servico.icone === "fa-solid fa-folder-open" ? "selected" : ""}>Folder Open</option>
                        <option value="fa-solid fa-chart-simple" ${servico.icone === "fa-solid fa-chart-simple" ? "selected" : ""}>Chart Simple</option>
                        <option value="fa-solid fa-briefcase-medical" ${servico.icone === "fa-solid fa-briefcase-medical" ? "selected" : ""}>Briefcase Medical</option>
                        <option value="fa-solid fa-screwdriver-wrench" ${servico.icone === "fa-solid fa-screwdriver-wrench" ? "selected" : ""}>Screwdriver Wrench</option>
                        <option value="fa-solid fa-file-lines" ${servico.icone === "fa-solid fa-file-lines" ? "selected" : ""}>File Lines</option>
                        <option value="fa-solid fa-hospital" ${servico.icone === "fa-solid fa-hospital" ? "selected" : ""}>Hospital</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Descrição</label>
                    <textarea 
                        class="form-control servico-descricao" 
                        rows="3" 
                        data-index="${index}">${servico.descricao || ""}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Estado</label>
                    <select class="form-select servico-estado" data-index="${index}">
                        <option value="Ativo" ${servico.estado === "Ativo" ? "selected" : ""}>Ativo</option>
                        <option value="Inativo" ${servico.estado === "Inativo" ? "selected" : ""}>Inativo</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ordem de apresentação</label>
                    <input 
                        type="number" 
                        class="form-control servico-ordem" 
                        value="${servico.ordem || index + 1}" 
                        data-index="${index}">
                </div>

            </div>
        </div>
    `;
}


// -------------------------------------------------------
// 7. Mostrar serviços na página privada
// -------------------------------------------------------

function mostrarServicosGestao() {
    const listaGestao = document.getElementById("listaServicosGestao");

    // Se não existir este id, significa que não estamos na página privada de serviços
    if (!listaGestao) return;

    listaGestao.innerHTML = "";

    servicosTemporarios.forEach(function (servico, index) {
        listaGestao.innerHTML += criarCardServico(servico, index);
    });

    ativarBotoesEliminarServico();
}


// -------------------------------------------------------
// 8. Carregar serviços para a página privada
// -------------------------------------------------------

function carregarServicosParaGestao() {
    const listaGestao = document.getElementById("listaServicosGestao");

    if (!listaGestao) return;

    servicosTemporarios = obterServicos().sort(function (a, b) {
        return a.ordem - b.ordem;
    });

    mostrarServicosGestao();
}


// -------------------------------------------------------
// 9. Adicionar novo card editável
// -------------------------------------------------------

function adicionarCardServico() {
    const novoServico = {
        titulo: "",
        descricao: "",
        icone: "",
        estado: "Ativo",
        ordem: servicosTemporarios.length + 1
    };

    servicosTemporarios.push(novoServico);

    mostrarServicosGestao();

    const cards = document.querySelectorAll(".servico-card");
    const ultimoCard = cards[cards.length - 1];

    if (ultimoCard) {
        ultimoCard.scrollIntoView({
            behavior: "smooth",
            block: "center"
        });
    }
}


// -------------------------------------------------------
// 10. Eliminar serviço
// -------------------------------------------------------

function eliminarServico(index) {
    const confirmar = confirm("Tem a certeza que pretende eliminar este serviço?");

    if (!confirmar) return;

    servicosTemporarios.splice(index, 1);

    servicosTemporarios.forEach(function (servico, i) {
        servico.ordem = i + 1;
    });

    mostrarServicosGestao();
}


// -------------------------------------------------------
// 11. Ativar botões eliminar
// -------------------------------------------------------

function ativarBotoesEliminarServico() {
    const botoesEliminar = document.querySelectorAll(".btn-eliminar-servico");

    botoesEliminar.forEach(function (botao) {
        botao.addEventListener("click", function () {
            const index = Number(botao.dataset.index);
            eliminarServico(index);
        });
    });
}


// -------------------------------------------------------
// 12. Guardar alterações
// -------------------------------------------------------

function guardarAlteracoesServicos() {
    const listaGestao = document.getElementById("listaServicosGestao");

    if (!listaGestao) return;

    const titulos = document.querySelectorAll(".servico-titulo");
    const descricoes = document.querySelectorAll(".servico-descricao");
    const icones = document.querySelectorAll(".servico-icone");
    const estados = document.querySelectorAll(".servico-estado");
    const ordens = document.querySelectorAll(".servico-ordem");

    let servicosAtualizados = [];

    for (let i = 0; i < titulos.length; i++) {
        const titulo = titulos[i].value.trim();
        const descricao = descricoes[i].value.trim();
        const icone = icones[i].value;
        const estado = estados[i].value;
        const ordem = Number(ordens[i].value) || i + 1;

        if (titulo === "" || descricao === "" || icone === "") {
            alert("Preenche o título, a descrição e o ícone de todos os serviços antes de guardar.");
            return;
        }

        servicosAtualizados.push({
            titulo: titulo,
            descricao: descricao,
            icone: icone,
            estado: estado,
            ordem: ordem
        });
    }

    servicosAtualizados.sort(function (a, b) {
        return a.ordem - b.ordem;
    });

    guardarServicos(servicosAtualizados);

    servicosTemporarios = servicosAtualizados;

    alert("Alterações guardadas com sucesso.");

    mostrarServicosGestao();
    mostrarServicosPublicos();
}


// -------------------------------------------------------
// 13. Inicialização da gestão de serviços
// -------------------------------------------------------

document.addEventListener("DOMContentLoaded", function () {

    // Mostrar serviços na página pública, se estivermos nela
    mostrarServicosPublicos();

    // Carregar serviços na página privada de gestão, se estivermos nela
    carregarServicosParaGestao();

    const botaoAdicionar = document.getElementById("btnAdicionarServico");

    if (botaoAdicionar) {
        botaoAdicionar.addEventListener("click", adicionarCardServico);
    }

    const botaoGuardarServicos = document.getElementById("btnGuardarServicos");

    if (botaoGuardarServicos) {
        botaoGuardarServicos.addEventListener("click", guardarAlteracoesServicos);
    }

});

// =======================================================
// GESTÃO DA SECÇÃO SOBRE NÓS - SIMULAÇÃO FRONTEND
// Esta parte permite editar a secção "Sobre Nós" da página pública
// através da área privada, usando localStorage.
// Mais tarde, esta lógica será substituída pela base de dados.
// =======================================================


// -------------------------------------------------------
// 1. Conteúdo inicial da secção Sobre Nós
// -------------------------------------------------------

const sobreInicial = {
    titulo: "Sobre Nós",

    texto: `A TechMed Solutions nasceu com o objetivo de apoiar instituições de saúde
na organização e digitalização dos seus processos internos, promovendo uma
gestão mais eficiente, segura e acessível da informação associada aos
equipamentos médicos.

Através de uma plataforma simples e intuitiva, procuramos reduzir a
dependência de registos manuais, facilitar o trabalho dos profissionais
responsáveis pela gestão hospitalar e contribuir para uma maior qualidade
e segurança nos serviços de saúde.`,

    bloco1Titulo: "Missão",
    bloco1Texto: "Apoiar hospitais na transição para processos digitais mais organizados, seguros e eficientes.",

    bloco2Titulo: "Inovação",
    bloco2Texto: "Desenvolver soluções tecnológicas simples, intuitivas e adaptadas às necessidades do contexto hospitalar.",

    bloco3Titulo: "Impacto",
    bloco3Texto: "Contribuir para uma gestão hospitalar mais eficaz e para melhores condições de apoio aos profissionais de saúde.",

    estado: "Ativo"
};


// -------------------------------------------------------
// 2. Criar conteúdo inicial no localStorage
// -------------------------------------------------------

if (!localStorage.getItem("sobrePublico")) {
    localStorage.setItem("sobrePublico", JSON.stringify(sobreInicial));
}


// -------------------------------------------------------
// 3. Obter e guardar conteúdo Sobre Nós
// -------------------------------------------------------

function obterSobre() {
    return JSON.parse(localStorage.getItem("sobrePublico")) || sobreInicial;
}

function guardarSobre(sobre) {
    localStorage.setItem("sobrePublico", JSON.stringify(sobre));
}


// -------------------------------------------------------
// 4. Mostrar Sobre Nós na página pública
// -------------------------------------------------------

function mostrarSobrePublico() {
    const secaoSobre = document.getElementById("sobre");

    const titulo = document.getElementById("sobrePublicoTitulo");
    const texto = document.getElementById("sobrePublicoTexto");

    const bloco1Titulo = document.getElementById("sobrePublicoBloco1Titulo");
    const bloco1Texto = document.getElementById("sobrePublicoBloco1Texto");

    const bloco2Titulo = document.getElementById("sobrePublicoBloco2Titulo");
    const bloco2Texto = document.getElementById("sobrePublicoBloco2Texto");

    const bloco3Titulo = document.getElementById("sobrePublicoBloco3Titulo");
    const bloco3Texto = document.getElementById("sobrePublicoBloco3Texto");

    // Se estes elementos não existirem, significa que não estamos na página pública
    if (
        !secaoSobre ||
        !titulo ||
        !texto ||
        !bloco1Titulo ||
        !bloco1Texto ||
        !bloco2Titulo ||
        !bloco2Texto ||
        !bloco3Titulo ||
        !bloco3Texto
    ) {
        return;
    }

    const sobre = obterSobre();

    // Se estiver inativo, esconde a secção completa
    if (sobre.estado === "Inativo") {
        secaoSobre.style.display = "none";
        return;
    }

    secaoSobre.style.display = "";

    titulo.innerText = sobre.titulo;
    texto.innerHTML = sobre.texto
    .trim()
    .split(/\n\s*\n/)
    .map(paragrafo => `<p>${paragrafo.replace(/\s+/g, " ")}</p>`)
    .join("");

    bloco1Titulo.innerText = sobre.bloco1Titulo;
    bloco1Texto.innerText = sobre.bloco1Texto;

    bloco2Titulo.innerText = sobre.bloco2Titulo;
    bloco2Texto.innerText = sobre.bloco2Texto;

    bloco3Titulo.innerText = sobre.bloco3Titulo;
    bloco3Texto.innerText = sobre.bloco3Texto;
}


// -------------------------------------------------------
// 5. Carregar dados na página privada
// -------------------------------------------------------

function carregarSobreGestao() {
    const titulo = document.getElementById("sobreTitulo");
    const texto = document.getElementById("sobreTexto");

    const bloco1Titulo = document.getElementById("sobreBloco1Titulo");
    const bloco1Texto = document.getElementById("sobreBloco1Texto");

    const bloco2Titulo = document.getElementById("sobreBloco2Titulo");
    const bloco2Texto = document.getElementById("sobreBloco2Texto");

    const bloco3Titulo = document.getElementById("sobreBloco3Titulo");
    const bloco3Texto = document.getElementById("sobreBloco3Texto");

    const estado = document.getElementById("sobreEstado");

    // Se estes elementos não existirem, significa que não estamos na página de gestão do Sobre Nós
    if (
        !titulo ||
        !texto ||
        !bloco1Titulo ||
        !bloco1Texto ||
        !bloco2Titulo ||
        !bloco2Texto ||
        !bloco3Titulo ||
        !bloco3Texto ||
        !estado
    ) {
        return;
    }

    const sobre = obterSobre();

    titulo.value = sobre.titulo;
    texto.value = sobre.texto;

    bloco1Titulo.value = sobre.bloco1Titulo;
    bloco1Texto.value = sobre.bloco1Texto;

    bloco2Titulo.value = sobre.bloco2Titulo;
    bloco2Texto.value = sobre.bloco2Texto;

    bloco3Titulo.value = sobre.bloco3Titulo;
    bloco3Texto.value = sobre.bloco3Texto;

    estado.value = sobre.estado;
}


// -------------------------------------------------------
// 6. Guardar alterações da página privada
// -------------------------------------------------------

function guardarAlteracoesSobre() {
    const titulo = document.getElementById("sobreTitulo");
    const texto = document.getElementById("sobreTexto");

    const bloco1Titulo = document.getElementById("sobreBloco1Titulo");
    const bloco1Texto = document.getElementById("sobreBloco1Texto");

    const bloco2Titulo = document.getElementById("sobreBloco2Titulo");
    const bloco2Texto = document.getElementById("sobreBloco2Texto");

    const bloco3Titulo = document.getElementById("sobreBloco3Titulo");
    const bloco3Texto = document.getElementById("sobreBloco3Texto");

    const estado = document.getElementById("sobreEstado");

    if (
        !titulo ||
        !texto ||
        !bloco1Titulo ||
        !bloco1Texto ||
        !bloco2Titulo ||
        !bloco2Texto ||
        !bloco3Titulo ||
        !bloco3Texto ||
        !estado
    ) {
        return;
    }

    if (
        titulo.value.trim() === "" ||
        texto.value.trim() === "" ||
        bloco1Titulo.value.trim() === "" ||
        bloco1Texto.value.trim() === "" ||
        bloco2Titulo.value.trim() === "" ||
        bloco2Texto.value.trim() === "" ||
        bloco3Titulo.value.trim() === "" ||
        bloco3Texto.value.trim() === ""
    ) {
        alert("Preenche todos os campos antes de guardar.");
        return;
    }

    const sobreAtualizado = {
        titulo: titulo.value.trim(),
        texto: texto.value.trim(),

        bloco1Titulo: bloco1Titulo.value.trim(),
        bloco1Texto: bloco1Texto.value.trim(),

        bloco2Titulo: bloco2Titulo.value.trim(),
        bloco2Texto: bloco2Texto.value.trim(),

        bloco3Titulo: bloco3Titulo.value.trim(),
        bloco3Texto: bloco3Texto.value.trim(),

        estado: estado.value
    };

    guardarSobre(sobreAtualizado);

    alert("Alterações guardadas com sucesso.");

    mostrarSobrePublico();
}

// =======================================================
// VALIDAÇÃO DO LOGIN - SIMULAÇÃO FRONTEND
// Esta parte valida o formulário de login antes de entrar
// na área reservada. Mais tarde será substituída por PHP.
// =======================================================

function iniciarValidacaoLogin() {
    const loginForm = document.getElementById("loginForm");
    const utilizador = document.getElementById("utilizador");
    const password = document.getElementById("password");
    const loginErro = document.getElementById("loginErro");

    // Se estes elementos não existirem, significa que não estamos na página de login
    if (!loginForm || !utilizador || !password || !loginErro) return;

    loginForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const nomeUtilizador = utilizador.value.trim();
        const palavraPasse = password.value.trim();

        const temLetra = /[A-Za-zÀ-ÿ]/.test(palavraPasse);
        const temNumero = /[0-9]/.test(palavraPasse);
        const temCaracterEspecial = /[^A-Za-zÀ-ÿ0-9]/.test(palavraPasse);

        if (nomeUtilizador === "" || palavraPasse === "") {
            loginErro.textContent = "Preencha o nome de utilizador e a palavra-passe.";
            loginErro.classList.remove("d-none");
            return;
        }

        if (!temLetra || !temNumero || !temCaracterEspecial) {
            loginErro.textContent = "A palavra-passe deve conter letras, números e caracteres especiais.";
            loginErro.classList.remove("d-none");
            return;
        }

        loginErro.classList.add("d-none");

        // Redireciona para a dashboard
        window.location.href = "../../private/views/dashboard/dashboard.html";
    });
}
// =======================================================
// FILTROS DA TABELA DE EQUIPAMENTOS - ÁREA PRIVADA
// Esta parte só funciona na página equipamentos.html
// =======================================================

function iniciarFiltrosEquipamentos() {
    const inputPesquisa = document.getElementById("pesquisa");
    const selectCategoria = document.getElementById("categoria");
    const selectEstado = document.getElementById("estado");
    const botaoFiltrar = document.querySelector(".filter-card button");
    const linhasEquipamentos = document.querySelectorAll("tbody tr");

    // Se não existirem estes elementos, significa que não estamos na página equipamentos.html
    if (!inputPesquisa || !selectCategoria || !selectEstado || !botaoFiltrar || linhasEquipamentos.length === 0) {
        return;
    }

    function filtrarEquipamentos() {
        const textoPesquisa = inputPesquisa.value.toLowerCase();
        const categoriaSelecionada = selectCategoria.value;
        const estadoSelecionado = selectEstado.value;

        linhasEquipamentos.forEach(function (linha) {
            const textoLinha = linha.innerText.toLowerCase();
            const categoriaLinha = linha.dataset.categoria;
            const estadoLinha = linha.dataset.estado;

            const correspondePesquisa = textoLinha.includes(textoPesquisa);

            const correspondeCategoria =
                categoriaSelecionada === "Todas" || categoriaLinha === categoriaSelecionada;

            const correspondeEstado =
                estadoSelecionado === "Todos" || estadoLinha === estadoSelecionado;

            if (correspondePesquisa && correspondeCategoria && correspondeEstado) {
                linha.style.display = "";
            } else {
                linha.style.display = "none";
            }
        });
    }

    botaoFiltrar.addEventListener("click", filtrarEquipamentos);

    inputPesquisa.addEventListener("keyup", filtrarEquipamentos);
    selectCategoria.addEventListener("change", filtrarEquipamentos);
    selectEstado.addEventListener("change", filtrarEquipamentos);
}

// =======================================================
// ARQUIVAR EQUIPAMENTO - ÁREA PRIVADA
// Esta parte só funciona na página equipamentos.html
// =======================================================

function iniciarArquivarEquipamentos() {
    const botoesArquivar = document.querySelectorAll(".btn-arquivar");

    // Se não existirem botões de arquivar, não faz nada
    if (botoesArquivar.length === 0) return;

    botoesArquivar.forEach(function (botao) {
        botao.addEventListener("click", function () {
            const linha = botao.closest("tr");

            const confirmar = confirm("Tem a certeza que pretende arquivar este equipamento?");

            if (confirmar) {
                linha.remove();
                atualizarCardsEquipamentos();
            }
        });
    });
}

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

    // ===============================
    // PÁGINA PÚBLICA
    // ===============================
    mostrarServicosPublicos();
    mostrarSobrePublico();


    // ===============================
    // GESTÃO DE SERVIÇOS
    // ===============================
    carregarServicosParaGestao();

    const botaoAdicionarServico = document.getElementById("btnAdicionarServico");

    if (botaoAdicionarServico) {
        botaoAdicionarServico.addEventListener("click", adicionarCardServico);
    }

    const botaoGuardarServicos = document.getElementById("btnGuardarServicos");

    if (botaoGuardarServicos) {
        botaoGuardarServicos.addEventListener("click", guardarAlteracoesServicos);
    }


    // ===============================
    // GESTÃO DO SOBRE NÓS
    // ===============================
    carregarSobreGestao();

    const botaoGuardarSobre = document.getElementById("btnGuardarSobre");

    if (botaoGuardarSobre) {
        botaoGuardarSobre.addEventListener("click", guardarAlteracoesSobre);
    }
     // ===============================
    // Validação do Login
    // ===============================

    iniciarValidacaoLogin();

    // ===============================
    // EQUIPAMENTOS
    // ===============================
    iniciarFiltrosEquipamentos();
    iniciarArquivarEquipamentos();
    atualizarCardsEquipamentos();

});