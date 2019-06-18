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
		
                $alunosQuery = $db->query("SELECT * FROM aluno where idTurma = $turma");
                $alunos = $alunosQuery->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($alunos as $aluno) {
                    $nota = $db->prepare("INSERT INTO notaporaluno (idAluno, idDisciplina, idTurma, nota1, nota2, nota3, nota4, rec1, rec2, rec3, rec4) VALUES (:idAluno, :idDisciplina, :idTurma, :nota1, :nota2, :nota3, :nota4, :rec1, :rec2, :rec3, :rec4)");
		
                    $nota->execute([
                            'idAluno' => $aluno['idAluno'],
                            'idDisciplina' => $disciplina,
                            'idTurma' => $turma,
                            'nota1' => 0,
                            'nota2' => 0,
                            'nota3' => 0,
                            'nota4' => 0,
                            'rec1' => 0,
                            'rec2' => 0,
                            'rec3' => 0,
                            'rec4' => 0,
                    ]);
                }
                
		header("Location: cadProfessor.php");

	}
} else {
header('Location: ../../index.php');
}	
?>