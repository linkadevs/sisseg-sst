<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado do Checklist</title>

    <link rel="stylesheet" href="../templates/assets/css/checklistresultado.css">
</head>

<body>

    <main>

        <div class="resultado_topo">

            <div class="icone_resultado">

                <img id="iconeResultado" src="../templates/assets/img/xvermelho.png" alt="Resultado">
            </div>

            <div class="dados_resultado">
                <div class="titulo_resultado">

                    <h1>Resultado do Checklist</h1>

                    <span id="statusResultado" class="status_nao_conforme">
                        NÃO CONFORME
                    </span>
                </div>

                <p>
                    Responsável:
                    <strong id="nome_responsavel">
                        ----
                    </strong>
                </p>

                <p>
                    Turno:
                    <strong id="turno_escolhido">
                        ----
                    </strong>
                </p>

                <p>
                    Data:
                    <span id="dataResultado">
                        --/--/----
                    </span>
                </p>

                <p>
                    Progresso:
                    <span id="progressoResultado">
                        0%
                    </span>
                </p>

            </div>

        </div>




        <div class="container_nao_conforme" id="containerNaoConforme">

            <div class="titulo_nao_conforme">
                <img src="../templates/assets/img/alerta_marrom.png" alt="Alerta">
                <h2>
                    Itens Não Conformes
                    (<span id="totalNaoConformes">6</span>)
                </h2>
            </div>

            <div class="lista_nao_conformes" id="listaNaoConformes">

                <div class="item_nao_conforme">
                    <strong>Organização do Canteiro</strong>
                    <p>Tapumes e cercamento instalados</p>
                </div>

                <div class="item_nao_conforme">
                    <strong>Organização do Canteiro</strong>
                    <p>Circulação desobstruída</p>
                </div>

                <div class="item_nao_conforme">
                    <strong>Áreas de Vivência</strong>
                    <p>Refeitório higienizado</p>
                </div>

                <div class="item_nao_conforme">
                    <strong>Áreas de Vivência</strong>
                    <p>Instalações sanitárias adequadas</p>
                </div>

                <div class="item_nao_conforme">
                    <strong>Condições de Trabalho</strong>
                    <p>Proteção de periferia</p>
                </div>

                <div class="item_nao_conforme">
                    <strong>Instalações Elétricas - NR-10</strong>
                    <p>DR instalado onde necessário</p>
                </div>

            </div>


            <div class="alerta_acao">
                <img src="../templates/assets/img/alerta_laranja.png" alt="Alerta">

                <p class="acao_necessaria">
                    <strong> Ação Necessária: </strong>
                    Regularizar os itens não conformes o mais rápido
                    possível. Avaliar necessidade de paralisação de
                    atividades com risco grave e iminente.
                </p>
            </div>

        </div>


        <!-- ESTATÍSTICAS -->

        <div class="estatisticas">

            <div class="card_estatistica" data-grupo="organizacao">
                <h3>Organização do Canteiro</h3>

                <div class="dados_card">
                    <span class="itens">0/4 itens</span>
                    <strong class="percentual">0%</strong>
                </div>

                <div class="barra">
                    <div class="barra_progresso"></div>
                </div>
            </div>

            <div class="card_estatistica" data-grupo="areas_vivencia">
                <h3>Áreas de Vivência</h3>

                <div class="dados_card">
                    <span class="itens">0/4 itens</span>
                    <strong class="percentual">0%</strong>
                </div>

                <div class="barra">
                    <div class="barra_progresso"></div>
                </div>

            </div>


            <div class="card_estatistica" data-grupo="condicoes_trabalho">
                <h3>Condições de Trabalho</h3>

                <div class="dados_card">
                    <span class="itens">0/4 itens</span>
                    <strong class="percentual">0%</strong>
                </div>

                <div class="barra">
                    <div class="barra_progresso"></div>
                </div>

            </div>


            <div class="card_estatistica" data-grupo="maquinas">
                <h3>Máquinas e Equipamentos</h3>
                <div class="dados_card">
                    <span class="itens">0/4 itens</span>
                    <strong class="percentual">0%</strong>
                </div>

                <div class="barra">
                    <div class="barra_progresso"></div>
                </div>
            </div>

            <div class="card_estatistica" data-grupo="nr10">
                <h3>Instalações Elétricas - NR-10</h3>

                <div class="dados_card">
                    <span class="itens">0/4 itens</span>
                    <strong class="percentual">0%</strong>
                </div>

                <div class="barra">
                    <div class="barra_progresso"></div>
                </div>

            </div>

            <div class="card_estatistica" data-grupo="nr11">
                <h3>Movimentação de Materiais - NR-11</h3>

                <div class="dados_card">
                    <span class="itens">0/4 itens</span>
                    <strong class="percentual">0%</strong>
                </div>

                <div class="barra">
                    <div class="barra_progresso"></div>
                </div>
            </div>

            <div class="card_estatistica" data-grupo="nr35">
                <h3>Trabalho em Altura - NR-35</h3>

                <div class="dados_card">
                    <span class="itens">0/4 itens</span>
                    <strong class="percentual">0%</strong>
                </div>

                <div class="barra">
                    <div class="barra_progresso"></div>
                </div>
            </div>

            <div class="card_estatistica" data-grupo="quimicos">
                <h3>Produtos Químicos</h3>

                <div class="dados_card">
                    <span class="itens">0/4 itens</span>
                    <strong class="percentual">0%</strong>
                </div>

                <div class="barra">
                    <div class="barra_progresso"></div>
                </div>
            </div>

            <div class="card_estatistica" data-grupo="nr23">
                <h3>Prevenção Contra Incêndio - NR-23</h3>

                <div class="dados_card">
                    <span class="itens">0/4 itens</span>
                    <strong class="percentual">0%</strong>
                </div>

                <div class="barra">
                    <div class="barra_progresso"></div>
                </div>
            </div>

            <div class="card_estatistica" data-grupo="documentacao">
                <h3>Documentação</h3>

                <div class="dados_card">
                    <span class="itens">0/4 itens</span>
                    <strong class="percentual">0%</strong>
                </div>

                <div class="barra">
                    <div class="barra_progresso"></div>
                </div>
            </div>

        </div>


        <div class="opcoes_botoes">
            <button type="button" class=" btn_novo_checklist" onclick="window.location.href='checklistpart1.php'">
                Novo Checklist
            </button>

            <button type="button" class="btn_voltar_paginainicial" onclick="window.location.href='paginainicial.php'">
                Voltar ao Menu Principal
            </button>
        </div>

    </main>

    <script src="../templates/assets/js/checklistresultado.js"></script>

</body>

</html>