<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers;

use App\DB\DB;
use App\DB\Storage\EnderecoStorage;
use App\DB\Storage\MateriaStorage;
use App\DB\Storage\NotaStorage;
use App\DB\Storage\TurmaStorage;
use App\DB\Storage\UsuarioStorage;
use App\Enum;
use App\Templates;
use App\Util;
use PDO;
use tFPDF;

class GeneralController
{
    protected $connection;
    protected $util;
    protected $template;
    protected $enderecoStorage;
    protected $materiaStorage;
    protected $turmaStorage;
    protected $usuarioStorage;
    protected $notaStorage;
    protected $links;

    public function __construct()
    {
        $this->connection = new DB;
        $this->util = new Util();
        $this->template = new Templates();
        $this->enderecoStorage = new EnderecoStorage();
        $this->materiaStorage = new MateriaStorage();
        $this->turmaStorage = new TurmaStorage();
        $this->usuarioStorage = new UsuarioStorage();
        $this->notaStorage = new NotaStorage();
        $this->links = $this->util->generateLinks();
        session_start();
    }

    public function index()
    {
        session_start();
        if (isset($_SESSION['tipo'])) {
            $tipo = $_SESSION['tipo'];
            if ($tipo == Enum::TIPO_ADMIN) {
                header('Location: admin/home');
            }
            if ($tipo == Enum::TIPO_RESPONSAVEL) {
                header('Location: responsavel/home');
            }
            if ($tipo == Enum::TIPO_PROFESSOR) {
                header('Location: professor/home');
            }
            if ($tipo == Enum::TIPO_ALUNO) {
                header('Location: aluno/home');
            }
        }

        $this->util->loadTemplate('index.html');
    }
    
