<?php
session_start();

ini_set('display_errors', true);

include '../../data/conn.php';

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "admin") {
        header('Location: ../index.php');
    } else {
        $responsavel = $_POST['id'];
        $aluno = $_POST['aluno'];

        $responsavelQuery = $db->query("select idresponsavel from responsavel where idUsuario=$responsavel");

        $responsavelQuery = $responsavelQuery->fetchObject();

        $alunoQuery = $db->query("select idaluno from aluno where idUsuario=$aluno");

        $alunoQuery = $alunoQuery->fetchObject();
        
        $user = $db->prepare("INSERT INTO responsavelporaluno (idresponsavel, idaluno)
		VALUES (:responsavel, :aluno)");
        
        $count = $user->execute([
            'responsavel' => $responsavelQuery->idresponsavel,
            'aluno' => $alunoQuery->idaluno,
        ]);
        header("Location: ../alterarResponsavel.php?user=$responsavel");
    }
} else {
    header('Location: ../index.php');
}
