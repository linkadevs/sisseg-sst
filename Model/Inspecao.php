<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class Inspecao {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function criarInspecao(
        string $data_hora_inspecao,
        int $epis_verificados_inspecao,
        string $status_inspecao,
        string $foto_inspecao,
        int $id_funcionario_fk,
        int $id_funcao_fk
    ) :int {
        try {
            $sql = 'INSERT INTO inspecao (
                data_hora_inspecao,
                epis_verificados_inspecao,
                status_inspecao,
                foto_inspecao,
                id_funcionario_fk,
                id_funcao_fk
            ) VALUES (
                :data_hora_inspecao,
                :epis_verificados_inspecao,
                :status_inspecao,
                :foto_inspecao,
                :id_funcionario_fk,
                :id_funcao_fk
            )';

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':data_hora_inspecao' => $data_hora_inspecao,
                ':epis_verificados_inspecao' => $epis_verificados_inspecao,
                ':status_inspecao' => $status_inspecao,
                ':foto_inspecao' => $foto_inspecao,
                ':id_funcionario_fk' => $id_funcionario_fk,
                ':id_funcao_fk' => $id_funcao_fk
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao criar inspeção',
                0,
                $e
            );
        }
    }

    public function selecionarInspecaoPorId(int $id_inspecao) :array {
        try {
            $sql = 'SELECT * FROM inspecao WHERE id_inspecao = :id_inspecao LIMIT 1';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_inspecao' => $id_inspecao
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar inspeção por id',
                0,
                $e
            );
        }
    }
}

?>