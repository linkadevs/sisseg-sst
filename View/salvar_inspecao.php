<?php

session_start();

$epis = $_SESSION['epis'];

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../templates/assets/css/salvar_inspecao.css">
        <!-- <link rel="shortcut icon" href="../templates/assets/img/favicon.ico" type="image/x-icon"> -->
        <title>Inspeção de EPI</title>
    </head>
    <body>
        <div class="modal">
            <p class="descricaoModal placeholder">Desenhe aqui a sua assinatura</p>
            <button class="sair" type="button"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></button>
            <p class="tituloModal">Assinar registro</p>
            <p class="descricaoModal">Desenhe sua assinatura abaixo</p>
            <canvas id="signature"></canvas>
            <div class="buttons">
                <button type="button" class="limpar modalButton desativado" id="confirmarAssinatura">Limpar</button>
                <button type="button" class="assinar modalButton desativado" id="limparCanvas">Assinar</button>
            </div>
            <button type="button" class="cancelar modalButton">Cancelar</button>
        </div>
        <div class="sombra"></div>
        <header>
            <button class="voltarBtn">
                <figure>
                    <img src="../templates/assets/img/seta_esquerda.png" alt="Black left-pointing arrow on white background, indicating a back or previous navigation action">
                </figure>
                Voltar
            </button>
        </header>
        <main>
            <div class="containerSuperior">
                <h2>Inspeção de EPI – Diário</h2>
                <h4><strong><?= htmlspecialchars($_SESSION['nome'])?></strong> | <?= htmlspecialchars($_SESSION['funcao'])?> | <?= htmlspecialchars($_SESSION['setor'])?></h4>
            </div>
            <form>
                <div class="container">
                    <h3 class="margin">EPIs Obrigatórios da Função</h3>
                    
                    <?php foreach($epis as $index => $epi):?>
                        <div class="input" onclick="event.stopPropagation()">
                            <input class="checkboxes" type="checkbox" data-check="<?= 'epi'.strval($index);?>" name="<?= htmlspecialchars($epi)?>" id="<?= htmlspecialchars($epi)?>">
                            <label for="<?= htmlspecialchars($epi)?>"><?= htmlspecialchars($epi)?> <span>*</span></label>
                            <figure><img src="../templates/assets/img/check_verde.png" alt="Green check mark icon beside required equipment item in a safety inspection form indicating the item is marked as completed"></figure>
                        </div>
                    <?php endforeach;?>

                    <!-- <div class="input" onclick="event.stopPropagation()">
                        <input type="checkbox" name="Óculos anti-impacto" id="Óculos anti-impacto">
                        <label for="Óculos anti-impacto">Óculos anti-impacto <span>*</span></label>
                        <figure><img src="../templates/assets/img/check_verde.png" alt="Green check mark icon beside required equipment item in a safety inspection form indicating the item is marked as completed"></figure>
                    </div>

                    <div class="input" onclick="event.stopPropagation()">
                        <input type="checkbox" name="Luvas de vaqueta/raspa" id="Luvas de vaqueta/raspa">
                        <label for="Luvas de vaqueta/raspa">Luvas de vaqueta/raspa <span>*</span></label>
                        <figure><img src="../templates/assets/img/check_verde.png" alt="Green check mark icon beside required equipment item in a safety inspection form indicating the item is marked as completed"></figure>
                    </div>

                    <div class="input" onclick="event.stopPropagation()">
                        <input type="checkbox" name="Luvas nitrílicas (cimento)" id="Luvas nitrílicas (cimento)">
                        <label for="Luvas nitrílicas (cimento)">Luvas nitrílicas (cimento) <span>*</span></label>
                        <figure><img src="../templates/assets/img/check_verde.png" alt="Green check mark icon beside required equipment item in a safety inspection form indicating the item is marked as completed"></figure>
                    </div>

                    <div class="input" onclick="event.stopPropagation()">
                        <input type="checkbox" name="Botina com biqueira" id="Botina com biqueira">
                        <label for="Botina com biqueira">Botina com biqueira <span>*</span></label>
                        <figure><img src="../templates/assets/img/check_verde.png" alt="Green check mark icon beside required equipment item in a safety inspection form indicating the item is marked as completed"></figure>
                    </div>

                    <div class="input" onclick="event.stopPropagation()">
                        <input type="checkbox" name="Máscara PFF2" id="Máscara PFF2">
                        <label for="Máscara PFF2">Máscara PFF2 <span>*</span></label>
                        <figure><img src="../templates/assets/img/check_verde.png" alt="Green check mark icon beside required equipment item in a safety inspection form indicating the item is marked as completed"></figure>
                    </div>

                    <div class="input" onclick="event.stopPropagation()">
                        <input type="checkbox" name="Protetor auricular" id="Protetor auricular">
                        <label for="Protetor auricular">Protetor auricular <span>*</span></label>
                        <figure><img src="../templates/assets/img/check_verde.png" alt="Green check mark icon beside required equipment item in a safety inspection form indicating the item is marked as completed"></figure>
                    </div>

                    <div class="input" onclick="event.stopPropagation()">
                        <input type="checkbox" name="Cinturão (altura)" id="Cinturão (altura)">
                        <label for="Cinturão (altura)">Cinturão (altura) <span>*</span></label>
                        <figure><img src="../templates/assets/img/check_verde.png" alt="Green check mark icon beside required equipment item in a safety inspection form indicating the item is marked as completed"></figure>
                    </div> -->
                </div>
                <div class="container">
                    <h3>Condição dos EPIs</h3>
                    <h4 class="condicoes">Marque as condições encontradas (pode marcar mais de uma)</h4>
                    <?php foreach($epis as $index => $epi):?>
                        <div class="item <?= 'epi'.strval($index)?>">
                            <p><?= htmlspecialchars($epi)?></p>
                            <div class="organizer">
                                <div class="input2" onclick="event.stopPropagation()">
                                    <input type="radio" name="estado" value="bom_estado" id="bom_estado">
                                    <label for="Bom estado">Bom estado</label>
                                </div>
                                <div class="input2" onclick="event.stopPropagation()">
                                    <input type="radio" name="estado" value="desgastado" id="desgastado">
                                    <label for="Desgastado">Desgastado</label>
                                </div>
                                <div class="input2" onclick="event.stopPropagation()">
                                    <input type="radio" name="estado" value="vencido" id="vencido">
                                    <label for="Vencido (CA)">Vencido (CA)</label>
                                </div>
                                <div class="input2" onclick="event.stopPropagation()">
                                    <input type="radio" name="estado" value="reposicao" id="reposicao">
                                    <label for="Solicitar reposição">Solicitar reposição</label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach;?>
                    <div class="item">
                        <p>Capacete com Jugular</p>
                        <div class="organizer">
                            <div class="input2" onclick="event.stopPropagation()">
                                <input type="checkbox" name="bom_estado" id="bom_estado">
                                <label for="Bom estado">Bom estado</label>
                            </div>
                            <div class="input2" onclick="event.stopPropagation()">
                                <input type="checkbox" name="desgastado" id="desgastado">
                                <label for="Desgastado">Desgastado</label>
                            </div>
                            <div class="input2" onclick="event.stopPropagation()">
                                <input type="checkbox" name="vencido" id="vencido">
                                <label for="Vencido (CA)">Vencido (CA)</label>
                            </div>
                            <div class="input2" onclick="event.stopPropagation()">
                                <input type="checkbox" name="reposicao" id="reposicao">
                                <label for="Solicitar reposição">Solicitar reposição</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container assinaturas">
                    <h3 class="margin">Assinaturas</h3>
                    <p>Assinatura do colaborador</p>
                    <button type="button" id="assinarColaborador">
                        <svg id="iconeCaneta" xmlns="http://w3.org" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pen"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" /></svg>
                        <svg id="iconeCheck" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-4 h-4"><path d="M20 6 9 17l-5-5"></path></svg>
                        <p>Assinatura digital - toque para assinar</p>
                    </button>
                    <p>Assinatura da supervisão</p>
                    <button type="button" id="assinarSupervisor">
                        <svg id="iconeCaneta" xmlns="http://w3.org" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pen"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z" /></svg>
                        <svg id="iconeCheck" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check w-4 h-4"><path d="M20 6 9 17l-5-5"></path></svg>
                        <p>Supervisor - toque para assinar</p>
                    </button>
                </div>
                <button class="submit" type="button">Salvar inspeção</button>
                <div class="container foto">
                    <figure>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-camera w-16 h-16 text-[#94A3B8]"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"></path><circle cx="12" cy="13" r="3"></circle></svg>
                    </figure>
                    <div class="foto_buttons">
                        <button type="submit" class="capturar">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-camera w-16 h-16 text-[#94A3B8]"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"></path><circle cx="12" cy="13" r="3"></circle></svg>
                            Capturar foto
                        </button>
                        <button type="button" class="cancel">Cancelar</button>
                    </div>
                </div>
                <input type="hidden" id="assinaturaColaborador" name="assinatura_colaborador">
                <input type="hidden" id="assinaturaSupervisor" name="assinatura_supervisor">
            </form>
        </main>
        <script src="../templates/assets/js/salvar_inspecao.js"></script>
    </body>
</html>