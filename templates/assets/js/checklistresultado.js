document.addEventListener("DOMContentLoaded", () => {

    const statusResultado =
        document.getElementById("statusResultado");

    const totalNaoConformesElement =
        document.getElementById("totalNaoConformes");

    const containerNaoConforme =
        document.getElementById("containerNaoConforme");

    const containerConforme =
        document.getElementById("containerConforme");

    let totalNaoConformes = 0;

    if (totalNaoConformesElement) {
        totalNaoConformes =
            parseInt(
                totalNaoConformesElement.textContent
            ) || 0;
    }

    // inicia escondido
    containerConforme.style.display = "none";

    if (totalNaoConformes === 0) {

        statusResultado.textContent = "CONFORME";

        statusResultado.classList.remove(
            "status_nao_conforme"
        );

        statusResultado.classList.add(
            "status_conforme"
        );

        containerNaoConforme.style.display =
            "none";

        containerConforme.style.display =
            "block";

    } else {

        statusResultado.textContent =
            "NÃO CONFORME";

        statusResultado.classList.remove(
            "status_conforme"
        );

        statusResultado.classList.add(
            "status_nao_conforme"
        );

        containerNaoConforme.style.display =
            "block";

        containerConforme.style.display =
            "none";
    }

});