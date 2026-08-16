<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class Auditoria {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function selecionarTodasAsAuditorias() :array {
        try {
            $sql = 'SELECT * FROM auditoria';
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar todas as auditorias',
                0,
                $e
            );
        }
    }

    public function criarNovaAuditoria(
        string $nome_auditoria,
        string $auditor_auditoria,
        string $data_auditoria,
        string $status_auditoria
    ) :bool {
        try {
            $sql = 'INSERT INTO auditoria (nome_auditoria, auditor_auditoria, data_auditoria, status_auditoria) VALUES (:nome_auditoria, :auditor_auditoria, :data_auditoria, :status_auditoria)';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nome_auditoria' => $nome_auditoria,
                ':auditor_auditoria' => $auditor_auditoria,
                ':data_auditoria' => $data_auditoria,
                ':status_auditoria' => $status_auditoria
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao criar nova auditoria',
                0,
                $e
            );
        }
    }

    public function atualizarAuditoria(
        int $id_auditoria,
        string $nome_auditoria,
        string $auditor_auditoria,
        string $data_auditoria,
        string $status_auditoria
    ) :bool {
        try {
            $sql = 'UPDATE auditoria SET
                nome_auditoria = :nome_auditoria,
                auditor_auditoria = :auditor_auditoria,
                data_auditoria = :data_auditoria,
                status_auditoria = :status_auditoria
            WHERE
                id_auditoria = :id_auditoria
            
            ';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_auditoria' => $id_auditoria,
                ':nome_auditoria' => $nome_auditoria,
                ':auditor_auditoria' => $auditor_auditoria,
                ':data_auditoria' => $data_auditoria,
                ':status_auditoria' => $status_auditoria
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao atualizar auditoria',
                0,
                $e
            );
        }
    }

    public function deletarAuditoria(
        int $id_auditoria
    ) :bool {
        try {
            $sql = 'DELETE FROM auditoria WHERE id_auditoria = :id_auditoria';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_auditoria' => $id_auditoria
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao deletar auditoria',
                0,
                $e
            );
        }
    }
}