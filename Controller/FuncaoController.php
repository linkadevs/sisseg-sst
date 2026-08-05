<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Funcao.php';

use Exception;
use Model\Funcao;

class FuncaoController {
    private $funcaoModel;

    public function __construct() {
        $this->funcaoModel = new Funcao();
    }

    public function selecionarTodasFuncoes() :array {
        try{
            return $this->funcaoModel->selecionarTodasFuncoes();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todas as funcoes',
                0,
                $e
            );
        }
    }

    public function selecionarFuncaoPorId(
        int $id_funcao
    ) :array {
        try{
            return $this->funcaoModel->selecionarFuncaoPorId(
                $id_funcao
            );
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar função por id',
                0,
                $e
            );
        }
    }
}

?>