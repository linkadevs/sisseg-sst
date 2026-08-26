<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Controller/FuncaoController.php';
require_once __DIR__ . '/../Controller/FuncionarioController.php';

use Controller\FuncaoController;
use Controller\FuncionarioController;

$funcaoController = new FuncaoController();
$funcionarioController = new FuncionarioController();

if(empty($_GET['id_funcao']) || !is_numeric($_GET['id_funcao'])) {
    header('Location: selecao_funcao_colaborador.php');
    exit;
}

$id_funcionario = $_SESSION['id_funcionario'];

$id_funcao = intval($_GET['id_funcao']);

$funcao = $funcaoController->selecionarFuncaoPorId($id_funcao);
$funcionarios = $funcionarioController->selecionarTodosOsFuncionarios();

$epis = explode(', ', $funcao['nome_epi']);
$id_epis = explode(', ', $funcao['id_epi']);

$epis_array = [];

foreach($epis as $index => $epi) {
    $epis_array[$id_epis[$index]] = $epi;
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!empty($_POST['nome']) && !empty($_POST['setor'])) {
        $_SESSION['funcionario'] = $funcionarioController->selecionarFuncionarioPorId($_POST['nome']);
        $_SESSION['setor'] = $_POST['setor'];
        $_SESSION['epis'] = $epis_array;
        $_SESSION['funcao'] = $funcao['nome_funcao'];
        $_SESSION['id_funcao'] = $id_funcao;
        header('Location: salvar_inspecao.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../templates/assets/css/selecao_dados_colaborador.css">
        <!-- <link rel="shortcut icon" href="../templates/assets/img/favicon.ico" type="image/x-icon"> -->
        <title>Inspeção de EPI</title>
    </head>
    <body>
        <header>
            <button class="voltarBtn" onclick="window.location.href = 'selecao_funcao_colaborador.php'">
                <figure>
                    <img src="../templates/assets/img/seta_esquerda.png" alt="Black left-pointing arrow on white background, indicating a back or previous navigation action">
                </figure>
                Voltar
            </button>
        </header>
        <main>
            <form method="POST">
                <div class="titles">
                    <h2>Dados do colaborador</h2>
                    <h4>Função: <strong><?= htmlspecialchars($funcao['nome_funcao'])?></strong></h4>
                </div>
                <div class="content">
                    <div class="input">
                        <label for="nome">Nome completo do colaborador</label>
                        <select name="nome" id="nome">
                            <option value="" disabled selected hidden>Selecione o funcionário</option>
                            <?php foreach($funcionarios as $funcionario):?>
                                <option value="<?= htmlspecialchars($funcionario['id_funcionario'])?>"><?= htmlspecialchars($funcionario['nome_funcionario'])?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="input">
                        <label for="setor">Setor / Frente de serviço</label>
                        <input name="setor" id="setor" type="text" placeholder="Ex: Pavimento 5, subsolo, fachada">
                    </div>
                    <div class="epis">
                        <h5>EPIs a serem inspecionados:</h5>
                        <ul>
                            <?php foreach($epis as $epi):?>
                                <li><?= htmlspecialchars($epi)?></li>
                            <?php endforeach;?>
                        </ul>
                        <p>* EPIs obrigatórios</p>
                    </div>
                    <button type="submit">Iniciar inspeção</button>
                </div>
            </form>
        </main>
    </body>
</html>