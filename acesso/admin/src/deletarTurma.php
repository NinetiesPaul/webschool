<?php

session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "admin") {
        header('Location: ../index.php');
    } else {
        ini_set('display_errors', true);

        include '../../../data/conn.php';
        
        $id = $_GET['user'];

        $user = $db->prepare("DELETE FROM turma WHERE idTurma=:user_id");

        $user->execute([
            'user_id' => $id,
        ]);

        header('Location: ../cadastrarTurma.php');
    }
} else {
    header('Location: ../index.php');
}
