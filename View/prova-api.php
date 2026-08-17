<?php
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../Controller/ProvaController.php');

use Controller\ProvaController;

session_start();

// TODO: substituir por sessão real quando o login de funcionário for implementado.
if (!isset($_SESSION['id_funcionario'])) {
    $_SESSION['id_funcionario'] = 1;
}

header('Content-Type: application/json; charset=utf-8');

// Ação pode vir por querystring (?acao=buscar) ou dentro do corpo JSON.
$acao = $_GET['acao'] ?? null;

$corpoBruto = file_get_contents('php://input');
$corpo = json_decode($corpoBruto, true);
$dados = is_array($corpo) ? $corpo : $_POST;

// GET (ex.: acao=buscar&id_treinamento=5) não manda corpo — mescla a querystring.
$dados = array_merge($_GET, $dados);

// O id do funcionário sempre vem da sessão, nunca do payload enviado pelo
// front (evita que alguém registre resultado em nome de outro funcionário).
$dados['id_funcionario'] = $_SESSION['id_funcionario'];

$acao = $acao ?? ($dados['acao'] ?? null);

if (!$acao) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ação não informada.']);
    exit;
}

$controller = new ProvaController();
$resultado = $controller->handle($acao, $dados);

if (!($resultado['success'] ?? false)) {
    http_response_code(400);
}

echo json_encode($resultado);
