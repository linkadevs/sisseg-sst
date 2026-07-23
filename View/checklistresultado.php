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


        <div class="dados_resultado">


            <div class="titulo_resultado">


                <h1>Resultado do Checklist</h1>



                <span 
                id="statusResultado"
                class="<?= htmlspecialchars($resultado['classe_status'], ENT_QUOTES, 'UTF-8') ?>">


                    <?= htmlspecialchars($resultado['status'], ENT_QUOTES, 'UTF-8') ?>


                </span>


            </div>





            <p>

                Responsável:

                <strong id="nome_responsavel">

                    <?= htmlspecialchars($dados["responsavel"], ENT_QUOTES, "UTF-8") ?>

                </strong>


            </p>





            <p>

                Turno:

                <strong id="turno_escolhido">

                    <?= htmlspecialchars($dados["turno"], ENT_QUOTES, "UTF-8") ?>

                </strong>


            </p>





            <p>

                Data e Hora:


                <strong id="data">


                    <?= htmlspecialchars($resultado["data"], ENT_QUOTES, "UTF-8") ?>


                </strong>


            </p>





            <p>

                Progresso:


                <span id="progressoResultado">


                    <?= $resultado["progresso"] ?>%


                </span>


            </p>



        </div>


    </div>





<?php if($resultado["classe_status"] == "nao_conforme"): ?>



    <div class="container_nao_conforme" id="containerNaoConforme">



        <div class="titulo_nao_conforme">


            <img src="../templates/assets/img/alerta_marrom.png" alt="Alerta">



            <h2>

                Itens Não Conformes

                (<span id="totalNaoConformes">

                    <?= $resultado["total_nao_conformes"] ?>

                </span>)


            </h2>


        </div>





        <div class="lista_nao_conformes" id="listaNaoConformes">





            <?php foreach($resultado["itens_nao_conformes"] as $item): ?>



                <div class="item_nao_conforme">


                    <strong>


                        <?= htmlspecialchars(
                            $item["grupo"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>


                    </strong>




                    <p>


                        <?= htmlspecialchars(
                            $item["descricao"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>


                    </p>



                </div>



            <?php endforeach; ?>





        </div>





        <div class="alerta_acao">


            <img src="../templates/assets/img/alerta_laranja.png" alt="Alerta">





            <p class="acao_necessaria">


                <strong>
                    Ação Necessária:
                </strong>


                Regularizar os itens não conformes o mais rápido
                possível. Avaliar necessidade de paralisação de
                atividades com risco grave e iminente.



            </p>



        </div>




    </div>



<?php endif; ?>

<?php if($resultado["classe_status"] == "parcialmente_conforme"): ?>


    <div class="container_parcialmente_conforme" id="containerParcialmenteConforme">


        <div class="titulo_parcialmente_conforme">


            <h2>

                Itens Parcialmente Conformes

                (<span id="totalParcialmenteConformes">

                    <?= $resultado["total_parcialmente_conformes"] ?>

                </span>)


            </h2>


        </div>





        <div class="lista_parcialmente_conformes" id="listaParcialmenteConformes">



            <?php foreach($resultado["itens_parcialmente_conformes"] as $item): ?>



                <div class="item_parcialmente_conforme">



                    <strong>


                        <?= htmlspecialchars(
                            $item["grupo"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>


                    </strong>





                    <p>


                        <?= htmlspecialchars(
                            $item["descricao"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>


                    </p>



                </div>



            <?php endforeach; ?>




        </div>





        <div class="alerta_parcialmente_conforme">


            <img src="../templates/assets/img/alerta_laranja.png" alt="Alerta">





            <p class="acao_parcialmente_conforme">


                <strong>
                    Ação Necessária:
                </strong>


                Regularizar os itens parcialmente conformes para atingir
                100% de conformidade.



            </p>




        </div>




    </div>



<?php endif; ?>





<?php if($resultado["classe_status"] == "conforme"): ?>



    <div class="container_conforme_wrapper" id="containerConforme">


        <div class="container_conforme">


            <div class="titulo_conforme">


                <img src="../templates/assets/img/conforme_verde.png" alt="Conforme">





                <div>



                    <h2>

                        100% Conforme

                    </h2>





                    <p>


                        Todos os itens de segurança estão adequados.
                        Parabéns pela manutenção das condições de trabalho!



                    </p>



                </div>




            </div>



        </div>



    </div>



<?php endif; ?>

    <!-- ESTATÍSTICAS -->


    <div class="estatisticas">



        <?php foreach($resultado["grupos"] as $grupo): ?>



            <div class="card_estatistica">


                <h3>

                    <?= htmlspecialchars(
                        $grupo["nome"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>


                </h3>





                <div class="dados_card">



                    <span class="itens">


                        <?= $grupo["conformes"] ?>/<?= $grupo["total"] ?> itens


                    </span>





                    <strong class="percentual">


                        <?= $grupo["percentual"] ?>%


                    </strong>



                </div>





                <div class="barra">


                    <div 
                    class="barra_progresso"
                    style="width: <?= $grupo["percentual"] ?>%;">
                    


                    </div>


                </div>



            </div>




        <?php endforeach; ?>




    </div>







    <div class="opcoes_botoes">



        <button 
        type="button" 
        class="btn_novo_checklist" 
        onclick="window.location.href='checklistpart1.php'">


            Novo Checklist


        </button>





        <button 
        type="button" 
        class="btn_voltar_paginainicial" 
        onclick="window.location.href='principal_adm.php'">


            Voltar ao Menu Principal


        </button>




    </div>





</main>



</body>


</html>