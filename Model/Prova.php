<?php
namespace Model;

require_once __DIR__ . "/../vendor/autoload.php";

use PDO;
use PDOException;

/**
 * Acesso a dados de provas e questões.
 * Regra de negócio e validação ficam no Controller\ProvaController.
 * Assume que Model\Connection::getInstance() devolve um PDO com
 * PDO::ATTR_ERRMODE = PDO::ERRMODE_EXCEPTION (padrão recomendado).
 */
class Prova
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    // ================= PROVA =================

    public function createProva(string $nome, int $idTreinamento, ?string $conteudo = null): int
    {
        $sql = "INSERT INTO prova (nome_prova, conteudo_prova, id_treinamento_fk)
                VALUES (:nome_prova, :conteudo_prova, :id_treinamento_fk)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":nome_prova", $nome, PDO::PARAM_STR);
        $stmt->bindParam(":conteudo_prova", $conteudo, PDO::PARAM_STR);
        $stmt->bindParam(":id_treinamento_fk", $idTreinamento, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    public function updateProva(int $idProva, string $nome, ?string $conteudo = null): bool
    {
        $sql = "UPDATE prova
                SET nome_prova = :nome_prova, conteudo_prova = :conteudo_prova
                WHERE id_prova = :id_prova";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":nome_prova", $nome, PDO::PARAM_STR);
        $stmt->bindParam(":conteudo_prova", $conteudo, PDO::PARAM_STR);
        $stmt->bindParam(":id_prova", $idProva, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteProva(int $idProva): bool
    {
        // As questões somem junto via FK ON DELETE CASCADE.
        $sql = "DELETE FROM prova WHERE id_prova = :id_prova";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id_prova", $idProva, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getProvaById(int $idProva): array|false
    {
        $sql = "SELECT * FROM prova WHERE id_prova = :id_prova";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id_prova", $idProva, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProvaByTreinamento(int $idTreinamento): array|false
    {
        $sql = "SELECT * FROM prova WHERE id_treinamento_fk = :id_treinamento_fk LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id_treinamento_fk", $idTreinamento, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ================= QUESTÃO =================

    public function getAllQuestion(int $idProva): array
    {
        $sql = "SELECT * FROM questao WHERE id_prova_fk = :id_prova_fk ORDER BY id_questao ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id_prova_fk", $idProva, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuestionById(int $idQuestao): array|false
    {
        $sql = "SELECT * FROM questao WHERE id_questao = :id_questao";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id_questao", $idQuestao, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createQuestion(
        int $idProva,
        string $enunciado,
        string $alternativa,
        string $altA,
        string $altB,
        string $altC,
        string $altD,
        string $altE
    ): int {
        $sql = "INSERT INTO questao
                (enunciado_questao, alternativa_questao, alt_a_questao, alt_b_questao, alt_c_questao, alt_d_questao, alt_e_questao, id_prova_fk)
                VALUES
                (:enunciado, :alternativa, :alt_a, :alt_b, :alt_c, :alt_d, :alt_e, :id_prova_fk)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":enunciado", $enunciado, PDO::PARAM_STR);
        $stmt->bindParam(":alternativa", $alternativa, PDO::PARAM_STR);
        $stmt->bindParam(":alt_a", $altA, PDO::PARAM_STR);
        $stmt->bindParam(":alt_b", $altB, PDO::PARAM_STR);
        $stmt->bindParam(":alt_c", $altC, PDO::PARAM_STR);
        $stmt->bindParam(":alt_d", $altD, PDO::PARAM_STR);
        $stmt->bindParam(":alt_e", $altE, PDO::PARAM_STR);
        $stmt->bindParam(":id_prova_fk", $idProva, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    public function updateQuestion(
        int $idQuestao,
        string $enunciado,
        string $alternativa,
        string $altA,
        string $altB,
        string $altC,
        string $altD,
        string $altE
    ): bool {
        $sql = "UPDATE questao SET
                enunciado_questao = :enunciado,
                alternativa_questao = :alternativa,
                alt_a_questao = :alt_a,
                alt_b_questao = :alt_b,
                alt_c_questao = :alt_c,
                alt_d_questao = :alt_d,
                alt_e_questao = :alt_e
                WHERE id_questao = :id_questao";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":enunciado", $enunciado, PDO::PARAM_STR);
        $stmt->bindParam(":alternativa", $alternativa, PDO::PARAM_STR);
        $stmt->bindParam(":alt_a", $altA, PDO::PARAM_STR);
        $stmt->bindParam(":alt_b", $altB, PDO::PARAM_STR);
        $stmt->bindParam(":alt_c", $altC, PDO::PARAM_STR);
        $stmt->bindParam(":alt_d", $altD, PDO::PARAM_STR);
        $stmt->bindParam(":alt_e", $altE, PDO::PARAM_STR);
        $stmt->bindParam(":id_questao", $idQuestao, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function deleteQuestion(int $idQuestao): bool
    {
        $sql = "DELETE FROM questao WHERE id_questao = :id_questao";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id_questao", $idQuestao, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
