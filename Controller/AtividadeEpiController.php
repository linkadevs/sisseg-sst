<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Controller/AtividadeController.php';

use Exception;
use Controller\AtividadeController;
use Model\AtividadeEpi;

class AtividadeEpiController {
    private $atividade_epi_model;
    private $atividade_controller;

    public function __construct() {
        $this->atividade_epi_model = new AtividadeEpi();
        $this->atividade_controller = new AtividadeController();
    }

    public function createAtvEpi (
        string $nome_atividade,
        string $icone_atividade,
        int $id_nr_fk,
        array $epis
    ) :bool {
        try {
            if($epis[0] == 'placeholder') {
                $_SESSION['message'] = 'Por favor, selecione ao menos um epi';
                return false;
            }
            $id_atividade = $this->atividade_controller->createAtv(
                $nome_atividade,
                $icone_atividade,
                $id_nr_fk
            );

            foreach ($epis as $epi) {
                $this->atividade_epi_model->createAtvEpi(
                    $id_atividade,
                    intval($epi)
                );
            }

            return true;
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao criar atividade_epi',
                0,
                $e
            );
        }
    }

    public function updateAtvEpi(
        int $id_atividade,
        string $nome_atividade,
        string $icone_atividade,
        int $id_nr_fk,
        array $epis
    ) :bool {
        try {
            $this->atividade_epi_model->deleteAtvEpiByAtvId($id_atividade);
            $this->atividade_controller->updateAtv(
                $id_atividade,
                $nome_atividade,
                $icone_atividade,
                $id_nr_fk
            );
            foreach ($epis as $epi) {
                $this->atividade_epi_model->createAtvEpi(
                    $id_atividade,
                    intval($epi)
                );
            }

            return true;
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao atualizar atividade_epi',
                0,
                $e
            );
        }
    }
}

?>