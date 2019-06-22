<?php
session_start();

ini_set('display_errors', true);

include '../../data/conn.php';

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "admin") {
        header('Location: ../../index.php');
    } else {
        $nome = $_POST['nome'];
        
        $user = $db->prepare("
            INSERT INTO disciplina (nomeDisciplina)
            VALUES (:nome)
        ");
        
        $count = $user->execute([
            'nome' => $nome,
        ]);
        
        $userId = (int) $db->lastInsertId();
    
        header('Location: cadDisciplina.php');
    }
} else {
    header('Location: ../../index.php');
}
