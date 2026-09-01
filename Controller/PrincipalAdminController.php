<?php

namespace Controller;

require_once __DIR__ . '/../Model/PrincipalAdmin.php';

use Exception;
use Model\PrincipalAdmin;

class PrincipalAdminController
{
    private $model;

    public function __construct()
    {
        $this->model = new PrincipalAdmin();
    }

    /**
     * Busca os dados do administrador
     */
    public function buscarAdmin($id_adm)
    {
        try {
            return $this->model->buscarAdmin($id_adm);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar administrador: " . $e->getMessage());
        }
    }

    /**
     * Retorna o total de treinamentos cadastrados
     */
    public function totalTreinamentos()
    {
        try {
            return $this->model->contarTotalTreinamentos();
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar total de treinamentos: " . $e->getMessage());
        }
    }

    /**
     * Dias sem acidentes
     */
    public function diasSemIncidentes()
    {
        try {

            $ultimo = $this->model->buscarUltimoIncidente();

            if (!$ultimo) {
                return 0;
            }

            $dataUltimo = new \DateTime($ultimo['data_incidente']);
            $hoje = new \DateTime('today', new \DateTimeZone('America/Sao_Paulo'));

            return $hoje->diff($dataUltimo)->days;

        } catch (Exception $e) {
            throw new Exception("Erro ao buscar dias sem acidentes: " . $e->getMessage());
        }
    }

    /**
     * Quantidade de incidentes 
     */
    public function incidentesqtd()
    {
        try {
            return $this->model->contarIncidentes();
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar incidentes : " . $e->getMessage());
        }
    }

    /**
     * Lista as notificações
     */
    public function notificacoes()
    {
        try {
            return $this->model->buscarNotificacoes();
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar notificações: " . $e->getMessage());
        }
    }
}