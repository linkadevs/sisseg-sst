<?php

namespace Controller;

use Model\ModelCad;
use Exception;

session_start();

require_once __DIR__ . '/../Model/ModelCad.php';

class ControllerCad
{
    private $db;

    public function __construct()
    {
        // Instancia o Model
        $this->db = new ModelCad();
    }

    /* Valida CPF: formato e dígitos verificadores*/
    private function validaCPF($cpf)
    {
        // Remove tudo que não for número
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Verifica se possui 11 dígitos ou se todos os números são iguais
        if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcula os dígitos verificadores
        for ($t = 9; $t < 11; $t++) {

            $d = 0;

            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }

            $d = ((10 * $d) % 11) % 10;

            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    /* Realiza o cadastro do funcionário*/
    public function cadastrar()
    {
        // Verifica se o formulário foi enviado
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return;
        }

        // Coleta os dados enviados pelo formulário
        $nome = trim($_POST['nome'] ?? '');
        $cpf = preg_replace('/[^0-9]/', '', ($_POST['cpf'] ?? ''));
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';
        $turno = $_POST['turno'] ?? '';
        $cargo = trim($_POST['cargo'] ?? '');
        $setor = trim($_POST['setor'] ?? '');

        // Vetor para armazenar os erros encontrados
        $erros = [];

        // Validação do nome
        if (empty($nome)) {
            $erros[] = 'O nome é obrigatório.';
        }

        // Validação do CPF
        if (!$this->validaCPF($cpf)) {

            $erros[] = 'CPF inválido.';

        } elseif ($this->db->cpfExistente($cpf)) {

            $erros[] = 'Já existe um cadastro com este CPF.';
        }

        // Validação da senha
        if (strlen($senha) < 6) {
            $erros[] = 'A senha deve possuir no mínimo 6 caracteres.';
        }

        if ($senha != $confirmarSenha) {
            $erros[] = 'As senhas não coincidem.';
        }

        // Validação do turno
        $turnosValidos = [
            'Matutino',
            'Vespertino',
            'Noturno',
            'Integral'
        ];

        if (!in_array($turno, $turnosValidos)) {
            $erros[] = 'Turno inválido.';
        }

        // Validação do cargo
        if (empty($cargo)) {
            $erros[] = 'O cargo é obrigatório.';
        }

        // Validação do setor
        if (empty($setor)) {
            $erros[] = 'O setor é obrigatório.';
        }

        // Caso exista algum erro, retorna para a tela de cadastro
        if (!empty($erros)) {

            $_SESSION['erro_cadastro'] = $erros;

            $_SESSION['dados_form'] = [
                'nome' => $nome,
                'cpf' => $_POST['cpf'],
                'turno' => $turno,
                'cargo' => $cargo,
                'setor' => $setor
            ];

            header('Location: ../View/cadastro.php');
            exit;
        }

        try {

            // Criptografa a senha antes de salvar no banco
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            // Monta o vetor de dados
            $dados = [
                'nome' => $nome,
                'cpf' => $cpf,
                'senha' => $senhaHash,
                'turno' => $turno,
                'cargo' => $cargo,
                'setor' => $setor
            ];

            // Chama o Model para inserir no banco
            if (!$this->db->inserirfuncionario($dados)) {
                throw new Exception('Erro ao cadastrar o funcionário.');
            }

            // Cadastro realizado com sucesso
            $_SESSION['sucesso_cadastro'] = 'Cadastro realizado com sucesso! Faça o login.';

            header('Location: ../View/login.php');
            exit;

        } catch (Exception $e) {

            // Salva a mensagem de erro
            $_SESSION['erro_cadastro'] = [
                $e->getMessage()
            ];

            // Mantém os dados preenchidos
            $_SESSION['dados_form'] = [
                'nome' => $nome,
                'cpf' => $_POST['cpf'],
                'turno' => $turno,
                'cargo' => $cargo,
                'setor' => $setor
            ];

            header('Location: ../View/cadastro.php');
            exit;
        }
    }
}

// Instancia o Controller
$controller = new ControllerCad();

// Executa o cadastro
$controller->cadastrar();

?>