<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class Atividade {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function getAllAtvs() :array {
        try {
            $sql = 'SELECT a.nome_atividade, a.foto_atividade, n.nome_nr, GROUP_CONCAT(e.nome_epi SEPARATOR ", ") AS nome_epi FROM atividade a
                LEFT JOIN atividade_epi ae ON a.id_atividade = ae.id_atividade_fk
                LEFT JOIN epi e ON ae.id_epi_fk = e.id_epi
                LEFT JOIN nr n ON a.id_nr_fk = n.id_nr
                GROUP BY a.id_atividade
            ';
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar todas as atividades',
                0,
                $e
            );
        }
    }

    public function createAtv(
        string $nome_atividade,
        string $foto_atividade,
        int $id_nr_fk
    ) :int {
        try {
            $sql = 'INSERT INTO atividade (
                nome_atividade,
                foto_atividade,
                id_nr_fk
            ) VALUES (
                :nome_atividade,
                :foto_atividade,
                :id_nr_fk
            )';

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':nome_atividade' => $nome_atividade,
                ':foto_atividade' => $foto_atividade,
                ':id_nr_fk' => $id_nr_fk
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e){
            throw new Exception(
                'Erro ao criar atividade',
                0,
                $e
            );
        }
    }
}

?>