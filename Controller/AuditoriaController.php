<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Auditoria.php';

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
}