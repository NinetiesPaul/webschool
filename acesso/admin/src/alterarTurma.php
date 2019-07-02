<?php

ini_set('display_errors', true);

session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "admin") {
        header('Location: index.php');
    } else {
        $userId = $_SESSION['user_id'];

        if (!empty($_POST)) {
            $turmaId = $_POST['id'];
            $nome = $_POST['nome'];
            $serie = $_POST['serie'];

            include '../../../data/conn.php';
        
            $user = $db->prepare("
		UPDATE turma
		SET serie=:serie, nomeTurma=:nome
		where idTurma=:turmaId
		");
        
            $user->execute([
                'nome' => $nome,
                'serie' => $serie,
                'turmaId' => $turmaId,
            ]);
        
            header('Location: ../turma.php');
        }
    }
} else {
    header('Location: index.php');
}
