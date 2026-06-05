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

// =======================================================
// GESTÃO DA SECÇÃO INÍCIO
// =======================================================

const inicioInicial = {

    titulo: "Gestão eficiente de equipamentos médicos",

    descricao:
        "A TechMed Solutions ajuda hospitais a gerir o inventário de equipamentos médicos de forma centralizada, segura e eficiente.",

    botao:
        "Conhecer Serviços",

    imagem:
        "../assets/images/imagem_inicio.png"
};


// Criar conteúdo inicial
if (!localStorage.getItem("inicioPublico")) {

    localStorage.setItem(
        "inicioPublico",
        JSON.stringify(inicioInicial)
    );
}


// Obter
function obterInicio() {

    return JSON.parse(
        localStorage.getItem("inicioPublico")
    ) || inicioInicial;
}


// Guardar
function guardarInicio(inicio) {

    localStorage.setItem(
        "inicioPublico",
        JSON.stringify(inicio)
    );
}


// Mostrar na página pública
function mostrarInicioPublico() {

    const titulo =
        document.getElementById("inicioPublicoTitulo");

    const descricao =
        document.getElementById("inicioPublicoDescricao");

    const botao =
        document.getElementById("inicioPublicoBotao");

    const imagem =
        document.getElementById("inicioPublicoImagem");

    if (
        !titulo ||
        !descricao ||
        !botao ||
        !imagem
    ) {
        return;
    }

    const inicio =
        obterInicio();

    titulo.innerText =
        inicio.titulo;

    descricao.innerText =
        inicio.descricao;

    botao.innerHTML =
        inicio.botao +
        ' <i class="fa-solid fa-arrow-right ms-2"></i>';

    imagem.src =
        inicio.imagem;
}


// Carregar gestão
function carregarInicioGestao() {

    const titulo =
        document.getElementById("inicioTitulo");

    const descricao =
        document.getElementById("inicioDescricao");

    const botao =
        document.getElementById("inicioBotao");

    if (
        !titulo ||
        !descricao ||
        !botao
    ) {
        return;
    }

    const inicio =
        obterInicio();

    titulo.value =
        inicio.titulo;

    descricao.value =
        inicio.descricao;

    botao.value =
        inicio.botao;
}


// Guardar alterações
function guardarAlteracoesInicio() {

    const titulo =
        document.getElementById("inicioTitulo");

    const descricao =
        document.getElementById("inicioDescricao");

    const botao =
        document.getElementById("inicioBotao");

    const imagemInput =
        document.getElementById("inicioImagem");

    if (
        !titulo ||
        !descricao ||
        !botao
    ) {
        return;
    }

    const inicioAtual =
        obterInicio();

    let imagemFinal =
        inicioAtual.imagem;

    if (
        imagemInput &&
        imagemInput.files &&
        imagemInput.files.length > 0
    ) {

        imagemFinal =
            URL.createObjectURL(
                imagemInput.files[0]
            );
    }

    const novoInicio = {

        titulo:
            titulo.value.trim(),

        descricao:
            descricao.value.trim(),

        botao:
            botao.value.trim(),

        imagem:
            imagemFinal
    };

    guardarInicio(
        novoInicio
    );

    mostrarInicioPublico();

    alert(
        "Página inicial atualizada com sucesso."
    );
}



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
// GESTÃO DA SECÇÃO CONTACTOS - SIMULAÇÃO FRONTEND
// =======================================================


// Conteúdo inicial dos contactos
const contactosInicial = {
    titulo: "Contactos",
    texto: "Entre em contacto connosco para obter mais informações sobre os nossos serviços.",
    formularioTitulo: "Envie-nos uma mensagem",
    botaoTexto: "Enviar Mensagem",
    estado: "Ativo"
};


// Criar dados iniciais no localStorage
if (!localStorage.getItem("contactosPublicos")) {
    localStorage.setItem("contactosPublicos", JSON.stringify(contactosInicial));
}


// Obter contactos
function obterContactos() {
    return JSON.parse(localStorage.getItem("contactosPublicos")) || contactosInicial;
}


// Guardar contactos
function guardarContactos(contactos) {
    localStorage.setItem("contactosPublicos", JSON.stringify(contactos));
}


