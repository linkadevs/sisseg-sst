<?php

namespace Model;

require_once __DIR__ . "/../Model/Connection.php";

class Epi
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function listar()
    {
        try {
            $sql = "SELECT id_epi, nome_epi, descricao_epi, funcao_epi, ca_epi, qtd_minima_epi, qtd_epi, status_epi
                    FROM epi
                    ORDER BY nome_epi ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('[Model\Epi::listar] ' . $e->getMessage());
            throw $e;
        }
    }

    public function buscarPorId($id_epi)
    {
        try {
            $sql = "SELECT id_epi, nome_epi, descricao_epi, funcao_epi, ca_epi, qtd_minima_epi, qtd_epi, status_epi
                    FROM epi
                    WHERE id_epi = :id_epi";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_epi', $id_epi, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('[Model\Epi::buscarPorId] ' . $e->getMessage());
            throw $e;
        }
    }

    public function criar(array $dados)
    {
        try {
            $sql = "INSERT INTO epi
                        (nome_epi, descricao_epi, funcao_epi, ca_epi, qtd_minima_epi, qtd_epi, status_epi)
                    VALUES
                        (:nome_epi, :descricao_epi, :funcao_epi, :ca_epi, :qtd_minima_epi, :qtd_epi, :status_epi)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nome_epi', $dados['nome_epi']);
            $stmt->bindParam(':descricao_epi', $dados['descricao_epi']);
            $stmt->bindParam(':funcao_epi', $dados['funcao_epi']);
            $stmt->bindParam(':ca_epi', $dados['ca_epi']);
            $stmt->bindParam(':qtd_minima_epi', $dados['qtd_minima_epi'], \PDO::PARAM_INT);
            $stmt->bindParam(':qtd_epi', $dados['qtd_epi'], \PDO::PARAM_INT);
            $stmt->bindParam(':status_epi', $dados['status_epi']);
            $stmt->execute();

            return (int) $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log('[Model\Epi::criar] ' . $e->getMessage());
            throw $e;
        }
    }

    public function atualizar($id_epi, array $dados)
    {
        try {
            $sql = "UPDATE epi SET
                        nome_epi = :nome_epi,
                        descricao_epi = :descricao_epi,
                        funcao_epi = :funcao_epi,
                        ca_epi = :ca_epi,
                        qtd_minima_epi = :qtd_minima_epi,
                        qtd_epi = :qtd_epi,
                        status_epi = :status_epi
                    WHERE id_epi = :id_epi";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nome_epi', $dados['nome_epi']);
            $stmt->bindParam(':descricao_epi', $dados['descricao_epi']);
            $stmt->bindParam(':funcao_epi', $dados['funcao_epi']);
            $stmt->bindParam(':ca_epi', $dados['ca_epi']);
            $stmt->bindParam(':qtd_minima_epi', $dados['qtd_minima_epi'], \PDO::PARAM_INT);
            $stmt->bindParam(':qtd_epi', $dados['qtd_epi'], \PDO::PARAM_INT);
            $stmt->bindParam(':status_epi', $dados['status_epi']);
            $stmt->bindParam(':id_epi', $id_epi, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('[Model\Epi::atualizar] ' . $e->getMessage());
            throw $e;
        }
    }

    public function atualizarQuantidade($id_epi, $qtd_epi, $status_epi)
    {
        try {
            $sql = "UPDATE epi SET qtd_epi = :qtd_epi, status_epi = :status_epi WHERE id_epi = :id_epi";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':qtd_epi', $qtd_epi, \PDO::PARAM_INT);
            $stmt->bindParam(':status_epi', $status_epi);
            $stmt->bindParam(':id_epi', $id_epi, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('[Model\Epi::atualizarQuantidade] ' . $e->getMessage());
            throw $e;
        }
    }

    public function excluir($id_epi)
    {
        try {
            $sql = "DELETE FROM epi WHERE id_epi = :id_epi";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_epi', $id_epi, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log('[Model\Epi::excluir] ' . $e->getMessage());
            throw $e;
        }
    }

    public function calcularStatus($qtd_epi, $qtd_minima_epi)
    {
        if ($qtd_minima_epi <= 0) {
            return 'ok';
        }
        if ($qtd_epi >= $qtd_minima_epi) {
            return 'ok';
        }
        if ($qtd_epi >= $qtd_minima_epi * 0.5) {
            return 'alert';
        }
        return 'critical';
    }
}
