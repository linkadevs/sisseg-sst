<?php

namespace Controller;

use Model\Checagem;


require_once __DIR__ . "/../Model/ChecklistModel.php";

class ChecklistController
{
    private $checagem;

    public function __construct()
    {
        $this->checagem = new Checagem();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function carregarChecklist()
    {
        $administradores = $this->checagem->listarAdministradores();
        require "../View/checklistpart1.php";
    }

    public function iniciar()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $_SESSION["erro"] = "Método de requisição inválido.";
            header("Location: ../View/checklistpart1.php");
            exit;
        }

        $id_adm = intval($_POST["id_adm"] ?? 0);
        $turno = trim($_POST["turno"] ?? "");

        if ($id_adm <= 0 || empty($turno)) {
            $_SESSION["erro"] = "Preencha todos os campos.";
            header("Location: ../View/checklistpart1.php");
            exit;
        }

        $administrador = $this->checagem->buscarAdministradorPorId($id_adm);

        if (!$administrador) {
            $_SESSION["erro"] = "Administrador inválido.";
            header("Location: ../View/checklistpart1.php");
            exit;
        }

        $_SESSION["checagem"] = [
            "id_adm" => $administrador["id_adm"],
            "responsavel" => $administrador["nome_adm"],
            "turno" => $turno
        ];

        session_write_close();

        header("Location: ../View/checklistpart2.php");
        exit;
    }

    public function salvarChecklist()
    {
        if (!isset($_SESSION["checagem"])) {
            $_SESSION["erro"] = "Sessão expirada. Preencha os dados novamente.";
            header("Location: ../View/checklistpart1.php");
            exit;
        }

        $resultado = $this->checagem->salvarChecklist(
            $_POST,
            $_SESSION["checagem"]
        );

        unset($_SESSION["checagem"]);

        if (!is_array($resultado)) {
            $_SESSION["erro"] = "Erro inesperado ao processar o checklist.";
            header("Location: ../View/checklistpart1.php");
            exit;
        }

        require "../View/checklistresultado.php";
    }

    public function listarChecklists()
    {
        // O Model já retorna o setor_adm silenciosamente
        $checklists = $this->checagem->listarTodosChecklists();
        require "../View/visualizacao_checklists.php";
    }

    public function pesquisarChecklists()
    {
        $pesquisa = trim($_GET["pesquisa"] ?? "");
        $pesquisa = filter_var($pesquisa, FILTER_SANITIZE_STRING);
        
        if (empty($pesquisa)) {
            $checklists = $this->checagem->listarTodosChecklists();
        } else {
            // A busca no Model já inclui administrador.setor_adm LIKE :pesquisa
            $checklists = $this->checagem->buscarChecklistsPorPesquisa($pesquisa);
        }
        require "../View/visualizacao_checklists.php";
    }

    public function exibirResultado()
    {
        $id_checklist = isset($_GET['id_checklist']) ? intval($_GET['id_checklist']) : 0;
        
        if ($id_checklist <= 0) {
            header("Location: visualizacao_checklists.php");
            exit;
        }

        $resultado = $this->checagem->buscarChecklistResultadoPorId($id_checklist);

        if (!$resultado) {
            $_SESSION["erro"] = "Checklist não encontrado.";
            header("Location: visualizacao_checklists.php");
            exit;
        }

        require "../View/checklistresultado.php";
    }
}

$controller = new ChecklistController();
$acao = $_GET["acao"] ?? "";

switch ($acao) {
    case "carregar":
        $controller->carregarChecklist();
        break;
    case "iniciar":
        $controller->iniciar();
        break;
    case "salvar":
        $controller->salvarChecklist();
        break;
    case "listar":
        $controller->listarChecklists();
        break;
    case "pesquisar":
        $controller->pesquisarChecklists();
        break;
    case "exibirResultado":
        $controller->exibirResultado();
        break;
    default:
        header("Location: ../View/checklistpart1.php");
        exit;
}