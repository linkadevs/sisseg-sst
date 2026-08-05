<?php
namespace Model;

require_once __DIR__ . "/Connection.php";

use PDO;
use PDOException;

class Treinamento{
    private $db;

    public function __construct(){
        $this->db = Connection::getInstance();
    }

    /**
     * Cria um novo treinamento.
     * $data_limite pode ser null (treinamento sem validade).
     * $imagem é a string binária da imagem (ou null).
     */
    public function createTreinamento($nome, $subtitulo, $nr, $carga_horaria, $link_aulas, $data_limite, $imagem){
        try{
            $sql = "INSERT INTO treinamento
                (nome_treinamento, subtitulo_treinamento, nr_treinamento, carga_horaria_treinamento, link_aulas_treinamento, data_limite_treinamento, imagem_treinamento)
                VALUES (:nome, :subtitulo, :nr, :carga_horaria, :link_aulas, :data_limite, :imagem)";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(":nome", $nome, PDO::PARAM_STR);
            $stmt->bindParam(":subtitulo", $subtitulo, PDO::PARAM_STR);
            $stmt->bindParam(":nr", $nr, PDO::PARAM_STR);
            $stmt->bindParam(":carga_horaria", $carga_horaria, PDO::PARAM_INT);
            $stmt->bindParam(":link_aulas", $link_aulas, PDO::PARAM_STR);

            if ($data_limite === null || $data_limite === '') {
                $stmt->bindValue(":data_limite", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(":data_limite", $data_limite, PDO::PARAM_STR);
            }

            if ($imagem === null) {
                $stmt->bindValue(":imagem", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(":imagem", $imagem, PDO::PARAM_LOB);
            }

            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            return false;

        } catch (PDOException $e){
            error_log("Erro ao criar treinamento: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza um treinamento existente. $imagem pode ser null para manter a imagem atual
     * (nesse caso a coluna de imagem simplesmente não entra no UPDATE).
     */
    public function updateTreinamento($id, $nome, $subtitulo, $nr, $carga_horaria, $link_aulas, $data_limite, $imagem = null){
        try{
            $camposImagem = $imagem !== null ? ", imagem_treinamento = :imagem" : "";

            $sql = "UPDATE treinamento SET
                nome_treinamento = :nome,
                subtitulo_treinamento = :subtitulo,
                nr_treinamento = :nr,
                carga_horaria_treinamento = :carga_horaria,
                link_aulas_treinamento = :link_aulas,
                data_limite_treinamento = :data_limite
                $camposImagem
                WHERE id_treinamento = :id";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":nome", $nome, PDO::PARAM_STR);
            $stmt->bindParam(":subtitulo", $subtitulo, PDO::PARAM_STR);
            $stmt->bindParam(":nr", $nr, PDO::PARAM_STR);
            $stmt->bindParam(":carga_horaria", $carga_horaria, PDO::PARAM_INT);
            $stmt->bindParam(":link_aulas", $link_aulas, PDO::PARAM_STR);

            if ($data_limite === null || $data_limite === '') {
                $stmt->bindValue(":data_limite", null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(":data_limite", $data_limite, PDO::PARAM_STR);
            }

            if ($imagem !== null) {
                $stmt->bindParam(":imagem", $imagem, PDO::PARAM_LOB);
            }

            $success = $stmt->execute();
            return ['success' => $success];

        } catch (PDOException $e){
            error_log("Erro ao atualizar treinamento: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Ocorreu um erro no servidor.']];
        }
    }

    public function deleteTreinamento($id){
        if (empty($id) || $id <= 0) {
            return ['success' => false, 'errors' => ['ID do treinamento inválido.']];
        }
        try {
            $stmt = $this->db->prepare("DELETE FROM treinamento WHERE id_treinamento = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $success = $stmt->execute();
            if ($success && $stmt->rowCount() > 0) {
                return ['success' => true];
            }
            return ['success' => false, 'errors' => ['Treinamento não encontrado ou já excluído.']];
        } catch (PDOException $e) {
            error_log("Erro ao deletar treinamento: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Ocorreu um erro no servidor.']];
        }
    }

    /**
     * status é calculado no próprio SQL: 'valido' se não houver data limite
     * ou se ela ainda não passou, 'invalido' caso contrário.
     */
    private function selectComStatus(){
        return "SELECT *,
            CASE
                WHEN data_limite_treinamento IS NULL OR data_limite_treinamento >= CURDATE()
                THEN 'valido' ELSE 'invalido'
            END AS status
            FROM treinamento";
    }

    public function getAllTreinamento(){
        try {
            $stmt = $this->db->prepare($this->selectComStatus() . " ORDER BY id_treinamento DESC");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'formatarImagem'], $rows);
        } catch (PDOException $e) {
            error_log("Erro ao buscar todos os treinamentos: " . $e->getMessage());
            return [];
        }
    }

    public function getTreinamentoById($id){
        try {
            $stmt = $this->db->prepare($this->selectComStatus() . " WHERE id_treinamento = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $this->formatarImagem($row) : null;
        } catch (PDOException $e) {
            error_log("Erro ao buscar treinamento por ID: " . $e->getMessage());
            return null;
        }
    }

    public function getTreinamentoByNR($nr){
        try {
            $stmt = $this->db->prepare($this->selectComStatus() . " WHERE nr_treinamento = :nr ORDER BY id_treinamento DESC");
            $stmt->bindParam(":nr", $nr, PDO::PARAM_STR);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map([$this, 'formatarImagem'], $rows);
        } catch (PDOException $e) {
            error_log("Erro ao buscar treinamentos por NR: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Converte o blob binário da imagem em data-URI base64, pronto pra ir
     * direto no atributo src de uma <img> no front-end.
     */
    private function formatarImagem($row){
        if (!empty($row['imagem_treinamento'])) {
            $row['imagem_treinamento'] = 'data:image/jpeg;base64,' . base64_encode($row['imagem_treinamento']);
        } else {
            $row['imagem_treinamento'] = null;
        }
        return $row;
    }
}
