<?php

ini_set('display_errors', true);

include 'data/conn.php';

if (!empty($_POST)) {
	
	$tipo = $_POST['tipo'];
	$name = $_POST['nome'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	
	$checkMail = $db->query("select * from usuario where email='$email'");
	$mail = $checkMail->rowCount();
	
	if ($mail > 0){
	?>
	<html lang="en">
		<head>
			<meta charset="UTF8">
			<link rel="stylesheet" type="text/css" href="css.css">
			<title>webSchool :: Home Page</title>

		</head>
		<body>
			<div id="header">
				<div class="title"><strong>webSchool</strong></div>
			</div>
			
			<br/>
		
			<div id="content">
				<div class="text">
					Esse login já está em uso! Por favor escolha outro.
					<p/><a href='index.php'>Voltar</a>
					
				</div>
			</div>
		</body>
	</html>
	<?php
	die();
	}
	
	
	
	$user = $db->prepare("INSERT INTO usuario (nome, email, pass)
	VALUES (:name, :email, :password)");
	
	$count = $user->execute([
		'name' => $name,
		'email' => $email,
		'password' => $password,
	]);
	
	$userId = (int) $db->lastInsertId();
	
	if ($tipo == "professor") {
		$user = $db->prepare("INSERT INTO professor (idusuario) VALUES (:idUusuario)");
		$user->execute([
			'idUusuario' => $userId,
		]);
	}
	if ($tipo == "aluno") {
		$user = $db->prepare("INSERT INTO aluno (idusuario) VALUES (:idUusuario)");
		$user->execute([
			'idUusuario' => $userId,
		]);
	}
	if ($tipo == "admin") {
		$user = $db->prepare("INSERT INTO admin (idusuario) VALUES (:idUusuario)");
		$user->execute([
			'idUusuario' => $userId,
		]);
	}
	if ($tipo == "responsavel") {
		$user = $db->prepare("INSERT INTO responsavel (idusuario) VALUES (:idUusuario)");
		$user->execute([
			'idUusuario' => $userId,
		]);
	}

	if ($count !== 0){
	?>
	<html lang="en">
		<head>
			<meta charset="UTF8">
			<link rel="stylesheet" type="text/css" href="css.css">
			<title>webSchool :: Home Page</title>

		</head>
		<body>
			<div id="header">
				<div class="title"><strong>webSchool</strong></div>
			</div>
			
			<br/>
		
			<div id="content">
				<div class="text">
					Usuario <?php $userId ?> registrado com sucesso, bem-vindo!
					<p/><a href='index.php'>Redirecionar</a> para minha tela inicial.
					
				</div>
			</div>
		</body>
	</html>
	<?php
		session_start();
		$_SESSION['user_id'] = $userId;
		$_SESSION['tipo'] = $tipo;
	}
	
} else {
	header('Location: index.php');
}

?>