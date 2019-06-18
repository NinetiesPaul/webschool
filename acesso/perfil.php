<?php

session_start();

if (!isset($_SESSION['tipo'])){
	header('Location: ../index.php');
} else {
	include '../data/functions.php';
	include '../data/conn.php';
	
	$tipo = $_SESSION['tipo'];
	$userId = $_SESSION['user_id'];
	$nome = '';
	
	if($tipo == 'admin') {
		header('Location: admin/index.php');
	}

	$usersQuery = $db->query("
	select usuario.* from usuario,$tipo where usuario.idUsuario=$tipo.idUsuario and usuario.idUsuario=$userId
	");
	
	$usersQuery = $usersQuery->fetchObject();
	
	$nome = $usersQuery->nome;
	
}

?>

<html>
	<head>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta charset="UTF8">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
		<link href="../css/glyphicons.css" rel="stylesheet">
		<link href="../res/css.css" rel="stylesheet">
		<link href="../res/navbar.css" rel="stylesheet">
		<script src="../res/jquery.js">
        </script>
		<script src="../res/detect.js">
        </script>
		<script src="../res/back.js">
        </script>
        <script>
		$(document).ready(function(){
			$(".contato").hide();
			$(".endereco").hide();
			
			$(".fotoBotao").css({
				"font-weight": "bold"
			});
			$(".contatoBotao").css({
				"font-weight": "normal"
			});
			$(".enderecoBotao").css({
				"font-weight": "normal"
			});

			$(".fotoBotao").click(function(){
				$(".foto").show();
				$(".contato").hide();
				$(".endereco").hide();
				
				$(".fotoBotao").css({
					"font-weight": "bold"
				});
				$(".contatoBotao").css({
					"font-weight": "normal"
				});
				$(".enderecoBotao").css({
					"font-weight": "normal"
				});
			});
			
			$(".contatoBotao").click(function(){
				$(".foto").hide();
				$(".contato").show();
				$(".endereco").hide();
				
				$(".fotoBotao").css({
					"font-weight": "normal"
				});
				$(".contatoBotao").css({
					"font-weight": "bold"
				});
				$(".enderecoBotao").css({
					"font-weight": "normal"
				});
			});
			
			$(".enderecoBotao").click(function(){
				$(".foto").hide();
				$(".contato").hide();
				$(".endereco").show();
				
				$(".fotoBotao").css({
					"font-weight": "normal"
				});
				$(".contatoBotao").css({
					"font-weight": "normal"
				});
				$(".enderecoBotao").css({
					"font-weight": "bold"
				});
			});
		});
        </script>
		<title>webSchool - Perfil</title>
	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
			<a class="navbar-brand" href="../index.php">webSchool</a>

			<div class="collapse navbar-collapse" id="navbarsExampleDefault">
				<ul class="navbar-nav mr-auto">
					
                                    <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                      Logado como <?php echo $nome; ?>

                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                      <a class="dropdown-item" href="index.php">Home</a>
                                      <a class="dropdown-item" href="../logout.php">Sair</a>
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
				<strong>Meu Perfil</strong><p/>
				<a href="#" class="fotoBotao nonmobile btn btn-secondary">Foto de Avatar</a>
				<a href="#" class="contatoBotao nonmobile btn btn-secondary">Contato</a>
				<a href="#" class="enderecoBotao nonmobile btn btn-secondary">Endereço</a>
				<a href="#" class="fotoBotao mobile btn btn-secondary btn btn-block">Foto de Avatar</a>
				<a href="#" class="contatoBotao mobile btn btn-secondary btn btn-block">Contato</a>
				<a href="#" class="enderecoBotao mobile btn btn-secondary btn btn-block">Endereço</a>
				<div class="foto">
				Foto de Avatar<p/>
				<?php
				
				$perfilQuery = $db->query("select * from usuario where idUsuario=$userId");
				$perfilQuery = $perfilQuery->fetchObject();
				
				$enderecoQuery = $db->query("select * from endereco where idEndereco=$perfilQuery->idEndereco");
				$enderecoQuery = $enderecoQuery->fetchObject();
				
				$avatarQuery = $db->query("select * from fotosdeavatar where idUsuario=$userId");
				$avatar = $avatarQuery->rowCount();
				$avatarQuery = $avatarQuery->fetchObject();
				
				if ($avatar==0){
					echo "<img src='../res/default_avatar.jpg' />";
				} else {
					echo "<img src='".$avatarQuery->imagemThumbUrl."' />";
				}
				
				?>
				
				<form action="upload.php" method="post" enctype="multipart/form-data" role="form" class="form-horizontal" >
					
					<input type="hidden" name="usuario" value="<?php echo $userId; ?>">
					
					<div class="form-group row justify-content-center">
						<label for="fileToUpload" class="col-form-label col-md-2 col-form-label-sm">Selecione foto:</label>
						<div class="col-md-3">
							<input type="file" name="fileToUpload" >
						</div>
					</div>
					
					<button type="submit" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-refresh'></span> Carregar</button>
				</form>
				
				</div>
				
				<div class="contato">
				Informações de contato e login<p/>
				
				<form action="atualizarPerfil.php" method="post" role="form" class="form-horizontal" >
					<input type="hidden" name="tipo" value="usuario">
					<input type="hidden" name="usuario" value="<?php echo $userId; ?>">
					
					<div class="form-group row justify-content-center">
						<label for="nome" class="col-form-label col-md-2 col-form-label-sm">Nome:</label>
						<div class="col-md-3">
							<input type="text" class="form-control form-control-sm" name="nome" value="<?php echo $perfilQuery->nome; ?>" >
						</div>
					</div>
					<div class="form-group row justify-content-center">
						<label for="email" class="col-form-label col-md-2 col-form-label-sm">E-Mail:</label>
						<div class="col-md-3">
							<input type="text" class="form-control form-control-sm" name="email" value="<?php echo $perfilQuery->email; ?>" >
						</div>
					</div>
					<div class="form-group row justify-content-center">
						<label for="senha" class="col-form-label col-md-2 col-form-label-sm">Senha:</label>
						<div class="col-md-3">
							<input type="password" class="form-control form-control-sm " name="senha" aria-describedby="avisoSenha">
							<small id="avisoSenha" class="form-text text-muted">Deixe em branco para manter a senha atual</small>
						</div>
					</div>
					<div class="form-group row justify-content-center">
						<label for="tel1" class="col-form-label col-md-2 col-form-label-sm">Telefone 1:</label>
						<div class="col-md-3">
							<input type="text" class="form-control form-control-sm " name="tel1" value="<?php echo $perfilQuery->telefone1; ?>">
						</div>
					</div>
					<div class="form-group row justify-content-center">
						<label for="tel2" class="col-form-label col-md-2 col-form-label-sm">Telefone  2:</label>
						<div class="col-md-3">
							<input type="text" class="form-control form-control-sm " name="tel2" value="<?php echo $perfilQuery->telefone2; ?>" >
						</div>
					</div>
					
					<button type="submit" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-refresh'></span> Atualizar</button>
				</form>
			
				</div>
				<div class="endereco">
				Endereço<p/>
				
				<form action="atualizarPerfil.php" method="post" role="form" class="form-horizontal" >
					<input type="hidden" name="tipo" value="endereco">
					<input type="hidden" name="endereco" value="<?php echo $enderecoQuery->idEndereco; ?>">
					
					<div class="form-group row justify-content-center">
						<label for="rua" class="col-form-label col-md-2 col-form-label-sm">Rua:</label>
						<div class="col-md-3">
							<input type="text" class="form-control form-control-sm" name="rua" value="<?php echo $enderecoQuery->rua; ?>" >
						</div>
					</div>
					
					<div class="form-group row justify-content-center">
						<label for="numero" class="col-form-label col-md-2 col-form-label-sm">Número:</label>
						<div class="col-md-3">
							<input type="text" class="form-control form-control-sm" name="numero" value="<?php echo $enderecoQuery->numero; ?>" >
						</div>
					</div>
					
					<div class="form-group row justify-content-center">
						<label for="bairro" class="col-form-label col-md-2 col-form-label-sm">Bairro:</label>
						<div class="col-md-3">
							<input type="text" class="form-control form-control-sm" name="bairro" value="<?php echo $enderecoQuery->bairro; ?>" >
						</div>
					</div>
					
					<div class="form-group row justify-content-center">
						<label for="cep" class="col-form-label col-md-2 col-form-label-sm">CEP:</label>
						<div class="col-md-3">
							<input type="text" class="form-control form-control-sm" name="cep" value="<?php echo $enderecoQuery->cep; ?>" >
						</div>
					</div>
					
					<div class="form-group row justify-content-center">
						<label for="complemento" class="col-form-label col-md-2 col-form-label-sm">Complemento:</label>
						<div class="col-md-3">
							<input type="text" class="form-control form-control-sm" name="complemento" value="<?php echo $enderecoQuery->complemento; ?>" >
						</div>
					</div>
					
					<div class="form-group row justify-content-center">
						<label for="cidade" class="col-form-label col-md-2 col-form-label-sm">Cidade:</label>
						<div class="col-md-3">
							<input type="text" class="form-control form-control-sm" name="cidade" value="<?php echo $enderecoQuery->cidade; ?>" >
						</div>
					</div>
					
					<div class="form-group row justify-content-center">
						<label for="estado" class="col-form-label col-md-2 col-form-label-sm">Estado:</label>
						<div class="col-md-3">
							<?php
							
							$estadoQuery = $db->query("select * from estado order by nome");

							$estadoQuery = $estadoQuery->fetchAll(PDO::FETCH_OBJ);	
							
							?>
							<select name="estado" class="form-control form-control-sm" aria-describedby="avisoEstado">
								<?php foreach ($estadoQuery as $estado) : ?>
									<?php echo "<option value='".$estado->idEstado."'>".$estado->nome."</option>"; ?>
								<?php endforeach; ?>
							</select> 
							<small id="avisoEstado" class="form-text text-muted">Atualmente reside em <?php echo pegarEstado($enderecoQuery->idEstado) ?></small>
						</div>
					</div>
					
					<button type="submit" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-refresh'></span> Atualizar</button>
				</form>
				</div>
				<a class="mobile back btn btn-light btn btn-block" >Voltar</a>
			</div>
		
		</div>
		
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
	</body>
</html>
