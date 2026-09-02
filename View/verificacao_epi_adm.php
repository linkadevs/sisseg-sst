<?php

session_start();

use Controller\AtividadeController;
use Controller\AtividadeEpiController;
use Controller\EpiController;
use Controller\NrController;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Controller/EpiController.php';
require_once __DIR__ . '/../Controller/NrController.php';
require_once __DIR__ . '/../Controller/AtividadeEpiController.php';
require_once __DIR__ . '/../Controller/AtividadeController.php';

$epiController = new EpiController();
$nrController = new NrController();
$atividadeEpiController = new AtividadeEpiController();
$atividadeController = new AtividadeController();

$nrs = $nrController->getAllNrs();
$epis = $epiController->get_all_epis();
$total_atividades = count($atividadeController->getAllAtvs());
if(!empty($_GET['search'])){
    $pesquisa = $_GET['search'];
    $atividades = $atividadeController->searchAtvs($pesquisa);
} else {
    $atividades = $atividadeController->getAllAtvs();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(empty($_POST['id_atividade'])) {
        $atividadeEpiController->createAtvEpi(
            $_POST['nome_atividade'],
            $_POST['icone'],
            $_POST['nr'],
            $_POST['epis']
        );
    } else {
        $atividadeEpiController->updateAtvEpi(
            intval($_POST['id_atividade']),
            $_POST['nome_atividade'],
            $_POST['icone'],
            $_POST['nr'],
            $_POST['epis']
        );
    }
    header('Location: verificacao_epi_adm.php');
    exit;
}

$icons = [
    '' => '🔧️',
    'chave_inglesa' => '🔧️',
    'guindaste' => '🏗️',
    'ferramentas' => '🛠️',
    'alta_tensao' => '⚡️',
    'engrenagem' => '⚙️',
    'fogo' => '🔥',
    'escada' => '🪜',
    'trator' => '🚜',
    'caixa_pacote' => '📦',
    'caminhao' => '🚛',
    'deposito_galpao' => '🏬',
    'etiqueta' => '🏷️',
    'colete_seguranca' => '🦺',
    'bota_protecao' => '🥾',
    'oculos_protecao' => '🥽',
    'protetor_auricular' => '🎧',
    'luvas' => '🧤',
    'mascara_protecao' => '😷',
    'corda_no' => '🪢',
    'capacete_obras' => '👷‍♀️',
    'capacete_obras_sol' => '👷‍♂️'
];

