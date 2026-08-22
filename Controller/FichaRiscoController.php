<?php

require_once __DIR__ . "/../Model/Connection.php";
require_once __DIR__ . '/../Model/FichaRisco.php';

use Model\FichaRisco;
use PDO;
use PDOException;

/**
 * Controller FichaRiscoController
 * -----------------------------------------------------------------
 * Recebe as requisições do módulo PGR (front-end modulo-pgr.js),
 * valida o payload, delega ao Model FichaRisco e devolve respostas
 * padronizadas em JSON.
 */
class FichaRiscoController {

    private $db;
    private $model;

    public function __construct(){
        try {
            $this->db    = Connection::getInstance();
            $this->model = new FichaRisco();
        } catch (PDOException $e) {
            throw new PDOException('Erro ao inicializar o controller. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    // POST /ficha-risco-api.php?action=criar
    public function criar($payload){
        try {
            $erro = $this->validarPayload($payload);
            if ($erro) return $this->responder(false, $erro, null, 422);

            $ficha = $this->model->criar(
                trim($payload['nomeAtividade']),
                (int)$payload['idNr'],
                $payload['riscos'],
                $payload['medidasColetivas'] ?? [],
                $payload['procedimentos'] ?? []
            );
            return $this->responder(true, 'Ficha de risco criada com sucesso.', $ficha, 201);
        } catch (PDOException $e) {
            return $this->responder(false, 'Erro ao criar ficha de risco. Código PDO: ' . $e->getCode() . '. ' . $e->getMessage(), null, 500);
        } catch (Exception $e) {
            return $this->responder(false, 'Erro ao criar ficha de risco: ' . $e->getMessage(), null, 500);
        }
    }

    // GET /ficha-risco-api.php?action=listarNR
    public function listarNRs(){
        try {
            $nrs = $this->model->listarNRs();
            return $this->responder(true, null, $nrs, 200);
        } catch (PDOException $e) {
            return $this->responder(false, 'Erro ao listar NRs. Código PDO: ' . $e->getCode() . '. ' . $e->getMessage(), null, 500);
        } catch (Exception $e) {
            return $this->responder(false, 'Erro ao listar NRs: ' . $e->getMessage(), null, 500);
        }
    }

    // GET /ficha-risco-api.php?action=listar
    public function listar(){
        try {
            $fichas = $this->model->listarTodas();
            return $this->responder(true, null, $fichas, 200);
        } catch (PDOException $e) {
            return $this->responder(false, 'Erro ao listar fichas de risco. Código PDO: ' . $e->getCode() . '. ' . $e->getMessage(), null, 500);
        } catch (Exception $e) {
            return $this->responder(false, 'Erro ao listar fichas de risco: ' . $e->getMessage(), null, 500);
        }
    }

    // GET /ficha-risco-api.php?action=buscar&id_atividade=X
    public function buscar($idAtividade){
        try {
            if (!$idAtividade) return $this->responder(false, 'id_atividade é obrigatório.', null, 422);

            $ficha = $this->model->buscarPorId((int)$idAtividade);
            if (!$ficha) return $this->responder(false, 'Ficha de risco não encontrada.', null, 404);

            return $this->responder(true, null, $ficha, 200);
        } catch (PDOException $e) {
            return $this->responder(false, 'Erro ao buscar ficha de risco. Código PDO: ' . $e->getCode() . '. ' . $e->getMessage(), null, 500);
        } catch (Exception $e) {
            return $this->responder(false, 'Erro ao buscar ficha de risco: ' . $e->getMessage(), null, 500);
        }
    }

    // POST /ficha-risco-api.php?action=atualizar&id_atividade=X
    public function atualizar($idAtividade, $payload){
        try {
            if (!$idAtividade) return $this->responder(false, 'id_atividade é obrigatório.', null, 422);

            $erro = $this->validarPayload($payload);
            if ($erro) return $this->responder(false, $erro, null, 422);

            $ficha = $this->model->atualizar(
                (int)$idAtividade,
                trim($payload['nomeAtividade']),
                $payload['riscos'],
                $payload['medidasColetivas'] ?? [],
                $payload['procedimentos'] ?? [],
                isset($payload['idNr']) ? (int)$payload['idNr'] : null
            );
            return $this->responder(true, 'Ficha de risco atualizada com sucesso.', $ficha, 200);
        } catch (PDOException $e) {
            return $this->responder(false, 'Erro ao atualizar ficha de risco. Código PDO: ' . $e->getCode() . '. ' . $e->getMessage(), null, 500);
        } catch (Exception $e) {
            return $this->responder(false, 'Erro ao atualizar ficha de risco: ' . $e->getMessage(), null, 500);
        }
    }

    // POST /ficha-risco-api.php?action=excluir&id_atividade=X
    public function excluir($idAtividade){
        try {
            if (!$idAtividade) return $this->responder(false, 'id_atividade é obrigatório.', null, 422);

            $this->model->excluir((int)$idAtividade);
            return $this->responder(true, 'Ficha de risco excluída com sucesso.', null, 200);
        } catch (PDOException $e) {
            return $this->responder(false, 'Erro ao excluir ficha de risco. Código PDO: ' . $e->getCode() . '. ' . $e->getMessage(), null, 500);
        } catch (Exception $e) {
            return $this->responder(false, 'Erro ao excluir ficha de risco: ' . $e->getMessage(), null, 500);
        }
    }

    // ------------------------------------------------------------
    // Validação das credenciais exigidas pelo formulário
    // ------------------------------------------------------------
    private function validarPayload($payload){
        try {
            if (empty($payload['nomeAtividade']) || !trim($payload['nomeAtividade'])) {
                return 'O nome da atividade é obrigatório.';
            }
            if (empty($payload['idNr'])) {
                return 'A NR (Norma Regulamentadora) é obrigatória.';
            }
            if (empty($payload['riscos']) || !is_array($payload['riscos'])) {
                return 'É necessário informar ao menos um risco.';
            }
            foreach ($payload['riscos'] as $r) {
                if (empty($r['tipo']) || empty($r['descricao']) ||
                    !isset($r['probabilidade']) || !isset($r['severidade'])) {
                    return 'Cada risco deve conter tipo, descrição, probabilidade e severidade.';
                }
                if ((int)$r['probabilidade'] < 1 || (int)$r['probabilidade'] > 5 ||
                    (int)$r['severidade'] < 1 || (int)$r['severidade'] > 5) {
                    return 'Probabilidade e severidade devem estar entre 1 e 5.';
                }
            }
            return null;
        } catch (PDOException $e) {
            throw new PDOException('Erro ao validar payload. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    private function responder($sucesso, $mensagem, $dados, $status){
        try {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'sucesso'  => $sucesso,
                'mensagem' => $mensagem,
                'dados'    => $dados
            ], JSON_UNESCAPED_UNICODE);
            exit;
        } catch (PDOException $e) {
            throw new PDOException('Erro ao gerar resposta. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }
}
?>