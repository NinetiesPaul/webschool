<?php
session_start();

ini_set('display_errors', true);

include '../../data/conn.php';

if (isset($_SESSION['tipo'])){
	$tipo = $_SESSION['tipo'];
	if($tipo != "admin"){
		header('Location: ../../index.php');
	} else {
		
		$endereco = $db->prepare("INSERT INTO endereco (idEstado)
		VALUES (:estado)");
		
		$endereco->execute([
			'estado' => 1,
		]);
		
		$idEndereco = (int) $db->lastInsertId();
		
		$nome = $_POST['nome'];
		$email = $_POST['email'];
                
		$password = $_POST['password'];
                $salt = time() + rand(100,1000);
                $password = md5($password . $salt);
		$turma = $_POST['turma'];
		
		$user = $db->prepare("INSERT INTO usuario (nome, email, pass, salt, idEndereco)
		VALUES (:name, :email, :password, :salt, :endereco)");
		
		$user->execute([
			'name' => $nome,
			'email' => $email,
			'password' => $password,
                        'salt' => $salt,
			'endereco' => $idEndereco,
		]);
		
		$userId = (int) $db->lastInsertId();

		$responsavel = $db->prepare("INSERT INTO responsavel (idUsuario) VALUES (:idUusuario)");
		
		$responsavel->execute([
			'idUusuario' => $userId,
		]);

		$avatar = $db->prepare("INSERT INTO fotosdeavatar (idUsuario) VALUES (:idUusuario)");
		
		$avatar->execute([
			'idUusuario' => $userId,
		]);
	
		header('Location: cadResponsavel.php');

	}
} else {
header('Location: ../../index.php');
}	
?>