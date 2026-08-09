<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class Incidente {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function SelecionarTodosOsIncidentes() :array {
        try {
            $sql = 'SELECT * FROM incidente';
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar todos os incidentes',
                0,
                $e
            );
        }
    }
}

?>