    public function gerarBoletim()
    {
        $aluno = $_POST["aluno-pdf"];
        $turma = $_POST["turma-pdf"];

        $notas = $this->notaStorage->verNotasPorTruma($aluno, $turma);

        $pdf = new tFPDF();
        $pdf->AddPage();

        // Colors, line width and bold font
        $pdf->SetFillColor(43, 74, 92);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(.3);
        $pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
        $pdf->SetFont('DejaVu', '', 8);

        // Header

        $headers = ['Matéria','Nota 1', 'Rec. 1', 'Nota 2', 'Rec. 2', 'Nota 3', 'Rec. 3', 'Nota 4', 'Rec. 4'];

        foreach ($headers as $header) {
            $width = ($header === 'Matéria') ? 40 : 10;
            $pdf->Cell($width, 7, $header, 1, 0, 'C', true);
        }
        $pdf->Ln();
        // Color and font restoration
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('DejaVu', '', 8);
        // Data
        $fill = false;
        foreach ($notas as $row) {
            $pdf->SetFont('DejaVu', '', 8);
            $pdf->Cell(40, 6, $row->materia, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->nota1, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->rec1, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->nota2, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->rec2, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->nota3, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->rec3, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->nota4, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->rec4, 'LR', 0, 'L', $fill);
            $pdf->Ln();
            $pdf->SetFont('DejaVu', '', 4);
            $pdf->Cell(40, 2, $row->nome_professor, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
            $pdf->Ln();
            $fill = !$fill;
        }
        // Closing line
        $pdf->Cell(120, 0, '', 'T');

        $pdf->Output('boletim.pdf', 'D');
        exit();
    }
    
    public function gerarHistorico()
    {
        $aluno = $_POST["aluno-pdf"];
        
        $aluno = explode('.', $aluno);

        $turmas = $this->notaStorage->verTurmasEMateriasComNotasDoAluno($aluno[0]);

        $pdf = new tFPDF();

        foreach ($turmas as $turma) {
            $notas = $this->notaStorage->verNotasPorTruma($aluno[0], $turma->turma);

            $pdf->AddPage();

            // Colors, line width and bold font
            $pdf->SetFillColor(43, 74, 92);
            $pdf->SetTextColor(255);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(.3);
            $pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
            $pdf->SetFont('DejaVu', '', 8);
            // Header

            $pdf->Cell(120, 7, $turma->nome_da_turma, 1, 0, 'C', true);
            $pdf->Ln();
            $headers = ['Matéria','Nota 1', 'Rec. 1', 'Nota 2', 'Rec. 2', 'Nota 3', 'Rec. 3', 'Nota 4', 'Rec. 4'];

            foreach ($headers as $header) {
                $width = ($header === 'Matéria') ? 40 : 10;
                $pdf->Cell($width, 7, $header, 1, 0, 'C', true);
            }
            $pdf->Ln();
            // Color and font restoration
            $pdf->SetFillColor(224, 235, 255);
            $pdf->SetTextColor(0);
            $pdf->SetFont('DejaVu', '', 8);
            // Data
            $fill = false;
            foreach ($notas as $row) {
                $pdf->SetFont('DejaVu', '', 8);
                $pdf->Cell(40, 6, $row->materia, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->nota1, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->rec1, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->nota2, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->rec2, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->nota3, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->rec3, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->nota4, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->rec4, 'LR', 0, 'L', $fill);
                $pdf->Ln();
                $pdf->SetFont('DejaVu', '', 4);
                $pdf->Cell(40, 2, $row->nome_professor, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 2, '', 'LR', 0, 'L', $fill);
                $pdf->Ln();
                $fill = !$fill;
            }
            // Closing line
            $pdf->Cell(120, 0, '', 'T');
        }

        $pdf->Output('historico.pdf', 'D');
        exit();
    }
    
    public function visualizarPerfil()
    {
        $this->links = $this->util->generateLinks('', true);
        $user = $_SESSION['user'];
        
        $tipo = (isset($user->aluno)) ? 'aluno' : ((isset($user->professor) > 0) ? 'professor' : 'responsavel');
        
        $estadoQuery = $this->connection->query("select * from estado order by nome");
        $estadoQuery = $estadoQuery->fetchAll(PDO::FETCH_OBJ);
        
        $estados = '';
        foreach ($estadoQuery as $estado) {
            $estados .= "<option value='".$estado->id."'>".$estado->nome."</option>";
        }
        
        $avatarQuery = $this->connection->query("select * from fotos_de_avatar where usuario=$user->id");
        $avatar = $avatarQuery->fetchObject();

        $img = "<img src='uploads/default_avatar.jpg' />";
        if (isset($avatar->endereco)) {
            $img = "<img src='".$avatar->endereco_thumb."' />";
        }
        
        $msg = '';
        if (isset($_SESSION['msg'])) {
            $msg = $_SESSION['msg'];
            unset($_SESSION['msg']);
        }
        
        $args = [
            'MSG' => $msg,
            'AVATAR' => $img,
            'TIPO' => $tipo,
            'ID' => $user->id,
            'NOME' => $user->nome,
            'EMAIL' => $user->email,
            'TELEFONE1' => $user->telefone1,
            'TELEFONE2' => $user->telefone2,
            'SALT' => $user->salt,
            'ENDERECO_ID' => $user->endereco->id,
            'ENDERECO_BAIRRO' => $user->endereco->bairro,
            'ENDERECO_CEP' => $user->endereco->cep,
            'ENDERECO_CIDADE' => $user->endereco->cidade,
            'ENDERECO_ESTADO' => $user->endereco->estado,
            'ENDERECO_NUMERO' => $user->endereco->numero,
            'ENDERECO_RUA' => $user->endereco->rua,
            'ENDERECO_COMPLEMENTO' => $user->endereco->complemento,
            'LOGADO' => $user->nome,
            'ESTADOS' => $estados,
            'ESTADO_ATUAL' => $this->enderecoStorage->pegarEstadoPeloEstado($user->endereco->estado),
            'LINKS' => $this->links
        ];
        
        $this->util->loadTemplate('perfil.html', $args);
    }
    
    public function alterarPerfil()
    {
        $tipo = (isset($_POST['tipo_update'])) ? $_POST['tipo_update'] : false;

        switch ($tipo) {
            case 'usuario':
                $userId = $_POST['id'];
                $nome = $_POST['nome'];
                $email = $_POST['email'];
                $password = $_POST['senha'];
                $tel1 = $_POST['telefone1'];
                $tel2 = $_POST['telefone2'];
                $tipo = $_POST['tipo'];
                $salt = $_POST['salt'];

                if ($this->usuarioStorage->loginTaken($email, $tipo, (int) $userId)) {
                    $_SESSION['msg'] = 'E-mail já está em uso';
                    header('Location: /webschool/perfil');
                    exit;
                }
                
                $updateQuery = "
                    UPDATE usuario
                    SET nome=:nome, email=:email, telefone1=:tel1, telefone2=:tel2
                ";
                
                $fields = [
                    'nome' => $nome,
                    'email' => $email,
                    'tel1' => $tel1,
                    'tel2' => $tel2,
                ];
                
                if (strlen($password) > 0) {
                    $password = md5($password . $salt);

                    $updateQuery .= ' ,pass=:pass';
                    $fields['pass'] = $password;
                }
                
                $updateQuery .= " where id=:userId";
                $fields['userId'] = $userId;
                
                $user = $this->connection->prepare($updateQuery);

                $user->execute($fields);

                $currentUser = $_SESSION['user'];
                
                $endereco = $currentUser->endereco;
                $role_id = $currentUser->$tipo;
                
                unset($_POST['tipo_update']);
                unset($_POST['_method']);
                unset($_POST['tipo']);
                
                $user = (object) $_POST;
                $user->endereco = $endereco;
                $user->$tipo = $role_id;
                
                $_SESSION['user'] = $user;
                break;

            case 'endereco':
                $rua = $_POST['rua'];
                $numero = $_POST['numero'];
                $bairro = $_POST['bairro'];
                $complemento = $_POST['complemento'];
                $cidade = $_POST['cidade'];
                $cep = $_POST['cep'];
                $estado = $_POST['estado'];
                $endereco = $_POST['id'];

                $user = $this->connection->prepare("
                    UPDATE endereco
                    SET rua=:rua, numero=:numero, bairro=:bairro, complemento=:complemento, cidade=:cidade, cep=:cep, estado=:estado
                    where id=:endereco
                ");

                $user->execute([
                    'rua' => $rua,
                    'numero' => $numero,
                    'bairro' => $bairro,
                    'complemento' => $complemento,
                    'cidade' => $cidade,
                    'cep' => $cep,
                    'estado' => $estado,
                    'endereco' => $endereco,
                ]);

                unset($_POST['tipo_update']);
                unset($_POST['_method']);
                $endereco = (object) $_POST;

                $user = $_SESSION['user'];
                $user->endereco = $endereco;
                $_SESSION['user'] = $user;

                break;

            case 'avatar':
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

                $path='uploads/images/';
                $pathThumb='uploads/images/thumbs/';

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

                $urlFinal = 'uploads/images/'.$file_name;
                $urlThumbFinal = 'uploads/images/thumbs/'.$file_name_thumb;

                $avatarQuery = $this->connection->query("select * from fotos_de_avatar where usuario=$userId");
                $avatar = $avatarQuery->fetchObject();

                if ($avatar) {
                    unlink($avatar->endereco_thumb);
                    unlink($avatar->endereco);

                    $deleteAvatar = $this->connection->prepare("DELETE FROM fotos_de_avatar WHERE usuario=:idUsuario");

                    $deleteAvatar->execute([
                        'idUsuario' => $userId,
                    ]);
                }

                $user = $this->connection->prepare("
                    INSERT INTO fotos_de_avatar (endereco_thumb, endereco, usuario) VALUES (:imagemThumbUrl, :imagemUrl, :idUsuario)
                ");

                $user->execute([
                    'imagemThumbUrl' => $urlThumbFinal,
                    'imagemUrl' => $urlFinal,
                    'idUsuario' => $userId,
                ]);
                die();
                break;

            case false:
                break;
        }
        
        header('Location: perfil');
    }
}
