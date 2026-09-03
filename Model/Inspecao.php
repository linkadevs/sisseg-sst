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
        int $id_funcionario_fk,
        int $id_funcao_fk
    ) :int {
        try {
            $sql = 'INSERT INTO inspecao (
                data_hora_inspecao,
                epis_verificados_inspecao,
                status_inspecao,
                id_funcionario_fk,
                id_funcao_fk
            ) VALUES (
                :data_hora_inspecao,
                :epis_verificados_inspecao,
                :status_inspecao,
                :id_funcionario_fk,
                :id_funcao_fk
            )';

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':data_hora_inspecao' => $data_hora_inspecao,
                ':epis_verificados_inspecao' => $epis_verificados_inspecao,
                ':status_inspecao' => $status_inspecao,
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

    public function selecionarQtdInspecaoPorFuncionario(int $id_funcionario_fk) :int {
        try {
            $sql = 'SELECT COUNT(id_inspecao) FROM inspecao WHERE id_funcionario_fk = :id_funcionario_fk';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_funcionario_fk' => $id_funcionario_fk
            ]);
            return $stmt->fetch(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar quantidade de inspeções por funcionario',
                0,
                $e
            );
        }
    }

    public function selecionarDadosConformidade() :array {
        try {
            $sql = 'SELECT 
                -- Soma real da coluna epis_verificados_inspecao (sem duplicações)
                SUM(i.epis_verificados_inspecao) AS total_inspecionado,
                
                -- Soma da quantidade de EPIs cadastrados para a função de cada inspeção realizada
                SUM(COALESCE(total_epis_funcao.qtd_epis, 0)) AS total_esperado,
                
                -- Cálculo da porcentagem exata
                ROUND(
                    (SUM(i.epis_verificados_inspecao) / NULLIF(SUM(COALESCE(total_epis_funcao.qtd_epis, 0)), 0)) * 100, 
                    2
                ) AS porcentagem_conclusao

            FROM inspecao i

            -- Subconsulta que conta previamente quantos EPIs cada função tem (sem duplicar as inspeções)
            LEFT JOIN (
                SELECT id_funcao_fk, COUNT(id_epi_fk) AS qtd_epis
                FROM epi_funcao
                GROUP BY id_funcao_fk
            ) AS total_epis_funcao ON i.id_funcao_fk = total_epis_funcao.id_funcao_fk';
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar dados para conformidade',
                0,
                $e
            );
        }
    }

    public function selecionarDadosConformidadePorFuncionario(int $id_funcionario_fk) :array {
        try {
            $sql = 'SELECT 
                ROUND(
                    (SUM(i.epis_verificados_inspecao) / NULLIF(SUM(COALESCE(total_epis_funcao.qtd_epis, 0)), 0)) * 100, 
                    2
                ) AS porcentagem_conclusao

            FROM inspecao i

            -- Subconsulta que conta previamente quantos EPIs cada função tem (sem duplicar as inspeções)
            LEFT JOIN (
                SELECT id_funcao_fk, COUNT(id_epi_fk) AS qtd_epis
                FROM epi_funcao
                GROUP BY id_funcao_fk
            ) AS total_epis_funcao ON i.id_funcao_fk = total_epis_funcao.id_funcao_fk
            WHERE i.id_funcionario_fk = :id_funcionario_fk';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_funcionario_fk' => $id_funcionario_fk
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar dados para conformidade por funcionario',
                0,
                $e
            );
        }
    }
}

?>