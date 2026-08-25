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

    public function selecionarTodasAsAtividades() :array {
        try {
            $sql = 'SELECT a.*,
            COUNT(ae.id_atividade_fk) AS epis,
            n.nome_nr
            FROM atividade a
            LEFT JOIN atividade_epi ae ON a.id_atividade = ae.id_atividade_fk
            LEFT JOIN nr n ON a.id_nr_fk = n.id_nr
            GROUP BY a.id_atividade';
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

    public function getAllAtvs() :array {
        try {
            $sql = 'SELECT a.id_atividade, a.nome_atividade, a.icone_atividade, n.id_nr, n.nome_nr, GROUP_CONCAT(e.nome_epi SEPARATOR ", ") AS nome_epi, GROUP_CONCAT(e.id_epi SEPARATOR ", ") AS id_epi FROM atividade a
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
        string $icone_atividade,
        int $id_nr_fk
    ) :int {
        try {
            $sql = 'INSERT INTO atividade (
                nome_atividade,
                icone_atividade,
                id_nr_fk
            ) VALUES (
                :nome_atividade,
                :icone_atividade,
                :id_nr_fk
            )';

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':nome_atividade' => $nome_atividade,
                ':icone_atividade' => $icone_atividade,
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

    public function updateAtv (
        int $id_atividade,
        string $nome_atividade,
        string $icone_atividade,
        int $id_nr_fk
    ) :bool {
        try {
            $sql = 'UPDATE atividade SET
                nome_atividade = :nome_atividade,
                icone_atividade =:icone_atividade,
                id_nr_fk = :id_nr_fk
                WHERE id_atividade = :id_atividade
            ';

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nome_atividade' => $nome_atividade,
                ':icone_atividade' => $icone_atividade,
                ':id_nr_fk' => $id_nr_fk,
                ':id_atividade' => $id_atividade
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao atualizar atividade',
                0,
                $e
            );
        }
    }

    public function searchAtvs($pesquisa) :array {
        try {
            $pesquisa = "%$pesquisa%";
            $sql = 'SELECT a.id_atividade, a.nome_atividade, a.icone_atividade, n.id_nr, n.nome_nr, GROUP_CONCAT(e.nome_epi SEPARATOR ", ") AS nome_epi, GROUP_CONCAT(e.id_epi SEPARATOR ", ") AS id_epi FROM atividade a
                LEFT JOIN atividade_epi ae ON a.id_atividade = ae.id_atividade_fk
                LEFT JOIN epi e ON ae.id_epi_fk = e.id_epi
                LEFT JOIN nr n ON a.id_nr_fk = n.id_nr
                WHERE 
                a.nome_atividade LIKE :pesquisa1 OR
                n.nome_nr LIKE :pesquisa2 OR
                e.nome_epi LIKE :pesquisa3
                GROUP BY a.id_atividade
            ';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':pesquisa1' => $pesquisa,
                ':pesquisa2' => $pesquisa,
                ':pesquisa3' => $pesquisa
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar todas as atividades',
                0,
                $e
            );
        }
    }
}