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
                'Erro ao selecionar todos os funcionarios treinados',
                0,
                $e
            );
        }
    }

    public function selecionarTreinamentosRealizados() :array {
        try {
            return $this->funcionario_treinamento_model->selecionarTreinamentosRealizados();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todos os treinamentos realizados',
                0,
                $e
            );
        }
    }

    public function selecionarTreinamentosRealizadosPorId(
        int $id_treinamento_fk,
        int $id_funcionario_fk
    ) :array {
        try {
            return $this->funcionario_treinamento_model->selecionarTreinamentosRealizadosPorId(
                $id_treinamento_fk,
                $id_funcionario_fk
            );
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar treinamentos realizados por id',
                0,
                $e
            );
        }
    }

    public function selecionarTreinamentosRealizadosFuncionario(
        int $id_funcionario_fk
    ) :array {
        try {
            return $this->funcionario_treinamento_model->selecionarTreinamentosRealizadosFuncionario($id_funcionario_fk);
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar treinamentos realizados por funcionario',
                0,
                $e
            );
        }
    }
}

?>