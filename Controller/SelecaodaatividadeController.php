<?php

namespace Controller;
require_once __DIR__ . '/../Model/Selecaodaatividade.php';

use Exception;
use \Model\Selecaodaatividade;

class SelecaodaatividadeController
{
    private $selecaoModel;

    public function __construct()
    {
        $this->selecaoModel = new Selecaodaatividade();
    }

    public function obteratividade()
    {
        try {
            $atividadesinfor = $this->selecaoModel->exibir_atividades();
            return $atividadesinfor;
        } catch (Exception $erro) {
            throw new Exception("Não foi possível obter as atividades: " . $erro->getMessage());
        }
    }
}