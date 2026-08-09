<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/FuncionarioTreinamento.php';

use Exception;
use Model\FuncionarioTreinamento;

class FuncionarioTreinamentoController {
    private $funcionario_treinamento_model;

    public function __construct() {
        $this->funcionario_treinamento_model = new FuncionarioTreinamento();
    }

    public function selecionarFuncionariosTreinados() :array {
        try {
            return $this->funcionario_treinamento_model->selecionarFuncionariosTreinados();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todos os treinamentos realizados',
                0,
                $e
            );
        }
    }
}

?>