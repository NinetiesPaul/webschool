<?php

ini_set('display_errors', true);

session_start();

if (isset($_SESSION['tipo'])){
	$tipo = $_SESSION['tipo'];
	
	if($tipo != "responsavel"){
		header('Location: ../../index.php');
		
	} else {
		$userId = $_SESSION['user_id'];
		include '../../data/functions.php';
			
		include '../../data/conn.php';

		$responsavelQuery = $db->query("select * from responsavel where idUsuario=$userId");
		$responsavelQuery = $responsavelQuery->fetchObject();

		if (!empty($_GET)) {
			
			$data = $_GET['a'];
			
			$idAluno=$data[0];
			$disciplina=$data[1];
			$turma=$data[2];

?>

<html>
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta charset="UTF8">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<link href="../../res/css.css" rel="stylesheet">
		<link href="../../res/navbar.css" rel="stylesheet">
		<script src="../../res/jquery.js">
        </script>
		<script src="../../res/detect.js">
        </script>
		<title>webSchool :: Alteração de Falta</title>
	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<a class="navbar-brand" href="index.php">webSchool</a>

			<div class="collapse navbar-collapse" id="navbarsExampleDefault">
				<ul class="navbar-nav mr-auto">
					
					<?php 
					
					if (isset($_SESSION['user_id'])){
						echo '<li class="nav-item active">';
						echo "<a class='nav-link' href='../perfil.php'>Meu Perfil</a>";
						echo '</li>';
						echo '<li class="nav-item active" >';
						echo "<div class='nav-link' > Bem-vindo, $tipo ".pegarNomeDoResponsavel($userId);
						echo ' (<a href="../../logout.php">Sair</a>)</div>';		
						echo '</li>';				
					}
						
					?>
					
				</ul>
			</div>
		</nav>
		
		<div class="container">
			<div class="jumbotron text-center">
				<strong>Visualização de Faltas</strong><p/>
				
				<?php
									
					$faltaQuery = $db->query("
					select * from faltaPorAluno where idTurma=$turma and idAluno=$idAluno and idDisciplina=$disciplina
					");
					
					$count = $faltaQuery->rowCount();
					
					$faltaQuery = $faltaQuery->fetchAll(PDO::FETCH_OBJ);	
				
				?>
				
				<?php echo pegarNomeDoAluno($idAluno); ?>
				<?php echo '<br/>'.pegarDisciplina($disciplina).', '.pegarTurma($turma).'<p/>'; ?>
				
				
				<strong>Faltas desse aluno</strong><br/>
				<?php
				if ($count > 0){
					
					foreach ($faltaQuery as $falta):
						echo date('d/m/Y',strtotime($falta->dataDaFalta)).'<br/>';
					endforeach;
					
				} else {
					echo 'Atualmente este aluno não possui faltas.<p/>';
				}	
				?>
				
			</div>
		</div>
		
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	</body>
</html>

<?php
		}
	}
}	else {
header('Location: ../../index.php');
}	
?>

