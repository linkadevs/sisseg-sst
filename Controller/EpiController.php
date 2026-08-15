<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Epi.php';

use Exception;
use Model\Epi;

class EpiController {
    private $epi_model;

    public function __construct() {
        $this->epi_model = new Epi();
    }

    public function selecionarTodosOsEpis() :array {
        try {
            return $this->epi_model->selecionarTodosOsEpis();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todos os epis',
                0,
                $e
            );
        }
    }
}

?>