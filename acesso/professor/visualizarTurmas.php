<?php
session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "professor") {
        header('Location: index.php');
    } else {
        $userId = $_SESSION['user_id'];
        include '../../data/functions.php';
        
        include '../../data/conn.php';

        $professorQuery = $db->query("select * from professor where idUsuario=$userId");
        $professorQuery = $professorQuery->fetchObject(); ?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../res/css.css" rel="stylesheet">
        <link href="../../res/navbar.css" rel="stylesheet">
        <script src="../../res/jquery.js">
        </script>
        <title>Professor :: Minhas Turmas</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="index.php">webSchool</a>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
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
            </div>
        </nav>

        <div class="container">
            <div class="jumbotron text-center">
                <strong>Minhas turmas</strong><p/>

                <?php

                $disciplinaQuery = $db->query("select * from disciplinaporprofessor where idProfessor=$professorQuery->idProfessor");
                $disciplinaQuery = $disciplinaQuery->fetchAll(PDO::FETCH_OBJ);

                foreach ($disciplinaQuery as $disciplina){
                    echo pegarDisciplina($disciplina->idDisciplina).', '.pegarTurma($disciplina->idTurma);
                    echo "(<a href='detalhesDaTurma.php?turma=$disciplina->idTurma&disc=$disciplina->idDisciplina'>Ver detalhes dessa turma</a>)";

                    $alunosQuery = $db->query("
                        select distinct usuario.* from usuario, aluno, turma, disciplinaporprofessor
                        where usuario.idUsuario=aluno.idUsuario
                        and aluno.idTurma=disciplinaporprofessor.idTurma
                        and disciplinaporprofessor.idTurma=$disciplina->idTurma
                        and disciplinaporprofessor.idDisciplina=$disciplina->idDisciplina
                        and disciplinaporprofessor.idProfessor=$professorQuery->idProfessor
                        order by usuario.nome
                    ");
                    $alunosQuery = $alunosQuery->fetchAll(PDO::FETCH_OBJ);

                    if (!empty($alunosQuery)) {
                        foreach ($alunosQuery as $aluno) {
                            echo '<br/>'.$aluno->nome;
                        }
                    } else {
                        echo '<br/>Sem alunos cadastrados nessa turma';
                    }

                    echo '<p/>';
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
} else {
    header('Location: index.php');
}
?>


	