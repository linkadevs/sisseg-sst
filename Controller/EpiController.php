<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Epi.php';

use Model\Epi;
use Exception;

class EpiController {
    private $epiModel;

    public function __construct() {
        $this->epiModel = new Epi();
    }

    public function create_epi(
        string $nome_epi,
        string $descricao_epi,
        string $funcao_epi,
        string $ca_epi,
        int $qtd_epi,
        int $qtd_minima_epi,
        string $status_epi
    ) :bool {
        try {
            $nome_epi = trim($nome_epi);
            $descricao_epi = trim($descricao_epi);
            $funcao_epi = trim($funcao_epi);
            $ca_epi = trim($ca_epi);
            $status_epi = trim($status_epi);
            
            return $this->epiModel->create_epi(
                $nome_epi,
                $descricao_epi,
                $funcao_epi,
                $ca_epi,
                $qtd_epi,
                $qtd_minima_epi,
                $status_epi
            );
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao criar epi',
                0,
                $e
            );
        }
    }
    public function get_all_epis() {
        try {
            return $this->epiModel->get_all_epis();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todos os EPIs',
                0,
                $e
            );
        }
    }
}

?>