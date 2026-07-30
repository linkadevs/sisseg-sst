<?php

namespace Model;

use Model\Connection;
use PDO;
use PDOException;
use Exception;

require_once __DIR__ . '/Connection.php';

class ModelLogin
{
    private $conn;

    public function __construct()
    {
        // Instancia a conexão com o banco
        $this->conn = Connection::getInstance();
    }

    /* Busca um administrador pelo CPF */
    public function buscarAdministrador($cpf)
    {
        try {

            // Comando SQL para buscar o administrador
            $sql = "SELECT
                        id_adm,
                        nome_adm,
                        cpf_adm,
                        setor_adm,
                        turno_adm,
                        senha_adm
                    FROM administrador
                    WHERE cpf_adm = :cpf
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);

            // Vincula o parâmetro
            $stmt->bindValue(':cpf', $cpf);

            // Executa a consulta
            $stmt->execute();

            // Retorna os dados do administrador
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            throw new Exception('Erro ao acessar o banco de dados.');

        }
    }

    /* Busca um funcionário pelo CPF */
    public function buscarFuncionario($cpf)
    {
        try {

            // Comando SQL para buscar o funcionário
            $sql = "SELECT
                        id_funcionario,
                        nome_funcionario,
                        cpf_funcionario,
                        setor_funcionario,
                        cargo_funcionario,
                        turno_funcionario,
                        senha_funcionario
                    FROM funcionario
                    WHERE cpf_funcionario = :cpf
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);

            // Vincula o parâmetro
            $stmt->bindValue(':cpf', $cpf);

            // Executa a consulta
            $stmt->execute();

            // Retorna os dados do funcionário
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            throw new Exception('Erro ao acessar o banco de dados.');

        }
    }
}

?>