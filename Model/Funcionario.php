<?php
namespace Model;

require_once __DIR__ ."../Model/Connection.php";

use PDO;
use PDOException;
use Exception;

class Funcionario{
    private $db;

    public function __construct(){
        $this-> db = Connection::getInstance(); 
    }

    public function getFuncionarioByID($id_funcionario){
        try{
            $sql = "SELECT * FROM funcionario WHERE id_funcionario = :id_funcionario";
            $stmt = $this->db->prepare($sql);
            $stmt -> execute([
                ':id_funcionario' => $id_funcionario
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Erro ao selecionar Funcionário por ID: ' . $e);
        }
    }

    public function getFuncionarioPorCpf($cpf_funcionario) {
        try {
            $sql = 'SELECT cpf_funcionario, id_administrador FROM administrador WHERE cpf_funcionario = :cpf_funcionario';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':cpf_funcionario' => $cpf_funcionario
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Erro ao selecionar Adm por ID: ' . $e);
        }
    }

    public function updateFuncionario($id_funcionario, $nome_funcionario, $cpf_funcionario, $setor_funcionario, $cargo_funcionario){
        try{
            $sql = 'UPDATE funcionario SET nome_funcionario = :nome_funcionario, cpf_funcionario = :cpf_funcionario, setor_funcionario = :setor_funcionario, cargo_funcionario = :cargo_funcionario WHERE id_funcionario = :id_funcionario';

            $stmt = $this->db->prepare($sql);
            $stmt -> bindParam("id_funcionario", $id_funcionario, PDO::PARAM_INT);
            $stmt -> bindParam("nome_funcionario", $nome_funcionario, PDO::PARAM_STR);
            $stmt -> bindParam("cpf_funcionario", $cpf_funcionario, PDO::PARAM_STR);
            $stmt -> bindParam("setor_funcionario", $setor_funcionario, PDO::PARAM_STR);
            $stmt -> bindParam("cargo_funcionario", $cargo_funcionario, PDO::PARAM_STR);

            return $stmt -> execute();
        }catch(PDOException $e) {
            die("Erro ao atualizar as informações do perfil. Código: " . $e);
            return false;
        }
    }

    public function changePassword($id_funcionario, $senha_funcionario){
        try{
            $hashedPassword = password_hash($senha_funcionario, PASSWORD_DEFAULT);
            $sql = "UPDATE funcionario SET senha_funcionario = :senha_funcionario WHERE id_funcionario = :id_funcionario";

            $stmt = $this->db->prepare($sql);
            $stmt -> bindParam("id_funcionario", $id_funcionario, PDO::PARAM_INT);
            $stmt -> bindParam("senha_funcionario", $hashedPassword, PDO::PARAM_STR);

            return $stmt -> execute();
        } catch(PDOException $e) {
            die("Erro ao trocar a senha" . $e);
            return false;
        }
    }
}
?>