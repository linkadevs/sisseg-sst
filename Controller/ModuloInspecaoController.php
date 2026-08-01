<?php

namespace Controller;
require_once __DIR__ . '/../Model/ModuloInspecao.php';

use Exception;
use Model\ModuloInspecao;

class ModuloInspecaoController
{
    private $model;

    public function __construct()
    {
        $this->model = new ModuloInspecao();
    }

    public function listarFuncoes()
    {
        try {
            return $this->model->listarFuncoes();
        } catch (Exception $e) {
            throw new Exception("Erro ao listar funções: " . $e->getMessage());
        }
    }

    public function buscarEpisPorFuncao($id_funcao)
    {
        try {
            return $this->model->buscarEpisPorFuncao($id_funcao);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar EPIs: " . $e->getMessage());
        }
    }

    public function listarTodosEpis()
    {
        try {
            return $this->model->listarTodosEpis();
        } catch (Exception $e) {
            throw new Exception("Erro ao listar EPIs: " . $e->getMessage());
        }
    }

    public function buscarFuncao($id)
    {
        try {
            $funcao = $this->model->buscarFuncaoPorId($id);
            $epis = $this->model->buscarEpisPorFuncao($id);
            
            return [
                'success' => true,
                'dados' => [
                    'funcao' => $funcao,
                    'epis' => $epis
                ]
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function criarFuncao()
    {
        try {
            $dados = [
                'nome_funcao' => $_POST['nome_funcao'] ?? '',
                'epis' => $_POST['epis'] ?? []
            ];

            if (empty($dados['nome_funcao'])) {
                return ['success' => false, 'message' => 'Nome da função é obrigatório'];
            }

            $this->model->criarFuncao($dados);
            return [
                'success' => true,
                'message' => 'Função criada com sucesso!'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function atualizarFuncao($id)
    {
        try {
            if ($id <= 0) {
                return ['success' => false, 'message' => 'ID inválido'];
            }

            $dados = [
                'nome_funcao' => $_POST['nome_funcao'] ?? '',
                'epis' => $_POST['epis'] ?? []
            ];

            if (empty($dados['nome_funcao'])) {
                return ['success' => false, 'message' => 'Nome da função é obrigatório'];
            }

            $this->model->atualizarFuncao($id, $dados);
            return [
                'success' => true,
                'message' => 'Função atualizada com sucesso!'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deletarFuncao($id)
    {
        try {
            if ($id <= 0) {
                return ['success' => false, 'message' => 'ID inválido'];
            }

            $this->model->deletarFuncao($id);
            return [
                'success' => true,
                'message' => 'Função excluída com sucesso!'
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

}