<?php

ini_set('display_errors', true);

session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "admin") {
        header('Location: index.php');
    } else {
        $userId = $_SESSION['user_id'];
        include '../../data/functions.php';

        if (!empty($_GET)) {
            $id = $_GET['user'];
        
            include '../../data/conn.php';
        
            $usersQuery = $db->query("
		select usuario.* from usuario, responsavel where usuario.idUsuario=responsavel.idUsuario and responsavel.idUsuario=$id
		");
        
            $usersQuery = $usersQuery->fetchObject(); ?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../css/glyphicons.css" rel="stylesheet">
        <link href="../../res/navbar.css" rel="stylesheet">
        <script src="../../res/jquery.js">
        </script>
        <script>
        function verificarLogin(val) {
            $.ajax({
                type: "POST",
                url: "../../data/verificarLoginEmAlteracao.php",
                data:'login='+val+'&tipo=responsavel&id='+<?php echo $id; ?>,
                success: function(data ){
                    if (data == 1){
                        $("#disponibilidade").show();
                        $("#btn").attr("disabled", true)
                    } else {
                        $("#disponibilidade").hide();
                        $("#btn").attr("disabled", false)
                    }
                }
            });
        }


        $(document).on('focusout', '#email', function(){
              verificarLogin($("#email").val());
        });

        $(document).ready(function(){
           $("#disponibilidade").hide();
        });


        </script>
        <title>webSchool :: Alteração de Responsável</title>
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
                <strong>Alteração de Responsável</strong> <p/>
                <form action="_editResponsavel.php" method="post" role="form" class="form-horizontal " >
                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                    <input type="hidden" name="salt" value="<?php echo $usersQuery->salt ?>" />
                    <div class="form-group row justify-content-center ">
                        <label for="nome" class="col-form-label col-md-2 col-form-label-sm">Nome:</label>
                        <div class="col-md-3">
                                <input type="text" name="nome" id="nome" placeholder="Nome" aria-describedby="disponibilidade" value="<?php echo $usersQuery->nome; ?>" class="form-control form-control-sm" required >
                        </div>
                    </div>
                    <div class="form-group row justify-content-center ">
                        <label for="email" class="col-form-label col-md-2 col-form-label-sm">Login:</label>
                        <div class="col-md-3">
                            <input type="text" name="email" id="email" value="<?php echo $usersQuery->email; ?>" class="form-control form-control-sm" required >
                            <small id="disponibilidade">
                                Login em uso!
                            </small>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center ">
                        <label for="password" class="col-form-label col-md-2 col-form-label-sm">Senha:</label>
                        <div class="col-md-3">
                                <input type="password" name="password" class="form-control form-control-sm" >
                        </div>
                    </div>
                    <button type="submit" id="btn" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-refresh'></span> Atualizar</button>
                </form>

                <?php

                include '../../data/conn.php';

            $usersQuery = $db->query("select usuario.* from usuario, aluno where usuario.idUsuario=aluno.idUsuario");

            $usersQuery = $usersQuery->fetchAll(PDO::FETCH_OBJ); ?>

                Selecione quais alunos pertencem a este Responsável:<p/>
                <form action="_addResponsavelPorAluno.php" method="post" role="form" class="form-horizontal " >
                    <input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <div class="form-group row justify-content-center ">
                            <label for="nome" class="col-form-label col-md-2 col-form-label-sm">Nome:</label>
                            <div class="col-md-3" >
                                <select name="aluno" class="form-control form-control-sm">
                                <?php foreach ($usersQuery as $user) : ?>
                                <option value="<?php echo $user->idUsuario; ?>"><?php echo $user->nome; ?> (<?php echo pegarTurmaDoAluno($user->idUsuario); ?>)</option>
                                <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <button type="submit" class="btn btn-primary btn-sm"><span class='glyphicon glyphicon-plus'></span> Cadastrar</button>
                </form>

                <?php

                include '../../data/conn.php';

            $responsavelQuery = $db->query("select idresponsavel from responsavel where idUsuario=$id");

            $responsavelQuery = $responsavelQuery->fetchObject();

            $usersQuery = $db->query("
                select distinct usuario.* from usuario, aluno, responsavelporaluno
                where aluno.idUsuario = usuario.idUsuario
                and aluno.idaluno = responsavelporaluno.idaluno
                and responsavelporaluno.idresponsavel=$responsavelQuery->idresponsavel
                ");

            $count = $usersQuery->rowCount();

            if ($count > 0) {
                $usersQuery = $usersQuery->fetchAll(PDO::FETCH_OBJ);

                echo '<table style="margin-left: auto; margin-right: auto; font-size: 13;">';
                foreach ($usersQuery as $user) {
                    echo '<tr><td>'.$user->nome.'</td><td>'.pegarTurmaDoAluno($user->idUsuario).'</td>';
                    echo "<td><a href='_deleteAlunoDoResponsavel.php?resp=$id&aluno=$user->idUsuario' class='btn btn-danger btn-sm'><span class='glyphicon glyphicon-remove'></span> Deletar</a></td></tr>";
                }
                echo '</table>';
            } else {
                echo 'Atualmente este responsável não possui responsabilizados cadastrados.<p/>';
            } ?>

            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
    </body>
</html>

<?php
        }

        if (!empty($_POST)) {
            $userId = $_POST['id'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $salt = $_POST['salt'];
            $newPassword = md5($password);

            include '../../data/conn.php';
                
            $sql = "
                UPDATE usuario
                SET nome=:nome, email=:email
                ";
            
            $fields = [
                'nome' => $nome,
                'email' => $email,
            ];
        
            if (strlen($password) > 0) {
                $password = $_POST['password'];
                $password = md5($password . $salt);

                $sql .= ' ,pass=:pass';
                $fields['pass'] = $password;
            }
                
            $fields['userId'] = $userId;
            $sql .= ' where idUsuario=:userId';
                    
            $user = $db->prepare($sql);
            $user->execute($fields);
        
            header('Location: cadastrarResponsavel.php');
        }
    }
} else {
    header('Location: index.php');
}
