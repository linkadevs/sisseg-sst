document.addEventListener("DOMContentLoaded", () => {

    const statusResultado =
        document.getElementById("statusResultado");

    const totalNaoConformesElement =
        document.getElementById("totalNaoConformes");

    const containerNaoConforme =
        document.getElementById("containerNaoConforme");

    const containerConforme =
        document.getElementById("containerConforme");

    const containerParcialmenteConforme =
        document.getElementById("containerParcialmenteConforme");

    const TOTAL_ITENS = 40; 

    let totalNaoConformes = 0;

    if (totalNaoConformesElement) {
        totalNaoConformes =
            parseInt(totalNaoConformesElement.textContent) || 0;
    }

    const itensConformes =
        TOTAL_ITENS - totalNaoConformes;

    const percentual =
        (itensConformes / TOTAL_ITENS) * 100;

    statusResultado.classList.remove(
        "status_nao_conforme",
        "status_parcialmente_conforme",
        "status_conforme"
    );

    containerNaoConforme.style.display = "none";
    containerParcialmenteConforme.style.display = "none";
    containerConforme.style.display = "none";

    if (percentual === 100) {

        statusResultado.textContent =
            "CONFORME";

        statusResultado.classList.add(
            "status_conforme"
        );

        containerConforme.style.display =
            "block";

    } else if (percentual >= 70) {

        statusResultado.textContent =
            "PARCIALMENTE CONFORME";

        statusResultado.classList.add(
            "status_parcialmente_conforme"
        );

        containerParcialmenteConforme.style.display =
            "block";

    } else {

        statusResultado.textContent =
            "NÃO CONFORME";

        statusResultado.classList.add(
            "status_nao_conforme"
        );

        containerNaoConforme.style.display =
            "block";
    }

});