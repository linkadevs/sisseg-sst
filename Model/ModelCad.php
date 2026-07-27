<?php

namespace Model;

use Model\Connection;
use PDOException;
use Exception;

require_once __DIR__ . '/Connection.php';

class ModelCad
{
    private $conn;

    public function __construct()
    {
        // Instancia a conexão com o banco
        $this->conn = Connection::getInstance();
    }

    /* Verifica se o CPF já existe em qualquer tabela (adm ou funcionario) */
    public function cpfExistente($cpf)
    {
        try {

            // Verifica na tabela adm
            $sql = "SELECT id_adm
                    FROM administrador
                    WHERE cpf_adm = :cpf
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true;
            }

            // Verifica na tabela funcionario
            $sql = "SELECT id_funcionario
                    FROM funcionario
                    WHERE cpf_funcionario = :cpf
                    LIMIT 1";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->execute();

            return $stmt->rowCount() > 0;

       } catch (PDOException $e) {

    throw new Exception($e->getMessage());

}
    }

    /* Insere um novo funcionário */
    public function inserirfuncionario($dados)
    {
        try {

            // Comando SQL para inserir o funcionário
            $sql = "INSERT INTO funcionario
                    (
                        nome_funcionario,
                        cpf_funcionario,
                        senha_funcionario,
                        turno_funcionario,
                        cargo_funcionario,
                        setor_funcionario
                    )
                    VALUES
                    (
                        :nome,
                        :cpf,
                        :senha,
                        :turno,
                        :cargo,
                        :setor
                    )";

            $stmt = $this->conn->prepare($sql);

            // Vincula os valores aos parâmetros
            $stmt->bindValue(':nome', $dados['nome']);
            $stmt->bindValue(':cpf', $dados['cpf']);
            $stmt->bindValue(':senha', $dados['senha']);
            $stmt->bindValue(':turno', $dados['turno']);
            $stmt->bindValue(':cargo', $dados['cargo']);
            $stmt->bindValue(':setor', $dados['setor']);

            // Executa o INSERT
            if (!$stmt->execute()) {
                throw new Exception('Erro ao cadastrar o funcionário.');
            }

            return true;

        } catch (PDOException $e) {

            throw new Exception('Erro ao acessar o banco de dados.');

        } catch (Exception $e) {

            throw $e;

        }
    }
}

?>