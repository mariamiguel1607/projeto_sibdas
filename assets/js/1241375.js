// FILTROS DA TABELA DE EQUIPAMENTOS

const inputPesquisa = document.getElementById("pesquisa");
const selectCategoria = document.getElementById("categoria");
const selectEstado = document.getElementById("estado");
const botaoFiltrar = document.querySelector(".filter-card button");

const linhasEquipamentos = document.querySelectorAll("tbody tr");

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

// ARQUIVAR EQUIPAMENTO
const botoesArquivar = document.querySelectorAll(".btn-arquivar");

botoesArquivar.forEach(function (botao) {
    botao.addEventListener("click", function () {
        const linha = botao.closest("tr");

        const confirmar = confirm("Tem a certeza que pretende arquivar este equipamento?");

        if (confirmar) {
            linha.remove();
            atualizarCards();
        }
    });
});

// ATUALIZAR CARDS DO TOPO
function atualizarCards() {
    const linhasAtuais = document.querySelectorAll("tbody tr");

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

    const cards = document.querySelectorAll(".private-card h3");

    cards[0].innerText = total;
    cards[1].innerText = ativos;
    cards[2].innerText = manutencao;
    cards[3].innerText = criticos;
}

atualizarCards();