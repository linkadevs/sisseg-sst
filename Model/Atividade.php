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
}