<?php

/**
 * ficha-risco-api.php
 * -----------------------------------------------------------------
 * Roteador HTTP do módulo PGR. Recebe as requisições do front-end
 * (modulo-pgr.js) via fetch() e direciona para o método correto do
 * FichaRiscoController, seguindo o mesmo padrão de "-api.php" já
 * usado no módulo de provas (prova-api.php).
 *
 * Ações disponíveis:
 *   GET  ?action=listar
 *   GET  ?action=listarNR
 *   GET  ?action=buscar&id_atividade=X
 *   POST ?action=criar                     (corpo em JSON)
 *   POST ?action=atualizar&id_atividade=X  (corpo em JSON)
 *   POST ?action=excluir&id_atividade=X
 */

require_once __DIR__ . '/FichaRiscoController.php';

$controller = new FichaRiscoController();

$metodo = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$corpo  = json_decode(file_get_contents('php://input'), true) ?? [];

switch (true) {

    case $metodo === 'GET' && $action === 'listar':
        $controller->listar();
        break;

    case $metodo === 'GET' && $action === 'listarNR':
        $controller->listarNRs();
        break;

    case $metodo === 'GET' && $action === 'buscar':
        $controller->buscar($_GET['id_atividade'] ?? null);
        break;

    case $metodo === 'POST' && $action === 'criar':
        $controller->criar($corpo);
        break;

    case $metodo === 'POST' && $action === 'atualizar':
        $idAtividade = $_GET['id_atividade'] ?? ($corpo['id_atividade'] ?? null);
        $controller->atualizar($idAtividade, $corpo);
        break;

    case $metodo === 'POST' && $action === 'excluir':
        $idAtividade = $_GET['id_atividade'] ?? ($corpo['id_atividade'] ?? null);
        $controller->excluir($idAtividade);
        break;

    default:
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida.']);
}
?>