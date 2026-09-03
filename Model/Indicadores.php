<?php 

namespace Model;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Connection.php';

use PDO;
use PDOException;
use Exception;
use Model\Connection;

class Indicadores {
    private $db;

    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function selecionarTodosIndicadores() :array {
        try {
            $sql = 'SELECT 
                        i.*, 
                        GROUP_CONCAT(f.nome_funcionario SEPARATOR ", ") AS funcionarios,
                        GROUP_CONCAT(f.id_funcionario SEPARATOR ", ") AS id_funcionarios
                    FROM indicadores i
                    LEFT JOIN funcionario f ON f.id_indicador_fk = i.id_indicadores
                    GROUP BY i.id_indicadores
                    ORDER BY i.pontos_indicadores DESC';
                    
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao selecionar todos os indicadores',
                0,
                $e
            );
        }
    }

    public function criarIndicador(
        string $nome_equipe_indicadores
    ) :int {
        try {
            $sql = 'INSERT INTO indicadores (
                nome_equipe_indicadores,
                treinamento_percentual_indicadores,
                dias_sem_acidentes_indicadores,
                epi_percentual_indicadores,
                pontos_indicadores
            ) VALUES (
                :nome,
                0,
                0,
                0,
                0
            )';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nome' => $nome_equipe_indicadores
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao criar indicador',
                0,
                $e
            );
        }
    }

    public function editarIndicador(
        int $id_indicador,
        string $nome_equipe_indicadores
    ) :bool {
        try {
            $sql = 'UPDATE indicadores SET nome_equipe_indicadores = :nome_equipe_indicadores WHERE id_indicadores = :id_indicadores';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_indicadores' => $id_indicador,
                ':nome_equipe_indicadores' => $nome_equipe_indicadores
            ]);
        } catch (PDOException $e) {
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
            $sql = 'DELETE FROM indicadores WHERE id_indicadores = :id_indicadores';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_indicadores' => $id_indicador
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao deletar indicador',
                0,
                $e
            );
        }
    }

    public function atualizarDadosIndicador(
        int $treinamentoP,
        int $dias,
        int $epi,
        int $id_indicador
    ) :bool {
        try {
            // die(var_dump($epi));
            $sql = 'UPDATE indicadores SET
                treinamento_percentual_indicadores = :treinamento_percentual_indicadores,
                dias_sem_acidentes_indicadores = :dias_sem_acidentes_indicadores,
                epi_percentual_indicadores = :epi_percentual_indicadores
                WHERE id_indicadores = :id_indicadores';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':treinamento_percentual_indicadores' => $treinamentoP,
                ':dias_sem_acidentes_indicadores' => $dias,
                ':epi_percentual_indicadores' => $epi,
                ':id_indicadores' => $id_indicador
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao atualizar dados indicador',
                0,
                $e
            );
        }
    }

    public function atualizarPontosIndicador(
        int $pontos,
        int $id_indicador
    ) :bool {
        try {
            $sql = 'UPDATE indicadores SET
                pontos_indicadores = :pontos_indicadores
                WHERE id_indicadores = :id_indicadores';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':pontos_indicadores' => $pontos,
                ':id_indicadores' => $id_indicador
            ]);
        } catch (PDOException $e) {
            throw new Exception(
                'Erro ao atualizar pontos indicador',
                0,
                $e
            );
        }
    }
}

?>