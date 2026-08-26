<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class Reposicao {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function criarReposicao(
        string $data_reposicao,
        int $id_epi_fk,
        int $id_funcionario_fk
    ) :bool {
        try {
            $sql = 'INSERT INTO reposicao
            (
                data_reposicao,
                id_epi_fk,
                id_funcionario_fk    
            ) VALUES (
                :data_reposicao,
                :id_epi_fk,
                :id_funcionario_fk
            )';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':data_reposicao' => $data_reposicao,
                ':id_epi_fk' => $id_epi_fk,
                ':id_funcionario_fk' => $id_funcionario_fk
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao criar reposição',
                0,
                $e
            );
        }
    }

    public function selecionarTodasAsReposicoes() :array {
        try {
            $sql = 'SELECT r.id_reposicao, r.data_reposicao, e.nome_epi, f.nome_funcionario FROM reposicao r
            INNER JOIN epi e ON r.id_epi_fk = e.id_epi
            INNER JOIN funcionario f ON r.id_funcionario_fk = f.id_funcionario';
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar todas as reposições',
                0,
                $e
            );
        }
    }

    public function deletarReposicaoPorId(
        int $id_reposicao
    ) :bool {
        try {
            $sql = 'DELETE FROM reposicao WHERE id_reposicao = :id_reposicao';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_reposicao' => $id_reposicao
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao deletar reposicao por id',
                0,
                $e
            );
        }
    }
}