<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Certificado.php';

use Exception;
use Model\Certificado;

class CertificadoController {
    private $certificadoModel;

    public function __construct() {
        $this->certificadoModel = new Certificado();
    }

    public function selecionarCertificadoPorId(int $id_certificado) :array {
        try {
            return $this->certificadoModel->selecionarCertificadoPorId($id_certificado);
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar certificado por id',
                0,
                $e
            );
        }
    }

    public function selecionarUltimoCertificado(
        int $id_treinamento,
        int $id_funcionario
    ) :array {
        try {
            return $this->certificadoModel->selecionarUltimoCertificado(
                $id_treinamento,
                $id_funcionario
            );
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar ultimo certificado',
                0,
                $e
            );
        }
    }
}