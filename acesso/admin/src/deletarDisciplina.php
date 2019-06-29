<?php

session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "admin") {
        header('Location: ../index.php');
    } else {
        ini_set('display_errors', true);

        include '../../../data/conn.php';
        
        $id = $_GET['disc'];

        $user = $db->prepare("DELETE FROM disciplina WHERE idDisciplina=:id");

        $user->execute([
            'id' => $id,
        ]);

        header('Location: ../cadastrarDisciplina.php');
    }
} else {
    header('Location: ../index.php');
}
