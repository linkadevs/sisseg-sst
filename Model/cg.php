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
    | Mapeamento dos grupos e dos itens do checklist
    |--------------------------------------------------------------------------
    |
    |
    |
    */
   
     private $itensChecklist = [
        'organizacao' => [
            'nome'  => 'Organização do Canteiro',
            'itens' => [
                'Tapumes e cercamento instalados',
                'Identificação da obra e CNPJ',
                'Circulação desobstruída',
                'Controle de acesso funcionando'
            ]
        ],
        'areas_vivencia' => [
            'nome'  => 'Áreas de Vivência',
            'itens' => [
                'Vestiários limpos',
                'Refeitório higienizado',
                'Instalações sanitárias adequadas',
                'Água potável disponível'
            ]
        ],
        'condicoes_trabalho' => [
            'nome'  => 'Condições de Trabalho',
            'itens' => [
                'Escadas e rampas seguras',
                'Plataformas com guarda-corpo',
                'Sinalização de risco instalada',
                'Proteção de periferia'
            ]
        ],
        'maquinas' => [
            'nome'  => 'Máquinas e Equipamentos',
            'itens' => [
                'Betoneira com proteção',
                'Serra circular protegida',
                'Guincho/guindaste inspecionado',
                'Retroescavadeira/empilhadeira OK'
            ]
        ],
        'nr10' => [
            'nome'  => 'Instalações Elétricas - NR-10',
            'itens' => [
                'Quadros identificados',
                'Aterramento testado',
                'Cabos sem emendas',
                'DR instalado onde necessário'
            ]
        ],
        'nr11' => [
            'nome'  => 'Movimentação de Materiais - NR-11',
            'itens' => [
                'Operadores habilitados',
                'Sinalização visível',
                'Carga identificada',
                'Rota de içamento isolada'
            ]
        ],
        'nr35' => [
            'nome'  => 'Trabalho em Altura - NR-35',
            'itens' => [
                'Linha de vida instalada',
                'Cinturões revisados',
                'Ancoragens certificadas',
                'PT emitida (Quando necessário)'
            ]
        ],
        'quimicos' => [
            'nome'  => 'Produtos Químicos',
            'itens' => [
                'FISPQ disponível',
                'Armazenamento ventilado',
                'EPIs adequados entregues',
                'Recipientes identificados'
            ]
        ],
        'nr23' => [
            'nome'  => 'Prevenção Contra Incêndio - NR-23',
            'itens' => [
                'Extintores acessíveis',
                'Sinalização de emergência',
                'Saídas desobstruídas',
                'Brigada treinada'
            ]
        ],
        'documentacao' => [
            'nome'  => 'Documentação',
            'itens' => [
                'PGR atualizado',
                'PCMSO disponível',
                'Lista de EPIs assinada',
                'Treinamentos dentro da validade'
            ]
        ]
    ];

    public function __construct()
    {
        $this->conexao = Connection::getInstance();
    }

    /*
    |--------------------------------------------------------------------------
    | Lista todos os administradores
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
    public function buscarAdministradorPorId($idAdm)
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
                $idAdm,
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

        $nomeGrupo = $dadosGrupo['nome'];
        $itensGrupo = $dadosGrupo['itens'];

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

        foreach ($itensGrupo as $descricao) {

            $marcado = in_array($descricao, $marcados);

            $itensBanco[] = [
                "nome" => $descricao,
                "valor" => $marcado ? 1 : 0
            ];

            if (!$marcado) {

                if ($conformes == 0) {

                    $itensNaoConformes[] = [
                        "grupo" => $nomeGrupo,
                        "descricao" => $descricao
                    ];

                } else {

                    $itensParcialmenteConformes[] = [
                        "grupo" => $nomeGrupo,
                        "descricao" => $descricao
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
        $classeStatus = "conforme";

    } elseif ($progresso >= 70) {

        $status = "Parcialmente Conforme";
        $classeStatus = "parcialmente_conforme";

    } else {

        $status = "Não Conforme";
        $classeStatus = "nao_conforme";

    }

    $idChecklist = $this->inserirChecklist([
        "data_checklist" => date("Y-m-d H:i:s"),
        "turno_checklist" => $dados["turno"],
        "progresso_checklist" => $progresso,
        "status_checklist" => $status,
        "observacao_checklist" => $postData["observacao"] ?? "",
        "id_adm_fk" => $dados["id_adm"]
    ]);

    $this->inserirChecklistCheckbox(
        $idChecklist,
        $itensBanco
    );

    $administrador = $this->buscarAdministradorPorId($dados["id_adm"]);

    return [

        "id_checklist" => $idChecklist,

        "responsavel" => $administrador["nome_adm"],

        "turno" => $dados["turno"],

        "data" => date("d/m/Y H:i"),

        "status" => $status,

        "classe_status" => $classeStatus,

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
private function inserirChecklist(array $dados)
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
                    :data,
                    :turno,
                    :progresso,
                    :status,
                    :observacao,
                    :id_adm
                )";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(":data", $dados["data_checklist"]);
        $stmt->bindValue(":turno", $dados["turno_checklist"]);
        $stmt->bindValue(":progresso", $dados["progresso_checklist"], PDO::PARAM_INT);
        $stmt->bindValue(":status", $dados["status_checklist"]);
        $stmt->bindValue(":observacao", $dados["observacao_checklist"]);
        $stmt->bindValue(":id_adm", $dados["id_adm_fk"], PDO::PARAM_INT);

        $stmt->execute();

        return $this->conexao->lastInsertId();

    } catch (PDOException $e) {

        die("Erro ao inserir checklist: " . $e->getMessage());

    }
}

/*
|--------------------------------------------------------------------------
| Salva os checkboxes do checklist
|--------------------------------------------------------------------------
*/
private function inserirChecklistCheckbox($idChecklist, array $itens)
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
            $stmt->bindValue(":id_checklist", $idChecklist, PDO::PARAM_INT);

            $stmt->execute();

        }

    } catch (PDOException $e) {

        die("Erro ao inserir checkboxes: " . $e->getMessage());

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
                    c.*,
                    a.nome_adm
                FROM checklist c
                INNER JOIN administrador a
                    ON a.id_adm = c.id_adm_fk
                ORDER BY c.data_checklist DESC";

        $stmt = $this->conexao->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {

        die("Erro ao listar checklists: " . $e->getMessage());

    }
}

