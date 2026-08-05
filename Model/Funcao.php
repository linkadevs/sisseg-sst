<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class Funcao {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function selecionarTodasFuncoes() :array {
        try {
            $sql = "SELECT 
            f.id_funcao,
            f.nome_funcao,
            GROUP_CONCAT(e.nome_epi SEPARATOR ', ') AS nome_epi
            FROM funcao f
            LEFT JOIN epi_funcao ef ON ef.id_funcao_fk = f.id_funcao
            LEFT JOIN epi e ON e.id_epi = ef.id_epi_fk
            GROUP BY f.id_funcao, f.nome_funcao;
            ";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar todas as funcoes',
                0,
                $e
            );
        }
    }

    public function selecionarFuncaoPorId(
        int $id_funcao
    ) :array {
        try {
            $sql = "SELECT 
            f.nome_funcao,
            GROUP_CONCAT(e.nome_epi SEPARATOR ', ') AS nome_epi
            FROM funcao f 
            LEFT JOIN epi_funcao ef ON ef.id_funcao_fk = f.id_funcao
            LEFT JOIN epi e ON e.id_epi = ef.id_epi_fk
            WHERE id_funcao = :id_funcao
            GROUP BY f.id_funcao, f.nome_funcao";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_funcao' => $id_funcao
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar função por id',
                0,
                $e
            );
        }
    }
}

?>