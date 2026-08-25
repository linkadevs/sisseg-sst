<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Auditoria.php';

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

use Exception;
use Model\Auditoria;

class AuditoriaController {
    private $auditoria_model;

    public function __construct() {
        $this->auditoria_model = new Auditoria();
    }

    public function selecionarTodasAsAuditorias() :array {
        try {
            return $this->auditoria_model->selecionarTodasAsAuditorias();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todas as auditorias',
                0,
                $e
            );
        }
    }

    public function criarNovaAuditoria(
        string $nome_auditoria,
        string $auditor_auditoria,
        string $data_auditoria,
        string $status_auditoria
    ) :bool {
        try {
            $result = $this->auditoria_model->criarNovaAuditoria(
                $nome_auditoria,
                $auditor_auditoria,
                $data_auditoria,
                $status_auditoria
            );
            if($result == true){
                $_SESSION['message'] = 'Auditoria marcada com sucesso';
            }
            return $result;
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao criar nova auditoria',
                0,
                $e
            );
        }
    }

    public function atualizarAuditoria(
        int $id_auditoria,
        string $nome_auditoria,
        string $auditor_auditoria,
        string $data_auditoria,
        string $status_auditoria
    ) :bool {
        try {
            $result = $this->auditoria_model->atualizarAuditoria(
                $id_auditoria,
                $nome_auditoria,
                $auditor_auditoria,
                $data_auditoria,
                $status_auditoria
            );
            if($result == true){
                $_SESSION['message'] = 'Auditoria atualizada com sucesso';
            }
            return $result;
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao atualizar auditoria',
                0,
                $e
            );
        }
    }

    public function deletarAuditoria(
        int $id_auditoria
    ) :bool {
        try {
            $result = $this->auditoria_model->deletarAuditoria(
                $id_auditoria
            );
            if($result == true){
                $_SESSION['message'] = 'Auditoria cancelada com sucesso';
            }
            return $result;
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao deletar auditoria',
                0,
                $e
            );
        }
    }
}