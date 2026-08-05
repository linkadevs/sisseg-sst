<?php 

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use Model\Connection;
use PDO;
use PDOException;
use Exception;

class Epi {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function create_epi(
        string $nome_epi,
        string $descricao_epi,
        string $funcao_epi,
        string $ca_epi,
        int $qtd_epi,
        int $qtd_minima_epi,
        string $status_epi
    ) :bool {
        try {
            $sql = 'INSERT INTO epi (
                nome_epi,
                descricao_epi,
                funcao_epi,
                ca_epi,
                qtd_epi,
                qtd_minima_epi,
                status_epi
            ) VALUES (
                :nome_epi,
                :descricao_epi,
                :funcao_epi,
                :ca_epi,
                :qtd_epi,
                :qtd_minima_epi,
                :status_epi
            )';

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':nome_epi' => $nome_epi,
                ':descricao_epi' => $descricao_epi,
                ':funcao_epi' => $funcao_epi,
                ':ca_epi' => $ca_epi,
                ':qtd_epi' => $qtd_epi,
                ':qtd_minima_epi' => $qtd_minima_epi,
                ':status_epi' => $status_epi,
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao criar epi',
                0,
                $e
            );
        }
    }

    public function get_all_epis() {
        try {
            $sql = 'SELECT * FROM epi';
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar todos os EPIs',
                0,
                $e
            );
        }
    }
}

?>