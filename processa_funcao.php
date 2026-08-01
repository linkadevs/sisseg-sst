<?php
session_start();
require_once __DIR__ . '/Controller/ModuloInspecaoController.php';

$controller = new Controller\ModuloInspecaoController();

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'criar':
        $result = $controller->criarFuncao();
        $_SESSION['mensagem'] = $result['message'];
        $_SESSION['mensagem_tipo'] = $result['success'] ? 'sucesso' : 'erro';
        break;
        
    case 'atualizar':
        $id = $_POST['id_funcao'] ?? 0;
        $result = $controller->atualizarFuncao($id);
        $_SESSION['mensagem'] = $result['message'];
        $_SESSION['mensagem_tipo'] = $result['success'] ? 'sucesso' : 'erro';
        break;
        
    case 'deletar':
        $id = $_POST['id_funcao'] ?? 0;
        $result = $controller->deletarFuncao($id);
        $_SESSION['mensagem'] = $result['message'];
        $_SESSION['mensagem_tipo'] = $result['success'] ? 'sucesso' : 'erro';
        break;
}

header('Location: View/modulo_funcoes.php');
exit;