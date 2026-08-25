<?php
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../Controller/TreinamentoController.php');
use Controller\TreinamentoController;

session_start();

// TODO: remover quando o login de administrador existir de verdade.
if (!isset($_SESSION['id_adm'])) {
    $_SESSION['id_adm'] = 1;
}

header('Content-Type: application/json; charset=utf-8');

$controller = new TreinamentoController();
$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

switch ($acao) {
    case 'listar':
        echo json_encode(['success' => true, 'data' => $controller->listAll()]);
        break;

    case 'criar':
        echo json_encode($controller->create());
        break;

    case 'editar':
        echo json_encode($controller->update());
        break;

    case 'excluir':
        echo json_encode($controller->delete());
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
        break;
}
?>