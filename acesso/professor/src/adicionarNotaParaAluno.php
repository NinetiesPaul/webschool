<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'professor') {
    header('Location: ../home');
    exit();
}

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
        UPDATE nota_por_aluno
        SET ".$notaNum."=:nota
        where aluno=:idAluno and disciplina=:idDisciplina and turma=:idTurma
    ");

    $user->execute([
        'nota' => $nota,
        'idAluno' => $aluno,
        'idDisciplina' => $disciplina,
        'idTurma' => $turma,
    ]);

    header("Location: ../turma/$turma");
}
