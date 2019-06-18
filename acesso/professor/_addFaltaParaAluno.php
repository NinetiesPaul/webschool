<?php

ini_set('display_errors', true);

session_start();

if (isset($_SESSION['tipo'])){
	$tipo = $_SESSION['tipo'];
	if($tipo != "professor"){
		header('Location: ../../index.php');
	} else {
	$userId = $_SESSION['user_id'];
	include '../../data/functions.php';
		
	include '../../data/conn.php';

	$professorQuery = $db->query("select * from professor where idUsuario=$userId");
	$professorQuery = $professorQuery->fetchObject();

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
		<link href="../../css/glyphicons.css" rel="stylesheet">
		<link href="../../res/navbar.css" rel="stylesheet">
		<script src="../../res/jquery.js">
        </script>
		<title>webSchool :: Alteração de Falta</title>
	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<a class="navbar-brand" href="index.php">webSchool</a>

			<div class="collapse navbar-collapse" id="navbarsExampleDefault">
				<ul class="navbar-nav mr-auto">
					
					<ul class="navbar-nav mr-auto">
					
					<li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      Logado como <?php echo pegarNomeProfessor($professorQuery->idProfessor); ?>
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                      <a class="dropdown-item" href="index.php">Home</a>
                                      <a class="dropdown-item" href="../perfil.php">Meu perfil</a>
                                      <a class="dropdown-item" href="../../logout.php">Sair</a>
                                      <!-- <a class="dropdown-item" href="#">Another action</a>
                                      <div class="dropdown-divider"></div>
                                      <a class="dropdown-item" href="#">Something else here</a> -->
                                    </div>
                                  </li>    
					
				</ul>
					
				</ul>
			</div>
		</nav>
		
		<div class="container">
			<div class="jumbotron text-center">
				<strong>Alteração de Falta</strong> <p/>
				
				<?php
					
					$idAluno = pegarIdDoAluno($idAluno);
					
					$faltaQuery = $db->query("
					select * from faltaPorAluno where idTurma=$turma and idAluno=$idAluno and idDisciplina=$disciplina
					");
					
					$count = $faltaQuery->rowCount();
					
					$faltaQuery = $faltaQuery->fetchAll(PDO::FETCH_OBJ);	
				
				?>
				
				<?php echo pegarNomeDoAluno($idAluno); ?>
				<?php echo '<br/>'.pegarDisciplina($disciplina).', '.pegarTurma($turma).'<p/>'; ?>
				
				<form action="_addFaltaParaAluno.php" method="post" role="form" class="form-horizontal ">
					<input type="hidden" name="aluno" value="<?php echo $idAluno; ?>" />
					<input type="hidden" name="disciplina" value="<?php echo $disciplina; ?>" />
					<input type="hidden" name="turma" value="<?php echo $turma; ?>" />
					<div class="form-group row justify-content-center ">
						<label for="data" class="col-form-label col-md-2 col-form-label-sm ">Data da Falta:</label>
						<div class="col-md-3">
							<input type="date" name="data" class="form-control form-control-sm">
						</div>
					</div>
					<button type="submit" class="btn btn-primary btn-sm btn-sm"><span class='glyphicon glyphicon-plus'></span> Cadastrar</button>
				</form>
				
				<strong>Faltas desse aluno</strong><p/>
				<table style="margin-left: auto; margin-right: auto; font-size: 13;">
				<?php
				if ($count > 0){ 
					foreach ($faltaQuery as $falta):
						echo "<tr><td>".date('d/m/Y',strtotime($falta->dataDaFalta))."</td>";
						echo "<td><a href=	'_delFaltaParaAluno.php?falta=$falta->idFaltaPorAluno' class='btn btn-danger btn-sm'><span class='glyphicon glyphicon-remove'></span> Deletar</a></td></tr>";
					endforeach;
				} else {
					echo 'Atualmente este aluno não possui faltas.<p/>';
				}
				?>
				</table>
				
			</div>
		</div>
		
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
	
	</body>
</html>

<?php 	
	}

	if (!empty($_POST)){
		$aluno = $_POST['aluno'];
		$turma = $_POST['turma'];
		$disciplina = $_POST['disciplina'];
		$data = $_POST['data'];
		
		$user = $db->prepare("
		INSERT INTO faltaPorAluno (idAluno, idDisciplina, idTurma, datadafalta) 
		values (:idAluno, :idDisciplina, :idTurma, :data)
		");
		
		$user->execute([
			'idAluno' => $aluno,
			'idDisciplina' => $disciplina,
			'idTurma' => $turma,
			'data' => $data,
		]);
		
		header("Location: detalhesDaTurma.php?turma=$turma&disc=$disciplina");
	}	

	}
} else {
header('Location: ../../index.php');
}	
?>

