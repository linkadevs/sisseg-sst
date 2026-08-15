<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Documento.php';

use Exception;
use Model\Documento;

class DocumentoController {
    private $documento_model;

    public function __construct() {
        $this->documento_model = new Documento();
    }

    public function selecionarTodosOsDocumentos() :array {
        try {
            return $this->documento_model->selecionarTodosOsDocumentos();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todos os documentos',
                0,
                $e
            );
        }
    }
}