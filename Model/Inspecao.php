<?php

namespace Model;

require_once __DIR__ . "/../Model/Connection.php";

class Inspecao
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function registrarCheckin($id_funcionario, $epis_verificados, $status_inspecao, $fotoBinaria)
    {
        try {
            $sql = "INSERT INTO inspecao
                        (data_hora_inspecao, epis_verificados_inspecao, status_inspecao, foto_inspecao, id_funcionario_fk)
                    VALUES
                        (NOW(), :epis_verificados, :status_inspecao, :foto_inspecao, :id_funcionario)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':epis_verificados', $epis_verificados, \PDO::PARAM_INT);
            $stmt->bindParam(':status_inspecao', $status_inspecao);
            $stmt->bindParam(':foto_inspecao', $fotoBinaria, \PDO::PARAM_LOB);
            $stmt->bindParam(':id_funcionario', $id_funcionario, \PDO::PARAM_INT);
            $stmt->execute();

            return (int) $this->db->lastInsertId();
        } catch (\PDOException $e) {
            error_log('[Model\Inspecao::registrarCheckin] ' . $e->getMessage());
            throw $e;
        }
    }

    public function buscarPorId($id_inspecao)
    {
        try {
            $sql = "SELECT id_inspecao, data_hora_inspecao, epis_verificados_inspecao, status_inspecao, id_funcionario_fk
                    FROM inspecao
                    WHERE id_inspecao = :id_inspecao";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_inspecao', $id_inspecao, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('[Model\Inspecao::buscarPorId] ' . $e->getMessage());
            throw $e;
        }
    }

    public function buscarFoto($id_inspecao)
    {
        try {
            $sql = "SELECT foto_inspecao FROM inspecao WHERE id_inspecao = :id_inspecao";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_inspecao', $id_inspecao, \PDO::PARAM_INT);
            $stmt->execute();
            $linha = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $linha ? $linha['foto_inspecao'] : null;
        } catch (\PDOException $e) {
            error_log('[Model\Inspecao::buscarFoto] ' . $e->getMessage());
            throw $e;
        }
    }

    public function checkinDoDia($id_funcionario)
    {
        try {
            $sql = "SELECT id_inspecao, status_inspecao, data_hora_inspecao
                    FROM inspecao
                    WHERE id_funcionario_fk = :id_funcionario AND DATE(data_hora_inspecao) = CURDATE()
                    ORDER BY data_hora_inspecao DESC
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_funcionario', $id_funcionario, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('[Model\Inspecao::checkinDoDia] ' . $e->getMessage());
            throw $e;
        }
    }
}
