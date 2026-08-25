<?php 

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class Indicadores {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function selecionarTodosIndicadores() :array {
        try {
            $sql = 'SELECT * FROM indicadores ORDER BY pontos_indicadores DESC';
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar todos os indicadores',
                0,
                $e
            );
        }
    }
}

?>