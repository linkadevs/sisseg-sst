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
        Recebe os dados da primeira etapa do checklist
        e guarda temporariamente na SESSION.
    */

    public function iniciar()
    {
        session_start();

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {

            header("Location: ../View/checklistpart1.php");
            exit;
        }

        // Agora recebe o ID do administrador
        $idAdm = intval($_POST["id_adm"] ?? 0);

        $turno = trim($_POST["turno"] ?? "");

        if ($idAdm <= 0 || empty($turno)) {

            $_SESSION["erro"] = "Todos os campos são obrigatórios.";

            header("Location: ../View/checklistpart1.php");
            exit;
        }

        // Busca o administrador no banco
        $administrador = $this->checagem->buscarAdministradorPorId($idAdm);

        if (!$administrador) {

            $_SESSION["erro"] = "Administrador inválido.";

            header("Location: ../View/checklistpart1.php");
            exit;
        }

        $turno = htmlspecialchars(
            $turno,
            ENT_QUOTES,
            "UTF-8"
        );

        $_SESSION["checagem"] = [

            "id_adm" => $administrador["id_adm"],

            "responsavel" => $administrador["nome_adm"],

            "turno" => $turno

        ];

        header("Location: ../View/checklistpart2.php");
        exit;
    }

    public function carregarChecklist()
    {
        $administradores = $this->checagem->listarAdministradores();

        require "../View/checklistpart1.php";
    }
}

/*
|--------------------------------------------------------------------------
| Roteamento
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

    default:

        header("Location: ../View/checklistpart1.php");
        exit;
}