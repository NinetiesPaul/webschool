<?php

ini_set('display_errors', true);

include 'data/conn.php';

if (!empty($_POST)) {
    $tipo = $_POST['tipo'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $usersQuery = $db->query("
	select usuario.idusuario, usuario.salt, usuario.pass from usuario, $tipo
	where usuario.idusuario=$tipo.idusuario
	and usuario.email='$email'
	");
    
    $user = $usersQuery->fetchObject();
                
    $msg = '';
    $redirect = '';
        
    if ($user) {
        $actualPassword = $user->pass;
            
        $salt = $user->salt;
        $password = md5($password . $salt);
                
        if ($password == $actualPassword) {
            $userId = $user->idusuario;
            session_start();
            $_SESSION['user_id'] = $userId;
            $_SESSION['tipo'] = $tipo;
                    
            $msg = 'Bem-vindo!';
            $redirect = "<p/><a href='index.php'>Ir</a> para minha tela inicial.";
        } else {
            $msg = 'Usuário não cadastrado ou senha incorreta!';
            $redirect = "<p/><a href='index.php'>Voltar</a>";
        }
    } else {
        $msg = 'Usuário não cadastrado ou senha incorreta!';
        $redirect = "<p/><a href='index.php'>Voltar</a>";
    } ?>
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

            <div class="container">
                <div class="text-center">
                        <?php echo $msg; ?>
                        <?php echo $redirect; ?>
                </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>				
        </body>
    </html>
	
		
	<?php
} else {
        header('Location: index.php');
    }
