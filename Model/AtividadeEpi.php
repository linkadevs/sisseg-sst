<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use PDOException;
use Exception;
use Model\Connection;

class AtividadeEpi {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function createAtvEpi(
        int $id_atividade_fk,
        int $id_epi_fk
    ) :bool {
        try{
            $sql = 'INSERT INTO atividade_epi
            (
                id_atividade_fk,
                id_epi_fk
            ) VALUES (
                :id_atividade_fk,
                :id_epi_fk
            )';

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':id_atividade_fk' => $id_atividade_fk,
                ':id_epi_fk' => $id_epi_fk
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Error creating atv_epi',
                0,
                $e
            );
        }
    }

    public function deleteAtvEpiByAtvId(
        int $id_atividade
    ) :bool {
        try {
            $sql = 'DELETE FROM atividade_epi WHERE id_atividade_fk = :id_atividade';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_atividade' => $id_atividade
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao deletar atv_epi',
                0,
                $e
            );
        }
    }
}

?>