// Mostrar contactos na página pública
function mostrarContactosPublicos() {
    const secaoContactos = document.getElementById("contactos");

    const titulo = document.getElementById("contactosPublicoTitulo");
    const texto = document.getElementById("contactosPublicoTexto");
    const formularioTitulo = document.getElementById("contactosFormularioTituloPublico");
    const botaoTexto = document.getElementById("contactosBotaoTextoPublico");

    if (
        !secaoContactos ||
        !titulo ||
        !texto ||
        !formularioTitulo ||
        !botaoTexto
    ) {
        return;
    }

    const contactos = obterContactos();

    if (contactos.estado === "Inativo") {
        secaoContactos.style.display = "none";
        return;
    }

    secaoContactos.style.display = "";

    titulo.innerText = contactos.titulo;
    texto.innerText = contactos.texto;
    formularioTitulo.innerText = contactos.formularioTitulo;
    botaoTexto.innerText = contactos.botaoTexto;
}


// Carregar dados na página privada
function carregarContactosGestao() {
    const titulo = document.getElementById("contactosTitulo");
    const texto = document.getElementById("contactosTexto");
    const formularioTitulo = document.getElementById("contactosFormularioTitulo");
    const botaoTexto = document.getElementById("contactosBotaoTexto");
    const estado = document.getElementById("contactosEstado");

    if (
        !titulo ||
        !texto ||
        !formularioTitulo ||
        !botaoTexto ||
        !estado
    ) {
        return;
    }

    const contactos = obterContactos();

    titulo.value = contactos.titulo;
    texto.value = contactos.texto;
    formularioTitulo.value = contactos.formularioTitulo;
    botaoTexto.value = contactos.botaoTexto;
    estado.value = contactos.estado;
}


// Guardar alterações dos contactos
function guardarAlteracoesContactos() {
    const titulo = document.getElementById("contactosTitulo");
    const texto = document.getElementById("contactosTexto");
    const formularioTitulo = document.getElementById("contactosFormularioTitulo");
    const botaoTexto = document.getElementById("contactosBotaoTexto");
    const estado = document.getElementById("contactosEstado");

    if (
        !titulo ||
        !texto ||
        !formularioTitulo ||
        !botaoTexto ||
        !estado
    ) {
        return;
    }

    if (
        titulo.value.trim() === "" ||
        texto.value.trim() === "" ||
        formularioTitulo.value.trim() === "" ||
        botaoTexto.value.trim() === ""
    ) {
        alert("Preenche todos os campos antes de guardar.");
        return;
    }

    const contactosAtualizados = {
        titulo: titulo.value.trim(),
        texto: texto.value.trim(),
        formularioTitulo: formularioTitulo.value.trim(),
        botaoTexto: botaoTexto.value.trim(),
        estado: estado.value
    };

    guardarContactos(contactosAtualizados);

    alert("Alterações guardadas com sucesso.");

    mostrarContactosPublicos();
}

// =======================================================
// FAQ
// =======================================================

const faqInicial = [

    {
        pergunta: "A quem se destina a plataforma TechMed Solutions?",
        resposta: "A plataforma destina-se a instituições de saúde que pretendem organizar e gerir equipamentos médicos de forma centralizada, digital e mais eficiente."
    },

    {
        pergunta: "Que tipo de informação pode ser registada?",
        resposta: "Podem ser registadas informações como identificação do equipamento, categoria, estado, localização, criticidade, documentação associada e histórico relevante."
    },

    {
        pergunta: "A plataforma permite consultar documentos dos equipamentos?",
        resposta: "Sim. A plataforma prevê a associação de documentos aos equipamentos, como manuais, certificados, contratos, relatórios técnicos e outros ficheiros importantes."
    },

    {
        pergunta: "Quem pode aceder à área reservada?",
        resposta: "A área reservada destina-se apenas a utilizadores autorizados, como profissionais responsáveis pela gestão e consulta da informação dos equipamentos médicos."
    }

];


// Criar FAQ inicial
if (!localStorage.getItem("faqPublica")) {
    localStorage.setItem("faqPublica", JSON.stringify(faqInicial));
}


// Obter FAQ
function obterFaq() {
    return JSON.parse(localStorage.getItem("faqPublica")) || [];
}


// Guardar FAQ
function guardarFaq(faqs) {
    localStorage.setItem("faqPublica", JSON.stringify(faqs));
}


