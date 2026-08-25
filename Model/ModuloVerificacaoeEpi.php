<?php

namespace Model;
require_once __DIR__ . '/../Model/Connection.php';

use Exception;
use PDOException;
use PDO;
use Model\Connection;

class ModuloVerificacaoeEpi
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }
    
    public function exibir_atividades()
    {
        try {
            $sql = "SELECT * FROM atividade";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $atividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($atividades)) {
                return [];
            }

            foreach ($atividades as $key => $atividade) {
                if (!empty($atividade['id_nr_fk'])) {
                    $sql = "SELECT nome_nr FROM nr WHERE id_nr = :id_nr";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bindParam(":id_nr", $atividade['id_nr_fk'], PDO::PARAM_INT);
                    $stmt->execute();
                    $nr = $stmt->fetch(PDO::FETCH_ASSOC);
                    $atividades[$key]['nome_nr'] = $nr['nome_nr'] ?? 'Não atribuído';
                } else {
                    $atividades[$key]['nome_nr'] = 'Não atribuído';
                }

                $sql = "SELECT COUNT(*) as total_epis 
                        FROM atividade_epi 
                        WHERE id_atividade_fk = :id_atividade";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(":id_atividade", $atividade['id_atividade'], PDO::PARAM_INT);
                $stmt->execute();
                $count = $stmt->fetch(PDO::FETCH_ASSOC);
                $atividades[$key]['quantidade_epis'] = $count['total_epis'] ?? 0;
            }

            return $atividades;

        } catch (Exception $erro) {
            throw new Exception("Erro ao obter atividades: " . $erro->getMessage());
        }
    }

    public function exibirnorma($id)
    {
        try {
            $sql = "SELECT nome_nr, descricao_nr FROM nr WHERE id_nr = :id_nr";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id_nr", $id, PDO::PARAM_INT);
            $stmt->execute();
            $norma = $stmt->fetch(PDO::FETCH_ASSOC);
            return $norma;
        } catch (Exception $erro) {
            throw new Exception("Erro ao obter NR: " . $erro->getMessage());
        }
    }

    public function exibirepis($id_atividade)
    {
        try {
            $sql = "SELECT 
                        e.id_epi,
                        e.nome_epi,
                        e.descricao_epi,
                        e.funcao_epi,
                        e.ca_epi,
                        e.qtd_minima_epi,
                        e.qtd_epi,
                        e.status_epi
                    FROM atividade_epi ae
                    INNER JOIN epi e
                    ON ae.id_epi_fk = e.id_epi
                    WHERE ae.id_atividade_fk = :id_atividade";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id_atividade", $id_atividade, PDO::PARAM_INT);
            $stmt->execute();
            $epis = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $epis;
            
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar EPIs da atividade: " . $erro->getMessage());
        }
    }

    public function buscarPontuacaoSetor($nome_setor)
    {
        try {
            $sql = "SELECT pontuacao FROM pontuacao_setor WHERE nome_setor = :nome_setor";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":nome_setor", $nome_setor, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado ? (int)$resultado['pontuacao'] : 0;
            
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar pontuação do setor: " . $erro->getMessage());
        }
    }

    public function atualizarPontuacaoSetor($nome_setor, $pontos_ganhos)
    {
        try {
            $sql = "SELECT id_pontuacao_setor FROM pontuacao_setor WHERE nome_setor = :nome_setor";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":nome_setor", $nome_setor, PDO::PARAM_STR);
            $stmt->execute();
            $existe = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existe) {
                $sql = "UPDATE pontuacao_setor SET pontuacao = pontuacao + :pontos WHERE nome_setor = :nome_setor";
            } else {
                $sql = "INSERT INTO pontuacao_setor (nome_setor, pontuacao) VALUES (:nome_setor, :pontos)";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":nome_setor", $nome_setor, PDO::PARAM_STR);
            $stmt->bindParam(":pontos", $pontos_ganhos, PDO::PARAM_INT);
            $stmt->execute();
            
            return true;
            
        } catch (Exception $erro) {
            throw new Exception("Erro ao atualizar pontuação do setor: " . $erro->getMessage());
        }
    }

    public function exibirIncidentesPorAtividade($id_atividade)
    {
        try {
            $sql = "SELECT 
                        id_incidente,
                        descricao_incidente,
                        local_incidente,
                        acao_imediata_incidente,
                        gravidade_incidente,
                        tipo_incidente,
                        testemunhas_incidente,
                        treinamento_reciclagem_incidente,
                        fotos_incidente
                    FROM incidente
                    WHERE id_atividade_fk = :id_atividade
                    ORDER BY id_incidente DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id_atividade", $id_atividade, PDO::PARAM_INT);
            $stmt->execute();
            $incidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $incidentes;
            
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar incidentes da atividade: " . $erro->getMessage());
        }
    }

