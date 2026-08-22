<?php
namespace Model;

require_once __DIR__ . "/../Model/Connection.php";

use PDO;
use PDOException;
use Exception;

class Administrador{
    private $db;

    public function __construct(){
        $this-> db = Connection::getInstance();
    }

    public function getAdministradorByID($id_adm){
        try{
            $sql = "SELECT * FROM administrador WHERE id_adm = :id_adm";
            $stmt = $this->db->prepare($sql);
            $stmt -> execute([
                ':id_adm' => $id_adm
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Erro ao selecionar Administrador por ID: ' . $e);
        }
    }

    public function updateAdministrador($id_adm, $nome_adm, $cpf_adm, $setor_adm, $cargo_adm){
        try{
            $sql = 'UPDATE administrador SET nome_adm = :nome_adm, cpf_adm = :cpf_adm, setor_adm = :setor_adm, cargo_adm = :cargo_adm WHERE id_adm = :id_adm';

            $stmt = $this->db->prepare($sql);
            $stmt -> bindParam("id_adm", $id_adm, PDO::PARAM_INT);
            $stmt -> bindParam("nome_adm", $nome_adm, PDO::PARAM_STR);
            $stmt -> bindParam("cpf_adm", $cpf_adm, PDO::PARAM_STR);
            $stmt -> bindParam("setor_adm", $setor_adm, PDO::PARAM_STR);
            $stmt -> bindParam("cargo_adm", $cargo_adm, PDO::PARAM_STR);

            return $stmt -> execute();
        }catch(PDOException $e) {
            die("Erro ao atualizar as informações do perfil. Código: " . $e);
            return false;
        }
    }

    public function changePassword($id_adm, $senha_adm){
        try{
            $hashedPassword = password_hash($senha_adm, PASSWORD_DEFAULT);
            $sql = "UPDATE administrador SET senha_adm = :senha_adm WHERE id_adm = :id_adm";

            $stmt = $this->db->prepare($sql);
            $stmt -> bindParam("id_adm", $id_adm, PDO::PARAM_INT);
            $stmt -> bindParam("senha_adm", $hashedPassword, PDO::PARAM_STR);

            return $stmt -> execute();
        } catch(PDOException $e) {
            die("Erro ao trocar a senha" . $e);
            return false;
        }
    }
}
 