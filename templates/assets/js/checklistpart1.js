
document.addEventListener("DOMContentLoaded", () => {
    const nome = document.getElementById("nome");
    const turno = document.getElementById("turno");
    const erroNome = document.getElementById("erroNome");
    const erroTurno = document.getElementById("erroTurno");
    const form = document.getElementById("formDadosInspecao");
    const contador = document.getElementById("contadorTotalItens");

    // Conta dinamicamente os itens da lista de categorias e multiplica por 4
    const categorias = document.querySelectorAll("#listaItensChecklist li");
    const ITENS_POR_CATEGORIA = 4;
    const totalItens = categorias.length * ITENS_POR_CATEGORIA; // 10 * 4 = 40
    contador.textContent = totalItens;

      erroNome.style.display = "none";
    erroTurno.style.display = "none";

    form.addEventListener("submit", (event) => {
        let formularioValido = true;

        erroNome.style.display = "none";
        erroTurno.style.display = "none";
        nome.classList.remove("input_erro");
        turno.classList.remove("input_erro");

        if (nome.value.trim() === "") {
            erroNome.style.display = "block";
            nome.classList.add("input_erro");
            formularioValido = false;
        }

        if (!turno.value) {
            erroTurno.style.display = "block";
            turno.classList.add("input_erro");
            formularioValido = false;
        }

        if (!formularioValido) {
            event.preventDefault(); // impede o envio
            return;
        }
        // Se válido, o formulário é enviado normalmente para checklistpart2.php
    });

    // Remoção dinâmica dos erros ao preencher
    nome.addEventListener("input", () => {
        if (nome.value.trim() !== "") {
            erroNome.style.display = "none";
            nome.classList.remove("input_erro");
        }
    });

    turno.addEventListener("change", () => {
        if (turno.value) {
            erroTurno.style.display = "none";
            turno.classList.remove("input_erro");
        }
    });
});
