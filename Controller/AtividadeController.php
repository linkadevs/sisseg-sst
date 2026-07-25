<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Atividade.php';

use Exception;
use Model\Atividade;

class AtividadeController {
    private $atividade_model;

    public function __construct() {
        $this->atividade_model = new Atividade();
    }

    public function getAllAtvs() :array {
        try {
            return $this->atividade_model->getAllAtvs();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todas as atividades',
                0,
                $e
            );
        }
    }

    public function createAtv (
        string $nome_atividade,
        string $foto_atividade,
        int $id_nr_fk
    ) :int {
        try {
            return $this->atividade_model->createAtv(
                $nome_atividade,
                $foto_atividade,
                $id_nr_fk
            );
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao criar atividade',
                0,
                $e
            );
        }
    }
}

?>