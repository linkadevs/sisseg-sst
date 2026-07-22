<?php

session_start();

date_default_timezone_set("America/Bahia");

if (!isset($_SESSION["checagem"])) {
    header("Location: checklistpart1.php");
    exit;
}

$dados = $_SESSION["checagem"];

?>





<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist Diário em Andamento- NR18</title>
    <link rel="stylesheet" href="../templates/assets/css/checklistpart2.css">
</head>

<body>

    <button class="setavoltarpreta" type="button" onclick="window.location.href='checklistpart1.php'">
        <figure>
            <img src="../templates/assets/img/seta_esquerda.png" alt="Voltar">
        </figure>
        <span>Voltar</span>
    </button>

    <main>

        <form id="formChecklist" method="POST" action="../Controller/ChecklistController.php?acao=salvarChecklist">
            <div class="container_topo">
                <div class="dados">
                    <h1>Checklist em Andamento</h1>
                    <p>
                        Responsável:
                        <strong
                            id="responsavel"><?= htmlspecialchars($dados["responsavel"], ENT_QUOTES, "UTF-8") ?></strong>
                    </p>

                    <p>
                        Turno:
                        <strong id="turno"><?= htmlspecialchars($dados["turno"], ENT_QUOTES, "UTF-8"); ?></strong>
                    </p>

                    <p>
                        Data e Hora:

                        <strong id="data"><?= date("d/m/Y H:i:s") ?></strong>

                    </p>
                </div>

                <div class="progresso">
                    <span class="progresso-text">0% Concluído</span>
                </div>

            </div>


            <div class="grupo_checklist">
                <div class="cabecalho_grupo">
                    <h2>1- Organização do Canteiro</h2>
                    <span class="contador">0/4</span>
                </div>

                <label class="item_check">
                    <input type="checkbox" name="organizacao[]" value="Tapumes e cercamento instalados">
                    <span>Tapumes e cercamento instalados</span>
                </label>

                <label class="item_check">
                    <input type="checkbox" name="organizacao[]" value="Identificação da obra e CNPJ">
                    <span>Identificação da obra e CNPJ</span>
                </label>

                <label class="item_check">
                    <input type="checkbox" name="organizacao[]" value="Circulação desobstruída">
                    <span>Circulação desobstruída</span>
                </label>

                <label class="item_check">
                    <input type="checkbox" name="organizacao[]" value="Controle de acesso funcionando">
                    <span>Controle de acesso funcionando</span>
                </label>

            </div>

            <!-- 2 -->
            <div class="grupo_checklist">
                <div class="cabecalho_grupo">
                    <h2>2- Áreas de Vivência</h2>
                    <span class="contador">0/4</span>
                </div>

                <label class="item_check">
                    <input type="checkbox" name="areas_vivencia[]" value="Vestiários limpos">
                    <span>Vestiários limpos</span>
                </label>

                <label class="item_check">
                    <input type="checkbox" name="areas_vivencia[]" value="Refeitório higienizado">
                    <span>Refeitório higienizado</span>
                </label>

                <label class="item_check">
                    <input type="checkbox" name="areas_vivencia[]" value="Instalações sanitárias adequadas">
                    <span>Instalações sanitárias adequadas</span>
                </label>

                <label class="item_check">
                    <input type="checkbox" name="areas_vivencia[]" value="Água potável disponível">
                    <span>Água potável disponível</span>
                </label>

            </div>

            <!-- 3 -->
            <div class="grupo_checklist">
                <div class="cabecalho_grupo">
                    <h2>3- Condições de Trabalho</h2>
                    <span class="contador">0/4</span>
                </div>

                <label class="item_check">
                    <input type="checkbox" name="condicoes_trabalho[]" value="Escadas e rampas seguras">
                    <span>Escadas e rampas seguras</span>
                </label>

                <label class="item_check">
                    <input type="checkbox" name="condicoes_trabalho[]" value="Plataformas com guarda-corpo">
                    <span>Plataformas com guarda-corpo</span>
                </label>

                <label class="item_check">
                    <input type="checkbox" name="condicoes_trabalho[]" value="Sinalização de risco instalada">
                    <span>Sinalização de risco instalada</span>
                </label>

                <label class="item_check">
                    <input type="checkbox" name="condicoes_trabalho[]" value="Proteção de periferia">
                    <span>Proteção de periferia</span>
                </label>

            </div>

            <!-- 4 -->
            <div class="grupo_checklist">

                <div class="cabecalho_grupo">
                    <h2>4- Máquinas e Equipamentos</h2>
                    <span class="contador">0/4</span>
                </div>

                <label class="item_check">
                    <input type="checkbox" name="maquinas[]" value="Betoneira com proteção">
                    <span>Betoneira com proteção</span>
                </label>

                <label class="item_check">
                    <input type="checkbox" name="maquinas[]" value="Serra circular protegida">
                    <span>Serra circular protegida</span>
                </label>

                <label class="item_check">
                    <input type="checkbox" name="maquinas[]" value="Guincho/guindaste inspecionado">
                    <span>Guincho/guindaste inspecionado</span>
                </label>

                <label class="item_check">
                    <input type="checkbox" name="maquinas[]" value="Retroescavadeira/empilhadeira OK">
                    <span>Retroescavadeira/empilhadeira OK</span>
                </label>

            </div>

            <!-- 5 -->
            <div class="grupo_checklist">

                <div class="cabecalho_grupo">
                    <h2>5- Instalações Elétricas - NR-10</h2>
                    <span class="contador">0/4</span>
                </div>

                <label class="item_check"><input type="checkbox" name="nr10[]"
                        value="Quadros identificados"><span>Quadros identificados</span></label>
                <label class="item_check"><input type="checkbox" name="nr10[]"
                        value="Aterramento testado"><span>Aterramento testado</span></label>
                <label class="item_check"><input type="checkbox" name="nr10[]" value="Cabos sem emendas"><span>Cabos sem
                        emendas</span></label>
                <label class="item_check"><input type="checkbox" name="nr10[]"
                        value="DR instalado onde necessário"><span>DR instalado onde necessário</span></label>

            </div>

            <!-- 6 -->
            <div class="grupo_checklist">

                <div class="cabecalho_grupo">
                    <h2>6- Movimentação de Materiais - NR-11</h2>
                    <span class="contador">0/4</span>
                </div>

                <label class="item_check"><input type="checkbox" name="nr11[]"
                        value="Operadores habilitados"><span>Operadores habilitados</span></label>
                <label class="item_check"><input type="checkbox" name="nr11[]"
                        value="Sinalização visível"><span>Sinalização visível</span></label>
                <label class="item_check"><input type="checkbox" name="nr11[]" value="Carga identificada"><span>Carga
                        identificada</span></label>
                <label class="item_check"><input type="checkbox" name="nr11[]"
                        value="Rota de içamento isolada"><span>Rota de içamento isolada</span></label>

            </div>

            <!-- 7 -->
            <div class="grupo_checklist">

                <div class="cabecalho_grupo">
                    <h2>7- Trabalho em Altura - NR-35</h2>
                    <span class="contador">0/4</span>
                </div>

                <label class="item_check"><input type="checkbox" name="nr35[]"
                        value="Linha de vida instalada"><span>Linha de vida instalada</span></label>
                <label class="item_check"><input type="checkbox" name="nr35[]"
                        value="Cinturões revisados"><span>Cinturões revisados</span></label>
                <label class="item_check"><input type="checkbox" name="nr35[]"
                        value="Ancoragens certificadas"><span>Ancoragens certificadas</span></label>
                <label class="item_check"><input type="checkbox" name="nr35[]" value="PT emitida"><span>PT emitida
                        (Quando necessário)</span></label>

            </div>

            <!-- 8 -->
            <div class="grupo_checklist">
                <div class="cabecalho_grupo">
                    <h2>8- Produtos Químicos</h2>
                    <span class="contador">0/4</span>
                </div>

                <label class="item_check"><input type="checkbox" name="quimicos[]" value="FISPQ disponível"><span>FISPQ
                        disponível</span></label>
                <label class="item_check"><input type="checkbox" name="quimicos[]"
                        value="Armazenamento ventilado"><span>Armazenamento ventilado</span></label>
                <label class="item_check"><input type="checkbox" name="quimicos[]"
                        value="EPIs adequados entregues"><span>EPIs adequados entregues</span></label>
                <label class="item_check"><input type="checkbox" name="quimicos[]"
                        value="Recipientes identificados"><span>Recipientes identificados</span></label>
            </div>


            <!-- 9 -->
            <div class="grupo_checklist">
                <div class="cabecalho_grupo">
                    <h2>9- Prevenção Contra Incêndio - NR-23</h2>
                    <span class="contador">0/4</span>
                </div>

                <label class="item_check"><input type="checkbox" name="nr23[]"
                        value="Extintores acessíveis"><span>Extintores acessíveis</span></label>
                <label class="item_check"><input type="checkbox" name="nr23[]"
                        value="Sinalização de emergência"><span>Sinalização de emergência</span></label>
                <label class="item_check"><input type="checkbox" name="nr23[]" value="Saídas desobstruídas"><span>Saídas
                        desobstruídas</span></label>
                <label class="item_check"><input type="checkbox" name="nr23[]" value="Brigada treinada"><span>Brigada
                        treinada</span></label>
            </div>


            <!-- 10 -->
            <div class="grupo_checklist">
                <div class="cabecalho_grupo">
                    <h2>10- Documentação</h2>
                    <span class="contador">0/4</span>
                </div>

                <label class="item_check"><input type="checkbox" name="documentacao[]" value="PGR atualizado"><span>PGR
                        atualizado</span></label>
                <label class="item_check"><input type="checkbox" name="documentacao[]"
                        value="PCMSO disponível"><span>PCMSO disponível</span></label>
                <label class="item_check"><input type="checkbox" name="documentacao[]"
                        value="Lista de EPIs assinada"><span>Lista de EPIs assinada</span></label>
                <label class="item_check"><input type="checkbox" name="documentacao[]"
                        value="Treinamentos dentro da validade"><span>Treinamentos dentro da validade</span></label>
            </div>


            <div class="observacoes_container">
                <label for="observacoes">Observações Gerais</label>

                <textarea id="observacoes" name="observacoes"
                    placeholder="Registre aqui observações importantes, não conformidades encontradas ou ações corretivas necessárias..."></textarea>
            </div>


            <div class="assinatura_container">

                <label>Assinatura</label>

                <div class="assinatura_box_gatilho">

                    <p>
                        <strong>
                            Assinatura Digital do Responsável
                        </strong>
                    </p>

                    <div class="campo_toque_assinar" id="abrirAssinatura">

                        <img src="../templates/assets/img/caneta.png" alt="Assinar">

                        <span id="textoAssinatura">
                            Assinatura digital — toque para assinar
                        </span>

                    </div>

                    <input type="hidden" name="assinatura" id="assinaturaBase64">
                    <p id="erroAssinatura" class="mensagem_erro">
                        A assinatura é obrigatória para finalizar o checklist.
                    </p>

                </div>

            </div>

            <!-- MODAL -->

            <div class="modal_overlay" id="modalAssinatura">

                <div class="modal_conteudo">

                    <div class="modal_cabecalho">

                        <div>

                            <h3>
                                Assinar Registro
                            </h3>

                            <p class="desenhe_aqui">
                                Desenhe sua assinatura abaixo
                            </p>

                        </div>

                        <button type="button" class="btn_fechar" id="fecharModalX">
                            ×
                        </button>

                    </div>

                    <div class="modal_corpo">

                        <canvas id="canvasAssinatura"></canvas>

                    </div>

                    <div class="modal_rodape">

                        <div class="botoes_acao">

                            <button type="button" class="btn_limpar" id="btnLimparCanvas">
                                Limpar
                            </button>

                            <button type="button" class="btn_assinar" id="btnSalvarAssinatura">
                                Assinar
                            </button>

                        </div>

                        <button type="button" class="btn_cancelar" id="fecharModalCancelar">
                            Cancelar
                        </button>

                    </div>
                </div>
            </div>

            <div class="btn_finalizar">

                <button class="btn_finalizar_checklist" type="submit" id="btnFinalizarChecklist">

                    <img class="icone_finalizar" src="../templates/assets/img/finalizarChecklist.png" alt="Finalizar">

                    <span>
                        Finalizar Checklist
                    </span>

                </button>

            </div>

        </form>

    </main>

    <script src="../templates/assets/js/checklistpart2.js"></script>

</body>

</html>