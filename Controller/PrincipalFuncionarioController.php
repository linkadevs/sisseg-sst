<?php

namespace Controller;
require_once __DIR__ . '/../Model/PrincipalFuncionario.php';

use Exception;
use Model\PrincipalFuncionario;

class PrincipalFuncionarioController
{
    private $model;

    public function __construct()
    {
        $this->model = new PrincipalFuncionario();
    }

    /**
     * Busca dados do funcionário
     */
    public function buscarFuncionario($id)
    {
        try {
            return $this->model->buscarFuncionario($id);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar funcionário: " . $e->getMessage());
        }
    }

    /**
     * Busca progresso de treinamentos do funcionário
     * Total disponível vs Concluídos
     */
    public function progressoTreinamentos($id_funcionario)
    {
        try {
            $total = $this->model->contarTotalTreinamentos();
            $concluidos = $this->model->contarTreinamentosConcluidos($id_funcionario);
            
            return [
                'total' => $total,
                'concluidos' => $concluidos,
                'percentual' => $total > 0 ? round(($concluidos / $total) * 100) : 0
            ];
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar progresso de treinamentos: " . $e->getMessage());
        }
    }

    /**
     * Busca dias sem incidentes
     */
    public function diasSemIncidentes()
    {
        try {
            $ultimo = $this->model->buscarUltimoIncidente();
            
            if (!$ultimo) {
                return 30;
            }
            
            $data_ultimo = new \DateTime($ultimo['data_incidente']);
            $hoje = new \DateTime('today', new \DateTimeZone('America/Sao_Paulo'));
            $dias = $hoje->diff($data_ultimo)->days;
            
            return $dias;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar dias sem incidentes: " . $e->getMessage());
        }
    }

    /**
     * Busca total de incidentes (todos)
     */
    public function totalIncidentes()
    {
        try {
            return $this->model->contarTotalIncidentes();
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar total de incidentes: " . $e->getMessage());
        }
    }

    /**
     * Busca notificações (últimos 3)
     */
    public function notificacoes()
    {
        try {
            return $this->model->buscarNotificacoes();
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar notificações: " . $e->getMessage());
        }
    }
}