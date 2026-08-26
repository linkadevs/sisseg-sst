<?php

session_start();

unset($_SESSION['id_funcionario']);
unset($_SESSION['id_adm']);
unset($_SESSION['nome']);
unset($_SESSION['setor']);
unset($_SESSION['tipo']);

header('Location: login.php');
exit;
?>