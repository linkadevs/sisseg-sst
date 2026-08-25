<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Documento.php';

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    public function criarNovoDocumento(
        string $nome_documento,
        string $data_documento,
        string $status_documento,
        string $arquivo_documento
    ) :bool {
        try {
            $result = $this->documento_model->criarNovoDocumento(
                $nome_documento,
                $data_documento,
                $status_documento,
                $arquivo_documento
            );
            if($result == true){
                $_SESSION['message'] = 'Documento criado com sucesso';
            }
            return $result; 
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao criar novo documento',
                0,
                $e
            );
        }
    }

    public function atualizarDocumento(
        int $id_documento,
        string $nome_documento,
        string $data_documento,
        string $status_documento,
        string $arquivo_documento
    ) :bool {
        try {
            $result = $this->documento_model->atualizarDocumento(
                $id_documento,
                $nome_documento,
                $data_documento,
                $status_documento,
                $arquivo_documento
            );
            if($result == true){
                $_SESSION['message'] = 'Documento atualizado com suceso';
            }
            return $result;
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao atualizar documento',
                0,
                $e
            );
        }
    }

    public function atualizarDocumentoSemArquivo(
        int $id_documento,
        string $nome_documento,
        string $data_documento,
        string $status_documento
    ) :bool {
        try {
            $result = $this->documento_model->atualizarDocumentoSemArquivo(
                $id_documento,
                $nome_documento,
                $data_documento,
                $status_documento
            );
            if($result == true){
                $_SESSION['message'] = 'Documento atualizado com sucesso';
            }
            return $result;
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao atualizar documento',
                0,
                $e
            );
        }
    }

    public function deletarDocumento(
        int $id_documento
    ) :bool {
        try {
            $result = $this->documento_model->deletarDocumento(
                $id_documento
            );
            if($result == true){
                $_SESSION['message'] = 'Documento apagado com sucesso';
            }
            return $result;
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao deletar documento',
                0,
                $e
            );
        }
    }
}