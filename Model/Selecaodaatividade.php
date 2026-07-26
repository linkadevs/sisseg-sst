<?php

namespace Model;
require_once __DIR__ . '/../Model/Connection.php';

use Exception;
use PDOException;
use PDO;
use Model\Connection;

class Selecaodaatividade
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    public function exibir_atividades()
    {
        try {
            // Busca todas as atividades
            $sql = "SELECT * FROM atividade";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $atividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($atividades)) {
                return [];
            }

            foreach ($atividades as $key => $atividade) {
                // Busca o nome do NR relacionado
                if (!empty($atividade['id_nr_fk'])) {
                    $sql = "SELECT nome_nr FROM nr WHERE id_nr = :id_nr";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(":id_nr", $atividade['id_nr_fk'], PDO::PARAM_INT);
                    $stmt->execute();
                    $nr = $stmt->fetch(PDO::FETCH_ASSOC);
                    $atividades[$key]['nome_nr'] = $nr['nome_nr'] ?? 'Não atribuído';
                } else {
                    $atividades[$key]['nome_nr'] = 'Não atribuído';
                }

                // Conta a quantidade de EPIs vinculados à atividade
                $sql = "SELECT COUNT(*) as total_epis 
                        FROM atividade_epi 
                        WHERE id_atividade_fk = :id_atividade";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(":id_atividade", $atividade['id_atividade'], PDO::PARAM_INT);
                $stmt->execute();
                $count = $stmt->fetch(PDO::FETCH_ASSOC);
                $atividades[$key]['quantidade_epis'] = $count['total_epis'] ?? 0;
            }

            return $atividades;

        } catch (Exception $erro) {
            throw new Exception("Erro ao obter atividades: " . $erro->getMessage());
        }
    }
}