<?php

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Funcionario.php';

use Exception;
use Model\Funcionario;

class FuncionarioController {
    private $funcionarioModel;

    public function __construct() {
        $this->funcionarioModel = new Funcionario();
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
            $senha_funcionario = password_hash($senha_funcionario, PASSWORD_DEFAULT);
            $cpf_funcionario = preg_replace('/\D/', '', $cpf_funcionario);
            if (strlen($cpf_funcionario) !== 11) {
                $_SESSION['erro'] = 'CPF Inválido';
                header('Location: ../View/gerenciamento_funcionarios.php');
                exit;
            }
            $cpf_funcionario = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf_funcionario);
            return $this->funcionarioModel->criarFuncionario(
                $nome_funcionario,
                $cpf_funcionario,
                $setor_funcionario,
                $cargo_funcionario,
                $turno_funcionario,
                $senha_funcionario
            );
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao criar funcionário',
                0,
                $e
            );
        }
    }

    public function selecionarTodosOsFuncionarios() :array {
        try {
            return $this->funcionarioModel->selecionarTodosOsFuncionarios();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todos os funcionarios',
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
        ?string $senha_funcionario = null
    ) :bool {
        try {
            if(empty($senha_funcionario)) {
                $senha_funcionario = $this->funcionarioModel->senhaFuncionario($id_funcionario);
            } else {
                $senha_funcionario = password_hash($senha_funcionario, PASSWORD_DEFAULT);
            }
            $cpf_funcionario = preg_replace('/\D/', '', $cpf_funcionario);
            return $this->funcionarioModel->atualizarFuncionario(
                $id_funcionario,
                $nome_funcionario,
                $cpf_funcionario,
                $setor_funcionario,
                $cargo_funcionario,
                $turno_funcionario,
                $senha_funcionario
            );
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao editar funcionario',
                0,
                $e
            );
        }
    }

    public function deletarFuncionario($id_funcionario) {
        try {
            return $this->funcionarioModel->deletarFuncionario(intval($id_funcionario));
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao deletar funcionario',
                0,
                $e
            );
        }
    }

    public function pesquisarFuncionario(string $pesquisa) :array {
        try {
            $pesquisa = "%$pesquisa%";
            return $this->funcionarioModel->pesquisarFuncionario($pesquisa);
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao pesquisar funcionario',
                0,
                $e
            );
        }
    }

    public function selecionarFuncionarioPorId(int $id_funcionario) :array {
        try {
            return $this->funcionarioModel->selecionarFuncionarioPorId($id_funcionario);
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar funcionário por ID',
                0,
                $e
            );
        }
    }

    public function selecionarFuncionarioByID($id_funcionario){
        try{
            return $this -> funcionarioModel -> getFuncionarioByID($id_funcionario);
        }catch(Exception $e){
            die("Erro ao selecionar o funcionário pelo id. Código: " . $e->getMessage());
            return false;
        }
    }

    public function updateFuncionario($id_funcionario, $nome_funcionario, $cpf_funcionario, $setor_funcionario, $cargo_funcionario){

        if($cpf_funcionario !== '') {
            $cpf_funcionario = preg_replace('/\D/', '', $cpf_funcionario);
            if (strlen($cpf_funcionario) !== 11) {
                echo '<script>
                        alert("O CPF deve conter exatamente 11 dígitos. (Insira apenas números)");
                        window.history.back();
                    </script>';
                exit;
            }
        }

        $user = $this->funcionarioModel->getFuncionarioByID($id_funcionario);

        if(empty($nome_funcionario)) $nome_funcionario = $user['nome'];
        if(empty($cpf_funcionario)) $cpf_funcionario = $user['cpf'];
        if(empty($setor_funcionario)) $setor_funcionario = $user['setor'];
        if(empty($cargo_funcionario)) $cargo_funcionario = $user['cargo'];

        $success = $this->funcionarioModel->updateFuncionario($id_funcionario, $nome_funcionario, $cpf_funcionario, $setor_funcionario, $cargo_funcionario);
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

    public function updatePassword($id_funcionario, $senha_funcionario){

        if (empty($id_funcionario) || empty($senha_funcionario)) {
            $_SESSION['error_message'] = "Todos os campos de senha são obrigatórios.";
            return false;
        }

        $success = $this->funcionarioModel->changePassword($id_funcionario, $senha_funcionario);

        if ($success) {
            $_SESSION['success_message'] = "Senha alterada com sucesso!";
        } else {
            $_SESSION['error_message'] = "Ocorreu um erro ao alterar a senha.";
        }
    }
}

?>