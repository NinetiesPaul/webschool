<?php

session_start();

if (isset($_SESSION['tipo'])){
	$tipo = $_SESSION['tipo'];
	if($tipo != "admin"){
		header('Location: ../../index.php');
	} else {
		
		ini_set('display_errors', true);

		include '../../data/conn.php';

		if (!empty($_GET)) {
			
			$id = $_GET['resp'];
			$aluno = $_GET['aluno'];
			
			$user = $db->prepare("
			delete from responsavelporaluno
			where responsavelporaluno.idresponsavel in (select idresponsavel from responsavel where idUsuario=:user_id)
			and responsavelporaluno.idaluno in (select idaluno from aluno where idUsuario=:idaluno)
			");
			
			$user->execute([
				'user_id' => $id,
				'idaluno' => $aluno,
			]);
			
			header("Location: _editResponsavel.php?user=$id");
			
		} else {
			echo "Error! <br/><a href='cadResponsavel.php'>Voltar</a>";
		}
	}
} else {
header('Location: ../../index.php');
}	

?>