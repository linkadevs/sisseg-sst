<?php

namespace Model;
require_once __DIR__ . '/../Model/Connection.php';

use Exception;
use PDO;
use Model\Connection;

class ExibirCertificadosModel
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    /**
     * Busca os dados do funcionário pelo ID
     */
    public function buscarFuncionario($id_funcionario)
    {
        try {
            $sql = "SELECT 
                        id_funcionario, 
                        nome_funcionario, 
                        cpf_funcionario,
                        cargo_funcionario, 
                        setor_funcionario,
                        turno_funcionario
                    FROM funcionario 
                    WHERE id_funcionario = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id_funcionario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar funcionário: " . $e->getMessage());
        }
    }

    /**
     * Busca todos os treinamentos do funcionário com seus certificados e status
     */
    public function buscarTreinamentosComStatus($id_funcionario)
    {
        try {
            $sql = "SELECT 
                        ft.id_funcionario_treinamento,
                        ft.data_funcionario_treinamento,
                        t.id_treinamento,
                        t.nome_treinamento,
                        t.subtitulo_treinamento,
                        t.nr_treinamento,
                        t.carga_horaria_treinamento,
                        t.data_limite_treinamento,
                        c.id_certificado,
                        c.data_certificado,
                        c.pontos_certificado,
                        p.id_prova,
                        p.nome_prova,
                        CASE 
                            WHEN c.id_certificado IS NOT NULL AND (t.data_limite_treinamento IS NULL OR t.data_limite_treinamento >= CURDATE()) 
                            THEN 'valido'
                            WHEN c.id_certificado IS NOT NULL AND t.data_limite_treinamento < CURDATE()
                            THEN 'invalido'
                            ELSE 'invalido'
                        END AS status_treinamento,
                        t.data_limite_treinamento AS data_validade
                    FROM funcionario_treinamento ft
                    INNER JOIN treinamento t ON ft.id_treinamento_fk = t.id_treinamento
                    LEFT JOIN prova p ON t.id_treinamento = p.id_treinamento_fk
                    LEFT JOIN certificado c ON c.id_prova_fk = p.id_prova AND c.id_funcionario_fk = ft.id_funcionario_fk
                    WHERE ft.id_funcionario_fk = :id_funcionario
                    GROUP BY ft.id_funcionario_treinamento
                    ORDER BY ft.data_funcionario_treinamento DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_funcionario', $id_funcionario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar treinamentos com status: " . $e->getMessage());
        }
    }

    /**
     * Conta o total de treinamentos do funcionário
     */
    public function contarTreinamentos($id_funcionario)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM funcionario_treinamento WHERE id_funcionario_fk = :id_funcionario";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_funcionario', $id_funcionario, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (Exception $e) {
            throw new Exception("Erro ao contar treinamentos: " . $e->getMessage());
        }
    }

    /**
     * Conta treinamentos válidos do funcionário
     */
    public function contarTreinamentosValidos($id_funcionario)
    {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM funcionario_treinamento ft
                    INNER JOIN treinamento t ON ft.id_treinamento_fk = t.id_treinamento
                    INNER JOIN prova p ON t.id_treinamento = p.id_treinamento_fk
                    INNER JOIN certificado c ON c.id_prova_fk = p.id_prova AND c.id_funcionario_fk = ft.id_funcionario_fk
                    WHERE ft.id_funcionario_fk = :id_funcionario
                    AND (t.data_limite_treinamento IS NULL OR t.data_limite_treinamento >= CURDATE())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_funcionario', $id_funcionario, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (Exception $e) {
            throw new Exception("Erro ao contar treinamentos válidos: " . $e->getMessage());
        }
    }

    /**
     * Conta treinamentos inválidos do funcionário
     */
    public function contarTreinamentosInvalidos($id_funcionario)
    {
        try {
            $sql = "SELECT COUNT(*) as total 
                    FROM funcionario_treinamento ft
                    INNER JOIN treinamento t ON ft.id_treinamento_fk = t.id_treinamento
                    LEFT JOIN prova p ON t.id_treinamento = p.id_treinamento_fk
                    LEFT JOIN certificado c ON c.id_prova_fk = p.id_prova AND c.id_funcionario_fk = ft.id_funcionario_fk
                    WHERE ft.id_funcionario_fk = :id_funcionario
                    AND (c.id_certificado IS NULL 
                        OR (t.data_limite_treinamento IS NOT NULL AND t.data_limite_treinamento < CURDATE()))";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_funcionario', $id_funcionario, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (Exception $e) {
            throw new Exception("Erro ao contar treinamentos inválidos: " . $e->getMessage());
        }
    }
}