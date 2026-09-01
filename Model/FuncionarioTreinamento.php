<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class FuncionarioTreinamento {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function selecionarFuncionariosTreinados() :array {
        try {
            $sql = 'SELECT COUNT(DISTINCT id_funcionario_fk) AS count_funcionarios FROM funcionario_treinamento';
            $stmt = $this->db->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar todos os funcionarios treinados',
                0,
                $e
            );
        }
    }

    public function selecionarTreinamentosRealizados() :array {
        try {
            $sql = 'SELECT * FROM funcionario_treinamento';
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar todos os treinamentos realizados',
                0,
                $e
            );
        }
    }

    public function selecionarTreinamentosRealizadosPorId(
        int $id_treinamento_fk,
        int $id_funcionario_fk
    ) :array {
        try {
            $sql = 'SELECT * FROM funcionario_treinamento WHERE (id_treinamento_fk = :id_treinamento_fk AND id_funcionario_fk = :id_funcionario_fk) ORDER BY data_funcionario_treinamento DESC';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_treinamento_fk' => $id_treinamento_fk,
                ':id_funcionario_fk' => $id_funcionario_fk
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar treinamentos realizados por id',
                0,
                $e
            );
        }
    }
}

?>