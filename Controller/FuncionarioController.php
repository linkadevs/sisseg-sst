<?php
namespace Controller;
use Model\Funcionario;


use PDO;
use PDOException;
use Exception;

class FuncionarioController{
    private $FuncionarioModel;

    public function __construct(){
        $this -> FuncionarioModel = new Funcionario();
    }

    public function selecionarFuncionarioByID($id_funcionario){
        try{
            return $this -> FuncionarioModel -> getFuncionarioByID($id_funcionario);
        }catch(PDOException $e){
            die("Erro ao selecionar o funcionário pelo id. Código: " . $e->getMessage());
            return false;
        }
    }

    public function updateFuncionario($id_funcionario, $nome_funcionario, $cpf_funcionario, $setor_funcionario, $cargo_funcionario){

        if($cpf_funcionario !== '') {
            if (strlen($cpf_funcionario) !== 11) {
                echo '<script>
                        alert("O CPF deve conter exatamente 11 dígitos. (Insira apenas números)");
                        window.history.back();
                    </script>';
                exit;
            }
    
            // FORMATA O CPF 000.000.000-00
            $cpf_funcionario = preg_replace(
                "/(\d{3})(\d{3})(\d{3})(\d{2})/",
                "$1.$2.$3-$4",
                $cpf_funcionario
            );
        }

        $user = $this->FuncionarioModel->getFuncionarioByID($id_funcionario);

        if(empty($nome_funcionario)) $nome_funcionario = $user['nome'];
        if(empty($cpf_funcionario)) $cpf_funcionario = $user['cpf'];
        if(empty($setor_funcionario)) $setor_funcionario = $user['setor'];
        if(empty($cargo_funcionario)) $cargo_funcionario = $user['cargo'];

        $success = $this->FuncionarioModel->updateFuncionario($id_funcionario, $nome_funcionario, $cpf_funcionario, $setor_funcionario, $cargo_funcionario);
        if ($success) {
            $_SESSION['nome_funcionario'] = $nome_funcionario;
            $_SESSION['cpf_funcionario'] = $cpf_funcionario;
            $_SESSION['setor_funcionario'] = $setor_funcionario;
            $_SESSION['cargo_funcionario'] = $cargo_funcionario;
            $_SESSION['success_message'] = "Perfil atualizado com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao atualizar o perfil.";
        }
    }

    public function updatePassword($id_funcionario, $senha_funcionario, $confirmar_senha){

        if (empty($id_funcionario) || empty($senha_funcionario) || empty($confirmar_senha)) {
            $_SESSION['error_message'] = "Todos os campos de senha são obrigatórios.";
            return false;
        }
        if ($senha_funcionario !== $confirmar_senha) {
            $_SESSION['error_message'] = "As senhas não coincidem.";
            return false;
        }

        $success = $this->FuncionarioModel->changePassword($senha_funcionario, $id_funcionario);

        if ($success) {
            $_SESSION['success_message'] = "Senha alterada com sucesso!";
        } else {
            $_SESSION['error_message'] = "Ocorreu um erro ao alterar a senha.";
        }
    }
}
?>