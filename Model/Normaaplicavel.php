<?php

namespace Model;
require_once __DIR__ . '/../Model/Connection.php';

use Exception;
use PDOException;
use Model\Connection;
use PDO;


class Normaaplicavel {

    private $db;

    public function __construct(){
        $this->db = Connection::getInstance();
    }


    public function exibirnorma($id){
    try{
    $sql = "SELECT nome_nr, descricao_nr FROM nr WHERE id_nr = :id_nr";
    $stmt = $this->db->prepare($sql);
    $stmt -> bindParam (":id_nr", $id, PDO::PARAM_INT);
    $stmt->execute();
    $norma = $stmt->fetch(PDO::FETCH_ASSOC);
    return $norma;

    }catch(Exception $erro){
        throw new Exception ("Erro ao obter NR" .$erro->getMessage());
    }
    }

}











?>