<?php

namespace Model;
require_once __DIR__ . '/../Model/Connection.php';

use Exception;
use PDO;
use Model\Connection;

class PrincipalFuncionario
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    /**
     * Busca dados do funcionário pelo ID
     */
    public function buscarFuncionario($id_funcionario)
    {
        try {
            $sql = "SELECT 
                        id_funcionario, 
                        nome_funcionario, 
                        cargo_funcionario,
                        setor_funcionario
                    FROM funcionario 
                    WHERE id_funcionario = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id_funcionario, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar funcionário: " . $e->getMessage());
        }
    }

    /**
     * Busca total de treinamentos disponíveis (TODOS da tabela treinamento)
     */
    public function contarTotalTreinamentos()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM treinamento";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (Exception $e) {
            throw new Exception("Erro ao contar total de treinamentos: " . $e->getMessage());
        }
    }

    /**
     * Busca total de treinamentos concluídos pelo funcionário (com certificado)
     */
    public function contarTreinamentosConcluidos($id_funcionario)
    {
        try {
            $sql = "SELECT COUNT(DISTINCT ft.id_treinamento_fk) as total 
                    FROM funcionario_treinamento ft
                    INNER JOIN prova p ON ft.id_treinamento_fk = p.id_treinamento_fk
                    INNER JOIN certificado c ON c.id_prova_fk = p.id_prova AND c.id_funcionario_fk = ft.id_funcionario_fk
                    WHERE ft.id_funcionario_fk = :id_funcionario";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_funcionario', $id_funcionario, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (Exception $e) {
            throw new Exception("Erro ao contar treinamentos concluídos: " . $e->getMessage());
        }
    }

    /**
     * Busca o último incidente para calcular dias sem incidentes
     */
    public function buscarUltimoIncidente()
    {
        try {
            $sql = "SELECT data_incidente FROM incidente ORDER BY data_incidente DESC LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar último incidente: " . $e->getMessage());
        }
    }

    /**
     * Busca total de incidentes (todos, sem filtrar por tipo)
     */
    public function contarTotalIncidentes()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM incidente";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (Exception $e) {
            throw new Exception("Erro ao contar total de incidentes: " . $e->getMessage());
        }
    }

    /**
     * Busca o treinamento mais recente
     */
    public function buscarUltimoTreinamento()
    {
        try {
            $sql = "SELECT 
                        id_treinamento AS id,
                        nome_treinamento AS mensagem,
                        'treinamento' AS tipo,
                        data_limite_treinamento AS data
                    FROM treinamento 
                    ORDER BY id_treinamento DESC 
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar último treinamento: " . $e->getMessage());
        }
    }

    /**
     * Busca os incidentes mais recentes (sem filtrar por tipo)
     */
    public function buscarUltimosIncidentes($limite = 2)
    {
        try {
            $sql = "SELECT 
                        id_incidente AS id,
                        descricao_incidente AS mensagem,
                        'incidente' AS tipo,
                        data_incidente AS data
                    FROM incidente 
                    ORDER BY id_incidente DESC 
                    LIMIT :limite";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar últimos incidentes: " . $e->getMessage());
        }
    }

    /**
     * Busca as últimas 3 notificações
     * REGRA: 1 treinamento mais recente + 2 incidentes mais recentes
     */
    public function buscarNotificacoes()
    {
        try {
            $notificacoes = [];

            // 1. Busca o treinamento mais recente
            $ultimoTreinamento = $this->buscarUltimoTreinamento();

            if ($ultimoTreinamento) {
                $notificacoes[] = [
                    'mensagem' => 'Novo treinamento disponível: ' . $ultimoTreinamento['mensagem'],
                    'tipo' => 'treinamento',
                    'data' => $ultimoTreinamento['data'],
                    'icone' => 'risco-azul.png',
                    'classe' => 'notificacao-informacao',
                    'label' => 'Info'
                ];
            }

            // 2. Busca os 2 incidentes mais recentes (QUALQUER TIPO)
            $incidentes = $this->buscarUltimosIncidentes(2);

            foreach ($incidentes as $incidente) {
                $notificacoes[] = [
                    'mensagem' => $this->resumirMensagem($incidente['mensagem']),
                    'tipo' => 'incidente',
                    'data' => $incidente['data'],
                    'icone' => 'risco-vermelho.png',
                    'classe' => 'notificacao-atencao',
                    'label' => 'Atenção'
                ];
            }

            // 3. Se não há treinamento, preenche com mais incidentes
            if (!$ultimoTreinamento && count($notificacoes) < 3) {
                $incidentesExtra = $this->buscarUltimosIncidentes(3 - count($notificacoes));
                foreach ($incidentesExtra as $incidente) {
                    $notificacoes[] = [
                        'mensagem' => $this->resumirMensagem($incidente['mensagem']),
                        'tipo' => 'incidente',
                        'data' => $incidente['data'],
                        'icone' => 'risco-vermelho.png',
                        'classe' => 'notificacao-atencao',
                        'label' => 'Atenção'
                    ];
                }
            }

            // 4. Se não há incidentes, preenche com mais treinamentos
            if (empty($incidentes) && count($notificacoes) < 3) {
                $sql = "SELECT 
                            id_treinamento AS id,
                            nome_treinamento AS mensagem,
                            'treinamento' AS tipo,
                            data_limite_treinamento AS data
                        FROM treinamento 
                        ORDER BY id_treinamento DESC 
                        LIMIT " . (3 - count($notificacoes));
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                $treinamentosExtra = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($treinamentosExtra as $treinamento) {
                    $notificacoes[] = [
                        'mensagem' => 'Novo treinamento disponível: ' . $treinamento['mensagem'],
                        'tipo' => 'treinamento',
                        'data' => $treinamento['data'],
                        'icone' => 'risco-azul.png',
                        'classe' => 'notificacao-informacao',
                        'label' => 'Info'
                    ];
                }
            }

            return array_slice($notificacoes, 0, 3);

        } catch (Exception $e) {
            throw new Exception("Erro ao buscar notificações: " . $e->getMessage());
        }
    }

    /**
     * Resumo da mensagem para exibição
     */
    private function resumirMensagem($mensagem)
    {
        if (strlen($mensagem) > 80) {
            return substr($mensagem, 0, 80) . '...';
        }
        return $mensagem;
    }
}