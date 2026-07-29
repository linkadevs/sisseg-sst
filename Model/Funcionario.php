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
            $sql = 'SELECT id_funcionario, nome_funcionario, cpf_funcionario, cargo_funcionario, setor_funcionario, turno_funcionario FROM funcionario';

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

    public function senhaFuncionario(
        int $id_funcionario
    ) :string {
        try {
            $sql = 'SELECT senha_funcionario FROM funcionario WHERE id_funcionario = :id_funcionario LIMIT 1';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_funcionario' => $id_funcionario
            ]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar senha do funcionário',
                0,
                $e
            );
        }
    }

    public function atualizarFuncionario(
        int $id_funcionario,
        string $nome_funcionario,
        string $cpf_funcionario,
        string $setor_funcionario,
        string $cargo_funcionario,
        string $turno_funcionario,
        string $senha_funcionario
    ) :bool {
        try {
            $sql = 'UPDATE funcionario SET
                nome_funcionario = :nome_funcionario,
                cpf_funcionario = :cpf_funcionario,
                setor_funcionario = :setor_funcionario,
                cargo_funcionario = :cargo_funcionario,
                turno_funcionario = :turno_funcionario,
                senha_funcionario = :senha_funcionario
                WHERE
                id_funcionario = :id_funcionario';
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'nome_funcionario' => $nome_funcionario,
                'cpf_funcionario' => $cpf_funcionario,
                'setor_funcionario' => $setor_funcionario,
                'cargo_funcionario' => $cargo_funcionario,
                'turno_funcionario' => $turno_funcionario,
                'senha_funcionario' => $senha_funcionario,
                'id_funcionario' => $id_funcionario
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao atualizar funcionário',
                0,
                $e
            );
        }
    }
    public function deletarFuncionario(int $id_funcionario) {
        try {
            $sql = 'DELETE FROM funcionario WHERE id_funcionario = :id_funcionario';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_funcionario' => $id_funcionario
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao deletar funcionario',
                0,
                $e
            );
        }
    }

    public function pesquisarFuncionario(string $pesquisa) :array {
        try {
            $sql = 'SELECT id_funcionario, nome_funcionario, cpf_funcionario, cargo_funcionario, setor_funcionario, turno_funcionario FROM funcionario
                WHERE
                nome_funcionario LIKE :pesquisa1 OR
                cpf_funcionario LIKE :pesquisa2 OR
                cargo_funcionario LIKE :pesquisa3 OR
                setor_funcionario LIKE :pesquisa4 OR
                turno_funcionario LIKE :pesquisa5';

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                ':pesquisa1' => $pesquisa,
                ':pesquisa2' => $pesquisa,
                ':pesquisa3' => $pesquisa,
                ':pesquisa4' => $pesquisa,
                ':pesquisa5' => $pesquisa
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao pesquisar funcionario',
                0,
                $e
            );
        }
    }
}

?>