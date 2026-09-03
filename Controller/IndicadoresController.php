<?php 

namespace Controller;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Model/Indicadores.php';
require_once __DIR__ . '/../Model/Funcionario.php';

require_once __DIR__ . '/TreinamentoController.php';
require_once __DIR__ . '/FuncionarioTreinamentoController.php';
require_once __DIR__ . '/InspecaoController.php';
require_once __DIR__ . '/../Model/IncidenteFuncionario.php';

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

use Exception;
use Model\Indicadores;
use Model\Funcionario;

use Controller\TreinamentoController;
use Controller\FuncionarioTreinamentoController;
use Controller\InspecaoController;
use Model\IncidenteFuncionario;

class IndicadoresController {
    private $indicadores_model;
    private $funcionario_model;
    
    private $treinamento_controller;
    private $funcionario_treinamento_controller;
    private $inspecao_controller;
    private $incidente_funcionario;

    public function __construct() {
        $this->indicadores_model = new Indicadores();
        $this->funcionario_model = new Funcionario();
        $this->treinamento_controller = new TreinamentoController();
        $this->funcionario_treinamento_controller = new FuncionarioTreinamentoController();
        $this->inspecao_controller = new InspecaoController();
        $this->incidente_funcionario = new IncidenteFuncionario();
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
            $this->atualizarDadosIndicador();
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
            $this->atualizarDadosIndicador();
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
            $this->atualizarDadosIndicador();
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

    public function atualizarDadosIndicador() :bool {
        $indicadores = $this->selecionarTodosIndicadores();

        // Define o fuso horário padrão para São Paulo
        $fusoSaoPaulo = new \DateTimeZone('America/Sao_Paulo');

        // Captura a data de hoje zerando as horas (00:00:00) para fazer a comparação correta
        $hoje = new \DateTime('now', $fusoSaoPaulo);
        $hoje->setTime(0, 0, 0);

        $treinamentos2 = $this->treinamento_controller->listAll();
        $treinamentosFuturosOuHoje = [];

        foreach ($treinamentos2 as $treinamento) {
            if (!empty($treinamento['data_limite_treinamento'])) {
                // Converte a data do treinamento para DateTime no fuso de SP
                $dataTreinamento = new \DateTime($treinamento['data_limite_treinamento'], $fusoSaoPaulo);
                $dataTreinamento->setTime(0, 0, 0);

                // Se a data do treinamento for maior ou igual a hoje
                if ($dataTreinamento >= $hoje) {
                    $treinamentosFuturosOuHoje[] = $treinamento;
                }
            }
        }

        foreach($indicadores as $indicador){
            if(!empty($indicador['id_funcionarios'])){
                $funcionariosIndicador = explode(', ',$indicador['id_funcionarios']);
            } else {
                $funcionariosIndicador = [];
            }
            $total_equipe = 0;
            $percentuais = [];
            $qtdInspecoes = [];
            $datas = [];
            foreach($funcionariosIndicador as $f) {
                $ts = $this->funcionario_treinamento_controller->selecionarTreinamentosRealizadosFuncionario($f);
                $total_equipe += count($ts);
                $percentuais[$f] = $this->inspecao_controller->selecionarDadosConformidadePorFuncionario($f);
                $qtdInspecoes[$f] = $this->inspecao_controller->selecionarQtdInspecaoPorFuncionario($f);
                $datas[$f] = $this->incidente_funcionario->selecionarUltimoIncidenteFuncionario($f);
            }
            $qtdMaximaTreinamentos = count($funcionariosIndicador)*count($treinamentosFuturosOuHoje);
            if($qtdMaximaTreinamentos === 0) {
                $percentualTreinamento = 0;
            } else {
                $percentualTreinamento = ($total_equipe/$qtdMaximaTreinamentos)*100;
            }
            $somaInspecoes = 0;
            $multiplicacao = 0;
            foreach($funcionariosIndicador as $f) {
                if(empty($percentuais[$f]['porcentagem_conclusao'])){
                    $percentuais[$f]['porcentagem_conclusao'] = 0;
                }
                $multiplicacao += $qtdInspecoes[$f]*$percentuais[$f]['porcentagem_conclusao'];
                $somaInspecoes += $qtdInspecoes[$f];
            }
            if($somaInspecoes === 0) {
                $percentualEpi = 0;
            } else {
                $percentualEpi = $multiplicacao/$somaInspecoes;
            }
            
            if (!empty($datas)) {
                $dataRecente = max($datas);

                // 1. Cria o objeto da data recente
                $dataInicio = new \DateTime($dataRecente);

                // 2. Cria o objeto da data atual
                $hoje = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));

                // 3. Zera o horário de ambas para comparar apenas os DIAS inteiros (sem interferência de horas)
                $dataInicio->setTime(0, 0, 0);
                $hoje->setTime(0, 0, 0);

                // 4. Calcula a diferença entre as duas datas
                $diferenca = $dataInicio->diff($hoje);

                // 5. Quantidade total de dias decorridos
                $diasDecorridos = $diferenca->days;
            } else {
                $diasDecorridos = 0; // Garantia caso o array de datas esteja vazio
            }
            $this->indicadores_model->atualizarDadosIndicador(round($percentualTreinamento), $diasDecorridos, round($percentualEpi), $indicador['id_indicadores']);

            $minTreinamento = PHP_INT_MAX;
            $maxTreinamento = PHP_INT_MIN;

            $minEpi = PHP_INT_MAX;
            $maxEpi = PHP_INT_MIN;

            $minDias = PHP_INT_MAX;
            $maxDias = PHP_INT_MIN;

            // 3. Descobre o menor e o maior valor de cada atributo entre TODAS as equipes
            if(count($indicadores) > 1) {
                foreach ($indicadores as $indicador2) {
                    $treinamento = (int) $indicador2['treinamento_percentual_indicadores'];
                    $epi         = (int) $indicador2['epi_percentual_indicadores'];
                    $dias        = (int) $indicador2['dias_sem_acidentes_indicadores'];
    
                    if ($treinamento < $minTreinamento) $minTreinamento = $treinamento;
                    if ($treinamento > $maxTreinamento) $maxTreinamento = $treinamento;
    
                    if ($epi < $minEpi) $minEpi = $epi;
                    if ($epi > $maxEpi) $maxEpi = $epi;
    
                    if ($dias < $minDias) $minDias = $dias;
                    if ($dias > $maxDias) $maxDias = $dias;
                }

                // 4. Função interna para calcular a pontuação proporcional (Regra de Três)
                $calcularPontos = function ($valor, $min, $max, $pontosMaximos) {
                    // 1. Converte todos os valores para float
                    $valor         = (float) $valor;
                    $min           = (float) $min;
                    $max           = (float) $max;
                    $pontosMaximos = (float) $pontosMaximos;

                    // 2. Trata o cenário onde todas as equipes empatam ($max == $min)
                    if ($max - $min <= 0) {
                        // Se todas as equipes tiverem atingido a meta/nota (ex: todo mundo com 100%), 
                        // atribui a pontuação máxima. Caso contrário (todos 0%), atribui 0.
                        return ($valor > 0) ? $pontosMaximos : 0.0;
                    }

                    // 3. Aplica a min-max normalization relativa
                    $pontos = (($valor - $min) / ($max - $min)) * $pontosMaximos;

                    // 4. Garante que a pontuação fique estritamente entre 0 e o teto $pontosMaximos
                    return min(max($pontos, 0.0), $pontosMaximos);
                };
    
                // 5. Calcula os pontos da equipe atual usando a fórmula proporcional
                $pontosTreinamento = $calcularPontos($percentualTreinamento, $minTreinamento, $maxTreinamento, 300);
                $pontosEpi         = $calcularPontos($percentualEpi, $minEpi, $maxEpi, 300);
                $pontosDias        = $calcularPontos($dataRecente, $minDias, $maxDias, 400);
                // die(var_dump($maxDias));
    
                // Pontuação Total da equipe (arredondada para número inteiro)
                $pontosFinaisEquipe = (int) round($pontosTreinamento + $pontosEpi + $pontosDias);
    
                // 6. Atualiza o campo 'pontos_indicadores' da equipe no banco
                $this->indicadores_model->atualizarPontosIndicador($pontosFinaisEquipe, $indicador['id_indicadores']);
            } else {
                $pontosFinaisEquipe = 1000;
                $this->indicadores_model->atualizarPontosIndicador($pontosFinaisEquipe, $indicador['id_indicadores']);
            }

            
        }
        return true;
    }
}

?>