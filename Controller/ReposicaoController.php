<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Reposicao.php';

use DateTime;
use DateTimeZone;
use Exception;
use Model\Reposicao;

class ReposicaoController {
    private $reposicaoModel;

    public function __construct() {
        $this->reposicaoModel = new Reposicao();
    }

    public function criarReposicao(
        int $id_epi_fk,
        int $id_funcionario_fk
    ) :bool {
        try {
            $data = new DateTime('today', new DateTimeZone('America/Sao_Paulo'));
            return $this->reposicaoModel->criarReposicao(
                $data->format('Y-m-d'),
                $id_epi_fk,
                $id_funcionario_fk
            );
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao criar reposição',
                0,
                $e
            );
        }
    }
}