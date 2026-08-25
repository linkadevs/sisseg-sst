<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Atividade.php';

use Exception;
use Model\Atividade;
use Model\Connection;

class AtividadeController {
    private $atividadeModel;

    public function __construct() {
        $this->atividadeModel = new Atividade();
    }

    public function selecionarTodasAsAtividades() :array {
        try {
            return $this->atividadeModel->selecionarTodasAsAtividades();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todas as atividades',
                0,
                $e
            );
        }
    }
}