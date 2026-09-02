<?php 

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Indicadores.php';
require_once __DIR__ . '/../Model/Funcionario.php';

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

use Exception;
use Model\Indicadores;
use Model\Funcionario;

class IndicadoresController {
    private $indicadores_model;
    private $funcionario_model;

    public function __construct() {
        $this->indicadores_model = new Indicadores();
        $this->funcionario_model = new Funcionario();
    }

    public function selecionarTodosIndicadores() {
        try {
            return $this->indicadores_model->selecionarTodosIndicadores();
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao selecionar todos os indicadores',
                0,
                $e
            );
        }
    }

    public function criarIndicador(
        string $nome_equipe_indicadores,
        array $funcionarios
    ) :bool {
        try {
            $success1 = $id_indicador = $this->indicadores_model->criarIndicador($nome_equipe_indicadores);
            $success2 = true;
            foreach($funcionarios as $funcionario) {
                $temp = $this->funcionario_model->atribuirIndicador(
                    $funcionario,
                    $id_indicador
                );
                if($temp == false) {
                    $success2 = false;
                }
            }
            if($success1 == true && $success2 == true) {
                $_SESSION['message'] = 'Equipe criada com sucesso';
                return true;
            } else {
                $_SESSION['message'] = 'Erro ao criar equipe';
                return false;
            }
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao criar indicador',
                0,
                $e
            );
        }
    }

    public function editarIndicador(
        int $id_indicador,
        string $nome_equipe_indicadores,
        array $funcionarios
    ) :bool {
        try {
            $success1 = $this->indicadores_model->editarIndicador($id_indicador, $nome_equipe_indicadores);
            $success2 = $this->funcionario_model->desatribuirIndicador($id_indicador);
            $success3 = true;
            foreach($funcionarios as $funcionario) {
                $temp = $this->funcionario_model->atribuirIndicador(
                    $funcionario,
                    $id_indicador
                );
                if($temp == false) {
                    $success3 = false;
                }
            }
            if($success1 == true && $success2 == true && $success3 == true) {
                $_SESSION['message'] = 'Equipe editada com sucesso';
                return true;
            } else {
                $_SESSION['message'] = 'Erro ao editar equipe';
                return false;
            }
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao editar indicador',
                0,
                $e
            );
        }
    }

    public function deletarIndicador(
        int $id_indicador
    ) :bool {
        try {
            $success = $this->indicadores_model->deletarIndicador($id_indicador);
            if($success == true) {
                $_SESSION['message'] = 'Equipe apagada com sucesso';
                return true;
            } else {
                $_SESSION['message'] = 'Erro ao apagar equipe';
                return false;
            }
        } catch (Exception $e) {
            throw new Exception(
                'Erro ao deletar indicador',
                0,
                $e
            );
        }
    }
}

?>