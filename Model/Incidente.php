<?php
namespace Model;

require_once __DIR__ . "/Connection.php";

use PDO;
use PDOException;

class Incidente{
    private $db;

    /** Status possíveis — usado pelo Controller para validar entrada */
    const STATUS_VALIDOS = ['Aberto', 'Investigando', 'Concluído'];

    public function __construct(){
        $this->db = Connection::getInstance();
    }

    /**
     * Gera o próximo código sequencial (INC-001, INC-002...).
     * Olha o maior número já usado nos códigos existentes, em vez de
     * usar id_incidente/COUNT(*), pra não colidir se algum registro
     * antigo for excluído no meio do caminho.
     */
    private function gerarProximoCodigo(){
        $sql = "SELECT codigo_incidente FROM incidente
                WHERE codigo_incidente REGEXP '^INC-[0-9]+$'
                ORDER BY CAST(SUBSTRING(codigo_incidente, 5) AS UNSIGNED) DESC
                LIMIT 1";
        $stmt = $this->db->query($sql);
        $ultimo = $stmt ? $stmt->fetchColumn() : false;

        $proximoNumero = 1;
        if ($ultimo) {
            $proximoNumero = ((int) substr($ultimo, 4)) + 1;
        }

        return 'INC-' . str_pad((string) $proximoNumero, 3, '0', STR_PAD_LEFT);
    }

