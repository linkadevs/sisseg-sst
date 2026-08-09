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
                'Erro ao selecionar todos os treinamentos realizados',
                0,
                $e
            );
        }
    }
}

?>