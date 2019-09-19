<?php

session_start();

if (isset($_SESSION['tipo'])) {
    $tipo = $_SESSION['tipo'];
    if ($tipo != "admin") {
        header('Location: index.php');
    } else {
        ini_set('display_errors', true);

        include '../../../data/conn.php';
        
        $id = $_GET['id'];

        $prof = $db->prepare("DELETE FROM professor WHERE idUsuario=:user_id");

        $prof->execute([
            'user_id' => $id,
        ]);

        $avatarQuery = $db->query("select * from fotosdeavatar where idUsuario=$id");
        $avatar = $avatarQuery->rowCount();
        $avatarQuery = $avatarQuery->fetchObject();

        if ($avatar > 0) {
            /*$res = unlink('../../'.$avatarQuery->imagemThumbUrl);
            $res2 = unlink('../../'.$avatarQuery->imagemUrl);*/
        }

        $avatar = $db->prepare("DELETE FROM fotosdeavatar WHERE idUsuario=:user_id");

        $avatar->execute([
            'user_id' => $id,
        ]);

        $user = $db->prepare("DELETE FROM usuario WHERE idUsuario=:user_id");

        $user->execute([
            'user_id' => $id,
        ]);

        header('Location: ../professor');
    }
} else {
    header('Location: index.php');
}
