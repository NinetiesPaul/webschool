<?php
session_start();

ini_set('display_errors', true);

include '../../../data/conn.php';

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "admin") {
        header('Location: index.php');
    } else {
        $serie = $_POST['serie'];
        $nome = $_POST['nome'];
        
        $user = $db->prepare("INSERT INTO turma (nomeTurma, serie)
		VALUES (:nome, :serie)");
        
        $count = $user->execute([
            'nome' => $nome,
            'serie' => $serie,
        ]);
        
        $userId = (int) $db->lastInsertId();
    
        header('Location: ../turma');
    }
} else {
    header('Location: index.php');
}
