<?php
session_start();

$tipo = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : false;
if ($tipo !== "professor" || !$tipo) {
    header('Location: ../../home');
}

$user = $_SESSION['user'];
include '../../includes/php/functions.php';
include '../../includes/php/conn.php';
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta charset="UTF8">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link href="../../includes/css/glyphicons.css" rel="stylesheet">
        <link href="../../includes/css/navbar.css" rel="stylesheet">
        <link href="../../includes/css/css.css" rel="stylesheet">
        <style>
            #btn_disciplina {
                text-decoration: none;
            }
        </style>
        <script src="../../includes/js/jquery.js"></script>
        <title>Professor :: Minhas Turmas</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="home">webSchool</a>
            <div class="collapse navbar-collapse" id="navbarsExampleDefault">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Logado como <?php echo $user->nome; ?>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="home">Home</a>
                            <a class="dropdown-item" href="../perfil">Meu perfil</a>
                            <a class="dropdown-item" href="../../logout">Sair</a>
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
                
                $disciplinaQuery = $db->query("select * from disciplina_por_professor where professor=$user->professor");
                $disciplinas = $disciplinaQuery->fetchAll(PDO::FETCH_OBJ);

                foreach ($disciplinas as $disciplina){
                    echo pegarDisciplina($disciplina->disciplina).', '.pegarTurma($disciplina->turma);
                    echo "<a href='turma/$disciplina->id' class='btn-sm btn-info' id='btn_disciplina' '><span class='glyphicon glyphicon-eye-open'></span> Visualizar</a>";
                    
                    $alunosQuery = $db->query("
                        select distinct usuario.* from usuario, aluno, turma, disciplina_por_professor
                        where usuario.id=aluno.usuario
                        and aluno.turma=disciplina_por_professor.turma
                        and disciplina_por_professor.turma=$disciplina->turma
                        and disciplina_por_professor.disciplina=$disciplina->disciplina
                        and disciplina_por_professor.professor=$user->professor
                        order by usuario.nome
                    ");
                    $alunosQuery = $alunosQuery->fetchAll(PDO::FETCH_OBJ);
                    
                    $text = '<br/>Sem alunos cadastrados nessa turma no momento';
                    if (!empty($alunosQuery)) {
                        $text = '';
                        foreach ($alunosQuery as $aluno) {
                            $text .= '<br/>'.$aluno->nome;
                        }
                    }

                    echo "$text<p/>";
                }

                ?>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	

    </body>
</html>
