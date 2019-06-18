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
			
			$id = $_GET['user'];
			
			$aluno = $db->prepare("DELETE FROM aluno WHERE idUsuario=:user_id");
			
			$aluno->execute([
				'user_id' => $id,
			]);

			$avatarQuery = $db->query("select * from fotosdeavatar where idUsuario=$id");
			$avatar = $avatarQuery->rowCount();
			$avatarQuery = $avatarQuery->fetchObject();
			
			if ($avatar > 0) {
				
				$res = unlink('../'.$avatarQuery->imagemThumbUrl);
				$res2 = unlink('../'.$avatarQuery->imagemUrl);
				
			}
			
			$avatar = $db->prepare("DELETE FROM fotosdeavatar WHERE idUsuario=:user_id");
			
			$avatar->execute([
				'user_id' => $id,
			]);
			
			$user = $db->prepare("DELETE FROM usuario WHERE idUsuario=:user_id");
			
			$user->execute([
				'user_id' => $id,
			]);
			
			header('Location: cadAluno.php');
			
		} else {
			echo "Error! <br/><a href='cadAluno.php'>Voltar</a>";
		}
	}
} else {
header('Location: ../../index.php');
}	

?>