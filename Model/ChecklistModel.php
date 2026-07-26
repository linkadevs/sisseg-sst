<?php

namespace Model;

use Model\Connection;
use PDO;
use PDOException;

require_once __DIR__ . '/../Model/Connection.php';

class Checagem
{
    private $conexao;

    private $itensChecklist = [
        "organizacao" => [
            "nome" => "Organização do Canteiro",
            "itens" => [
                "Tapumes e cercamento instalados",
                "Identificação da obra e CNPJ",
                "Circulação desobstruída",
                "Controle de acesso funcionando"
            ]
        ],
        "areas_vivencia" => [
            "nome" => "Áreas de Vivência",
            "itens" => [
                "Vestiários limpos",
                "Refeitório higienizado",
                "Instalações sanitárias adequadas",
                "Água potável disponível"
            ]
        ],
        "condicoes_trabalho" => [
            "nome" => "Condições de Trabalho",
            "itens" => [
                "Escadas e rampas seguras",
                "Plataformas com guarda-corpo",
                "Sinalização de risco instalada",
                "Proteção de periferia"
            ]
        ],
        "maquinas" => [
            "nome" => "Máquinas e Equipamentos",
            "itens" => [
                "Betoneira com proteção",
                "Serra circular protegida",
                "Guincho/Guindaste inspecionado",
                "Retroescavadeira/Empilhadeira OK"
            ]
        ],
        "nr10" => [
            "nome" => "Instalações Elétricas - NR-10",
            "itens" => [
                "Quadros identificados",
                "Aterramento testado",
                "Cabos sem emendas",
                "DR instalado onde necessário"
            ]
        ],
        "nr11" => [
            "nome" => "Movimentação de Materiais - NR-11",
            "itens" => [
                "Operadores habilitados",
                "Sinalização visível",
                "Carga identificada",
                "Rota de içamento isolada"
            ]
        ],
        "nr35" => [
            "nome" => "Trabalho em Altura - NR-35",
            "itens" => [
                "Linha de vida instalada",
                "Cinturões revisados",
                "Ancoragens certificadas",
                "PT emitida (quando necessário)"
            ]
        ],
        "quimicos" => [
            "nome" => "Produtos Químicos",
            "itens" => [
                "FISPQ disponível",
                "Armazenamento ventilado",
                "EPIs adequados entregues",
                "Recipientes identificados"
            ]
        ],
        "nr23" => [
            "nome" => "Prevenção Contra Incêndio - NR-23",
            "itens" => [
                "Extintores acessíveis",
                "Sinalização de emergência",
                "Saídas desobstruídas",
                "Brigada treinada"
            ]
        ],
        "documentacao" => [
            "nome" => "Documentação",
            "itens" => [
                "PGR atualizado",
                "PCMSO disponível",
                "Lista de EPIs assinada",
                "Treinamentos dentro da validade"
            ]
        ]
    ];

    public function __construct()
    {
        $this->conexao = Connection::getInstance();
    }

