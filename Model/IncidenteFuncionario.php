<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class IncidenteFuncionario {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function criarIncidenteFuncionario(
        string $data_incidente,
        int $id_incidente,
        int $id_funcionario
    ) :bool {
        try {
            $sql = 'INSERT INTO incidente_funcionario (data_incidente_funcionario, id_incidente_fk, id_funcionario_fk) VALUES (:data_incidente_funcionario, :id_incidente_fk, :id_funcionario_fk)';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':data_incidente_funcionario' => $data_incidente,
                ':id_incidente_fk' => $id_incidente,
                ':id_funcionario_fk' => $id_funcionario
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao criar incidente funcionario',
                0,
                $e
            );
        }
    }

    public function selecionarUltimoIncidenteFuncionario(
        int $id_funcionario
    ) :string {
        try {
            $sql = 'SELECT data_incidente_funcionario FROM incidente_funcionario WHERE id_funcionario_fk = :id_funcionario_fk ORDER BY data_incidente_funcionario DESC LIMIT 1';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_funcionario_fk' => $id_funcionario
            ]);
            return $stmt->fetch(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar quantidade de incidentes por funcionario',
                0,
                $e
            );
        }
    }
}