<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class Certificado {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function selecionarCertificadoPorId(int $id_certificado) :array {
        try {
            $sql = 'SELECT * FROM certificado WHERE id_certificado = :id_certificado';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_certificado' => $id_certificado
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar certificado por id',
                0,
                $e
            );
        }
    }
    
    public function selecionarUltimoCertificado(
        int $id_treinamento,
        int $id_funcionario
    ) :array {
        try {
            $sql = 'SELECT c.* FROM certificado c
            INNER JOIN prova p ON c.id_prova_fk = p.id_prova
            INNER JOIN treinamento t ON p.id_treinamento_fk = t.id_treinamento
            WHERE p.id_treinamento_fk = :id_treinamento_fk AND c.id_funcionario_fk = :id_funcionario_fk
            ORDER BY c.data_certificado DESC
            LIMIT 1
            ';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_treinamento_fk' => $id_treinamento,
                ':id_funcionario_fk' => $id_funcionario
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar ultimo certificado',
                0,
                $e
            );
        }
    }
}