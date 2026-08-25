<?php

namespace Controller;
require_once __DIR__ . '/../Model/ExibirCertificados.php';

use Exception;
use Model\ExibirCertificados;

class ExibirCertificadosController
{
    private $model;

    public function __construct()
    {
        $this->model = new ExibirCertificados();
    }

    /**
     * Busca os dados do funcionário
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
     * Busca todos os treinamentos do funcionário com status
     */
    public function buscarTreinamentos($id)
    {
        try {
            return $this->model->buscarTreinamentosComStatus($id);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar treinamentos: " . $e->getMessage());
        }
    }

    /**
     * Retorna o total de treinamentos
     */
    public function totalTreinamentos($id)
    {
        try {
            return $this->model->contarTreinamentos($id);
        } catch (Exception $e) {
            throw new Exception("Erro ao contar treinamentos: " . $e->getMessage());
        }
    }

    /**
     * Retorna o total de treinamentos válidos
     */
    public function totalValidos($id)
    {
        try {
            return $this->model->contarTreinamentosValidos($id);
        } catch (Exception $e) {
            throw new Exception("Erro ao contar treinamentos válidos: " . $e->getMessage());
        }
    }

    /**
     * Retorna o total de treinamentos inválidos
     */
    public function totalInvalidos($id)
    {
        try {
            return $this->model->contarTreinamentosInvalidos($id);
        } catch (Exception $e) {
            throw new Exception("Erro ao contar treinamentos inválidos: " . $e->getMessage());
        }
    }

    /**
     * Busca treinamentos filtrados por status
     */
    public function buscarTreinamentosFiltrados($id, $filtro = null)
    {
        try {
            $treinamentos = $this->model->buscarTreinamentosComStatus($id);
            
            if ($filtro === 'validos') {
                return array_filter($treinamentos, function($item) {
                    return $item['status_treinamento'] === 'valido';
                });
            } elseif ($filtro === 'invalidos') {
                return array_filter($treinamentos, function($item) {
                    return $item['status_treinamento'] === 'invalido';
                });
            }
            
            return $treinamentos;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar treinamentos filtrados: " . $e->getMessage());
        }
    }
}