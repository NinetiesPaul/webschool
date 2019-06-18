<?php

include '../data/functions.php';
include '../data/conn.php';

if(!isset($_POST['tipo'])){
	header('Location: perfil.php');
}

if($_POST['tipo']==='usuario'){
	
	$userId = $_POST['usuario'];
	$nome = $_POST['nome'];
	$email = $_POST['email'];
	$password = $_POST['senha'];
	$newPassword = md5($password);
	$tel1 = $_POST['tel1'];
	$tel2 = $_POST['tel2'];
	
	if (strlen($password) == 0) {
	
		$user = $db->prepare("
		UPDATE usuario
		SET nome=:nome, email=:email, telefone1=:tel1, telefone2=:tel2
		where idUsuario=:userId
		");
		
		$user->execute([
			'nome' => $nome,
			'email' => $email,
			'tel1' => $tel1,
			'tel2' => $tel2,
			'userId' => $userId,
		]);
		
	} else {
	
		$user = $db->prepare("
		UPDATE usuario
		SET nome=:nome, email=:email, telefone1=:tel1, telefone2=:tel2, pass=:password
		where idUsuario=:userId
		");
		
		$user->execute([
			'nome' => $nome,
			'email' => $email,
			'password' => $newPassword,
			'tel1' => $tel1,
			'tel2' => $tel2,
			'userId' => $userId,
		]);
	}
} 

if($_POST['tipo']==='endereco'){
	
	$rua = $_POST['rua'];
	$numero = $_POST['numero'];
	$bairro = $_POST['bairro'];
	$complemento = $_POST['complemento'];
	$cidade = $_POST['cidade'];
	$cep = $_POST['cep'];
	$estado = $_POST['estado'];
	$endereco = $_POST['endereco'];
	
	$user = $db->prepare("
	UPDATE endereco
	SET rua=:rua, numero=:numero, bairro=:bairro, complemento=:complemento, cidade=:cidade, cep=:cep, idEstado=:estado
	where idEndereco=:endereco
	");
	
	$user->execute([
		'rua' => $rua,
		'numero' => $numero,
		'bairro' => $bairro,
		'complemento' => $complemento,
		'cidade' => $cidade,
		'cep' => $cep,
		'estado' => $estado,
		'endereco' => $endereco,
	]);
	
}

header('Location: perfil.php');

?>