<?php

session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "admin") {
        header('Location: ../index.php');
    } else {
        ini_set('display_errors', true);

        include '../../../data/conn.php';
        
        $id = $_GET['resp'];
        $aluno = $_GET['aluno'];

        $user = $db->prepare("
            delete from responsavelporaluno
            where responsavelporaluno.idresponsavel in (select idresponsavel from responsavel where idUsuario=:user_id)
            and responsavelporaluno.idaluno in (select idaluno from aluno where idUsuario=:idaluno)
        ");

        $user->execute([
            'user_id' => $id,
            'idaluno' => $aluno,
        ]);

        header("Location: ../alterarResponsavel.php?user=$id");
    }
} else {
    header('Location: ../index.php');
}
