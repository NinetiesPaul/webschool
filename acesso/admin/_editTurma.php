<?php

ini_set('display_errors', true);

session_start();

if (isset($_SESSION['tipo'])){
	$tipo = $_SESSION['tipo'];
	if($tipo != "admin"){
		header('Location: ../../index.php');
	} else {
	$userId = $_SESSION['user_id'];

	if (!empty($_GET)) {
		
		$id = $_GET['user'];
		
		include '../../data/conn.php';
		
		$usersQuery = $db->query("
		select * from turma where idTurma=$id
		");
		
		$usersQuery = $usersQuery->fetchObject();
?>

<html>
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta charset="UTF8">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<link href="../../css/glyphicons.css" rel="stylesheet">
		<link href="../../res/navbar.css" rel="stylesheet">
		<script src="../../res/jquery.js">
        </script>
		<title>webSchool :: Alteração de Turma</title>
	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<a class="navbar-brand" href="index.php">webSchool</a>

			<div class="collapse navbar-collapse" id="navbarsExampleDefault">
				<ul class="navbar-nav mr-auto">
					
                                    <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      Logado como admin
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                      <a class="dropdown-item" href="index.php">Home</a>
                                      <a class="dropdown-item" href="../../logout.php">Sair</a>
                                      <!-- <a class="dropdown-item" href="#">Another action</a>
                                      <div class="dropdown-divider"></div>
                                      <a class="dropdown-item" href="#">Something else here</a> -->
                                    </div>
                                  </li>	
					
				</ul>
			</div>
		</nav>
		
		<div class="container">
			<div class="jumbotron text-center">
				<strong>Alteração de Turma</strong><p/>
				<form action="_editTurma.php" method="post" role="form" class="form-horizontal " >
					<input type="hidden" name="id" value="<?php echo $id ?>" />
					<div class="form-group row justify-content-center ">
						<label for="serie" class="col-form-label col-md-2 col-form-label-sm">Série:</label>
						<div class="col-md-3">
							<select name="serie" class="form-control form-control-sm" aria-describedby="avisoSerie">
								<option value='1'>1º Série</option>
								<option value='2'>2º Série</option>
								<option value='3'>3º Série</option>
								<option value='4'>4º Série</option>
								<option value='5'>5º Série</option>
								<option value='6'>6º Série</option>
								<option value='7'>7º Série</option>
								<option value='8'>8º Série</option>
								<option value='9'>9º Série</option>
							</select> 
							<small id="avisoSerie" class="form-text text-muted">(Atualmente <?php echo $usersQuery->serie; ?> º Série)</small>
						</div>
					</div>
					<div class="form-group row justify-content-center ">
						<label for="nome" class="col-form-label col-md-2 col-form-label-sm">Letra da Turma:</label>
						<div class="col-md-3">
							<input type="text" name="nome" class="form-control form-control-sm" value="<?php echo $usersQuery->nomeTurma; ?>">
						</div>
					</div>
					<button type="submit" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-refresh'></span> Atualizar</button>
				</form>
				
			</div>
		</div>
		
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
		
	</body>
</html>

<?php 	
	} else {
		header('Location: cadTurma.php');
	}

	if (!empty($_POST)){
		$turmaId = $_POST['id'];
		$nome = $_POST['nome'];
		$serie = $_POST['serie'];

		include '../../data/conn.php';
		
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
		
		header('Location: cadTurma.php');
	}	

	}
} else {
header('Location: ../../index.php');
}	
?>

