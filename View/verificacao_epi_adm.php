<?php

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
$atividades = $atividadeController->getAllAtvs();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(empty($_POST['id_atividade'])) {
        $atividadeEpiController->createAtvEpi(
            $_POST['nome_atividade'],
            file_get_contents($_FILES['foto']['tmp_name']),
            $_POST['nr'],
            $_POST['epis']
        );
        header('Location: verificacao_epi_adm.php');
        exit;
    }
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
        <form method="POST" enctype=multipart/form-data>
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
                    <label for="foto">Foto</label>
                    <button class="foto" type="button" onclick="document.querySelector('#foto').value = ''; document.querySelector('#foto').click()">Selecione a foto da atividade</button>
                </div>
                <input type="file" id="foto" name="foto" accept="image/*">
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
            <button class="voltar">
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
                    <h2><?= htmlspecialchars(count($atividades))?></h2>
                </div>
            </div>
            <div class="search_div">
                <div class="search_input">
                    <figure><img src="../templates/assets/img/lupa_azul.png" alt=""></figure>
                    <input type="text" name="search" id="search" placeholder="Busque por atividades">
                </div>
                <div class="buttons">
                    <button class="criar_atividade">
                        <figure><img src="../templates/assets/img/mais_branco.png" alt=""></figure>
                        Criar atividade
                    </button>
                    <button>
                        <figure><img src="../templates/assets/img/filtro.png" alt=""></figure>
                        Filtrar
                    </button>
                </div>
            </div>
            <div class="grid">
                <div class="card">
                    <figure><img src="../templates/assets/img/trabalho.png" alt=""></figure>
                    <div class="title">
                        <h3 class="trabalho">Trabalho em altura</h3>
                        <p>NR-35</p>
                    </div>
                    <p class="nEPIs">Nº de EPIs: 5</p>
                    <button>Editar atividade</button>
                </div>
                <?php foreach($atividades as $atividade):?>
                    <div class="card">
                        <figure><img src="data:image/jpeg;base64,<?= base64_encode($atividade['foto_atividade'])?>" alt=""></figure>
                        <div class="title">
                            <h3 class="trabalho"><?= htmlspecialchars($atividade['nome_atividade'])?></h3>
                            <p><?= htmlspecialchars($atividade['nome_nr'])?></p>
                        </div>
                        <?php $nomes_epi = explode(', ',$atividade['nome_epi']);?>
                        <p class="nEPIs">Nº de EPIs: <?= htmlspecialchars(count($nomes_epi))?></p>
                        <button>Editar atividade</button>
                    </div>
                <?php endforeach;?>
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
                    <option value="<?= htmlspecialchars($epi['id_epi'])?>"><?= htmlspecialchars($epi['nome_epi'])?></option>
                <?php endforeach;?>
            `
            let opcoesNr = `
                <?php foreach($nrs as $nr):?>
                    <option value="<?= htmlspecialchars($nr['id_nr'])?>"><?= htmlspecialchars($nr['nome_nr'])?></option>
                <?php endforeach;?>
            `
        </script>
        <script src="../templates/assets/js/verificacao_epi_adm.js"></script>
    </body>
</html>