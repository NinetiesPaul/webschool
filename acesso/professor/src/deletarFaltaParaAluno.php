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
        
        $diarioQuery = $db->query("
        select *
        from diariodeclasse
        where idDiario = $id
        ");

        $diario = $diarioQuery->fetch(PDO::FETCH_OBJ);

        $user = $db->prepare("UPDATE diariodeclasse SET presenca = 0 WHERE idDiario=:id");

        $user->execute([
            'id' => $id,
        ]);
        
        header("Location: ../diario-de-classe/$diario->idTurma/$diario->idDisciplina");
        
    }
} else {
    header('Location: ../index.php');
}
