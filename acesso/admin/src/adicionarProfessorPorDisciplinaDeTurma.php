<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../includes/php/conn.php';

if (sizeof($_POST) < 3) {
    header("Location: ../professor");
    exit();
}

$disciplina = $_POST['disciplina'];
$turma = $_POST['turma'];
$professor = $_POST['professor'];

$user = $db->prepare("INSERT INTO disciplina_por_professor (professor, disciplina, turma)
        VALUES (:idProfessor, :idDisciplina, :idTurma)");

$count = $user->execute([
    'idProfessor' => $professor,
    'idDisciplina' => $disciplina,
    'idTurma' => $turma,
]);

$alunosQuery = $db->query("SELECT * FROM aluno where turma = $turma");
$alunos = $alunosQuery->fetchAll(PDO::FETCH_OBJ);

foreach ($alunos as $aluno) {
    $nota = $db->prepare("INSERT INTO nota_por_aluno (aluno, disciplina, turma, nota1, nota2, nota3, nota4, rec1, rec2, rec3, rec4) VALUES (:idAluno, :idDisciplina, :idTurma, 0, 0, 0, 0, 0, 0, 0, 0)");

    $nota->execute([
        'idAluno' => $aluno->id,
        'idDisciplina' => $disciplina,
        'idTurma' => $turma,
    ]);
    
    $diario = $db->prepare("INSERT INTO diario_de_classe (aluno, disciplina, turma, data, contexto, presenca) VALUES (:idAluno, :idDisciplina, :idTurma, NOW(), 'presenca', 0)");

    $diario->execute([
        'idAluno' => $aluno->id,
        'idDisciplina' => $disciplina->disciplina,
        'idTurma' => $turma,
    ]);
}

header("Location: ../professor");