/*
|--------------------------------------------------------------------------
| Pesquisa checklists
|--------------------------------------------------------------------------
*/
public function buscarChecklistsPorPesquisa($texto)
{
    try {

        $sql = "SELECT
                    c.*,
                    a.nome_adm
                FROM checklist c
                INNER JOIN administrador a
                    ON a.id_adm = c.id_adm_fk
                WHERE

                    a.nome_adm LIKE :pesquisa

                    OR

                    c.turno_checklist LIKE :pesquisa

                    OR

                    c.status_checklist LIKE :pesquisa

                    OR

                    DATE_FORMAT(
                        c.data_checklist,
                        '%d/%m/%Y'
                    ) LIKE :pesquisa

                ORDER BY c.data_checklist DESC";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ":pesquisa",
            "%".$texto."%"
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {

        die("Erro na pesquisa: " . $e->getMessage());

    }
}

/*
|--------------------------------------------------------------------------
| Busca um checklist pelo ID
|--------------------------------------------------------------------------
*/
public function buscarChecklistPorId($idChecklist)
{
    try {

        $sql = "SELECT
                    c.*,
                    a.nome_adm
                FROM checklist c
                INNER JOIN administrador a
                    ON a.id_adm = c.id_adm_fk
                WHERE c.id_checklist = :id";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ":id",
            $idChecklist,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {

        die("Erro ao buscar checklist: " . $e->getMessage());

    }
}

/*
|--------------------------------------------------------------------------
| Busca os checkboxes de um checklist
|--------------------------------------------------------------------------
*/
public function buscarItensChecklist($idChecklist)
{
    try {

        $sql = "SELECT
                    nome_checklist_checkbox,
                    opcoes_valor_checklist_checkbox
                FROM checklist_checkbox
                WHERE id_checklist_fk = :id";

        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(
            ":id",
            $idChecklist,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {

        die("Erro ao buscar itens: " . $e->getMessage());

    }
}

}
