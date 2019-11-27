<?php

session_start();

$tipo = (isset($_SESSION['tipo'])) ? $_SESSION['tipo'] : false;
if (!$tipo || $tipo !== 'professor') {
    header('Location: ../home');
    exit();
}

include '../../../includes/php/functions.php';
include '../../../includes/php/conn.php';

if (!empty($_POST)) {
    $dataComentario = $_POST['data'];
    
    $arquivos = false;
    $file_name = '';
    
    if (empty($_FILES['fileToUpload']['error'])) {
        echo 'salvar arquivo';
        
        $arquivos = true;
        $file = $_FILES["fileToUpload"]["name"];
        $file = str_replace(" ", "_", $file);
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        
        if (in_array($ext, ['pdf', 'txt', 'mp4', 'mp3', 'ogg'])) {
            $path='../../../uploads/files/';
            
            if (!file_exists($path)) {
                mkdir($path);
            }
            
            $data = getdate();
            $long = $data[0];
            $file_name=$long.'-'.$file;
            move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $path . $file_name);
            $urlFinal = '../../../uploads/files/'.$file_name;
            $urlThumbFinal = '';
        }
        
        if (in_array($ext, ['jpg', 'jpeg'])) {
            $path='../../../uploads/images/';
            $pathThumb='../../../uploads/images/thumbs/';

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

            $urlFinal = '../uploads/images/'.$file_name;
            $urlThumbFinal = '../uploads/images/thumbs/'.$file_name_thumb;
        }
    }
    
    $aluno = $_POST['aluno'];
    $turma = $_POST['turma'];
    $disciplina = $_POST['disciplina'];
    $prof = $_POST['prof'];
    $mensagem = $_POST['comentario'];
    
    $return = explode('-', $dataComentario);
    
    $user = $db->prepare("
        INSERT INTO diario_de_classe
        (turma, aluno, disciplina, observacao, data, professor, contexto) VALUES
        (:turma, :aluno, :disciplina, :mensagem, :data, :professor, 'observacao')
    ");

    $user->execute([
        'turma' => $turma,
        'aluno' => $aluno,
        'disciplina' => $disciplina,
        'mensagem' => $mensagem,
        'data' => $dataComentario,
        'professor' => $prof,
    ]);
    
    if ($arquivos) {
        $id = (int) $db->lastInsertId();
        
        $fileQuery = $db->prepare("
            INSERT INTO arquivos (nome, endereco_thumb, endereco, contexto, diario, descricao, data) VALUES (:nome, :endereco_thumb, :endereco, 'ddc', $id, '', :data)
        ");

        $fileQuery->execute([
            'nome' => $file_name,
            'endereco_thumb' => $urlThumbFinal,
            'endereco' => $urlFinal,
            'data' => $dataComentario,
        ]);
    }
    
    header("Location: ../comentario/$return[0]/$return[1]/$return[2]/$aluno/$disciplina/$turma");
}
