<?php

ini_set('display_errors', true);

session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "admin") {
        header('Location: index.php');
    } else {
        include '../../data/functions.php';
        $userId = $_SESSION['user_id'];

        if (!empty($_GET)) {
            $id = $_GET['user'];
        
            include '../../data/conn.php';
        
            $usersQuery = $db->query("
		select usuario.*,aluno.idAluno from usuario, aluno where usuario.idUsuario=aluno.idUsuario and usuario.idUsuario=$id
            ");
        
            $usersQuery = $usersQuery->fetchObject();
            
            if (empty ($usersQuery)) {
                header('Location: cadastrarAluno.php');
            }
            
            ?>

<html lang="en">
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
                data:'login='+val+'&tipo=aluno&id='+<?php echo $id; ?>,
                success: function(data ){
                    if (data == 1){
                        $("#disponibilidade").show();
                        $("#btn").attr("disabled", true);
                    } else {
                        $("#disponibilidade").hide();
                        $("#btn").attr("disabled", false);
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
        <title>webSchool :: Alteração de Aluno</title>
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
                <strong>Alteração de Aluno</strong><p/>
                <form action="alterarAluno.php" method="post" role="form" class="form-horizontal " >
                    <input type="hidden" name="id" value="<?php echo $id ?>" />
                    <input type="hidden" name="idAluno" value="<?php echo $usersQuery->idAluno ?>" />
                    <input type="hidden" name="salt" value="<?php echo $usersQuery->salt ?>" />
                    <div class="form-group row justify-content-center ">
                        <label for="nome" class="col-form-label col-md-2 col-form-label-sm">Nome:</label>
                        <div class="col-md-3">
                                <input type="text" name="nome" id="nome" class="form-control form-control-sm" value="<?php echo $usersQuery->nome; ?>" required>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center ">
                        <label for="email" class="col-form-label col-md-2 col-form-label-sm">Login:</label>
                        <div class="col-md-3">
                            <input type="text" name="email" id="email" class="form-control form-control-sm" aria-describedby="disponibilidade" value="<?php echo $usersQuery->email; ?>" required>
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
                    <?php

                    include '../../data/conn.php';

            $turmaQuery = $db->query("select * from turma order by serie");

            $turmaQuery = $turmaQuery->fetchAll(PDO::FETCH_OBJ); ?>
                    <div class="form-group row justify-content-center ">
                        <label for="password" class="col-form-label col-md-2 col-form-label-sm">Turma:</label>
                        <div class="col-md-3">
                            <select name="turma" class="form-control form-control-sm"  aria-describedby="avisoTurma">
                            <?php foreach ($turmaQuery as $turma) : ?>
                                    <option value="<?php echo $turma->idTurma; ?>"><?php echo $turma->serie.'º Série '.$turma->nomeTurma; ?></option>
                            <?php endforeach; ?>
                            </select> 
                            <small id="avisoTurma" class="form-text text-muted">(Atualmente na <?php echo pegarTurmaDoAluno($id); ?>)</small>
                        </div>
                    </div>
                    <button type="submit" id="btn" class="btn btn-success btn-sm"><span class='glyphicon glyphicon-refresh'></span> Atualizar</button>
                </form>
            </div>
        </div>


        <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
    </body>
</html>

<?php
        } else {
            header('Location: cadastrarAluno.php');
        }

        if (!empty($_POST)) {
            $userId = $_POST['id'];
            $idAluno = $_POST['idAluno'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $salt = $_POST['salt'];
            $turma = $_POST['turma'];

            include '../../data/conn.php';
                
            $sql = 'UPDATE usuario
			SET nome=:nome, email=:email';
                
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
                
            $alunoQuery = $db->prepare($sql);
            $alunoQuery->execute($fields);
                
            $turmaQuery = $db->prepare("UPDATE aluno SET idTurma=:idTurma WHERE idUsuario=:idUusuario");
            $turmaQuery->execute([
                        'idTurma' => $turma,
                        'idUusuario' => $userId,
                ]);
                
            $disciplinasQuery = $db->query("SELECT * FROM disciplinaporprofessor where idTurma = $turma");
            $disciplinas = $disciplinasQuery->fetchAll(PDO::FETCH_OBJ);
                
            foreach ($disciplinas as $disciplina) {
                $checkDisciplinaQuery = $db->query("SELECT idNotaPorAluno FROM notaporaluno WHERE idTurma = $turma and idAluno = $idAluno and idDisciplina = $disciplina->idDisciplina");
                $checkDisciplina = $checkDisciplinaQuery->fetchAll(PDO::FETCH_OBJ);
                    
                if (empty($checkDisciplina)) {
                    $nota = $db->prepare("INSERT INTO notaporaluno (idAluno, idDisciplina, idTurma, nota1, nota2, nota3, nota4, rec1, rec2, rec3, rec4) VALUES (:idAluno, :idDisciplina, :idTurma, :nota1, :nota2, :nota3, :nota4, :rec1, :rec2, :rec3, :rec4)");

                    $nota->execute([
                                'idAluno' => $idAluno,
                                'idDisciplina' => $disciplina->idDisciplina,
                                'idTurma' => $turma,
                                'nota1' => 0,
                                'nota2' => 0,
                                'nota3' => 0,
                                'nota4' => 0,
                                'rec1' => 0,
                                'rec2' => 0,
                                'rec3' => 0,
                                'rec4' => 0,
                        ]);
                }
            }
        
            header('Location: cadastrarAluno.php');
        }
    }
} else {
    header('Location: index.php');
}
