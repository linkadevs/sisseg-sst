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

    public function getAllAtvs() :array {
        try {
            return $this->atividadeModel->getAllAtvs();
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
            return $this->atividadeModel->createAtv(
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
            return $this->atividadeModel->updateAtv(
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
                return $this->atividadeModel->searchAtvs($pesquisa);
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