// Mostrar FAQ na página pública
function mostrarFaqPublica() {

    const container = document.getElementById("faqAccordionPublico");

    if (!container) return;

    const faqs = obterFaq();

    container.innerHTML = "";

    faqs.forEach(function (faq, index) {

        container.innerHTML += `

            <div class="accordion-item mb-3 border-0 shadow-sm rounded-4 overflow-hidden">

                <h3 class="accordion-header">

                    <button
                        class="accordion-button ${index !== 0 ? "collapsed" : ""} fw-bold"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#faq${index}">

                        ${faq.pergunta}

                    </button>

                </h3>

                <div
                    id="faq${index}"
                    class="accordion-collapse collapse ${index === 0 ? "show" : ""}"
                    data-bs-parent="#faqAccordionPublico">

                    <div class="accordion-body">
                        ${faq.resposta}
                    </div>

                </div>

            </div>
        `;
    });
}


// Mostrar FAQ na gestão
function carregarFaqGestao() {

    const lista = document.getElementById("listaFaqGestao");

    if (!lista) return;

    const faqs = obterFaq();

    lista.innerHTML = "";

    faqs.forEach(function (faq, index) {

        lista.innerHTML += `

            <div class="faq-gestao-card border rounded-4 p-4 mb-4 bg-light mx-auto">

                <h5 class="fw-bold mb-3">
                    FAQ ${index + 1}
                </h5>

                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Pergunta
                    </label>

                    <input
                        type="text"
                        class="form-control faq-pergunta"
                        value="${faq.pergunta}">
                </div>

                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Resposta
                    </label>

                    <textarea
                        class="form-control faq-resposta"
                        rows="4">${faq.resposta}</textarea>

                </div>

            </div>
        `;
    });
}


// Adicionar FAQ
function adicionarFaq() {

    const faqs = obterFaq();

    faqs.push({
        pergunta: "",
        resposta: ""
    });

    guardarFaq(faqs);

    carregarFaqGestao();
}


// Guardar alterações
function guardarAlteracoesFaq() {

    const perguntas = document.querySelectorAll(".faq-pergunta");
    const respostas = document.querySelectorAll(".faq-resposta");

    let novasFaqs = [];

    for (let i = 0; i < perguntas.length; i++) {

        novasFaqs.push({

            pergunta: perguntas[i].value,
            resposta: respostas[i].value

        });
    }

    guardarFaq(novasFaqs);

    mostrarFaqPublica();

    alert("FAQs guardadas com sucesso.");
}

// =======================================================
// RODAPÉ
// =======================================================

const rodapeInicial = {

    logo: "../assets/images/imagem_logo2.png",

    texto: "Plataforma digital para gestão eficiente de equipamentos médicos.",

    localizacao:
        "Rua Dr. António Bernardino de Almeida\n4249-015 Porto\nPortugal",

    horario:
        "2º a 6º Feira - 09h00 às 18h00\n\nSábado: Encerrado\n\nDomingo: Encerrado",

    contactos:
        "geral@techmedsolutions.pt | +351 912 345 678"

};


// Criar rodapé inicial
if (!localStorage.getItem("rodapePublico")) {

    localStorage.setItem(
        "rodapePublico",
        JSON.stringify(rodapeInicial)
    );
}


// Obter rodapé
function obterRodape() {

    return JSON.parse(
        localStorage.getItem("rodapePublico")
    ) || rodapeInicial;
}


// Guardar rodapé
function guardarRodape(rodape) {

    localStorage.setItem(
        "rodapePublico",
        JSON.stringify(rodape)
    );
}


// Mostrar rodapé na página pública
function mostrarRodapePublico() {

    const logo = document.getElementById("footerLogo");

    const texto = document.getElementById("footerTexto");

    const localizacao =
        document.getElementById("footerLocalizacao");

    const horario =
        document.getElementById("footerHorario");

    const contactos =
        document.getElementById("footerContactos");

    if (
        !logo ||
        !texto ||
        !localizacao ||
        !horario ||
        !contactos
    ) return;


    const rodape = obterRodape();


    if (rodape.logo) {
        logo.src = rodape.logo;
    }

    texto.innerText = rodape.texto;
    localizacao.innerHTML =
        rodape.localizacao.replace(/\n/g, "<br>");

    horario.innerHTML =
        rodape.horario.replace(/\n/g, "<br>");

    contactos.innerText =
        rodape.contactos;
}


// Carregar dados na gestão de conteúdos
function carregarRodapeGestao() {

    const texto =
        document.getElementById("rodapeTexto");

    const localizacao =
        document.getElementById("rodapeLocalizacao");

    const horario =
        document.getElementById("rodapeHorario");

    const contactos =
        document.getElementById("rodapeContactos");

    if (
        !texto ||
        !localizacao ||
        !horario ||
        !contactos
    ) return;


    const rodape = obterRodape();


    texto.value = rodape.texto;

    localizacao.value =
        rodape.localizacao;

    horario.value =
        rodape.horario;

    contactos.value =
        rodape.contactos;
}


