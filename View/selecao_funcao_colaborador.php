<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Controller/FuncaoController.php';

use Controller\FuncaoController;

$funcaoController = new FuncaoController();

$funcoes = $funcaoController->selecionarTodasFuncoes();

$_SESSION['id_funcionario'] = 6;

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../templates/assets/css/selecao_funcao_colaborador.css">
        <!-- <link rel="shortcut icon" href="../templates/assets/img/favicon.ico" type="image/x-icon"> -->
        <title>Inspeção de EPI</title>
    </head>
    <body>
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
                <div class="sTitle">
                    <figure>
                        <img src="../templates/assets/img/double_check.png" alt="Two overlapping blue check marks forming a verification logo; one darker blue tick overlaps a lighter blue tick, both angled upward. Small square icon with transparent background. No visible text. Conveys confidence and reliability.">
                    </figure>
                    <h2>Inspeção de EPI – Diário</h2>
                </div>
                <h4>Selecione a função do colaborador para iniciar a inspeção</h4>
            </div>
            <div class="grid">
                <?php foreach($funcoes as $funcao):?>
                    <div class="card" id="<?= htmlspecialchars($funcao['id_funcao'])?>">
                        <h3><?= htmlspecialchars($funcao['nome_funcao'])?></h3>
                        <?php $epis = explode(', ', $funcao['nome_epi'])?>
                        <h5><?= count($epis);?> EPIs obrigatórios</h5>
                        <ul>
                            <?php foreach(array_slice($epis, 0, 3) as $epi):?>
                                <li><?= htmlspecialchars($epi)?></li>
                            <?php endforeach;?>
                        </ul>
                        <?php if(count($epis) > 3):?>
                            <p>+<?= count($epis)-3?> mais...</p>
                        <?php endif;?>
                    </div>
                <?php endforeach;?>
            </div>
        </main>
        <script src="../templates/assets/js/selecao_funcao_colaborador.js"></script>
    </body>
</html>