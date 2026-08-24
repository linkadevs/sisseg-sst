<?php

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../Controller/InspecaoController.php';

use Controller\InspecaoController;

$controller = new InspecaoController();
$metodo = $_SERVER['REQUEST_METHOD'];
$acao = $_GET['acao'] ?? '';
$id_funcionario = $_SESSION['id_funcionario'] ?? null;

switch (true) {

    case $metodo === 'POST' && $acao === 'checkin':
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'multipart/form-data')) {
            // Upload de arquivo (fallback sem câmera)
            $epis_verificados = $_POST['epis_verificados'] ?? 0;
        } else {
            // JSON com foto_base64 (captura pela câmera)
            $corpo = json_decode(file_get_contents('php://input'), true) ?? [];
            $epis_verificados = $corpo['epis_verificados'] ?? 0;
        }
        $controller->realizarCheckin($id_funcionario, $epis_verificados);
        break;

    case $metodo === 'GET' && $acao === 'foto':
        $id_inspecao = $_GET['id_inspecao'] ?? null;
        if (!$id_inspecao) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'id_inspecao é obrigatório.']);
            break;
        }
        $controller->buscarFoto((int) $id_inspecao);
        break;

    default:
        http_response_code(404);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ação não encontrada.']);
}
?>