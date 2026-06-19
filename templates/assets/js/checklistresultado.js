document.addEventListener("DOMContentLoaded", () => {

    const dados =
        JSON.parse(
            localStorage.getItem("resultadoChecklist")
        );

    if (!dados) return;

    document.getElementById("nomeResultado").textContent =
        dados.responsavel;

    document.getElementById("turnoResultado").textContent =
        dados.turno;

    document.getElementById("dataResultado").textContent =
        dados.data;

    const listaNaoConformes =
        document.getElementById("listaNaoConformes");

    const estatisticasContainer =
        document.getElementById("estatisticasContainer");

    let totalItens = 0;
    let itensConformes = 0;
    let naoConformes = 0;

    dados.grupos.forEach(grupo => {

        let marcados = 0;

        grupo.itens.forEach(item => {

            totalItens++;

            if (item.conforme) {

                marcados++;
                itensConformes++;

            } else {

                naoConformes++;

                listaNaoConformes.innerHTML += `
                    <div class="item_nao_conforme">
                        <strong>${grupo.categoria}</strong>
                        <p>${item.descricao}</p>
                    </div>
                `;
            }

        });

        const percentualGrupo =
            Math.round(
                (marcados / grupo.itens.length) * 100
            );

        estatisticasContainer.innerHTML += `
            <div class="card_estatistica">

                <h3>${grupo.categoria}</h3>

                <div class="dados_card">

                    <span>
                        ${marcados}/${grupo.itens.length} itens
                    </span>

                    <strong>
                        ${percentualGrupo}%
                    </strong>

                </div>

                <div class="barra">
                    <div
                        class="progresso"
                        style="width:${percentualGrupo}%">
                    </div>
                </div>

            </div>
        `;
    });

    const progressoGeral =
        Math.round(
            (itensConformes / totalItens) * 100
        );

    document.getElementById(
        "progressoResultado"
    ).textContent =
        `${progressoGeral}%`;

    document.getElementById(
        "tituloNaoConformes"
    ).textContent =
        `Itens Não Conformes (${naoConformes})`;

});