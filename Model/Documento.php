<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class Documento {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function selecionarTodosOsDocumentos() :array {
        try {
            $sql = 'SELECT * FROM documento';
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
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
        string $atualizacao_documento,
        string $status_documento,
        string $arquivo_documento
    ) :bool {
        try {
            $sql = 'INSERT INTO documento (nome_documento, data_documento, atualizacao_documento, status_documento, arquivo_documento) VALUES (:nome_documento, :data_documento, :atualizacao_documento, :status_documento, :arquivo_documento)';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nome_documento' => $nome_documento,
                ':data_documento' => $data_documento,
                ':atualizacao_documento' => $atualizacao_documento,
                ':status_documento' => $status_documento,
                ':arquivo_documento' => $arquivo_documento
            ]);
        } catch (PDOException $e) {
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
        string $atualizacao_documento,
        string $status_documento,
        string $arquivo_documento
    ) :bool {
        try {
            $sql = 'UPDATE documento SET
                nome_documento = :nome_documento,
                data_documento = :data_documento,
                atualizacao_documento = :atualizacao_documento,
                status_documento = :status_documento,
                arquivo_documento = :arquivo_documento
            WHERE
                id_documento = :id_documento
            
            ';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_documento' => $id_documento,
                ':nome_documento' => $nome_documento,
                ':data_documento' => $data_documento,
                ':atualizacao_documento' => $atualizacao_documento,
                ':status_documento' => $status_documento,
                ':arquivo_documento' => $arquivo_documento
            ]);
        } catch (PDOException $e) {
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
        string $atualizacao_documento,
        string $status_documento
    ) :bool {
        try {
            $sql = 'UPDATE documento SET
                nome_documento = :nome_documento,
                data_documento = :data_documento,
                atualizacao_documento = :atualizacao_documento,
                status_documento = :status_documento
            WHERE
                id_documento = :id_documento
            
            ';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_documento' => $id_documento,
                ':nome_documento' => $nome_documento,
                ':data_documento' => $data_documento,
                ':atualizacao_documento' => $atualizacao_documento,
                ':status_documento' => $status_documento
            ]);
        } catch (PDOException $e) {
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
            $sql = 'DELETE FROM documento WHERE id_documento = :id_documento';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_documento' => $id_documento
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao deletar documento',
                0,
                $e
            );
        }
    }

    public function atualizarStatusDocumentosAutomatico(string $data_hoje): bool {
        try {
            $sql = "UPDATE documento 
                    SET status_documento = CASE 
                        WHEN atualizacao_documento < :data_hoje THEN 'Vencido'
                        ELSE 'Atualizado'
                    END";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':data_hoje' => $data_hoje
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao atualizar status dos documentos automaticamente',
                0,
                $e
            );
        }
    }

    public function selecionarDocumentoPorId(int $id_documento) :array {
        try {
            $sql = 'SELECT * FROM documento WHERE id_documento = :id_documento';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_documento' => $id_documento
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar documento por ID',
                0,
                $e
            );
        }
    }
}