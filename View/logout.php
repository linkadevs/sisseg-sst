<?php

session_start();

require_once __DIR__ . '/../Controller/AuthController.php';

$AuthController = new \Controller\AuthController();
$AuthController->logout();

header('Location: /../View/login.php');
exit;
?>