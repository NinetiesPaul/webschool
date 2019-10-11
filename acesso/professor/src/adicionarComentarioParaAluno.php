<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'professor') {
    header('Location: ../home');
    exit();
}

include '../../../includes/php/functions.php';
include '../../../includes/php/conn.php';

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
        from diario_de_classe
        where aluno = $aluno
        and disciplina = $disciplina
        and turma = $turma
        and data = '$data'
        and professor = $prof
        and contexto = 'observacao'
    ");
    $comentario = $comentarioQuery->fetch(PDO::FETCH_OBJ);    
    
    if ($comentario) {
        $user = $db->prepare("
            UPDATE diario_de_classe
            SET observacao = :mensagem
            where id=$comentario->id
        ");

        $res = $user->execute([
            'mensagem' => $mensagem
        ]);
        
        header("Location: ../comentario/$return[0]/$return[1]/$return[2]/$aluno/$disciplina/$turma");
        exit();
    }
    
    $user = $db->prepare("
        INSERT INTO diario_de_classe
        (turma, aluno, disciplina, observacao, data, professor, contexto) VALUES
        (:turma, :aluno, :disciplina, :mensagem, :data, :professor, 'observacao')
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