    public function listarAdministradores()
    {
        try {
            $sql = "SELECT id_adm, nome_adm FROM administrador ORDER BY nome_adm";
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function buscarAdministradorPorId($id_adm)
    {
        try {
            $sql = "SELECT id_adm, nome_adm FROM administrador WHERE id_adm = :id_adm LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id_adm", $id_adm, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function salvarChecklist(array $postData, array $dados)
    {
        if (!isset($dados["id_adm"]) || !isset($dados["turno"])) {
            return ["erro" => "Dados da sessão incompletos.", "grupos" => []];
        }

        $totalItens = 0;
        $totalConformes = 0;
        $estatisticas = [];
        $itensNaoConformes = [];
        $itensParcialmenteConformes = [];
        $itensBanco = [];

        foreach ($this->itensChecklist as $grupo => $dadosGrupo) {
            $nomeGrupo = $dadosGrupo["nome"];
            $itensGrupo = $dadosGrupo["itens"];
            $marcados = $postData[$grupo] ?? [];
            $conformes = count($marcados);
            $totalGrupo = count($itensGrupo);
            $percentual = $totalGrupo > 0 ? round(($conformes / $totalGrupo) * 100) : 0;

            $estatisticas[$grupo] = [
                "nome" => $nomeGrupo,
                "conformes" => $conformes,
                "total" => $totalGrupo,
                "percentual" => $percentual
            ];

            foreach ($itensGrupo as $item) {
                $marcado = in_array($item, $marcados);
                $itensBanco[] = ["nome" => $item, "valor" => $marcado ? 1 : 0];

                if (!$marcado) {
                    if ($conformes == 0) {
                        $itensNaoConformes[] = ["grupo" => $nomeGrupo, "descricao" => $item];
                    } else {
                        $itensParcialmenteConformes[] = ["grupo" => $nomeGrupo, "descricao" => $item];
                    }
                }
            }
            $totalItens += $totalGrupo;
            $totalConformes += $conformes;
        }

        $progresso = $totalItens > 0 ? round(($totalConformes / $totalItens) * 100) : 0;

        if ($progresso == 100) {
            $status = "Conforme";
            $classe_status = "conforme";
        } elseif ($progresso >= 70) {
            $status = "Parcialmente Conforme";
            $classe_status = "parcialmente_conforme";
        } else {
            $status = "Não Conforme";
            $classe_status = "nao_conforme";
        }

        $data_checklist = date("Y-m-d H:i:s");
        $turno_checklist = $dados["turno"];
        $observacao_checklist = $postData["observacoes"] ?? "";
        $id_adm_fk = $dados["id_adm"];

        $id_checklist = $this->inserirChecklist($data_checklist, $turno_checklist, $progresso, $status, $observacao_checklist, $id_adm_fk);

        if (!$id_checklist) {
            return ["erro" => "Falha ao salvar o checklist.", "grupos" => $estatisticas];
        }

        $this->inserirChecklistCheckbox($id_checklist, $itensBanco);
        $administrador = $this->buscarAdministradorPorId($id_adm_fk);

        return [
            "id_checklist" => $id_checklist,
            "responsavel" => $administrador ? $administrador["nome_adm"] : "Desconhecido",
            "turno" => $turno_checklist,
            "data" => date("d/m/Y H:i"),
            "status" => $status,
            "classe_status" => $classe_status,
            "progresso" => $progresso,
            "grupos" => $estatisticas,
            "total_nao_conformes" => count($itensNaoConformes),
            "total_parcialmente_conformes" => count($itensParcialmenteConformes),
            "itens_nao_conformes" => $itensNaoConformes,
            "itens_parcialmente_conformes" => $itensParcialmenteConformes
        ];
    }

    private function inserirChecklist($data_checklist, $turno_checklist, $progresso_checklist, $status_checklist, $observacao_checklist, $id_adm_fk)
    {
        try {
            $sql = "INSERT INTO checklist (data_checklist, turno_checklist, progresso_checklist, status_checklist, observacao_checklist, id_adm_fk) VALUES (:data_checklist, :turno_checklist, :progresso_checklist, :status_checklist, :observacao_checklist, :id_adm_fk)";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":data_checklist", $data_checklist);
            $stmt->bindValue(":turno_checklist", $turno_checklist);
            $stmt->bindValue(":progresso_checklist", $progresso_checklist);
            $stmt->bindValue(":status_checklist", $status_checklist);
            $stmt->bindValue(":observacao_checklist", $observacao_checklist);
            $stmt->bindValue(":id_adm_fk", $id_adm_fk, PDO::PARAM_INT);
            $stmt->execute();
            return (int) $this->conexao->lastInsertId();
        } catch (PDOException $e) {
            return 0;
        }
    }

    private function inserirChecklistCheckbox($id_checklist, $itens)
    {
        try {
            $sql = "INSERT INTO checklist_checkbox (nome_checklist_checkbox, opcoes_valor_checklist_checkbox, id_checklist_fk) VALUES (:nome, :valor, :id_checklist)";
            $stmt = $this->conexao->prepare($sql);
            foreach ($itens as $item) {
                $stmt->bindValue(":nome", $item["nome"]);
                $stmt->bindValue(":valor", $item["valor"], PDO::PARAM_INT);
                $stmt->bindValue(":id_checklist", $id_checklist, PDO::PARAM_INT);
                $stmt->execute();
            }
        } catch (PDOException $e) {
        }
    }

    
    public function listarTodosChecklists()
    {
        try {
            $sql = "SELECT checklist.id_checklist, checklist.data_checklist, checklist.turno_checklist, checklist.progresso_checklist, checklist.status_checklist, checklist.observacao_checklist, administrador.nome_adm, administrador.setor_adm FROM checklist INNER JOIN administrador ON checklist.id_adm_fk = administrador.id_adm ORDER BY checklist.data_checklist DESC";
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function buscarChecklistsPorPesquisa($pesquisa)
    {
        try {
            $sql = "SELECT checklist.id_checklist, checklist.data_checklist, checklist.turno_checklist, checklist.progresso_checklist, checklist.status_checklist, checklist.observacao_checklist, administrador.nome_adm, administrador.setor_adm FROM checklist INNER JOIN administrador ON checklist.id_adm_fk = administrador.id_adm WHERE administrador.nome_adm LIKE :pesquisa OR administrador.setor_adm LIKE :pesquisa OR checklist.turno_checklist LIKE :pesquisa OR checklist.status_checklist LIKE :pesquisa OR DATE_FORMAT(checklist.data_checklist,'%d/%m/%Y') LIKE :pesquisa ORDER BY checklist.data_checklist DESC";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":pesquisa", "%" . $pesquisa . "%");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function buscarChecklistResultadoPorId($id_checklist)
    {
        if (!$this->conexao) {
            return null;
        }
        try {
            $sql = "SELECT c.id_checklist, c.data_checklist, c.turno_checklist, c.progresso_checklist, c.status_checklist, c.observacao_checklist, a.nome_adm FROM checklist c INNER JOIN administrador a ON c.id_adm_fk = a.id_adm WHERE c.id_checklist = :id_checklist LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id_checklist", $id_checklist, PDO::PARAM_INT);
            $stmt->execute();
            $checklist = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$checklist) {
                return null;
            }

            $sqlItems = "SELECT nome_checklist_checkbox, opcoes_valor_checklist_checkbox FROM checklist_checkbox WHERE id_checklist_fk = :id_checklist";
            $stmtItems = $this->conexao->prepare($sqlItems);
            $stmtItems->bindValue(":id_checklist", $id_checklist, PDO::PARAM_INT);
            $stmtItems->execute();
            $itensDb = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            $estatisticas = [];
            $itensNaoConformesRefinados = [];
            $itensParcialmenteConformesRefinados = [];

            foreach ($this->itensChecklist as $grupo => $dadosGrupo) {
                $nomeGrupo = $dadosGrupo["nome"];
                $itensGrupo = $dadosGrupo["itens"];
                $conformes = 0;
                $totalGrupo = count($itensGrupo);
                foreach ($itensGrupo as $item) {
                    $marcado = 0;
                    foreach ($itensDb as $itemDb) {
                        if ($itemDb['nome_checklist_checkbox'] === $item) {
                            $marcado = (int) $itemDb['opcoes_valor_checklist_checkbox'];
                            break;
                        }
                    }
                    if ($marcado) {
                        $conformes++;
                    }
                }
                $percentual = $totalGrupo > 0 ? round(($conformes / $totalGrupo) * 100) : 0;
                $estatisticas[$grupo] = ["nome" => $nomeGrupo, "conformes" => $conformes, "total" => $totalGrupo, "percentual" => $percentual];

                foreach ($itensGrupo as $item) {
                    $marcado = 0;
                    foreach ($itensDb as $itemDb) {
                        if ($itemDb['nome_checklist_checkbox'] === $item) {
                            $marcado = (int) $itemDb['opcoes_valor_checklist_checkbox'];
                            break;
                        }
                    }
                    if (!$marcado) {
                        if ($conformes == 0) {
                            $itensNaoConformesRefinados[] = ["grupo" => $nomeGrupo, "descricao" => $item];
                        } else {
                            $itensParcialmenteConformesRefinados[] = ["grupo" => $nomeGrupo, "descricao" => $item];
                        }
                    }
                }
            }

            return [
                "id_checklist" => $checklist['id_checklist'],
                "responsavel" => $checklist['nome_adm'],
                "turno" => $checklist['turno_checklist'],
                "data" => date("d/m/Y H:i", strtotime($checklist['data_checklist'])),
                "status" => $checklist['status_checklist'],
                "classe_status" => strtolower(str_replace(' ', '_', $checklist['status_checklist'])),
                "progresso" => $checklist['progresso_checklist'],
                "observacao" => $checklist['observacao_checklist'],
                "grupos" => $estatisticas,
                "total_nao_conformes" => count($itensNaoConformesRefinados),
                "total_parcialmente_conformes" => count($itensParcialmenteConformesRefinados),
                "itens_nao_conformes" => $itensNaoConformesRefinados,
                "itens_parcialmente_conformes" => $itensParcialmenteConformesRefinados
            ];
        } catch (PDOException $e) {
            return null;
        }
    }
}