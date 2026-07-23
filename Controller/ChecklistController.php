<?php

namespace Controller;

use Model\Checagem;

require_once __DIR__ . "/../Model/Checagem.php";

class ChecklistController
{
    private $checagem;

    public function __construct()
    {
        $this->checagem = new Checagem();
    }


    /*
    |--------------------------------------------------------------------------
    | Página 1
    | Carrega administradores
    |--------------------------------------------------------------------------
    */

    public function carregarChecklist()
    {
        $administradores = $this->checagem->listarAdministradores();
        require "../View/checklistpart1.php";

    }


    /*
    |--------------------------------------------------------------------------
    | Página 1 -> Página 2
    | Salva dados temporários na SESSION
    |--------------------------------------------------------------------------
    */

    public function iniciar()
    {
        session_start();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {

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

        header("Location: ../View/checklistpart2.php");

        exit;

    }


    /*
    |--------------------------------------------------------------------------
    | Página 2 -> Página 3
    | Salva checklist e prepara resultado
    |--------------------------------------------------------------------------
    */


    public function salvarChecklist()
    {
        session_start();

        // mantém os dados da página 1
        if (!isset($_SESSION["checagem"])) {

            header("Location: ../View/checklistpart1.php");
            exit;

        }

        // dados vindos da página 1
        $dados = $_SESSION["checagem"];



        // dados vindos da página 2 (checkboxes)
        $postData = $_POST;


        // envia para a Model
        $resultado = $this->checagem->salvarChecklist(
            $postData,
            $dados
        );

        // abre página 3
        require "../View/checklistresultado.php";

    }



    /*
    |--------------------------------------------------------------------------
    | Página 4
    | Lista todos os checklists
    |--------------------------------------------------------------------------
    */

    public function listarChecklists()
    {
        $checklists = $this->checagem->listarTodosChecklists();
        require "../View/visualizacao_checklists.php";

    }

    /*
    |--------------------------------------------------------------------------
    | Página 4
    | Pesquisa
    |--------------------------------------------------------------------------
    */

    public function pesquisarChecklists()
    {
        $pesquisa = trim($_GET["pesquisa"] ?? "");

        if (empty($pesquisa)) {
            $checklists = $this->checagem->listarTodosChecklists();

        } else {
            $checklists = $this->checagem->buscarChecklistsPorPesquisa($pesquisa);
        }

        require "../View/visualizacao_checklists.php";

    }

}



/*
|--------------------------------------------------------------------------
| Rotas
|--------------------------------------------------------------------------
*/

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

    default:
        header("Location: ../View/checklistpart1.php");
        exit;

}