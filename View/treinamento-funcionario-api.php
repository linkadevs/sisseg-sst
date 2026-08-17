<?php
require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../Controller/TreinamentoController.php');
use Controller\TreinamentoController;

session_start();

// TODO: substituir por sessão real quando o login de funcionário for implementado.
if (!isset($_SESSION['id_funcionario'])) {
    $_SESSION['id_funcionario'] = 1;
}

header('Content-Type: application/json; charset=utf-8');

$controller = new TreinamentoController();
$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

switch ($acao) {
    case 'listar':
        // NOTA (Pedro): este método ainda não existe no TreinamentoController que você
        // me passou — só o listAll() genérico do painel admin. Precisa de um método
        // que, para cada treinamento, faça LEFT JOIN com funcionario_treinamento
        // (filtrando id_funcionario_fk = sessão) e devolva também:
        //   - data_conclusao   -> funcionario_treinamento.data_funcionario_treinamento (ou null)
        //   - informativo      -> true quando nr_treinamento in ('NR-01','PGR','PCMSO')
        //   - status           -> 'valido' quando concluído E (sem validade OU data_limite >= hoje)
        //                         'invalido' nos demais casos
        echo json_encode([
            'success' => true,
            'data' => $controller->listForFuncionario($_SESSION['id_funcionario'])
        ]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ação inválida.']);
        break;
}
