<?php

ini_set('display_errors', true);

include 'includes/php/conn.php';

if (!empty($_POST)) {
    $tipo = $_POST['tipo'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $usersQuery = $db->query("
        SELECT usuario.*, $tipo.id AS $tipo FROM usuario, $tipo
	WHERE usuario.id=$tipo.usuario
	AND usuario.email='$email'
	");
    
    $user = $usersQuery->fetchObject();
    
    $msg = '';
    $redirect = '';
        
    if ($user) {
        $actualPassword = $user->pass;
        $password = md5($password . $user->salt);
                
        if ($password == $actualPassword) {
            
            if ($user->endereco) {
                $enderecoQuery = $db->query("
                    SELECT * from endereco where id = $user->endereco
                ");

                $endereco = $enderecoQuery->fetchObject();
                $user->endereco = $endereco;
            }
            
            /*echo "<pre>";
            print_r($user);
            echo "</pre>";*/
            
            session_start();
            $_SESSION['user'] = $user;
            $_SESSION['tipo'] = $tipo;
            
            $msg = 'Bem-vindo!';
            $redirect = "<p/><a href='home'>Ir</a> para minha tela inicial.";
        } else {
            $msg = 'Usuário não cadastrado ou senha incorreta!';
            $redirect = "<p/><a href='home'>Voltar</a>";
        }
    } else {
        $msg = 'Usuário não cadastrado ou senha incorreta!';
        $redirect = "<p/><a href='home'>Voltar</a>";
    } ?>
    <html lang="en">
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
            <meta charset="UTF8">
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
            <link href="includes/css/navbar.css" rel="stylesheet">
            <link href="includes/css/login.css" rel="stylesheet">
            <script src="includes/js/jquery.js"></script>
            <title>webSchool :: Home Page</title>
        </head>
        <body>
            <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
                <a class="navbar-brand" >webSchool</a>
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
