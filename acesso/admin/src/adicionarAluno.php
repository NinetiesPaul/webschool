<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';
include '../../../data/functions.php';
        
$endereco = $db->prepare("INSERT INTO endereco (idEstado)
    VALUES (:estado)");

$endereco->execute([
    'estado' => 1,
]);

$idEndereco = (int) $db->lastInsertId();

$nome = $_POST['nome'];
$email = $_POST['email'];

$exists = verificarLoginOnPost('aluno', $email);

if ($exists) {
    header('Location: ../aluno');
    exit();
}

$password = $_POST['password'];
$salt = time() + rand(100, 1000);
$password = md5($password . $salt);
$turma = $_POST['turma'];

$user = $db->prepare("
    INSERT INTO usuario (nome, email, pass, idEndereco, salt)
    VALUES (:name, :email, :password, :endereco, :salt)
");

$user->execute([
    'name' => $nome,
    'email' => $email,
    'password' => $password,
    'endereco' => $idEndereco,
    'salt' => $salt,
]);

$userId = (int) $db->lastInsertId();

$aluno = $db->prepare("INSERT INTO aluno (idUsuario, idTurma) VALUES (:idUusuario, :idTurma)");
$aluno->execute([
    'idUusuario' => $userId,
    'idTurma' => $turma,
]);

$lastidQuery = $db->query("SELECT last_insert_id() as id");
$lastid = $lastidQuery->fetch()['id'];

$avatar = $db->prepare("INSERT INTO fotosdeavatar (idUsuario) VALUES (:idUusuario)");
$avatar->execute([
    'idUusuario' => $userId,
]);

$disciplinasQuery = $db->query("SELECT * FROM disciplinaporprofessor where idTurma = $turma");
$disciplinas = $disciplinasQuery->fetchAll(PDO::FETCH_ASSOC);

foreach ($disciplinas as $disciplina) {
    $nota = $db->prepare("INSERT INTO notaporaluno (idAluno, idDisciplina, idTurma, nota1, nota2, nota3, nota4, rec1, rec2, rec3, rec4) VALUES (:idAluno, :idDisciplina, :idTurma, :nota1, :nota2, :nota3, :nota4, :rec1, :rec2, :rec3, :rec4)");

    $nota->execute([
        'idAluno' => $lastid,
        'idDisciplina' => $disciplina['idDisciplina'],
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
    
    $diario = $db->prepare("INSERT INTO diariodeclasse (idAluno, idDisciplina, idTurma, dataDaFalta, presenca) VALUES (:idAluno, :idDisciplina, :idTurma, NOW(), :presenca)");

    $diario->execute([
        'idAluno' => $lastid,
        'idDisciplina' => $disciplina['idDisciplina'],
        'idTurma' => $turma,
        'presenca' => 0,
    ]);
}

header('Location: ../aluno');
