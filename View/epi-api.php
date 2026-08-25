<?php

header('Content-Type: application/json; charset=utf-8');
session_start();

require_once __DIR__ . '/../Controller/EpiController.php';

use Controller\EpiController;

$controller = new EpiController();
$metodo = $_SERVER['REQUEST_METHOD'];
$acao = $_GET['acao'] ?? '';

function corpoJson()
{
    $corpo = json_decode(file_get_contents('php://input'), true);
    return is_array($corpo) ? $corpo : [];
}

switch (true) {

    case $metodo === 'GET' && $acao === 'listar':
        $controller->listar();
        break;

    case $metodo === 'POST' && $acao === 'criar':
        $controller->criar(corpoJson());
        break;

    case $metodo === 'POST' && $acao === 'atualizar':
        $dados = corpoJson();
        $id_epi = $dados['id_epi'] ?? null;
        if (!$id_epi) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'id_epi é obrigatório.']);
            break;
        }
        $controller->atualizar((int) $id_epi, $dados);
        break;

    case $metodo === 'POST' && $acao === 'ajustar-quantidade':
        $dados = corpoJson();
        $id_epi = $dados['id_epi'] ?? null;
        $delta = isset($dados['delta']) ? (int) $dados['delta'] : 0;
        if (!$id_epi || $delta === 0) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'id_epi e delta são obrigatórios.']);
            break;
        }
        $controller->ajustarQuantidade((int) $id_epi, $delta);
        break;

    case $metodo === 'POST' && $acao === 'excluir':
        $dados = corpoJson();
        $id_epi = $dados['id_epi'] ?? null;
        if (!$id_epi) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'id_epi é obrigatório.']);
            break;
        }
        $controller->excluir((int) $id_epi);
        break;

    default:
        http_response_code(404);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ação não encontrada.']);
}
?>