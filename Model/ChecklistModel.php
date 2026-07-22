<?php

namespace Model;

use Model\Connection;
use PDO;
use PDOException;

require_once __DIR__ . '/../config/Connection.php';

class Checagem
{
    private $conexao;

    public function __construct()
    {
        $this->conexao = new Connection();

        if (method_exists($this->conexao, "getConnection")) {
            $this->conexao = $this->conexao->getConnection();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Lista todos os administradores
    |--------------------------------------------------------------------------
    */

    public function listarAdministradores()
    {
        try {

            $sql = "
                SELECT
                    id_adm,
                    nome_adm
                FROM administrador
                ORDER BY nome_adm
            ";

            $stmt = $this->conexao->prepare($sql);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            die("Erro ao listar administradores: " . $e->getMessage());

        }
    }

    /*
    |--------------------------------------------------------------------------
    | Busca um administrador pelo ID
    |--------------------------------------------------------------------------
    */

    public function buscarAdministradorPorId($idAdm)
    {
        try {

            $sql = "
                SELECT
                    id_adm,
                    nome_adm
                FROM administrador
                WHERE id_adm = :id_adm
                LIMIT 1
            ";

            $stmt = $this->conexao->prepare($sql);

            $stmt->bindValue(
                ":id_adm",
                $idAdm,
                PDO::PARAM_INT
            );

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            die("Erro ao buscar administrador: " . $e->getMessage());

        }
    }
}