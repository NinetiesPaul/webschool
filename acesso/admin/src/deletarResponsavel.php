<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'admin') {
    header('Location: ../home');
    exit();
}

include '../../../includes/php/conn.php';

$id = $_GET['id'];

$user = $db->prepare("UPDATE usuario SET is_deleted = 1 WHERE id=:user_id");
$user->execute([
    'user_id' => $id,
]);

/* $resp = $db->prepare("DELETE FROM responsavel WHERE usuario=:user_id");
$resp->execute([
    'user_id' => $id,
]);

$avatarQuery = $db->query("select * from fotos_de_avatar where usuario=$id");
$avatar = $avatarQuery->fetchObject();

if ($avatar) {
    unlink('../'.$avatarQuery->imagemThumbUrl);
    unlink('../'.$avatarQuery->imagemUrl);
}

$avatar = $db->prepare("DELETE FROM fotos_de_avatar WHERE usuario=:user_id");
$avatar->execute([
    'user_id' => $id,
]);

$user = $db->prepare("DELETE FROM usuario WHERE id=:user_id");
$user->execute([
    'user_id' => $id,
]);

$avatar = $db->prepare("DELETE FROM endereco WHERE usuario=:user_id");
$avatar->execute([
    'user_id' => $id,
]);

$user = $db->prepare("DELETE FROM usuario WHERE id=:user_id");
$user->execute([
    'user_id' => $id,
]); */



header('Location: ../responsavel');
