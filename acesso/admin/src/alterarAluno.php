<?php

ini_set('display_errors', true);

session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "admin") {
        header('Location: index.php');
    } else {
        $userId = $_SESSION['user_id'];

        if (!empty($_POST)) {
            $userId = $_POST['id'];
            $idAluno = $_POST['idAluno'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $salt = $_POST['salt'];
            $turma = $_POST['turma'];

            include '../../../data/conn.php';
                
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
        
            header('Location: ../aluno.php');
        }
    }
} else {
    header('Location: index.php');
}