<?php 

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Indicadores.php';

use Exception;
use Model\Indicadores;

class IndicadoresController {
    private $indicadores_model;

    public function __construct() {
        $this->indicadores_model = new Indicadores();
    }

    public function selecionarTodosIndicadores() {
        try {
            return $this->indicadores_model->selecionarTodosIndicadores();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todos os indicadores',
                0,
                $e
            );
        }
    }
}

?>