// Guardar alterações do rodapé
function guardarAlteracoesRodape() {

    const logoInput =
        document.getElementById("rodapeLogoInput");

    const texto =
        document.getElementById("rodapeTexto");

    const localizacao =
        document.getElementById("rodapeLocalizacao");

    const horario =
        document.getElementById("rodapeHorario");

    const contactos =
        document.getElementById("rodapeContactos");

    if (
        !texto ||
        !localizacao ||
        !horario ||
        !contactos
    ) {
        return;
    }

    const rodapeAtual = obterRodape();

    let logoFinal = rodapeAtual.logo;

    // Só altera o logo se existir imagem nova
    if (
        logoInput &&
        logoInput.files &&
        logoInput.files.length > 0
    ) {

        logoFinal =
            URL.createObjectURL(logoInput.files[0]);
    }

    const novoRodape = {

        logo: logoFinal,

        texto: texto.value,

        localizacao: localizacao.value,

        horario: horario.value,

        contactos: contactos.value
    };

    guardarRodape(novoRodape);

    mostrarRodapePublico();

    alert("Rodapé atualizado com sucesso.");
}

// =======================================================
// VALIDAÇÃO DO LOGIN - SIMULAÇÃO FRONTEND
// Esta parte valida o formulário de login antes de entrar
// na área reservada. Mais tarde será substituída por PHP.
// =======================================================

