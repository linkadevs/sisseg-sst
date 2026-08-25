<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id_atividade = isset($_POST['id_atividade']) ? (int)$_POST['id_atividade'] : 0;
    $nome_atividade = isset($_POST['nome_atividade']) ? $_POST['nome_atividade'] : '';
    $nome_nr = isset($_POST['nome_nr']) ? $_POST['nome_nr'] : '';
    $id_nr_fk = isset($_POST['id_nr_fk']) ? (int)$_POST['id_nr_fk'] : 0;
    $quantidade_epis = isset($_POST['quantidade_epis']) ? (int)$_POST['quantidade_epis'] : 0;
    
    $_SESSION['id_atividade_modulo'] = $id_atividade;
    $_SESSION['nome_atividade_modulo'] = $nome_atividade;
    $_SESSION['nr_atividade_modulo'] = $nome_nr;
    $_SESSION['idnr_atividade_modulo'] = $id_nr_fk;
    $_SESSION['qtdepis_atividade_modulo'] = $quantidade_epis;
    
    if ($id_atividade > 0) {
        require_once __DIR__ . '/../Controller/ModuloVerificacaoeEpiController.php';
        $controller = new Controller\ModuloVerificacaoeEpiController();
        $atividades = $controller->obteratividade();
        
        foreach ($atividades as $atividade) {
            if ($atividade['id_atividade'] == $id_atividade) {
                $_SESSION['icone_atividade_modulo'] = $atividade['icone_atividade'] ?? '';
                break;
            }
        }
    }
    
    header('Location: confirmacao_epis.php');
    exit;
    
} else {
    header('Location: Selecao_da_atividade.php');
    exit;
}
?>