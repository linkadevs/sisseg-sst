<?php
namespace Model;

require_once __DIR__ . "/../Model/Connection.php";

use PDO;
use PDOException;
use Exception;


/**
 * Model FichaRisco
 * -----------------------------------------------------------------
 * Responsável por todo o acesso a dados do módulo PGR (Programa de
 * Gerenciamento de Riscos), cobrindo as tabelas:
 *   - atividade      (nome da atividade / vínculo com a NR)
 *   - ficha           (medidas coletivas + procedimentos obrigatórios)
 *   - risco           (cada risco identificado)
 *   - ficha_risco     (tabela de junção ficha <-> risco)
 *
 * Convenção adotada: cada "atividade" possui exatamente 1 "ficha"
 * (1:1). As listas (medidas coletivas, procedimentos, medidas de
 * controle e EPIs de cada risco) são persistidas como JSON dentro
 * das colunas TEXT já existentes no schema.
 */
class FichaRisco {

    private $db;

    public function __construct(){
        try {
            $this->db = Connection::getInstance();
        } catch (PDOException $e) {
            throw new PDOException('Erro ao conectar ao banco de dados. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    // ------------------------------------------------------------
    // MATRIZ DE RISCO (Probabilidade x Severidade)
    // Cálculo feito sempre no servidor, para garantir que o status
    // salvo no banco nunca seja divergente do informado pelo front.
    // ------------------------------------------------------------
    private function calcularNivel($probabilidade, $severidade){
        try {
            $score = (int)$probabilidade * (int)$severidade;
            if ($score >= 15) return 'Crítico';
            if ($score >= 9)  return 'Alto';
            if ($score >= 4)  return 'Médio';
            return 'Baixo';
        } catch (PDOException $e) {
            throw new PDOException('Erro ao calcular nível de risco. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    // ------------------------------------------------------------
    // NRs — lista para popular o <select> do formulário e validação
    // de que a NR escolhida pelo usuário realmente existe no banco.
    // ------------------------------------------------------------
    public function listarNRs(){
        try {
            $stmt = $this->db->query("SELECT id_nr, nome_nr FROM nr ORDER BY nome_nr ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException('Erro ao listar NRs. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    private function verificarNRExiste($idNr){
        try {
            $stmt = $this->db->prepare("SELECT id_nr FROM nr WHERE id_nr = :id LIMIT 1");
            $stmt->execute([':id' => $idNr]);
            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                throw new \Exception("A NR selecionada não foi encontrada.");
            }
        } catch (PDOException $e) {
            throw new PDOException('Erro ao verificar NR. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    private function buscarAtividadePorNome($nome){
        try {
            $stmt = $this->db->prepare("SELECT * FROM atividade WHERE nome_atividade = :nome LIMIT 1");
            $stmt->execute([':nome' => $nome]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            throw new PDOException('Erro ao buscar atividade. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    private function buscarFichaPorAtividade($idAtividade){
        try {
            $stmt = $this->db->prepare("SELECT * FROM ficha WHERE id_atividade_fk = :id LIMIT 1");
            $stmt->execute([':id' => $idAtividade]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            throw new PDOException('Erro ao buscar ficha. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    // ==============================================================
    // CREATE — Nova ficha de risco (ou anexação a uma já existente
    // com o mesmo nome de atividade, mesma regra do protótipo em JS)
    // ==============================================================
    public function criar($nomeAtividade, $idNr, array $riscos, array $medidasColetivas, array $procedimentos){
        $this->verificarNRExiste($idNr);

        $this->db->beginTransaction();
        try {
            $atividade = $this->buscarAtividadePorNome($nomeAtividade);

            if ($atividade) {
                $idAtividade = $atividade['id_atividade'];
                $ficha = $this->buscarFichaPorAtividade($idAtividade);

                $medidasAtuais       = $ficha ? (json_decode($ficha['medidas_protecao_ficha'], true) ?: []) : [];
                $procedimentosAtuais = $ficha ? (json_decode($ficha['procedimentos_obrigatorios_ficha'], true) ?: []) : [];

                foreach ($medidasColetivas as $m) if (!in_array($m, $medidasAtuais, true)) $medidasAtuais[] = $m;
                foreach ($procedimentos as $p)   if (!in_array($p, $procedimentosAtuais, true)) $procedimentosAtuais[] = $p;

                if ($ficha) {
                    $stmt = $this->db->prepare("UPDATE ficha SET medidas_protecao_ficha = :m, procedimentos_obrigatorios_ficha = :p WHERE id_ficha = :id");
                    $stmt->execute([
                        ':m'  => json_encode($medidasAtuais, JSON_UNESCAPED_UNICODE),
                        ':p'  => json_encode($procedimentosAtuais, JSON_UNESCAPED_UNICODE),
                        ':id' => $ficha['id_ficha']
                    ]);
                    $idFicha = $ficha['id_ficha'];
                } else {
                    $idFicha = $this->inserirFicha($idAtividade, $medidasAtuais, $procedimentosAtuais);
                }
            } else {
                $stmt = $this->db->prepare("INSERT INTO atividade (nome_atividade, icone_atividade, id_nr_fk) VALUES (:nome, :icone, :nr)");
                $stmt->execute([
                    ':nome'  => $nomeAtividade,
                    ':icone' => '',
                    ':nr'    => $idNr
                ]);
                $idAtividade = $this->db->lastInsertId();
                $idFicha = $this->inserirFicha($idAtividade, $medidasColetivas, $procedimentos);
            }

            foreach ($riscos as $risco) {
                $this->inserirRisco($idFicha, $risco);
            }

            $this->db->commit();
            return $this->buscarPorId($idAtividade);
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new PDOException('Erro ao criar ficha. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function inserirFicha($idAtividade, array $medidas, array $procedimentos){
        try {
            $stmt = $this->db->prepare("INSERT INTO ficha (procedimentos_obrigatorios_ficha, medidas_protecao_ficha, id_atividade_fk) VALUES (:proc, :med, :id)");
            $stmt->execute([
                ':proc' => json_encode(array_values($procedimentos), JSON_UNESCAPED_UNICODE),
                ':med'  => json_encode(array_values($medidas), JSON_UNESCAPED_UNICODE),
                ':id'   => $idAtividade
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new PDOException('Erro ao inserir ficha. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    private function inserirRisco($idFicha, array $r){
        try {
            $nivel = $this->calcularNivel($r['probabilidade'], $r['severidade']);
            $nome  = mb_substr(trim($r['descricao']), 0, 255);

        $stmt = $this->db->prepare("INSERT INTO risco
            (nome_risco, descricao_risco, tipo_risco, nivel_risco, probabilidade_risco, severidade_risco, medidas_controle_risco, epis_relacionados_risco)
            VALUES (:nome, :descricao, :tipo, :nivel, :prob, :sev, :medidas, :epis)");
        $stmt->execute([
            ':nome'      => $nome,
            ':descricao' => $r['descricao'],
            ':tipo'      => $r['tipo'],
            ':nivel'     => $nivel,
            ':prob'      => (int)$r['probabilidade'],
            ':sev'       => (int)$r['severidade'],
            ':medidas'   => json_encode($r['medidasControle'] ?? [], JSON_UNESCAPED_UNICODE),
            ':epis'      => json_encode($r['epis'] ?? [], JSON_UNESCAPED_UNICODE)
        ]);
        $idRisco = $this->db->lastInsertId();

        $stmt = $this->db->prepare("INSERT INTO ficha_risco (id_ficha_fk, id_risco_fk) VALUES (:ficha, :risco)");
        $stmt->execute([':ficha' => $idFicha, ':risco' => $idRisco]);

            return $idRisco;
        } catch (PDOException $e) {
            throw new PDOException('Erro ao inserir risco. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    // ==============================================================
    // READ — Lista resumida para os cards do dashboard
    // ==============================================================
    public function listarTodas(){
        try {
            $sql = "SELECT a.id_atividade, a.nome_atividade, f.id_ficha,
                       f.medidas_protecao_ficha, f.procedimentos_obrigatorios_ficha
                FROM atividade a
                INNER JOIN ficha f ON f.id_atividade_fk = a.id_atividade
                ORDER BY a.id_atividade DESC";
        $atividades = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $resultado = [];
        foreach ($atividades as $a) {
            $riscos = $this->listarRiscosDaFicha($a['id_ficha']);
            $niveis = ['Crítico' => 0, 'Alto' => 0, 'Médio' => 0, 'Baixo' => 0];
            foreach ($riscos as $r) {
                if (isset($niveis[$r['nivel_risco']])) $niveis[$r['nivel_risco']]++;
            }

            $resultado[] = [
                'id_atividade'       => (int)$a['id_atividade'],
                'nome'               => $a['nome_atividade'],
                'totalRiscos'        => count($riscos),
                'niveis'             => $niveis,
                'totalMedidas'       => count(json_decode($a['medidas_protecao_ficha'], true) ?: []),
                'totalProcedimentos' => count(json_decode($a['procedimentos_obrigatorios_ficha'], true) ?: [])
            ];
        }
            return $resultado;
        } catch (PDOException $e) {
            throw new PDOException('Erro ao listar fichas. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    private function listarRiscosDaFicha($idFicha){
        try {
            $stmt = $this->db->prepare("SELECT r.* FROM risco r
                                     INNER JOIN ficha_risco fr ON fr.id_risco_fk = r.id_risco
                                     WHERE fr.id_ficha_fk = :id
                                     ORDER BY r.id_risco ASC");
        $stmt->execute([':id' => $idFicha]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException('Erro ao listar riscos da ficha. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    // ==============================================================
    // READ — Ficha completa (usada em "Ver Ficha Completa" e na edição)
    // ==============================================================
    public function buscarPorId($idAtividade){
        try {
            $stmt = $this->db->prepare("SELECT a.*, n.nome_nr FROM atividade a
                                     INNER JOIN nr n ON n.id_nr = a.id_nr_fk
                                     WHERE a.id_atividade = :id");
        $stmt->execute([':id' => $idAtividade]);
        $atividade = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$atividade) return null;

        $ficha = $this->buscarFichaPorAtividade($idAtividade);
        if (!$ficha) return null;

        $riscos = $this->listarRiscosDaFicha($ficha['id_ficha']);
        foreach ($riscos as &$r) {
            $r['medidas_controle_risco']  = json_decode($r['medidas_controle_risco'], true) ?: [];
            $r['epis_relacionados_risco'] = json_decode($r['epis_relacionados_risco'], true) ?: [];
        }
        unset($r);

            return [
            'id_atividade'      => (int)$atividade['id_atividade'],
            'nome'              => $atividade['nome_atividade'],
            'id_nr'             => (int)$atividade['id_nr_fk'],
            'nomeNr'            => $atividade['nome_nr'],
            'id_ficha'          => (int)$ficha['id_ficha'],
            'medidasColetivas'  => json_decode($ficha['medidas_protecao_ficha'], true) ?: [],
            'procedimentos'     => json_decode($ficha['procedimentos_obrigatorios_ficha'], true) ?: [],
            'riscos'            => $riscos
            ];
        } catch (PDOException $e) {
            throw new PDOException('Erro ao buscar ficha por atividade. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    // ==============================================================
    // UPDATE — Edita nome da atividade, medidas/procedimentos e a
    // lista de riscos (insere novos, atualiza existentes, remove os
    // que não vieram mais na submissão)
    // ==============================================================
    public function atualizar($idAtividade, $novoNome, array $riscos, array $medidasColetivas, array $procedimentos, $idNr = null){
        $this->db->beginTransaction();
        try {
            if ($idNr !== null) {
                $this->verificarNRExiste($idNr);
                $stmt = $this->db->prepare("UPDATE atividade SET nome_atividade = :nome, id_nr_fk = :nr WHERE id_atividade = :id");
                $stmt->execute([':nome' => $novoNome, ':nr' => $idNr, ':id' => $idAtividade]);
            } else {
                $stmt = $this->db->prepare("UPDATE atividade SET nome_atividade = :nome WHERE id_atividade = :id");
                $stmt->execute([':nome' => $novoNome, ':id' => $idAtividade]);
            }

            $ficha = $this->buscarFichaPorAtividade($idAtividade);
            if (!$ficha) throw new Exception("Ficha não encontrada para esta atividade.");

            $stmt = $this->db->prepare("UPDATE ficha SET medidas_protecao_ficha = :m, procedimentos_obrigatorios_ficha = :p WHERE id_ficha = :id");
            $stmt->execute([
                ':m'  => json_encode(array_values($medidasColetivas), JSON_UNESCAPED_UNICODE),
                ':p'  => json_encode(array_values($procedimentos), JSON_UNESCAPED_UNICODE),
                ':id' => $ficha['id_ficha']
            ]);

            $riscosAtuais = $this->listarRiscosDaFicha($ficha['id_ficha']);
            $idsAtuais    = array_map(function($r){ return (int)$r['id_risco']; }, $riscosAtuais);
            $idsEnviados  = [];

            foreach ($riscos as $r) {
                if (!empty($r['id_risco'])) {
                    $idsEnviados[] = (int)$r['id_risco'];
                    $this->atualizarRisco((int)$r['id_risco'], $r);
                } else {
                    $idsEnviados[] = (int)$this->inserirRisco($ficha['id_ficha'], $r);
                }
            }

            foreach (array_diff($idsAtuais, $idsEnviados) as $idRemover) {
                $this->removerRisco($idRemover);
            }

            $this->db->commit();
            return $this->buscarPorId($idAtividade);
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new PDOException('Erro ao atualizar ficha. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function atualizarRisco($idRisco, array $r){
        try {
            $nivel = $this->calcularNivel($r['probabilidade'], $r['severidade']);
            $nome  = mb_substr(trim($r['descricao']), 0, 255);

        $stmt = $this->db->prepare("UPDATE risco SET
            nome_risco = :nome, descricao_risco = :descricao, tipo_risco = :tipo, nivel_risco = :nivel,
            probabilidade_risco = :prob, severidade_risco = :sev,
            medidas_controle_risco = :medidas, epis_relacionados_risco = :epis
            WHERE id_risco = :id");
        $stmt->execute([
            ':nome'      => $nome,
            ':descricao' => $r['descricao'],
            ':tipo'      => $r['tipo'],
            ':nivel'     => $nivel,
            ':prob'      => (int)$r['probabilidade'],
            ':sev'       => (int)$r['severidade'],
            ':medidas'   => json_encode($r['medidasControle'] ?? [], JSON_UNESCAPED_UNICODE),
            ':epis'      => json_encode($r['epis'] ?? [], JSON_UNESCAPED_UNICODE),
            ':id'        => $idRisco
            ]);
        } catch (PDOException $e) {
            throw new PDOException('Erro ao atualizar risco. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    private function removerRisco($idRisco){
        try {
            $stmt = $this->db->prepare("DELETE FROM ficha_risco WHERE id_risco_fk = :id");
            $stmt->execute([':id' => $idRisco]);
            $stmt = $this->db->prepare("DELETE FROM risco WHERE id_risco = :id");
            $stmt->execute([':id' => $idRisco]);
        } catch (PDOException $e) {
            throw new PDOException('Erro ao remover risco. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        }
    }

    // ==============================================================
    // DELETE — Exclui a ficha inteira (atividade + ficha + riscos)
    // ==============================================================
    public function excluir($idAtividade){
        $this->db->beginTransaction();
        try {
            $ficha = $this->buscarFichaPorAtividade($idAtividade);
            $idsRiscos = [];
            if ($ficha) {
                $riscos = $this->listarRiscosDaFicha($ficha['id_ficha']);
                $idsRiscos = array_map(function($r){ return $r['id_risco']; }, $riscos);
            }

            // Excluir a atividade cascateia para "ficha" e "ficha_risco"
            // (ON DELETE CASCADE já definido no schema), mas os
            // registros de "risco" precisam ser removidos manualmente.
            $stmt = $this->db->prepare("DELETE FROM atividade WHERE id_atividade = :id");
            $stmt->execute([':id' => $idAtividade]);

            foreach ($idsRiscos as $idRisco) {
                $stmt = $this->db->prepare("DELETE FROM risco WHERE id_risco = :id");
                $stmt->execute([':id' => $idRisco]);
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new PDOException('Erro ao excluir ficha. Código: ' . $e->getCode(), (int)$e->getCode(), $e);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
?>