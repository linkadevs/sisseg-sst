<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use PDOException;
use Exception;
use PDO;
use Model\Connection;

class Nr {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function getAllNrs() :array {
        try {
            $sql = 'SELECT * FROM nr';
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar todas as NRs',
                0,
                $e
            );
        }
    }
}

?>