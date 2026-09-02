<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Administrador.php';

use Model\Administrador;

use PDO;
use PDOException;
use Exception;

class AdministradorController{
    private $AdministradorModel;

    public function __construct(){
        $this -> AdministradorModel = new Administrador();
    }

    public function selecionarAdministradorByID($id_adm){
        try{
            return $this -> AdministradorModel -> getAdministradorByID($id_adm);
        }catch(PDOException $e){
            die("Erro ao selecionar o administrador pelo id. Código: " . $e->getMessage());
            return false;
        }
    }

    public function updateAdministrador($id_adm, $nome_adm, $cpf_adm, $setor_adm, $cargo_adm){

        if($cpf_adm !== '') {
            $cpf_adm = preg_replace('/\D/', '', $cpf_adm);
            if (strlen($cpf_adm) !== 11) {
                $_SESSION['profile_message'] = 'O CPF deve conter exatamente 11 dígitos. (Insira apenas números)';
                exit;
            }
            for ($t = 9; $t < 11; $t++) {

                $d = 0;

                for ($c = 0; $c < $t; $c++) {
                    $d += $cpf_adm[$c] * (($t + 1) - $c);
                }

                $d = ((10 * $d) % 11) % 10;

                if ($cpf_adm[$c] != $d) {
                    $_SESSION['profile_message'] = 'CPF inválido';
                    return false;
                }
            }
        }

        if(empty($nome_adm) || empty($cpf_adm) || empty($setor_adm) || empty($cargo_adm)) {
            $_SESSION['profile_message'] = 'Por favor, preencha todos os campos';
            return false;
        }

        // Busca os dados atuais para preencher qualquer campo que não tenha sido enviado
        $user = $this->AdministradorModel->getAdministradorByID($id_adm);

        if(empty($nome_adm)) $nome_adm = $user['nome_adm'];
        if(empty($cpf_adm)) $cpf_adm = $user['cpf_adm'];
        if(empty($setor_adm)) $setor_adm = $user['setor_adm'];
        if(empty($cargo_adm)) $cargo_adm = $user['cargo_adm'];

        $success = $this->AdministradorModel->updateAdministrador($id_adm, $nome_adm, $cpf_adm, $setor_adm, $cargo_adm);
        if ($success) {
            $_SESSION['nome_adm'] = $nome_adm;
            $_SESSION['cpf_adm'] = $cpf_adm;
            $_SESSION['setor_adm'] = $setor_adm;
            $_SESSION['cargo_adm'] = $cargo_adm;
            $_SESSION['profile_message'] = "Perfil atualizado com sucesso!";
        } else {
            $_SESSION['profile_message'] = "Erro ao atualizar o perfil.";
        }
    }

    public function updatePassword($id_adm, $senha_adm){

        if (strlen($senha_adm) < 6) {
            $_SESSION['profile_message'] = "A senha precisa conter no mínimo 6 caracteres";
            return false;
        }

        $success = $this->AdministradorModel->changePassword($id_adm, $senha_adm);

        if ($success) {
            $_SESSION['profile_message'] = "Senha alterada com sucesso!";
        } else {
            $_SESSION['profile_message'] = "Ocorreu um erro ao alterar a senha.";
        }
    }
}