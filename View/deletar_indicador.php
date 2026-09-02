<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Controller/IndicadoresController.php';

use Controller\IndicadoresController;

$indicadoresController = new IndicadoresController();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!empty($_POST['id_indicador'])) {
        $indicadoresController->deletarIndicador($_POST['id_indicador']);
        header('Location: modulo-indicadores.php');
        exit;
    }
}