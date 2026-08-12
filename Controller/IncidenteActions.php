<?php
/**
 * Endpoint JSON para o módulo de incidentes.
 *
 * ATENÇÃO: eu não tenho acesso ao seu index.php/router nem ao arquivo
 * de sessão do projeto, então montei este endpoint como um arquivo
 * PHP simples e autocontido, no padrão de "actions" separadas por
 * arquivo. Se o seu projeto já usa um router central (tipo
 * index.php?rota=...), me manda esse arquivo que eu adapto certinho.
 *
 * A checagem de sessão abaixo usa `idAdm`, que é o nome de variável
 * de sessão que aparece nos seus outros arquivos (perfil-funcionario,
 * etc.) — ajuste se o nome for outro.
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../Controller/IncidenteController.php';

use Controller\IncidenteController;

if (!isset($_SESSION['idAdm'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sessão expirada. Faça login novamente.']);
    exit;
}

$controller = new IncidenteController();
$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

switch ($acao) {

    case 'criar':
        echo json_encode($controller->create());
        break;

    case 'atualizar':
        echo json_encode($controller->update());
        break;

    case 'atualizar_status':
        echo json_encode($controller->updateStatus());
        break;

    case 'excluir':
        echo json_encode($controller->delete());
        break;

    case 'listar':
        $status = $_GET['status'] ?? null;
        $busca  = $_GET['busca'] ?? null;
        echo json_encode([
            'success'     => true,
            'incidentes'  => $controller->listAll($status, $busca),
            'contadores'  => $controller->getCounts()
        ]);
        break;

    case 'detalhe':
        $id = $_GET['id'] ?? null;
        $incidente = $controller->findById($id);
        echo json_encode(['success' => (bool) $incidente, 'incidente' => $incidente]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
}