if(!empty($_SESSION['message'])) {
    echo '<script>alert("'. $_SESSION['message'] .'")</script>';
    unset($_SESSION['message']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Verificação & EPIs</title>
        <link rel="stylesheet" href="../templates/assets/css/verificacao_epi_adm.css">
    </head>
    <body>
        <div class="sombra"></div>
        <form class="form" method="POST" enctype=multipart/form-data>
            <figure><img src="../templates/assets/img/seta_esquerda.png" alt=""></figure>
            <h2>Criar nova atividade</h2>
            <div class="inputs">
                <div class="input">
                    <label for="nome_atividade">Nome</label>
                    <input type="text" name="nome_atividade" id="nome_atividade" placeholder="Digite o nome da atividade">
                </div>
                <div class="input">
                    <label for="nr">NR</label>
                    <select name="nr" id="nr">
                        <option value="placeholder" selected>Selecione a NR da atividade</option>
                        <?php foreach($nrs as $nr):?>
                            <option value="<?= htmlspecialchars($nr['id_nr'])?>"><?= htmlspecialchars($nr['nome_nr'])?></option>
                        <?php endforeach;?>
                    </select>
                    <!-- <input type="text" name="nr" id="nr" placeholder="Selecione a NR da atividade"> -->
                </div>
                <div class="input">
                    <label for="icone">Ícone</label>
                    <div class="icones">
                        <button class="icone" value="chave_inglesa" type="button">🔧️</button>
                        <button class="icone" value="guindaste" type="button">🏗️</button>
                        <button class="icone" value="ferramentas" type="button">🛠️</button>
                        <button class="icone" value="alta_tensao" type="button">⚡️</button>
                        <button class="icone" value="engrenagem" type="button">⚙️</button>
                        <button class="icone" value="fogo" type="button">🔥</button>
                        <button class="icone" value="escada" type="button">🪜</button>
                        <button class="icone" value="trator" type="button">🚜</button>
                        <button class="icone" value="caixa_pacote" type="button">📦</button>
                        <button class="icone" value="caminhao" type="button">🚛</button>
                        <button class="icone" value="deposito_galpao" type="button">🏬</button>
                        <button class="icone" value="etiqueta" type="button">🏷️</button>
                        <button class="icone" value="colete_seguranca" type="button">🦺</button>
                        <button class="icone" value="bota_protecao" type="button">🥾</button> 
                        <button class="icone" value="oculos_protecao" type="button">🥽</button>
                        <button class="icone" value="protetor_auricular" type="button">🎧</button>
                        <button class="icone" value="luvas" type="button">🧤</button>
                        <button class="icone" value="mascara_protecao" type="button">😷</button>
                        <button class="icone" value="corda_no" type="button">🪢</button>
                        <button class="icone" value="capacete_obras" type="button">👷‍♀️</button>
                        <button class="icone" value="capacete_obras_sol" type="button">👷‍♂️</button>
                    </div>
                </div>
                <input type="hidden" id="icone" name="icone">
                <div class="input">
                    <label for="epi-1">EPI - 1</label>
                    <select name="epis[]" id="epi-1" class="epi">
                        <option value="placeholder" selected>Selecione um EPI</option>
                        <?php foreach($epis as $epi):?>
                            <option value="<?= htmlspecialchars($epi['id_epi'])?>"><?= htmlspecialchars($epi['nome_epi'])?></option>
                        <?php endforeach;?>
                    </select>
                </div>
                <input type="hidden" name="id_atividade" value="">
                <!-- <input type="text" name="epi-1" id="epi-1" placeholder="Insira o nome do EPI 1"> -->
                <!-- <div class="input">
                    <label for="funcao-1">Função - 1</label>
                    <input type="text" name="funcao-1" id="funcao-1" placeholder="Insira a função do EPI 1">
                </div>
                <div class="input">
                    <label for="descricao-1">Descrição - 1</label>
                    <input type="text" name="descricao-1" id="descricao-1" placeholder="Insira a descrição do EPI 1">
                </div>
                <div class="input">
                    <label for="ca-1">CA - 1</label>
                    <input type="text" name="ca-1" id="ca-1" placeholder="Certificado de aprovação do EPI 1">
                </div> -->
            </div>
            <button type="button" class="adicionar_epi form_button">Adicionar EPI</button>
            <button type="submit" class="salvar form_button">Salvar</button>
        </form>
        <header>
            <button class="voltar" onclick="window.location.href = 'principal_adm.php'">
                <figure><img src="../templates/assets/img/seta_esquerda.png" alt=""></figure>
                Voltar
            </button>
        </header>
        <main>
            <div class="upper">
                <h1>Atividades</h1>
                <p class="gerencie">Gerencie as atividades realizadas pelo trabalhador</p>
                <div class="atvs">
                    <p>Total de atividades</p>
                    <h2><?= htmlspecialchars($total_atividades)?></h2>
                </div>
            </div>
            <div class="search_div">
                <div class="search_input">
                    <figure><img src="../templates/assets/img/lupa_azul.png" alt=""></figure>
                    <form class="search_form" method="GET"><input type="text" name="search" id="search" placeholder="Busque por atividades"></form>
                    <a class="limpar" href="verificacao_epi_adm.php"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#6d2dd4"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg></a>
                </div>
                <div class="buttons">
                    <button class="send_search">
                        <figure><img src="../templates/assets/img/lupa.png" alt=""></figure>
                        Buscar
                    </button>
                    <button class="criar_atividade">
                        <figure><img src="../templates/assets/img/mais_branco.png" alt=""></figure>
                        Criar atividade
                    </button>
                </div>
            </div>
            <div class="grid">
                <?php if(empty($atividades)):?>
                    <h2>Nenhuma atividade encontrada</h2>
                <?php else:?>
                    <?php foreach($atividades as $atividade):?>
                        <div class="card">
                            <span><?= htmlspecialchars($icons[$atividade['icone_atividade']])?></span>
                            <div class="title">
                                <h3 class="trabalho"><?= htmlspecialchars($atividade['nome_atividade'])?></h3>
                                <p><?= htmlspecialchars($atividade['nome_nr'])?></p>
                            </div>
                            <?php if(empty($atividade['nome_epi'])):?>
                                <?php $nomes_epi = [];?>
                            <?php else:?>
                                <?php $nomes_epi = explode(', ',$atividade['nome_epi']);?>
                            <?php endif;?>
                            <p class="nEPIs">Nº de EPIs: <?= htmlspecialchars(count($nomes_epi))?></p>
                            <button class="edit" data-id="<?= htmlspecialchars($atividade['id_atividade'])?>" data-name="<?= htmlspecialchars($atividade['nome_atividade'])?>" data-nr="<?= htmlspecialchars($atividade['id_nr'])?>" data-icone="<?= htmlspecialchars($atividade['icone_atividade'])?>" data-epis="<?= htmlspecialchars($atividade['id_epi'])?>">Editar atividade</button>
                        </div>
                    <?php endforeach;?>
                <?php endif;?>
                <!-- <div class="card">
                    <figure><img src="../templates/assets/img/trabalho.png" alt=""></figure>
                    <div class="title">
                        <h3 class="trabalho">Trabalho em altura</h3>
                        <p>NR-35</p>
                    </div>
                    <p class="nEPIs">Nº de EPIs: 5</p>
                    <button>Editar atividade</button>
                </div>
                <div class="card">
                    <figure><img src="../templates/assets/img/trabalho.png" alt=""></figure>
                    <div class="title">
                        <h3 class="trabalho">Trabalho em altura</h3>
                        <p>NR-35</p>
                    </div>
                    <p class="nEPIs">Nº de EPIs: 5</p>
                    <button>Editar atividade</button>
                </div>
                <div class="card">
                    <figure><img src="../templates/assets/img/trabalho.png" alt=""></figure>
                    <div class="title">
                        <h3 class="trabalho">Trabalho em altura</h3>
                        <p>NR-35</p>
                    </div>
                    <p class="nEPIs">Nº de EPIs: 5</p>
                    <button>Editar atividade</button>
                </div>
                <div class="card">
                    <figure><img src="../templates/assets/img/trabalho.png" alt=""></figure>
                    <div class="title">
                        <h3 class="trabalho">Trabalho em altura</h3>
                        <p>NR-35</p>
                    </div>
                    <p class="nEPIs">Nº de EPIs: 5</p>
                    <button>Editar atividade</button>
                </div>
                <div class="card">
                    <figure><img src="../templates/assets/img/trabalho.png" alt=""></figure>
                    <div class="title">
                        <h3 class="trabalho">Trabalho em altura</h3>
                        <p>NR-35</p>
                    </div>
                    <p class="nEPIs">Nº de EPIs: 5</p>
                    <button>Editar atividade</button>
                </div>
                <div class="card">
                    <figure><img src="../templates/assets/img/trabalho.png" alt=""></figure>
                    <div class="title">
                        <h3 class="trabalho">Trabalho em altura</h3>
                        <p>NR-35</p>
                    </div>
                    <p class="nEPIs">Nº de EPIs: 5</p>
                    <button>Editar atividade</button>
                </div>
                <div class="card">
                    <figure><img src="../templates/assets/img/trabalho.png" alt=""></figure>
                    <div class="title">
                        <h3 class="trabalho">Trabalho em altura</h3>
                        <p>NR-35</p>
                    </div>
                    <p class="nEPIs">Nº de EPIs: 5</p>
                    <button>Editar atividade</button>
                </div> -->
            </div>
        </main>
        <script>
            let opcoesEpi = `
                <?php foreach($epis as $epi):?>
                    <option class="epi_option" value="<?= htmlspecialchars($epi['id_epi'])?>"><?= htmlspecialchars($epi['nome_epi'])?></option>
                <?php endforeach;?>
            `
            let opcoesNr = `
                <?php foreach($nrs as $nr):?>
                    <option class="nr_option" value="<?= htmlspecialchars($nr['id_nr'])?>"><?= htmlspecialchars($nr['nome_nr'])?></option>
                <?php endforeach;?>
            `
        </script>
        <script src="../templates/assets/js/verificacao_epi_adm.js"></script>
    </body>
</html>