<?php

namespace Model;

use Model\Connection;
use PDO;
use PDOException;

require_once __DIR__ . '/../config/Connection.php';

class Checagem
{
    private $conexao;

    /*
    |--------------------------------------------------------------------------
    | Todos os grupos e itens do checklist
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Conexão
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        $this->conexao = Connection::getInstance();
    }

    /*
    |--------------------------------------------------------------------------
    | Lista todos os administradores
    | Utilizada para preencher o SELECT da página 1
    |--------------------------------------------------------------------------
    */

    public function listarAdministradores()
    {
        try {

            $sql = "SELECT
                        id_adm,
                        nome_adm
                    FROM administrador
                    ORDER BY nome_adm";

            $stmt = $this->conexao->prepare($sql);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            die("Erro ao listar administradores: " . $e->getMessage());

        }
    }

    /*
    |--------------------------------------------------------------------------
    | Busca um administrador pelo ID
    |--------------------------------------------------------------------------
    */

    public function buscarAdministradorPorId($id_adm)
    {
        try {

            $sql = "SELECT
                        id_adm,
                        nome_adm
                    FROM administrador
                    WHERE id_adm = :id_adm
                    LIMIT 1";

            $stmt = $this->conexao->prepare($sql);

            $stmt->bindValue(
                ":id_adm",
                $id_adm,
                PDO::PARAM_INT
            );

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            die("Erro ao buscar administrador: " . $e->getMessage());

        }
    }

        /*
    |--------------------------------------------------------------------------
    | Salva o checklist
    |--------------------------------------------------------------------------
    */

    public function salvarChecklist(array $postData, array $dados)
    {
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

            $percentual = round(($conformes / $totalGrupo) * 100);

            $estatisticas[$grupo] = [

                "nome" => $nomeGrupo,
                "conformes" => $conformes,
                "total" => $totalGrupo,
                "percentual" => $percentual

            ];

            foreach ($itensGrupo as $item) {

                $marcado = in_array($item, $marcados);

                $itensBanco[] = [

                    "nome" => $item,
                    "valor" => $marcado ? 1 : 0

                ];

                if (!$marcado) {

                    if ($conformes == 0) {

                        $itensNaoConformes[] = [

                            "grupo" => $nomeGrupo,
                            "descricao" => $item

                        ];

                    } else {

                        $itensParcialmenteConformes[] = [

                            "grupo" => $nomeGrupo,
                            "descricao" => $item

                        ];

                    }

                }

            }

            $totalItens += $totalGrupo;
            $totalConformes += $conformes;

        }

        $progresso = round(($totalConformes / $totalItens) * 100);

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

        $observacao_checklist = $postData["observacao"] ?? "";

        $id_adm_fk = $dados["id_adm"];

        $id_checklist = $this->inserirChecklist(

            $data_checklist,
            $turno_checklist,
            $progresso,
            $status,
            $observacao_checklist,
            $id_adm_fk

        );

        $this->inserirChecklistCheckbox(

            $id_checklist,
            $itensBanco

        );

        $administrador = $this->buscarAdministradorPorId($id_adm_fk);

        return [

            "id_checklist" => $id_checklist,

            "responsavel" => $administrador["nome_adm"],

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

        /*
    |--------------------------------------------------------------------------
    | Insere um checklist
    |--------------------------------------------------------------------------
    */

    private function inserirChecklist(
        $data_checklist,
        $turno_checklist,
        $progresso_checklist,
        $status_checklist,
        $observacao_checklist,
        $id_adm_fk
    )

    {
        try {

            $sql = "INSERT INTO checklist
                    (
                        data_checklist,
                        turno_checklist,
                        progresso_checklist,
                        status_checklist,
                        observacao_checklist,
                        id_adm_fk
                    )
                    VALUES
                    (
                        :data_checklist,
                        :turno_checklist,
                        :progresso_checklist,
                        :status_checklist,
                        :observacao_checklist,
                        :id_adm_fk
                    )";

            $stmt = $this->conexao->prepare($sql);

            $stmt->bindValue(":data_checklist", $data_checklist);
            $stmt->bindValue(":turno_checklist", $turno_checklist);
            $stmt->bindValue(":progresso_checklist", $progresso_checklist);
            $stmt->bindValue(":status_checklist", $status_checklist);
            $stmt->bindValue(":observacao_checklist", $observacao_checklist);
            $stmt->bindValue(":id_adm_fk", $id_adm_fk, PDO::PARAM_INT);

            $stmt->execute();

            return $this->conexao->lastInsertId();

        } catch (PDOException $e) {

            die("Erro ao inserir checklist: " . $e->getMessage());

        }
    }

    /*
    |--------------------------------------------------------------------------
    | Salva os 40 checkboxes do checklist
    |--------------------------------------------------------------------------
    */

    private function inserirChecklistCheckbox(
        $id_checklist,
        $itens
    )
    {
        try {

            $sql = "INSERT INTO checklist_checkbox
                    (
                        nome_checklist_checkbox,
                        opcoes_valor_checklist_checkbox,
                        id_checklist_fk
                    )
                    VALUES
                    (
                        :nome,
                        :valor,
                        :id_checklist
                    )";

            $stmt = $this->conexao->prepare($sql);

            foreach ($itens as $item) {

                $stmt->bindValue(":nome", $item["nome"]);
                $stmt->bindValue(":valor", $item["valor"], PDO::PARAM_INT);
                $stmt->bindValue(":id_checklist", $id_checklist, PDO::PARAM_INT);

                $stmt->execute();

            }

        } catch (PDOException $e) {

            die("Erro ao salvar itens do checklist: " . $e->getMessage());

        }
    }

    /*
    |--------------------------------------------------------------------------
    | Lista todos os checklists (Página 4)
    |--------------------------------------------------------------------------
    */

    public function listarTodosChecklists()
    {
        try {

            $sql = "SELECT

                        checklist.id_checklist,
                        checklist.data_checklist,
                        checklist.turno_checklist,
                        checklist.progresso_checklist,
                        checklist.status_checklist,
                        checklist.observacao_checklist,

                        administrador.nome_adm

                    FROM checklist

                    INNER JOIN administrador

                    ON checklist.id_adm_fk = administrador.id_adm

                    ORDER BY checklist.data_checklist DESC";

            $stmt = $this->conexao->prepare($sql);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            die("Erro ao listar checklists: " . $e->getMessage());

        }
    }

    /*
    |--------------------------------------------------------------------------
    | Pesquisa checklists (Página 4)
    |--------------------------------------------------------------------------
    */

    public function buscarChecklistsPorPesquisa($pesquisa)
    {
        try {

            $sql = "SELECT

                        checklist.id_checklist,
                        checklist.data_checklist,
                        checklist.turno_checklist,
                        checklist.progresso_checklist,
                        checklist.status_checklist,
                        checklist.observacao_checklist,

                        administrador.nome_adm

                    FROM checklist

                    INNER JOIN administrador

                    ON checklist.id_adm_fk = administrador.id_adm

                    WHERE

                        administrador.nome_adm LIKE :pesquisa

                        OR checklist.turno_checklist LIKE :pesquisa

                        OR checklist.status_checklist LIKE :pesquisa

                        OR DATE_FORMAT(checklist.data_checklist,'%d/%m/%Y')
                        LIKE :pesquisa

                    ORDER BY checklist.data_checklist DESC";

            $stmt = $this->conexao->prepare($sql);

            $stmt->bindValue(
                ":pesquisa",
                "%" . $pesquisa . "%"
            );

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            die("Erro ao pesquisar checklists: " . $e->getMessage());

        }
    }
}

