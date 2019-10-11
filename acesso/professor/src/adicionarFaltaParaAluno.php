<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'professor') {
    header('Location: ../home');
    exit();
}

include '../../../includes/php/functions.php';
include '../../../includes/php/conn.php';

if (!empty($_GET)) {

    $ano = $_GET['ano'];
    $mes = $_GET['mes'];
    $dia = $_GET['dia'];

    $aluno = $_GET['a'];
    $disciplina = $_GET['di'];
    $turma = $_GET['t'];

    $mes = (strlen($mes) == 1) ? "0".$mes : $mes;
    $dia = (strlen($dia) == 1) ? "0".$dia : $dia;

    $data = $ano.'-'.$mes.'-'.$dia;

    $diarioQuery = $db->query("
    select id
    from diario_de_classe
    where aluno = $aluno
    and disciplina = $disciplina
    and turma = $turma
    and data = '$data'
    ");

    $diario = $diarioQuery->fetch(PDO::FETCH_OBJ);

    if (!$diario) {
        $user = $db->prepare("
            INSERT INTO diario_de_classe (aluno, disciplina, turma, data, presenca, contexto) 
            values (:aluno, :disciplina, :turma, :data, 1, 'presenca')
        ");

        $user->execute([
            'aluno' => $aluno,
            'disciplina' => $disciplina,
            'turma' => $turma,
            'data' => $data,
        ]);
    } else {
        $presenca = $diario->presenca;

        if ($presenca == False) {
            $presenca = 1;
        } else {
            $presenca = 0;
        }

        $user = $db->prepare("UPDATE diario_de_classe SET presenca = :presenca WHERE id=:id");

        $user->execute([
            'presenca' => $presenca,
            'id' => $diario->id,
        ]);
    }

    header("Location: ../../../../../../diario-de-classe/$turma/$disciplina");
}
