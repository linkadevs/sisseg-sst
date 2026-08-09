<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Incidente.php';

use Exception;
use Model\Incidente;

class IncidenteController {
    private $incidente_model;

    public function __construct() {
        $this->incidente_model = new Incidente();
    }

    public function selecionarTodosOsIncidentes() :array {
        try {
            return $this->incidente_model->SelecionarTodosOsIncidentes();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todos os incidentes',
                0,
                $e
            );
        }
    }
}

?>