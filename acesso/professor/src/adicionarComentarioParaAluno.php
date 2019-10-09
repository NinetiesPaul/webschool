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
    $data = $_POST['data'];
    $prof = $_POST['prof'];
    $mensagem = $_POST['comentario'];
    
    $return = explode('-', $data);

    $comentarioQuery = $db->query("
        select *
        from diariodeclasse_comentarios
        where aluno = $aluno
        and disciplina = $disciplina
        and turma = $turma
        and data = '$data'
        and professor = $prof
    ");
    
    $comentario = $comentarioQuery->fetch(PDO::FETCH_OBJ);    
    
    if ($comentario) {
        
        $user = $db->prepare("
            UPDATE diariodeclasse_comentarios
            SET mensagem = :mensagem
            where id=$comentario->id
        ");

        $res = $user->execute([
            'mensagem' => $mensagem
        ]);
        
        header("Location: ../comentario/$return[0]/$return[1]/$return[2]/$aluno/$disciplina/$turma");
        exit();
        
    }
    
    $user = $db->prepare("
        INSERT INTO diariodeclasse_comentarios
        (turma, aluno, disciplina, mensagem, data, professor) VALUES
        (:turma, :aluno, :disciplina, :mensagem, :data, :professor)
    ");

    $user->execute([
        'turma' => $turma,
        'aluno' => $aluno,
        'disciplina' => $disciplina,
        'mensagem' => $mensagem,
        'data' => $data,
        'professor' => $prof,
    ]);

    header("Location: ../comentario/$return[0]/$return[1]/$return[2]/$aluno/$disciplina/$turma");
}
