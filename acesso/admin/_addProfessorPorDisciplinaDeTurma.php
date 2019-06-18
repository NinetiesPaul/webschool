<?php
session_start();

ini_set('display_errors', true);

include '../../data/conn.php';

if (isset($_SESSION['tipo'])){
	$tipo = $_SESSION['tipo'];
	if($tipo != "admin"){
		header('Location: ../../index.php');
	} else {
	
		$disciplina = $_POST['disciplina'];
		$turma = $_POST['turma'];
		$professor = $_POST['professor'];
		
		$user = $db->prepare("INSERT INTO disciplinaporprofessor (idProfessor, idDisciplina, idTurma)
		VALUES (:idProfessor, :idDisciplina, :idTurma)");
		
		$count = $user->execute([
			'idProfessor' => $professor,
			'idDisciplina' => $disciplina,
			'idTurma' => $turma,
		]);
		
		header("Location: cadProfessor.php");

	}
} else {
header('Location: ../../index.php');
}	
?>