<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Controller/InspecaoController.php';
require_once __DIR__ . '/../Controller/FuncionarioController.php';
require_once __DIR__ . '/../Controller/FuncaoController.php';

use Controller\InspecaoController;
use Controller\FuncionarioController;
use Controller\FuncaoController;
use DateTime;

$inspecao_controller = new InspecaoController();
$funcionario_controller = new FuncionarioController();
$funcao_controller = new FuncaoController();

$inspecao = $inspecao_controller->selecionarInspecaoPorId($_SESSION['inspecao_id']);
$funcionario = $funcionario_controller->selecionarFuncionarioPorId($inspecao['id_funcionario_fk']);
$funcao = $funcao_controller->selecionarFuncaoPorId($inspecao['id_funcao_fk']);

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inspeção de EPI</title>
        <link rel="stylesheet" href="../templates/assets/css/resultado_inspecao.css">
    </head>
    <body>
        <div class="upper">
            <div class="title">
                <figure><img src="../templates/assets/img/check_verde.png" alt=""></figure>
                <h2>Resultado da inspeção</h2>
                <p>CONFORME</p>
            </div>
            <p class="info">
                Colaborador: <strong><?= htmlspecialchars($funcionario['nome_funcionario'])?></strong> | Função: <strong><?= htmlspecialchars($funcao['nome_funcao'])?></strong><br>
                Setor: <strong><?= htmlspecialchars($_SESSION['setor'])?></strong><br>
                <?php 
                    $data = new DateTime($inspecao['data_hora_inspecao']);
                ?>
                Data:  <?= $data->format('d/m/Y'). ' às ' . $data->format('H:i:s')?>
            </p>
        </div>
        <div class="result">
            <figure><img src="../templates/assets/img/check_verde.png" alt=""></figure>
            <div class="text">
                <h3>Todos os EPIs conformes</h3>
                <p>O colaborador está apto para iniciar as atividades com segurança.</p>
            </div>
        </div>
        <div class="buttons">
            <button class="inspecao">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-4 h-4 mr-2"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                Nova inspeção
            </button>
            <button class="voltar">Voltar ao menu principal</button>
        </div>
    </body>
</html>