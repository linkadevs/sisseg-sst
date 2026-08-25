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
                echo '<script>
                        alert("O CPF deve conter exatamente 11 dígitos. (Insira apenas números)");
                        window.history.back();
                    </script>';
                exit;
            }

            // FORMATA O CPF 000.000.000-00
            $cpf_adm = preg_replace(
                "/(\d{3})(\d{3})(\d{3})(\d{2})/",
                "$1.$2.$3-$4",
                $cpf_adm
            );
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
            $_SESSION['success_message'] = "Perfil atualizado com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao atualizar o perfil.";
        }
    }

    public function updatePassword($id_adm, $senha_adm){

        if (empty($id_adm) || empty($senha_adm)) {
            $_SESSION['error_message'] = "Todos os campos de senha são obrigatórios.";
            return false;
        }

        $success = $this->AdministradorModel->changePassword($id_adm, $senha_adm);

        if ($success) {
            $_SESSION['success_message'] = "Senha alterada com sucesso!";
        } else {
            $_SESSION['error_message'] = "Ocorreu um erro ao alterar a senha.";
        }
    }
}