public function exibirTodosIncidentes()
{
    try {
        $sql = "SELECT 
                    i.id_incidente,
                    i.data_incidente,           
                    i.descricao_incidente,
                    i.local_incidente,
                    i.acao_imediata_incidente,
                    i.gravidade_incidente,
                    i.tipo_incidente,
                    i.testemunhas_incidente,
                    i.treinamento_reciclagem_incidente,
                    i.fotos_incidente,
                    a.nome_atividade,
                    a.icone_atividade,
                    n.nome_nr
                FROM incidente i
                INNER JOIN atividade a ON i.id_atividade_fk = a.id_atividade
                LEFT JOIN nr n ON a.id_nr_fk = n.id_nr
                ORDER BY i.data_incidente DESC, i.id_incidente DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $incidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $incidentes;
        
    } catch (Exception $erro) {
        throw new Exception("Erro ao buscar todos os incidentes: " . $erro->getMessage());
    }
}

    public function exibirIncidentePorId($id_incidente)
    {
        try {
            $sql = "SELECT 
                        i.id_incidente,
                        i.descricao_incidente,
                        i.local_incidente,
                        i.acao_imediata_incidente,
                        i.gravidade_incidente,
                        i.tipo_incidente,
                        i.testemunhas_incidente,
                        i.treinamento_reciclagem_incidente,
                        i.fotos_incidente,
                        a.nome_atividade,
                        a.icone_atividade,
                        n.nome_nr
                    FROM incidente i
                    INNER JOIN atividade a ON i.id_atividade_fk = a.id_atividade
                    LEFT JOIN nr n ON a.id_nr_fk = n.id_nr
                    WHERE i.id_incidente = :id_incidente";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id_incidente", $id_incidente, PDO::PARAM_INT);
            $stmt->execute();
            $incidente = $stmt->fetch(PDO::FETCH_ASSOC);
            return $incidente;
            
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar incidente: " . $erro->getMessage());
        }
    }

    public function buscarTodasPontuacoes()
    {
        try {
            $sql = "SELECT 
                        id_pontuacao_setor,
                        nome_setor,
                        pontuacao
                    FROM pontuacao_setor
                    ORDER BY pontuacao DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $pontuacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $pontuacoes;
            
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar todas as pontuações: " . $erro->getMessage());
        }
    }

    public function buscarRankingSetores($limite = 10)
    {
        try {
            $sql = "SELECT 
                        nome_setor,
                        pontuacao
                    FROM pontuacao_setor
                    ORDER BY pontuacao DESC
                    LIMIT :limite";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
            $stmt->execute();
            $ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $ranking;
            
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar ranking de setores: " . $erro->getMessage());
        }
    }

    public function buscarTodosContatos()
    {
        try {
            $sql = "SELECT id_contato, nome_contato, numero_contato FROM contato ORDER BY id_contato";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $contatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $contatos;
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar contatos: " . $erro->getMessage());
        }
    }

    public function buscarContatoPorId($id_contato)
    {
        try {
            $sql = "SELECT id_contato, nome_contato, numero_contato FROM contato WHERE id_contato = :id_contato";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id_contato", $id_contato, PDO::PARAM_INT);
            $stmt->execute();
            $contato = $stmt->fetch(PDO::FETCH_ASSOC);
            return $contato;
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar contato: " . $erro->getMessage());
        }
    }

    public function buscarContatoPorTipo($tipo)
    {
        try {
            $sql = "SELECT id_contato, nome_contato, numero_contato FROM contato WHERE nome_contato LIKE :tipo LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $tipoBusca = '%' . $tipo . '%';
            $stmt->bindParam(":tipo", $tipoBusca, PDO::PARAM_STR);
            $stmt->execute();
            $contato = $stmt->fetch(PDO::FETCH_ASSOC);
            return $contato;
        } catch (Exception $erro) {
            throw new Exception("Erro ao buscar contato por tipo: " . $erro->getMessage());
        }
    }
}