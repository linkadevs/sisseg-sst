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
}

?>