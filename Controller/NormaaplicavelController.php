<?php

namespace Controller;
require_once __DIR__."/../Model/Normaaplicavel.php";

use Exception;
use Model\Normaaplicavel;


class NormaaplicavelController{

private $normamodel;

public function __construct(){
    $this->normamodel = new Normaaplicavel();
}

public function exibirNorma($id){
    try{
    $normainfor = $this->normamodel->exibirnorma($id);
    return $normainfor;

    }catch(Exception $erro){
    throw new Exception("Erro ao tentar exibir norma" .$erro->getMessage());
    }

}


}



?>