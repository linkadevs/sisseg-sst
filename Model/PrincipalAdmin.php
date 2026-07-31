<?php

namespace Model;

require_once __DIR__ . '/../Model/Connection.php';

use Exception;
use PDO;
use Model\Connection;

class PrincipalAdmin
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    /**
     * Busca os dados do administrador
     */
    public function buscarAdmin($id_adm)
    {
        try {

            $sql = "SELECT
                        id_adm,
                        nome_adm,
                        cargo_adm,
                        setor_adm
                    FROM administrador
                    WHERE id_adm = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id_adm, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            throw new Exception("Erro ao buscar administrador: " . $e->getMessage());
        }
    }

    /**
     * Conta o total de treinamentos cadastrados
     */
    public function contarTotalTreinamentos()
    {
        try {

            $sql = "SELECT COUNT(*) AS total
                    FROM treinamento";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) $resultado['total'];

        } catch (Exception $e) {
            throw new Exception("Erro ao contar total de treinamentos: " . $e->getMessage());
        }
    }

    /**
     * Busca o último incidente registrado
     */
    public function buscarUltimoIncidente()
    {
        try {

            $sql = "SELECT data_incidente
                    FROM incidente
                    ORDER BY data_incidente DESC
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (Exception $e) {
            throw new Exception("Erro ao buscar último incidente: " . $e->getMessage());
        }
    }

    /**
     * Conta os incidentes abertos
     */
    public function contarIncidentes()
    {
        try {

            $sql = "SELECT COUNT(*) AS total
                FROM incidente";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) $resultado['total'];

        } catch (Exception $e) {
            throw new Exception("Erro ao contar incidentes: " . $e->getMessage());
        }
    }

    /**
     * Busca o último treinamento cadastrado
     */
    public function buscarUltimoTreinamento()
    {
        try {

            $sql = "SELECT
                        id_treinamento AS id,
                        nome_treinamento AS mensagem,
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
     * Busca os últimos incidentes
     */
    public function buscarUltimosIncidentes($limite = 2)
    {
        try {

            $sql = "SELECT
                        id_incidente AS id,
                        tipo_incidente,
                        descricao_incidente AS mensagem,
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
     * Busca as notificações
     */
    public function buscarNotificacoes()
    {
        try {

            $notificacoes = [];

            $treinamento = $this->buscarUltimoTreinamento();

            if ($treinamento) {

                $notificacoes[] = [
                    'mensagem' => 'Novo treinamento disponível: ' . $treinamento['mensagem'],
                    'data' => $treinamento['data'],
                    'icone' => 'risco-azul.png',
                    'classe' => 'notificacao-informacao',
                    'label' => 'Info'
                ];
            }

            $incidentes = $this->buscarUltimosIncidentes(2);

            foreach ($incidentes as $incidente) {

                $notificacoes[] = [
                    'mensagem' => 'Novo ' . strtolower($incidente['tipo_incidente']) . ': ' . $this->resumirMensagem($incidente['mensagem']),
                    'data' => $incidente['data'],
                    'icone' => 'risco-vermelho.png',
                    'classe' => 'notificacao-atencao',
                    'label' => 'Atenção'
                ];
            }

            return array_slice($notificacoes, 0, 3);

        } catch (Exception $e) {
            throw new Exception("Erro ao buscar notificações: " . $e->getMessage());
        }
    }

    /**
     * Resume mensagens longas
     */
    private function resumirMensagem($mensagem)
    {
        if (strlen($mensagem) > 80) {
            return substr($mensagem, 0, 80) . "...";
        }

        return $mensagem;
    }
}