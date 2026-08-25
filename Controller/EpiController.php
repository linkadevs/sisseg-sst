<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Epi.php';

use Model\Epi;
use Exception;

class EpiController {
    private $epiModel;

    public function __construct() {
        $this->epiModel = new Epi();
    }

    public function create_epi(
        string $nome_epi,
        string $descricao_epi,
        string $funcao_epi,
        string $ca_epi,
        int $qtd_epi,
        int $qtd_minima_epi,
        string $status_epi
    ) :bool {
        try {
            $nome_epi = trim($nome_epi);
            $descricao_epi = trim($descricao_epi);
            $funcao_epi = trim($funcao_epi);
            $ca_epi = trim($ca_epi);
            $status_epi = trim($status_epi);
            
            return $this->epiModel->create_epi(
                $nome_epi,
                $descricao_epi,
                $funcao_epi,
                $ca_epi,
                $qtd_epi,
                $qtd_minima_epi,
                $status_epi
            );
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao criar epi',
                0,
                $e
            );
        }
    }
    public function get_all_epis() {
        try {
            return $this->epiModel->get_all_epis();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todos os EPIs',
                0,
                $e
            );
        }
    }

    public function listar()
    {
        try {
            $epis = $this->epiModel->listar();
            echo json_encode(['sucesso' => true, 'epis' => $epis]);
        } catch (\PDOException $e) {
            error_log('[Controller\EpiController::listar] ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao consultar o estoque de EPIs.']);
        }
    }

    public function criar(array $dados)
    {
        [$valido, $nome, $qtd, $minimo, $erro] = $this->validar($dados);
        if (!$valido) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => $erro]);
            return;
        }

        try {
            $status = $this->epiModel->calcularStatus($qtd, $minimo);

            $id_epi = $this->epiModel->criar([
                'nome_epi'       => $nome,
                'descricao_epi'  => trim($dados['descricao_epi'] ?? ''),
                'funcao_epi'     => trim($dados['funcao_epi'] ?? ''),
                'ca_epi'         => trim($dados['ca_epi'] ?? ''),
                'qtd_minima_epi' => $minimo,
                'qtd_epi'        => $qtd,
                'status_epi'     => $status,
            ]);

            $epi = $this->epiModel->buscarPorId($id_epi);
            echo json_encode(['sucesso' => true, 'epi' => $epi]);
        } catch (\PDOException $e) {
            error_log('[Controller\EpiController::criar] ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao criar o EPI.']);
        }
    }

    public function atualizar($id_epi, array $dados)
    {
        try {
            $epiExistente = $this->epiModel->buscarPorId($id_epi);
        } catch (\PDOException $e) {
            error_log('[Controller\EpiController::atualizar - buscarPorId] ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao buscar o EPI.']);
            return;
        }

        if (!$epiExistente) {
            http_response_code(404);
            echo json_encode(['sucesso' => false, 'mensagem' => 'EPI não encontrado.']);
            return;
        }

        [$valido, $nome, $qtd, $minimo, $erro] = $this->validar($dados);
        if (!$valido) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => $erro]);
            return;
        }

        try {
            $status = $this->epiModel->calcularStatus($qtd, $minimo);

            $this->epiModel->atualizar($id_epi, [
                'nome_epi'       => $nome,
                'descricao_epi'  => trim($dados['descricao_epi'] ?? $epiExistente['descricao_epi']),
                'funcao_epi'     => trim($dados['funcao_epi'] ?? $epiExistente['funcao_epi']),
                'ca_epi'         => trim($dados['ca_epi'] ?? $epiExistente['ca_epi']),
                'qtd_minima_epi' => $minimo,
                'qtd_epi'        => $qtd,
                'status_epi'     => $status,
            ]);

            $epi = $this->epiModel->buscarPorId($id_epi);
            echo json_encode(['sucesso' => true, 'epi' => $epi]);
        } catch (\PDOException $e) {
            error_log('[Controller\EpiController::atualizar] ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao atualizar o EPI.']);
        }
    }

    public function ajustarQuantidade($id_epi, $delta)
    {
        try {
            $epi = $this->epiModel->buscarPorId($id_epi);
            if (!$epi) {
                http_response_code(404);
                echo json_encode(['sucesso' => false, 'mensagem' => 'EPI não encontrado.']);
                return;
            }

            $novaQtd = max(0, (int) $epi['qtd_epi'] + (int) $delta);
            $status = $this->epiModel->calcularStatus($novaQtd, (int) $epi['qtd_minima_epi']);

            $this->epiModel->atualizarQuantidade($id_epi, $novaQtd, $status);

            echo json_encode(['sucesso' => true, 'qtd_epi' => $novaQtd, 'status_epi' => $status]);
        } catch (\PDOException $e) {
            error_log('[Controller\EpiController::ajustarQuantidade] ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao ajustar a quantidade do EPI.']);
        }
    }

    public function excluir($id_epi)
    {
        try {
            $epi = $this->epiModel->buscarPorId($id_epi);
            if (!$epi) {
                http_response_code(404);
                echo json_encode(['sucesso' => false, 'mensagem' => 'EPI não encontrado.']);
                return;
            }

            $this->epiModel->excluir($id_epi);
            echo json_encode(['sucesso' => true]);
        } catch (\PDOException $e) {
            error_log('[Controller\EpiController::excluir] ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao excluir o EPI.']);
        }
    }

    private function validar(array $dados)
    {
        $nome = trim($dados['nome_epi'] ?? '');
        $qtd = isset($dados['qtd_epi']) && $dados['qtd_epi'] !== '' ? (int) $dados['qtd_epi'] : null;
        $minimo = isset($dados['qtd_minima_epi']) && $dados['qtd_minima_epi'] !== '' ? (int) $dados['qtd_minima_epi'] : null;

        if ($nome === '') {
            return [false, $nome, $qtd, $minimo, 'O nome do EPI é obrigatório.'];
        }
        if ($qtd === null || $qtd < 0) {
            return [false, $nome, $qtd, $minimo, 'A quantidade disponível deve ser um número válido.'];
        }
        if ($minimo === null || $minimo < 0) {
            return [false, $nome, $qtd, $minimo, 'A quantidade mínima deve ser um número válido.'];
        }

        return [true, $nome, $qtd, $minimo, null];
    }
}

?>