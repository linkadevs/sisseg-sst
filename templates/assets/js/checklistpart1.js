
document.addEventListener("DOMContentLoaded", () => {

    const nome = document.getElementById("nome");
    const turno = document.getElementById("turno");

    const erroNome = document.getElementById("erroNome");
    const erroTurno = document.getElementById("erroTurno");

    const btnIniciar = document.getElementById("btnIniciarChecklist");

    erroNome.style.display = "none";
    erroTurno.style.display = "none";

    btnIniciar.addEventListener("click", () => {

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
            return;
        }

        window.location.href = "checklistpart2.php";
    });

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