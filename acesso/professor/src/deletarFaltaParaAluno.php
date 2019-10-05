<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'professor') {
    header('Location: ../home');
    exit();
}

include '../../../data/conn.php';

$id = $_GET['falta'];

$diarioQuery = $db->query("
select *
from diariodeclasse
where idDiario = $id
");

$diario = $diarioQuery->fetch(PDO::FETCH_OBJ);

$user = $db->prepare("UPDATE diariodeclasse SET presenca = 0 WHERE idDiario=:id");
$user->execute([
    'id' => $id,
]);

header("Location: ../diario-de-classe/$diario->idTurma/$diario->idDisciplina");
