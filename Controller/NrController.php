<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Nr.php';

use Exception;
use Model\Nr;

class NrController {
    private $nr_model;

    public function __construct() {
        $this->nr_model = new Nr;
    }

    public function getAllNrs() :array {
        try {
            return $this->nr_model->getAllNrs();
        } catch (Exception $e){
            throw new Exception(
                'Erro ao selecionar todas as nrs',
                0,
                $e
            );
        }
    }
}

?>