function iniciarValidacaoLogin() {
    const loginForm = document.getElementById("loginForm");
    const email = document.getElementById("email");
    const password = document.getElementById("password");
    const loginErro = document.getElementById("loginErro");

    // Se estes elementos não existirem, significa que não estamos na página de login
    if (!loginForm || !email || !password || !loginErro) return;

    loginForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const nomeUtilizador = email.value.trim();
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

    // ===================================================
    // PÁGINA PÚBLICA
    // ===================================================

    try {
        mostrarInicioPublico();
    } catch (e) {
        console.log("Erro início público:", e);
    }
    try {
        mostrarServicosPublicos();
    } catch (e) {
        console.log("Erro serviços públicos:", e);
    }

    try {
        mostrarSobrePublico();
    } catch (e) {
        console.log("Erro sobre público:", e);
    }

    try {
        mostrarContactosPublicos();
    } catch (e) {
        console.log("Erro contactos públicos:", e);
    }

    try {
        mostrarFaqPublica();
    } catch (e) {
        console.log("Erro FAQ pública:", e);
    }

    try {
        mostrarRodapePublico();
    } catch (e) {
        console.log("Erro rodapé público:", e);
    }

    // ===================================================
    // GESTÃO INÍCIO
    // ===================================================

    try {
        carregarInicioGestao();
    } catch (e) {
        console.log("Erro gestão início:", e);
    }

    const btnGuardarInicio =
        document.getElementById("btnGuardarInicio");

    if (btnGuardarInicio) {

        btnGuardarInicio.addEventListener(
            "click",
            guardarAlteracoesInicio
        );
    }


    // ===================================================
    // GESTÃO DE SERVIÇOS
    // ===================================================

    try {
        carregarServicosParaGestao();
    } catch (e) {
        console.log("Erro gestão serviços:", e);
    }

    const btnAdicionarServico =
        document.getElementById("btnAdicionarServico");

    if (btnAdicionarServico) {

        btnAdicionarServico.addEventListener(
            "click",
            adicionarCardServico
        );
    }

    const btnGuardarServicos =
        document.getElementById("btnGuardarServicos");

    if (btnGuardarServicos) {

        btnGuardarServicos.addEventListener(
            "click",
            guardarAlteracoesServicos
        );
    }


    // ===================================================
    // GESTÃO SOBRE NÓS
    // ===================================================

    try {
        carregarSobreGestao();
    } catch (e) {
        console.log("Erro gestão sobre:", e);
    }

    const btnGuardarSobre =
        document.getElementById("btnGuardarSobre");

    if (btnGuardarSobre) {

        btnGuardarSobre.addEventListener(
            "click",
            guardarAlteracoesSobre
        );
    }


    // ===================================================
    // GESTÃO CONTACTOS
    // ===================================================

    try {
        carregarContactosGestao();
    } catch (e) {
        console.log("Erro gestão contactos:", e);
    }

    const btnGuardarContactos =
        document.getElementById("btnGuardarContactos");

    if (btnGuardarContactos) {

        btnGuardarContactos.addEventListener(
            "click",
            guardarAlteracoesContactos
        );
    }


    // ===================================================
    // GESTÃO FAQ
    // ===================================================

    try {
        carregarFaqGestao();
    } catch (e) {
        console.log("Erro gestão FAQ:", e);
    }

    const btnAdicionarFaq =
        document.getElementById("btnAdicionarFaq");

    if (btnAdicionarFaq) {

        btnAdicionarFaq.addEventListener(
            "click",
            adicionarFaq
        );
    }

    const btnGuardarFaq =
        document.getElementById("btnGuardarFaq");

    if (btnGuardarFaq) {

        btnGuardarFaq.addEventListener(
            "click",
            guardarAlteracoesFaq
        );
    }


    // ===================================================
    // GESTÃO RODAPÉ
    // ===================================================

    try {
        carregarRodapeGestao();
    } catch (e) {
        console.log("Erro gestão rodapé:", e);
    }

    const btnGuardarRodape =
        document.getElementById("btnGuardarRodape");

    if (btnGuardarRodape) {

        btnGuardarRodape.addEventListener(
            "click",
            guardarAlteracoesRodape
        );
    }


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
    console.log("Entrou no código dos acessórios");
    document.getElementById("temAcessorios")
        .addEventListener("change", function () {
            console.log("Valor:", this.value);
            const secaoAcessorios =
                document.getElementById("secaoAcessorios");

            if (this.value === "sim") {

                secaoAcessorios.style.display = "block";

            } else {

                secaoAcessorios.style.display = "none";

            }

        });

    // MOSTRAR / ESCONDER CONSUMÍVEIS

    document.getElementById("temConsumiveis")
        .addEventListener("change", function () {

            const secaoConsumiveis =
                document.getElementById("secaoConsumiveis");

            if (this.value === "sim") {

                secaoConsumiveis.style.display = "block";

            } else {

                secaoConsumiveis.style.display = "none";

            }

        });

    // ADICIONAR ACESSÓRIO

    document.getElementById("btnAdicionarAcessorio")
        .addEventListener("click", function () {

            document.getElementById("listaAcessorios")
                .insertAdjacentHTML(
                    "beforeend",
                    `
        <div class="row g-4 mt-3 acessorio-item">

            <div class="col-md-6">

                <input type="text"
                    class="form-control"
                    placeholder="Nome do Acessório">

            </div>

            <div class="col-md-3">

                <input type="number"
                    class="form-control"
                    min="1"
                    placeholder="Quantidade">

            </div>

            <div class="col-md-3">

                <select class="form-select">

                    <option>Ativo</option>
                    <option>Inativo</option>
                    <option>Avariado</option>

                </select>

            </div>

        </div>
        `
                );

        });

    // ADICIONAR CONSUMÍVEL

    document.getElementById("btnAdicionarConsumivel")
        .addEventListener("click", function () {

            document.getElementById("listaConsumiveis")
                .insertAdjacentHTML(
                    "beforeend",
                    `
        <div class="row g-4 mt-3 consumivel-item">

            <div class="col-md-8">

                <input type="text"
                    class="form-control"
                    placeholder="Nome do Consumível">

            </div>

            <div class="col-md-4">

                <input type="number"
                    class="form-control"
                    min="1"
                    placeholder="Quantidade">

            </div>

        </div>
        `
                );

        });

    // GARANTIAS

    const temGarantia =
        document.getElementById("temGarantia");

    if (temGarantia) {

        temGarantia.addEventListener("change", function () {

            const cardGarantia =
                document.getElementById("cardGarantia");

            if (this.value === "sim") {

                cardGarantia.style.display = "block";

            } else {

                cardGarantia.style.display = "none";

            }

        });

    }


    // CONTRATOS

    const temContrato =
        document.getElementById("temContrato");

    if (temContrato) {

        temContrato.addEventListener("change", function () {

            const secaoContrato =
                document.getElementById("secaoContrato");

            const cardContrato =
                document.getElementById("cardContrato");

            if (this.value === "sim") {

                secaoContrato.style.display = "block";
                cardContrato.style.display = "block";

            } else {

                secaoContrato.style.display = "none";
                cardContrato.style.display = "none";

            }

        });

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

        temDocumentacaoAdicional.addEventListener("change", function () {

            const secao =
                document.getElementById("secaoDocumentacaoAdicional");

            if (this.value === "sim") {

                secao.style.display = "block";

            } else {

                secao.style.display = "none";

            }

        });

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
});



