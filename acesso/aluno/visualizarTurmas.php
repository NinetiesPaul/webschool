<?php
session_start();

$tipo = isset($_SESSION['tipo']) ? $_SESSION['tipo'] : false;
if ($tipo !== "aluno" || !$tipo) {
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
        <link href="../../includes/css/css.css" rel="stylesheet">
        <link href="../../includes/css/navbar.css" rel="stylesheet">
        <script src="../../includes/js/jquery.js"></script>
        <script>
        function pesquisarFaltas(aluno,turma,disciplina) {
            $.ajax({
                type: "POST",
                url: "../../includes/php/ajax/pesquisarFaltas.php",
                data:'aluno='+aluno+'&turma='+turma+'&disciplina='+disciplina,
                success: function(data ){
                    $("#result").html(data);
                }
            });
        }
        
        $(document).ready(function(){
            $(".faltas").click(function(){
                dado = this.id;
                dado = dado.split('.');
                aluno = dado[0];
                turma = dado[1];
                disciplina = dado[2];
                pesquisarFaltas(aluno,turma,disciplina);
                
            });
        });
        </script>
        <title>Aluno :: Minhas Turmas</title>
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

                $turmasQuery = $db->query("select turma from nota_por_aluno where aluno=$user->aluno group by turma");
                $turmas = $turmasQuery->fetchAll(PDO::FETCH_OBJ);
                
                foreach ($turmas as $turma) {
                    echo pegarTurma($turma->turma).'<br/>';
                    $notasQuery = $db->query("select * from nota_por_aluno where aluno=$user->aluno and turma=$turma->turma order by disciplina");
                    $notas = $notasQuery->fetchAll(PDO::FETCH_OBJ);
                    
                    echo "<table style='margin-left: auto; margin-right: auto; font-size: 13;' class='table'>";
                    echo '<thead><tr><th></th><th>Nota 1</th><th>Rec. 1</th><th>Nota 2</th><th>Rec. 2</th><th>Nota 3</th><th>Rec. 3</th><th>Nota 4</th><th>Rec. 4</th><th></th></tr></thead>';
                    foreach ($notas as $nota) {
                        echo '<tr><td>'.pegarDisciplina($nota->disciplina).'</td>';
                        echo '<td>'.$nota->nota1.'</td>';
                        echo '<td>'.$nota->rec1.'</td>';
                        echo '<td>'.$nota->nota2.'</td>';
                        echo '<td>'.$nota->rec2.'</td>';
                        echo '<td>'.$nota->nota3.'</td>';
                        echo '<td>'.$nota->rec3.'</td>';
                        echo '<td>'.$nota->nota4.'</td>';
                        echo '<td>'.$nota->rec4.'</td>';
                        echo "<td>
                            <button class='btn btn-sm btn-info faltas' data-toggle='modal' data-target='#modalExemplo' id='$user->aluno.$turma->turma.$nota->disciplina'>
                                Faltas
                            </button>
                        </td>";
                        echo '</tr>';
                    }
                    echo '</table>';
                }
                
                ?>

            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalExemplo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel"><strong>Faltas</strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div id="result">
                    Resultado aqui.
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
              </div>
            </div>
          </div>
        </div>
        
        <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>	
    </body>
</html>
