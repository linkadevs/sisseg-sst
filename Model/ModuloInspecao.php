<?php

namespace Model;
require_once __DIR__ . '/../Model/Connection.php';

use Exception;
use PDO;
use Model\Connection;

class ModuloInspecao
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function listarFuncoes()
    {
        try {
            $sql = "SELECT 
                        f.id_funcao,
                        f.nome_funcao,
                        COUNT(ef.id_epi_funcao) as total_epis
                    FROM funcao f
                    LEFT JOIN epi_funcao ef ON f.id_funcao = ef.id_funcao_fk
                    GROUP BY f.id_funcao
                    ORDER BY f.nome_funcao";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao listar funções: " . $e->getMessage());
        }
    }

    public function buscarEpisPorFuncao($id_funcao)
    {
        try {
            $sql = "SELECT 
                        e.id_epi,
                        e.nome_epi,
                        e.descricao_epi,
                        e.ca_epi,
                        ef.id_epi_funcao
                    FROM epi_funcao ef
                    INNER JOIN epi e ON ef.id_epi_fk = e.id_epi
                    WHERE ef.id_funcao_fk = :id_funcao
                    ORDER BY e.nome_epi";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_funcao', $id_funcao, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar EPIs da função: " . $e->getMessage());
        }
    }

    public function buscarFuncaoPorId($id_funcao)
    {
        try {
            $sql = "SELECT id_funcao, nome_funcao FROM funcao WHERE id_funcao = :id_funcao";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_funcao', $id_funcao, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar função: " . $e->getMessage());
        }
    }

    public function criarFuncao($dados)
    {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO funcao (nome_funcao) VALUES (:nome)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nome', $dados['nome_funcao']);
            $stmt->execute();
            $id_funcao = $this->db->lastInsertId();

            if (!empty($dados['epis'])) {
                foreach ($dados['epis'] as $id_epi) {
                    if (!empty($id_epi)) {
                        $sql = "INSERT INTO epi_funcao (id_epi_fk, id_funcao_fk) VALUES (:id_epi, :id_funcao)";
                        $stmt = $this->db->prepare($sql);
                        $stmt->bindParam(':id_epi', $id_epi, PDO::PARAM_INT);
                        $stmt->bindParam(':id_funcao', $id_funcao, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                }
            }

            $this->db->commit();
            return $id_funcao;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Erro ao criar função: " . $e->getMessage());
        }
    }

    public function atualizarFuncao($id_funcao, $dados)
    {
        try {
            $this->db->beginTransaction();

            $sql = "UPDATE funcao SET nome_funcao = :nome WHERE id_funcao = :id_funcao";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nome', $dados['nome_funcao']);
            $stmt->bindParam(':id_funcao', $id_funcao, PDO::PARAM_INT);
            $stmt->execute();

            $sql = "DELETE FROM epi_funcao WHERE id_funcao_fk = :id_funcao";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_funcao', $id_funcao, PDO::PARAM_INT);
            $stmt->execute();

            if (!empty($dados['epis'])) {
                foreach ($dados['epis'] as $id_epi) {
                    if (!empty($id_epi)) {
                        $sql = "INSERT INTO epi_funcao (id_epi_fk, id_funcao_fk) VALUES (:id_epi, :id_funcao)";
                        $stmt = $this->db->prepare($sql);
                        $stmt->bindParam(':id_epi', $id_epi, PDO::PARAM_INT);
                        $stmt->bindParam(':id_funcao', $id_funcao, PDO::PARAM_INT);
                        $stmt->execute();
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Erro ao atualizar função: " . $e->getMessage());
        }
    }

    public function deletarFuncao($id_funcao)
    {
        try {
            $this->db->beginTransaction();

            $sql = "DELETE FROM epi_funcao WHERE id_funcao_fk = :id_funcao";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_funcao', $id_funcao, PDO::PARAM_INT);
            $stmt->execute();

            $sql = "DELETE FROM funcao WHERE id_funcao = :id_funcao";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_funcao', $id_funcao, PDO::PARAM_INT);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Erro ao excluir função: " . $e->getMessage());
        }
    }

    public function listarTodosEpis()
    {
        try {
            $sql = "SELECT id_epi, nome_epi, descricao_epi FROM epi WHERE status_epi = 'Ativo' ORDER BY nome_epi";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao listar EPIs: " . $e->getMessage());
        }
    }

    public function listarInspecoes()
    {
        try {
            $sql = "SELECT 
                        i.id_inspecao,
                        i.data_hora_inspecao,
                        i.epis_verificados_inspecao,
                        i.status_inspecao,
                        i.id_funcionario_fk,
                        f.nome_funcionario,
                        f.cargo_funcionario,
                        f.setor_funcionario
                    FROM inspecao i
                    INNER JOIN funcionario f ON i.id_funcionario_fk = f.id_funcionario
                    ORDER BY i.data_hora_inspecao DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao listar inspeções: " . $e->getMessage());
        }
    }

    public function contarTotalInspecoes()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM inspecao";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (Exception $e) {
            throw new Exception("Erro ao contar total de inspeções: " . $e->getMessage());
        }
    }

public function buscarInspecoes($termo)
{
    try {
        $sql = "SELECT 
                    i.id_inspecao,
                    i.data_hora_inspecao,
                    i.epis_verificados_inspecao,
                    i.status_inspecao,
                    i.id_funcionario_fk,
                    f.nome_funcionario,
                    f.cargo_funcionario,
                    f.setor_funcionario
                FROM inspecao i
                INNER JOIN funcionario f ON i.id_funcionario_fk = f.id_funcionario
                WHERE f.nome_funcionario LIKE :termo1 
                   OR f.cargo_funcionario LIKE :termo2 
                   OR f.setor_funcionario LIKE :termo3
                   OR i.status_inspecao LIKE :termo4
                ORDER BY i.data_hora_inspecao DESC";
        $stmt = $this->db->prepare($sql);
        $termoBusca = '%' . $termo . '%';
        $stmt->bindParam(':termo1', $termoBusca, PDO::PARAM_STR);
        $stmt->bindParam(':termo2', $termoBusca, PDO::PARAM_STR);
        $stmt->bindParam(':termo3', $termoBusca, PDO::PARAM_STR);
        $stmt->bindParam(':termo4', $termoBusca, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        throw new Exception("Erro ao buscar inspeções: " . $e->getMessage());
    }
}
}