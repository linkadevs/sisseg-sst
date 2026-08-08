<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Controller/InspecaoController.php';
require_once __DIR__ . '/../Controller/FuncionarioController.php';
require_once __DIR__ . '/../Controller/FuncaoController.php';

use Controller\InspecaoController;
use Controller\FuncionarioController;
use Controller\FuncaoController;

$inspecao_controller = new InspecaoController();
$funcionario_controller = new FuncionarioController();
$funcao_controller = new FuncaoController();

if(!empty($_SESSION['inspecao_id'])) {
    header('Location: selecao_funcao_colaborador.php');
    exit;
}

$estados = $_SESSION['estados'];

$inspecao = $inspecao_controller->selecionarInspecaoPorId($_SESSION['inspecao_id']);
$funcionario = $funcionario_controller->selecionarFuncionarioPorId($inspecao['id_funcionario_fk']);
$funcao = $funcao_controller->selecionarFuncaoPorId($inspecao['id_funcao_fk']);

$qtd_funcao = count(explode(', ', $funcao['nome_epi']));
$qtd_inspecionado = $inspecao['epis_verificados_inspecao'];
$porcentagem = (round(($qtd_inspecionado/$qtd_funcao)*100));
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
                <?php if($porcentagem<70):?>
                    <svg class="svgnaoconforme" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-triangle-alert w-8 h-8 text-[#F59E0B]"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>
                    <h2>Resultado da inspeção</h2>
                    <p class="naoconforme">CRÍTICO</p>
                <?php elseif($porcentagem<100):?>
                    <svg class="svgparcialmenteconforme" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-triangle-alert w-8 h-8 text-[#F59E0B]" data-fg-dsqk166="61.27:69.10700:node_modules/lucide-react:452:43:18649:52:e:AlertTriangle::::::Ruk" data-fgid-dsqk166=":r29b:"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path><path d="M12 9v4"></path><path d="M12 17h.01"></path></svg>
                    <h2>Resultado da inspeção</h2>
                    <p class="parcialmenteconforme">PENDÊNCIAS</p>
                <?php else:?>
                    <figure><img src="../templates/assets/img/check_verde.png" alt=""></figure>
                    <h2>Resultado da inspeção</h2>
                    <p class="conforme">CONFORME</p>
                <?php endif;?>
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
        <?php if($porcentagem<70):?>
            <div class="resultnaoconforme">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-x w-6 h-6 text-[#DC2626] mt-1" data-fg-dsqk203="61.27:69.10700:node_modules/lucide-react:499:19:20940:51:e:XCircle::::::BEhq" data-fgid-dsqk203=":rl1:"><circle cx="12" cy="12" r="10"></circle><path d="m15 9-6 6"></path><path d="m9 9 6 6"></path></svg>
                <div class="text">
                    <h3>EPIs não conformes</h3>
                    <?php foreach($estados as $estado => $index):?>
                        <div class="epi">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert w-4 h-4 mt-0.5 flex-shrink-0" data-fg-dsqk210="61.27:69.10700:node_modules/lucide-react:505:27:21360:60:e:AlertCircleIcon::::::Dupf" data-fgid-dsqk210=":rl6:"><circle cx="12" cy="12" r="10"></circle><line x1="12" x2="12" y1="8" y2="12"></line><line x1="12" x2="12.01" y1="16" y2="16"></line></svg>
                            <p><strong><?= htmlspecialchars($estado)?></strong> - <?php switch($index) {
                                case 'bom_estado':
                                    echo 'Bom estado';
                                    break;

                                case 'desgastado':
                                    echo 'Desgastado';
                                    break;

                                case 'vencido':
                                    echo 'Vencido';
                                    break;

                                case 'reposicao':
                                    echo 'Reposição';
                                    break;

                                case 'Não entregue':
                                    echo 'Não entregue';
                                    break;
                            }?></p>
                        </div>
                    <?php endforeach;?>
                    <strong>⚠️ Colaborador NÃO PODE iniciar atividades até regularização dos EPIs.</strong>
                </div>
            </div>
        <?php elseif($porcentagem==100):?>
            <div class="resultconforme">
                <figure><img src="../templates/assets/img/check_verde.png" alt=""></figure>
                <div class="text">
                    <h3>Todos os EPIs conformes</h3>
                    <p>O colaborador está apto para iniciar as atividades com segurança.</p>
                </div>
            </div>        
        <?php endif;?>
        <div class="buttons">
            <button class="inspecao">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text w-4 h-4 mr-2"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                Nova inspeção
            </button>
            <button class="voltar">Voltar ao menu principal</button>
        </div>
        <script>
            const inspecao = document.querySelector('.inspecao')
            const voltar = document.querySelector('.voltar')

            inspecao.addEventListener('click', () => {
                window.location.href = 'selecao_funcao_colaborador.php'
            })

            voltar.addEventListener('click', () => {
                window.location.href = ''
            })
        </script>
    </body>
</html>