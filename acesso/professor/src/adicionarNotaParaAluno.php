<?php

ini_set('display_errors', true);
session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "professor") {
        header('Location: index.php');
    } else {
        $userId = $_SESSION['user_id'];
        include '../../../data/functions.php';
        include '../../../data/conn.php';

        if (!empty($_POST)) {
            $aluno = $_POST['aluno'];
            $turma = $_POST['turma'];
            $disciplina = $_POST['disciplina'];
            $nota = $_POST['nota'];
            $notaNum = $_POST['notaNum'];
        
            $user = $db->prepare("
                UPDATE notaPorAluno
                SET ".$notaNum."=:nota
                where idAluno=:idAluno and idDisciplina=:idDisciplina and idTurma=:idTurma
            ");

            $user->execute([
                'nota' => $nota,
                'idAluno' => $aluno,
                'idDisciplina' => $disciplina,
                'idTurma' => $turma,
            ]);
        
            header("Location: ../turmas");
        }
    }
} else {
    header('Location: index.php');
}

