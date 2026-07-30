<?php

namespace Controller;

use Model\ModelLogin;
use Exception;

session_start();

require_once __DIR__ . '/../Model/ModelLogin.php';

class ControllerLogin
{
    private $db;

    public function __construct()
    {
        // Instancia o Model
        $this->db = new ModelLogin();
    }

    /* Realiza o login do usuário */
    public function login()
    {
        // Verifica se o formulário foi enviado
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return;
        }

        // Coleta os dados enviados pelo formulário
        $cpf = preg_replace('/[^0-9]/', '', ($_POST['cpf'] ?? ''));

        $senha = $_POST['senha'] ?? '';

        // Vetor para armazenar os erros encontrados
        $erros = [];

        // Validação do CPF
        if (empty($cpf)) {
            $erros[] = 'O CPF é obrigatório.';
        }

        // Validação da senha
        if (empty($senha)) {
            $erros[] = 'A senha é obrigatória.';
        }

        // Caso exista algum erro, retorna para a tela de login
        if (!empty($erros)) {

            $_SESSION['erro_login'] = $erros;

            $_SESSION['dados_login'] = [
                'cpf' => $_POST['cpf']
            ];

            header('Location: ../View/login.php');
            exit;
        }

        try {

            // Procura o usuário na tabela de administradores
            $usuario = $this->db->buscarAdministrador($cpf);

            // Caso não encontre, procura na tabela de funcionários
            if (!$usuario) {

                $usuario = $this->db->buscarFuncionario($cpf);

                if ($usuario) {

                    // Verifica a senha do funcionário
                    if (!password_verify($senha, $usuario['senha_funcionario'])) {

                        throw new Exception('CPF ou senha inválidos.');
                    }

                    // Cria as sessões do funcionário
                    $_SESSION['id_funcionario'] = $usuario['id_funcionario'];
                    $_SESSION['nome'] = $usuario['nome_funcionario'];
                    $_SESSION['setor'] = $usuario['setor_funcionario'];
                    $_SESSION['tipo'] = 'funcionario';

                    header('Location: ../View/pagina_principal_funcionario.php');
                    exit;
                }

                throw new Exception('CPF ou senha inválidos.');
            }

            // Verifica a senha do administrador
            if (!password_verify($senha, $usuario['senha_adm'])) {

                throw new Exception('CPF ou senha inválidos.');
            }

            // Cria as sessões do administrador
            $_SESSION['id_adm'] = $usuario['id_adm'];
            $_SESSION['nome'] = $usuario['nome_adm'];
            $_SESSION['setor'] = $usuario['setor_adm'];
            $_SESSION['tipo'] = 'administrador';

            header('Location: ../View/pagina_principal_adm.php');
            exit;

        } catch (Exception $e) {

            // Salva a mensagem de erro
            $_SESSION['erro_login'] = [
                $e->getMessage()
            ];

            // Mantém o CPF preenchido
            $_SESSION['dados_login'] = [
                'cpf' => $_POST['cpf']
            ];

            header('Location: ../View/login.php');
            exit;
        }
    }
}

// Instancia o Controller
$controller = new ControllerLogin();

// Executa o login
$controller->login();

?>