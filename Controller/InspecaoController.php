<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Inspecao.php';
require_once __DIR__ . '/../Controller/FuncaoController.php';

use Exception;
use Model\Inspecao;
use Controller\FuncaoController;
use DateTime;
use DateTimeZone;

class InspecaoController {
    private $inspecao_model, $funcao_controller;

    public function __construct() {
        $this->inspecao_model = new Inspecao();
        $this->funcao_controller = new FuncaoController();
    }

    public function criarInspecao(
        int $epis_verificados_inspecao,
        int $id_funcionario_fk,
        int $id_funcao_fk,
        string $foto_inspecao
    ) :int {
        try {
            $funcao = $this->funcao_controller->selecionarFuncaoPorId($id_funcao_fk);
            $epis = explode(', ', $funcao['nome_epi']);
            $qtd_epis = count($epis);
            $data_hoje = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
            if($epis_verificados_inspecao === $qtd_epis) {
                $status_inspecao = 'Liberado';
            } else {
                $status_inspecao = 'Recusado';
            }
            return $this->inspecao_model->criarInspecao(
                $data_hoje->format('Y-m-d H:i:s'),
                $epis_verificados_inspecao,
                $status_inspecao,
                $foto_inspecao,
                $id_funcionario_fk,
                $id_funcao_fk
            );
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao criar inspeção',
                0,
                $e
            );
        }
    }

    public function selecionarInspecaoPorId(int $id_inspecao) :array {
        try {
            return $this->inspecao_model->selecionarInspecaoPorId($id_inspecao);
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar inspeção por id',
                0,
                $e
            );
        }
    }
}

?>