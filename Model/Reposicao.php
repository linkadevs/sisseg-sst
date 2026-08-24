<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class Reposicao {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function criarReposicao(
        string $data_reposicao,
        int $id_epi_fk,
        int $id_funcionario_fk
    ) :bool {
        try {
            $sql = 'INSERT INTO reposicao
            (
                data_reposicao,
                id_epi_fk,
                id_funcionario_fk    
            ) VALUES (
                :data_reposicao,
                :id_epi_fk,
                :id_funcionario_fk
            )';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':data_reposicao' => $data_reposicao,
                ':id_epi_fk' => $id_epi_fk,
                ':id_funcionario_fk' => $id_funcionario_fk
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao criar reposição',
                0,
                $e
            );
        }
    }
}