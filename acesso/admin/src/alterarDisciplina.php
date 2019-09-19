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
            $idDisciplina = $_POST['id'];
            $nome = $_POST['nome'];

            include '../../../data/conn.php';
        
            $user = $db->prepare("
		UPDATE disciplina
		SET nomeDisciplina=:nome
		where idDisciplina=:idDisciplina
		");
        
            $user->execute([
                'nome' => $nome,
                'idDisciplina' => $idDisciplina,
            ]);
        
            header('Location: ../disciplina');
        }
    }
} else {
    header('Location: index.php');
}
