<?php

session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "professor") {
        header('Location: ../index.php');
    } else {
        ini_set('display_errors', true);

        include '../../../data/conn.php';
        
        $id = $_GET['falta'];

        $user = $db->prepare("DELETE FROM faltaPorAluno WHERE idfaltaporaluno=:id");

        $user->execute([
            'id' => $id,
        ]);

        header('Location: verTurmas.php');
        
    }
} else {
    header('Location: ../index.php');
}
