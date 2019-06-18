<?php

session_start();

if (isset($_SESSION['tipo'])){
	$tipo = $_SESSION['tipo'];
	if($tipo == "admin"){
		header('Location: acesso/admin/index.php');
	}
	if($tipo == "responsavel"){
		header('Location: acesso/responsavel/index.php');
	}
	if($tipo == "professor"){
		header('Location: acesso/professor/index.php');
	}
	if($tipo == "aluno"){
		header('Location: acesso/aluno/index.php');
	}
	
} else {

?>

<html lang="en">
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta charset="UTF8">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<link href="res/navbar.css" rel="stylesheet">
		<link href="res/login.css" rel="stylesheet">
		<script src="res/jquery.js">
        </script>
		<title>webSchool :: Home Page</title>
	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<a class="navbar-brand" href="index.php">webSchool</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="PokeXchange">
			<span class="navbar-toggler-icon"></span>
			</button>

		</nav>
	
		<div class="container text-center">
			<h3>Bem-vindo</h3>
			
			<div class="login">
				<form action="login.php" method="post" class="form-signin">
					<input type="text" name="email" placeholder="Digite seu login" class="form-control" >
					<input type="password" name="password" placeholder="Digite sua senha" class="form-control" >
						<select name="tipo" class="form-control" required>
							<option value=''>Selecione tipo de login</option>
							<option value='admin'>Admin</option>
							<option value='professor'>Professor</option>
							<option value='responsavel'>Responsavel</option>
							<option value='aluno'>Aluno</option>
						</select><br/>
					<input type="submit" value="Login" class="btn btn-lg btn-primary btn-block" ><br/>
				</form>
				Branch
			</div>
		</div>
		
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>			
	</body>
</html>

<?php

}

?>