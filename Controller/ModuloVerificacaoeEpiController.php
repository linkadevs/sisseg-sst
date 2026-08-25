<?php

namespace Controller;
require_once __DIR__ . '/../Model/ModuloVerificacaoeEpi.php';

use Exception;
use \Model\ModuloVerificacaoeEpi;

class ModuloVerificacaoeEpiController
{
    private $modulomodel;

    public function __construct()
    {
        $this->modulomodel = new ModuloVerificacaoeEpi();
    }

    public function obteratividade()
    {
        try {
            $atividadesinfor = $this->modulomodel->exibir_atividades();
            return $atividadesinfor;
        } catch (Exception $erro) {
            throw new Exception("Não foi possível obter as atividades: " . $erro->getMessage());
        }
    }

    public function exibirNorma($id)
    {
        try {
            $normainfor = $this->modulomodel->exibirnorma($id);
            return $normainfor;
        } catch (Exception $erro) {
            throw new Exception("Erro ao tentar exibir norma: " . $erro->getMessage());
        }
    }

    public function obterepis($id_atividade)
    {
        try {
            $epis = $this->modulomodel->exibirepis($id_atividade);
            return $epis;
        } catch (Exception $erro) {
            throw new Exception("Não foi possível obter os EPIs: " . $erro->getMessage());
        }
    }

    public function obterPontuacaoSetor($nome_setor)
    {
        try {
            $pontuacao = $this->modulomodel->buscarPontuacaoSetor($nome_setor);
            return $pontuacao;
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar pontuação do setor: " . $erro->getMessage());
        }
    }

    public function atualizarPontuacaoSetor($nome_setor, $pontos_ganhos)
    {
        try {
            $resultado = $this->modulomodel->atualizarPontuacaoSetor($nome_setor, $pontos_ganhos);
            return $resultado;
        } catch (Exception $erro) {
            throw new Exception("Erro ao atualizar pontuação do setor: " . $erro->getMessage());
        }
    }

    public function obterIncidentesPorAtividade($id_atividade)
    {
        try {
            $incidentes = $this->modulomodel->exibirIncidentesPorAtividade($id_atividade);
            return $incidentes;
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar incidentes: " . $erro->getMessage());
        }
    }

    public function obterTodosIncidentes()
    {
        try {
            $incidentes = $this->modulomodel->exibirTodosIncidentes();
            return $incidentes;
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar todos os incidentes: " . $erro->getMessage());
        }
    }

    public function obterIncidentePorId($id_incidente)
    {
        try {
            $incidente = $this->modulomodel->exibirIncidentePorId($id_incidente);
            return $incidente;
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar incidente: " . $erro->getMessage());
        }
    }

    public function obterTodasPontuacoes()
    {
        try {
            $pontuacoes = $this->modulomodel->buscarTodasPontuacoes();
            return $pontuacoes;
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar todas as pontuações: " . $erro->getMessage());
        }
    }

    public function obterRankingSetores($limite = 10)
    {
        try {
            $ranking = $this->modulomodel->buscarRankingSetores($limite);
            return $ranking;
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar ranking de setores: " . $erro->getMessage());
        }
    }

    public function obterTodosContatos()
    {
        try {
            $contatos = $this->modulomodel->buscarTodosContatos();
            return $contatos;
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar contatos: " . $erro->getMessage());
        }
    }

    public function obterContatoPorId($id_contato)
    {
        try {
            $contato = $this->modulomodel->buscarContatoPorId($id_contato);
            return $contato;
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar contato: " . $erro->getMessage());
        }
    }

    public function obterContatoPorTipo($tipo)
    {
        try {
            $contato = $this->modulomodel->buscarContatoPorTipo($tipo);
            return $contato;
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar contato por tipo: " . $erro->getMessage());
        }
    }
}