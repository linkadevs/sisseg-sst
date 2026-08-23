<?php
/**
 * incidente-api.php
 * Front controller único para o CRUD de Incidentes via AJAX (fetch).
 * Todas as respostas são JSON. Ações via GET (list, getById, counts)
 * ou POST (create, update, updateStatus, delete).
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . "/IncidenteController.php";

use Controller\IncidenteController;

$controller = new IncidenteController();
$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {

        case 'create':
            echo json_encode($controller->create());
            break;

        case 'update':
            echo json_encode($controller->update());
            break;

        case 'updateStatus':
            echo json_encode($controller->updateStatus());
            break;

        case 'delete':
            echo json_encode($controller->delete());
            break;

        case 'list':
            $status = $_GET['status'] ?? null;
            $busca  = $_GET['busca'] ?? null;
            $lista  = $controller->listAll($status, $busca);
            echo json_encode(['success' => true, 'data' => $lista]);
            break;

        case 'getById':
            $id  = $_GET['id'] ?? null;
            $inc = $controller->findById($id);

            if (!$inc) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Incidente não encontrado.']);
                break;
            }

            // converte o BLOB da foto em base64 pro front consumir direto num <img>
            if (!empty($inc['fotos_incidente'])) {
                $inc['foto_base64'] = 'data:image/jpeg;base64,' . base64_encode($inc['fotos_incidente']);
            } else {
                $inc['foto_base64'] = null;
            }
            unset($inc['fotos_incidente']);

            echo json_encode(['success' => true, 'data' => $inc]);
            break;

        case 'counts':
            echo json_encode(['success' => true, 'data' => $controller->getCounts()]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
    }
} catch (\Throwable $e) {
    error_log('Erro incidente-api.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro inesperado no servidor.']);
}
