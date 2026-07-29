<?php

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class Funcionario {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function criarFuncionario(
        string $nome_funcionario,
        string $cpf_funcionario,
        string $setor_funcionario,
        string $cargo_funcionario,
        string $turno_funcionario,
        string $senha_funcionario
    ) :bool {
        try {
            $sql = 'INSERT INTO funcionario (
                nome_funcionario,
                cpf_funcionario,
                setor_funcionario,
                cargo_funcionario,
                turno_funcionario,
                senha_funcionario
            ) VALUES (
                :nome_funcionario,
                :cpf_funcionario,
                :setor_funcionario,
                :cargo_funcionario,
                :turno_funcionario,
                :senha_funcionario
            )';

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':nome_funcionario' => $nome_funcionario,
                ':cpf_funcionario' => $cpf_funcionario,
                ':setor_funcionario' => $setor_funcionario,
                ':cargo_funcionario' => $cargo_funcionario,
                ':turno_funcionario' => $turno_funcionario,
                ':senha_funcionario' => $senha_funcionario
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao criar funcionario',
                0,
                $e
            );
        } 
    }

    public function selecionarTodosOsFuncionarios() :array {
        try {
            $sql = 'SELECT nome_funcionario, cpf_funcionario, cargo_funcionario, setor_funcionario FROM funcionario';

            $stmt = $this->db->query($sql);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar todos os funcionarios',
                0,
                $e
            );
        }
    }
}

?>