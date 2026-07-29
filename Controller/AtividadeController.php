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
        string $icone_atividade,
        int $id_nr_fk
    ) :int {
        try {
            return $this->atividade_model->createAtv(
                $nome_atividade,
                $icone_atividade,
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

    public function updateAtv(
        int $id_atividade,
        string $nome_atividade,
        string $icone_atividade,
        int $id_nr_fk
    ) :bool {
        try {
            return $this->atividade_model->updateAtv(
                $id_atividade,
                $nome_atividade,
                $icone_atividade,
                $id_nr_fk
            );
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao atualizar atividade',
                0,
                $e
            );
        }
    }
    public function searchAtvs($pesquisa) :array {
        try {
            if(empty($pesquisa)) {
                return $this->getAllAtvs();
            } else {
                return $this->atividade_model->searchAtvs($pesquisa);
            }
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao pesquisar atividades',
                0,
                $e
            );
        }
    }
}

?>