    public function createIncidente($data, $tipo, $gravidade, $local, $atividade, $descricao, $testemunhas, $acao, $treinamento, $foto){
        try{
            $codigo = $this->gerarProximoCodigo();

            $sql = "INSERT INTO incidente
                (codigo_incidente, data_incidente, tipo_incidente, status_incidente, local_incidente, atividade_incidente,
                 descricao_incidente, testemunhas_incidente, acao_imediata_incidente, gravidade_incidente,
                 treinamento_reciclagem_incidente, fotos_incidente)
                VALUES
                (:codigo, :data_incidente, :tipo, 'Aberto', :local_incidente, :atividade,
                 :descricao, :testemunha, :acao, :gravidade,
                 :treinamento, :foto)";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(":codigo", $codigo, PDO::PARAM_STR);
            $stmt->bindParam(":data_incidente", $data, PDO::PARAM_STR);
            $stmt->bindParam(":tipo", $tipo, PDO::PARAM_STR);
            $stmt->bindParam(":local_incidente", $local, PDO::PARAM_STR);
            $stmt->bindParam(":atividade", $atividade, PDO::PARAM_STR);
            $stmt->bindParam(":descricao", $descricao, PDO::PARAM_STR);
            $stmt->bindParam(":testemunha", $testemunhas, PDO::PARAM_STR);
            $stmt->bindParam(":acao", $acao, PDO::PARAM_STR);
            $stmt->bindParam(":gravidade", $gravidade, PDO::PARAM_STR);
            $stmt->bindParam(":treinamento", $treinamento, PDO::PARAM_STR);

            if ($foto !== null && $foto !== '') {
                $stmt->bindParam(":foto", $foto, PDO::PARAM_LOB);
            } else {
                $stmt->bindValue(":foto", null, PDO::PARAM_NULL);
            }

            if ($stmt->execute()) {
                return ['success' => true, 'id' => (int) $this->db->lastInsertId(), 'codigo' => $codigo];
            }
            return ['success' => false];

        } catch(PDOException $e){
            error_log("Erro ao criar incidente: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function updateIncidente($id, $data, $tipo, $status, $local, $atividade, $descricao, $testemunhas, $acao, $gravidade, $treinamento, $foto){
        try{
            $sql = "UPDATE incidente SET
                    data_incidente = :data_incidente,
                    tipo_incidente = :tipo,
                    status_incidente = :status,
                    local_incidente = :local_incidente,
                    atividade_incidente = :atividade,
                    descricao_incidente = :descricao,
                    testemunhas_incidente = :testemunha,
                    acao_imediata_incidente = :acao,
                    gravidade_incidente = :gravidade,
                    treinamento_reciclagem_incidente = :treinamento";

            // foto só entra no UPDATE se uma nova foi enviada; senão mantém a atual
            if ($foto !== null && $foto !== '') {
                $sql .= ", fotos_incidente = :foto";
            }

            $sql .= " WHERE id_incidente = :id";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":data_incidente", $data, PDO::PARAM_STR);
            $stmt->bindParam(":tipo", $tipo, PDO::PARAM_STR);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
            $stmt->bindParam(":local_incidente", $local, PDO::PARAM_STR);
            $stmt->bindParam(":atividade", $atividade, PDO::PARAM_STR);
            $stmt->bindParam(":descricao", $descricao, PDO::PARAM_STR);
            $stmt->bindParam(":testemunha", $testemunhas, PDO::PARAM_STR);
            $stmt->bindParam(":acao", $acao, PDO::PARAM_STR);
            $stmt->bindParam(":gravidade", $gravidade, PDO::PARAM_STR);
            $stmt->bindParam(":treinamento", $treinamento, PDO::PARAM_STR);
            if ($foto !== null && $foto !== '') {
                $stmt->bindParam(":foto", $foto, PDO::PARAM_LOB);
            }

            return $stmt->execute();

        } catch(PDOException $e){
            error_log("Erro ao atualizar incidente: " . $e->getMessage());
            return false;
        }
    }

    public function updateStatus($id, $status){
        try{
            $sql = "UPDATE incidente SET status_incidente = :status WHERE id_incidente = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e){
            error_log("Erro ao atualizar status do incidente: " . $e->getMessage());
            return false;
        }
    }

    public function deleteIncidente($id){
        try{
            $sql = "DELETE FROM incidente WHERE id_incidente = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e){
            error_log("Erro ao excluir incidente: " . $e->getMessage());
            return false;
        }
    }

    public function getById($id){
        try{
            $sql = "SELECT * FROM incidente WHERE id_incidente = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch(PDOException $e){
            error_log("Erro ao buscar incidente: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lista incidentes com filtro opcional de status e busca opcional
     * por descrição, local ou código. Não traz o BLOB da foto (fica pesado
     * pra listagem) — a foto só é buscada em getById, no detalhe.
     * $status = null | 'todos' | 'Aberto' | 'Investigando' | 'Concluído'
     */
    public function getAllFiltered($status = null, $busca = null){
        try{
            $sql = "SELECT id_incidente, codigo_incidente, data_incidente, tipo_incidente, status_incidente,
                           local_incidente, atividade_incidente, descricao_incidente, gravidade_incidente,
                           testemunhas_incidente, acao_imediata_incidente, treinamento_reciclagem_incidente,
                           (fotos_incidente IS NOT NULL) AS tem_foto,
                           criado_em
                    FROM incidente
                    WHERE 1=1";
            $params = [];

            if ($status && $status !== 'todos') {
                $sql .= " AND status_incidente = :status";
                $params[':status'] = $status;
            }

            if ($busca !== null && trim($busca) !== '') {
                $sql .= " AND (descricao_incidente LIKE :busca1 OR local_incidente LIKE :busca2 OR codigo_incidente LIKE :busca3)";
                $termo = '%' . trim($busca) . '%';
                $params[':busca1'] = $termo;
                $params[':busca2'] = $termo;
                $params[':busca3'] = $termo;
            }

            $sql .= " ORDER BY id_incidente DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(PDOException $e){
            error_log("Erro ao listar incidentes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Contadores para os cards de resumo: total + por status.
     */
    public function getCounts(){
        $counts = ['todos' => 0, 'Aberto' => 0, 'Investigando' => 0, 'Concluído' => 0];
        try{
            $sql = "SELECT status_incidente, COUNT(*) as total FROM incidente GROUP BY status_incidente";
            $stmt = $this->db->query($sql);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $counts[$row['status_incidente']] = (int) $row['total'];
                $counts['todos'] += (int) $row['total'];
            }
        } catch(PDOException $e){
            error_log("Erro ao contar incidentes: " . $e->getMessage());
        }
        return $counts;
    }
}
