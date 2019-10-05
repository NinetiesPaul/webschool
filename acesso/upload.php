<?php

include '../data/functions.php';
include '../data/conn.php';

$userId = $_POST['usuario'];

$file = $_FILES["fileToUpload"]["name"];
$file = str_replace(" ", "_", $file);

$imageFileType = strtolower(pathinfo($file, PATHINFO_EXTENSION));
$types = ['jpg', 'gif', 'jpeg'];

if (!in_array($imageFileType, $types)) {
    session_start();
    $_SESSION['msg'] = "Erro! O arquivo '" . $file . "' está em formato inválido (apenas JPG, JPEG e GIF são aceitos)";
    header('Location: perfil');
    exit();
}

$path='../res/images/';
$pathThumb='../res/images/thumbs/';

if (!file_exists($path)) {
    mkdir($path);
}
if (!file_exists($pathThumb)) {
    mkdir($pathThumb);
}

$data = getdate();
$long = $data[0];
$file_name=$long.'-'.$file;
$file_name_thumb=$long.'-thumbs-'.$file;

$images = $_FILES["fileToUpload"]["tmp_name"];
$new_images = $long.'-thumbs-'.$file;
$width=200;
$size=GetimageSize($images);
$height=round($width*$size[1]/$size[0]);
$images_orig = ImageCreateFromJPEG($images);
$photoX = ImagesX($images_orig);
$photoY = ImagesY($images_orig);
$images_fin = ImageCreateTrueColor($width, $height);
ImageCopyResampled($images_fin, $images_orig, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);
ImageJPEG($images_fin, $pathThumb.$new_images);
ImageDestroy($images_orig);
ImageDestroy($images_fin);
move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $path . $file_name);
move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $path . $file_name_thumb);

$urlFinal = '../res/images/'.$file_name;
$urlThumbFinal = '../res/images/thumbs/'.$file_name_thumb;

$avatarQuery = $db->query("select * from fotosdeavatar where idUsuario=$userId");
$avatar = $avatarQuery->fetchObject();

if ($avatar) {
    unlink($avatar->imagemThumbUrl);
    unlink($avatar->imagemUrl);

    $deleteAvatar = $db->prepare("DELETE FROM fotosdeavatar WHERE idUsuario=:idUsuario");

    $deleteAvatar->execute([
        'idUsuario' => $userId,
    ]);
}

$user = $db->prepare("
    INSERT INTO fotosdeavatar (imagemThumbUrl, imagemUrl, idUsuario) VALUES (:imagemThumbUrl, :imagemUrl, :idUsuario)
");

$user->execute([
    'imagemThumbUrl' => $urlThumbFinal,
    'imagemUrl' => $urlFinal,
    'idUsuario' => $userId,
]);

header('Location: perfil');
