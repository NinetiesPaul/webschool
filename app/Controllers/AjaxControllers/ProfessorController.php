<?php

namespace App\Controllers\AjaxControllers;

use App\DB\Storage\MateriaStorage;
use App\DB\Storage\ProfessorStorage;
use App\DB\Storage\DiarioDeClasseStorage;
use App\DB\Storage\NotaStorage;
use App\DB\Storage\ArquivoStorage;
use App\Enum;
use App\Templates;
use App\Util;
use DateTime;

class ProfessorController extends AjaxController
{
    protected $template;
    protected $util;
    protected $materiaStorage;
    protected $professorStorage;
    protected $diarioDeClasseStorage;
    protected $arquivoStorage;
    protected $notaStorage;
    protected $links;

    public function __construct()
    {
        parent::__construct();
        $this->template = new Templates();
        $this->util = new Util();
        $this->util->userPermission(Enum::TIPO_PROFESSOR);
        $this->links = $this->util->generateLinks();

        $this->materiaStorage = new MateriaStorage();
        $this->professorStorage = new ProfessorStorage();
        $this->diarioDeClasseStorage = new DiarioDeClasseStorage();
        $this->arquivoStorage = new ArquivoStorage();
        $this->notaStorage = new NotaStorage();
    }

    public function inserirNota()
    {
        try {
            $this->notaStorage->adicionarNota(json_decode(json_encode($_POST), true));
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $this->response();
    }
    
    public function pesquisarFrequencia()
    {
        $data = json_decode(json_encode($_POST), true);
        
        $mes = $data["mes"];
        $ano = $data["ano"];
        $turma = $data["turma"];
        $disc = $data["disc"];

        $days = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

        $dates = [];
        for ($i = 1; $i < $days+1; $i++) {
            $date = $ano.'-'.$mes.'-'.$i;
            $dates[] = new DateTime($date);
        }

        try {
            $alunos = $this->diarioDeClasseStorage->verFaltasDoAlunoDaTurma($turma, $disc);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $output = "<table style='margin-left: auto; margin-right: auto; font-size: 13px;' class='table table-striped table-responsive'>";
        $output .= "<thead class='thead-dark'><tr><th scope='col'>  </th>";
        foreach ($dates as $date) {
            $output .= "<th scope='col'>".$date->format('d')."</th>";
        }
        $output .= "<p/></tr></thead><tbody>";
        foreach ($alunos as $aluno) {
            $output .= "<tr><td>$aluno->nome</td>";
            foreach ($dates as $date) {
                try {
                    $diario = $this->diarioDeClasseStorage->verFaltasDoAlunoDaturmaPorData($aluno->aluno, $turma, $disc, $date->format('Y-m-d'));
                } catch (\Exception $ex) {
                    $this->throwError($ex);
                }

                $date = explode('-', $date->format('Y-m-d'));

                $spanPresenca = "<span class='glyphicon glyphicon-trash'></span>";
                $linkPresenca = "$date[0]".'_'."$date[1]".'_'."$date[2]".'_'."$aluno->aluno".'_'."$aluno->disciplina".'_'."$aluno->turma";

                if ($diario && $diario->presenca == 1) {
                    $spanPresenca = "<span class='glyphicon glyphicon-ok'></span>";
                }

                $linkComentario = "$date[0]".'_'."$date[1]".'_'."$date[2]".'_'."$aluno->aluno".'_'."$aluno->disciplina".'_'."$aluno->turma";

                $output .= "
                    <td>
                        <a href='#' id='presenca-$linkPresenca' class='alterar-presenca'>$spanPresenca</a> <a href='#' id='comentario-$linkComentario' class='comentarios' data-toggle='modal' data-target='#modalExemplo' ><span class='glyphicon glyphicon-comment'></span></a>
                    </td>
                ";
            }
        }
        $output .= "</tr></tbody></table>";

        $this->response($output);
    }
    
    public function alterarFrequencia()
    {
        $data = json_decode(json_encode($_POST), true);
        $data = explode('-', $data['id']);
        $data = explode('_', $data[1]);
                
        $ano = $data[0];
        $mes = $data[1];
        $dia = $data[2];

        $aluno = $data[3];
        $disciplina = $data[4];
        $turma = $data[5];

        $mes = (strlen($mes) == 1) ? "0".$mes : $mes;
        $dia = (strlen($dia) == 1) ? "0".$dia : $dia;

        $data = $ano.'-'.$mes.'-'.$dia;

        $diario = false;
        try {
            $diario = $this->diarioDeClasseStorage->verFaltasDoAlunoDaturmaPorData($aluno, $turma, $disciplina, $data);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $span = '';

        if (!$diario) {
            try {
                $this->diarioDeClasseStorage->adicionarFalta($aluno, $turma, $disciplina, $data);
            } catch (\Exception $ex) {
                $this->throwError($ex);
            }

            $span = "<span class='glyphicon glyphicon-ok'></span>";
        } else {
            $presenca = $diario->presenca;

            $span = '';
            if ($presenca == false) {
                $presenca = 1;
                $span = "<span class='glyphicon glyphicon-ok'></span>";
            } else {
                $presenca = 0;
                $span = "<span class='glyphicon glyphicon-trash'></span>";
            }

            try {
                $this->diarioDeClasseStorage->alterarFalta($presenca, $diario->id);
            } catch (\Exception $ex) {
                $this->throwError($ex);
            }
        }

        $this->response($span);
    }

    public function adicionarComentario()
    {
        $user = $_SESSION['user'];

        $data = json_decode(json_encode($_POST), true);

        $mensagem = $data['comentario'];

        $dados = explode('-', $data['dado']);
        $dados = explode('_', $dados[1]);

        $ano = $dados[0];
        $mes = $dados[1];
        $dia = $dados[2];

        $aluno = $dados[3];
        $disciplina = $dados[4];
        $turma = $dados[5];

        $mes = (strlen($mes) == 1) ? "0".$mes : $mes;
        $dia = (strlen($dia) == 1) ? "0".$dia : $dia;

        $dataComentario = $ano.'-'.$mes.'-'.$dia;

        $arquivos = false;
        $file_name = '';

        if (empty($_FILES['file_btn']['error'])) {
            $arquivos = true;
            $file = $_FILES["file_btn"]["name"];
            $file = str_replace(" ", "_", $file);
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if (in_array($ext, ['pdf', 'txt', 'mp4', 'mp3', 'ogg'])) {
                $path="uploads/files/";

                if (!file_exists($path)) {
                    mkdir($path);
                }

                $data = getdate();
                $long = $data[0];
                $file_name=$long.'-'.$file;
                move_uploaded_file($_FILES['file_btn']['tmp_name'], $path . $file_name);
                $urlFinal = 'webschool/uploads/files/'.$file_name;
                $urlThumbFinal = '';
            }

            if (in_array($ext, ['jpg', 'jpeg'])) {
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

                $images = $_FILES["file_btn"]["tmp_name"];
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
                move_uploaded_file($_FILES['file_btn']['tmp_name'], $path . $file_name);
                move_uploaded_file($_FILES['file_btn']['tmp_name'], $path . $file_name_thumb);

                $urlFinal = 'webschool/uploads/images/'.$file_name;
                $urlThumbFinal = 'webschool/uploads/images/thumbs/'.$file_name_thumb;
            }
        }

        $prof = $user->professor;

        try {
            $id_comentario = $this->diarioDeClasseStorage->adicionarComentario($turma, $aluno, $disciplina, $mensagem, $dataComentario, $prof);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $id_arquivo = '';

        if ($arquivos) {
            try {
                $id_arquivo = $this->arquivoStorage->adicionarArquivo($file_name, $urlThumbFinal, $urlFinal, $dataComentario, $id_comentario);
            } catch (\Exception $ex) {
                $this->throwError($ex);
            }
        }

        $span = 'colspan=2';
        $line = 'Essa observação não contem arquivos anexados';
        $cellid = '';
        if (empty($_FILES['file_btn']['error'])) {
            $nome = (strlen($file_name) > 30) ? substr($file_name, 0, 25) . '...' : $file_name;

            $span = '';
            $line = '';
            $cellid = "id='cell-file-head-$id_arquivo'";
            $line .= "
                <a href='../../../../../$urlFinal'>$nome</a></td>
                <td id='cell-file-remove-$id_arquivo'><a href='#' class='deletar-arquivo' id='$id_arquivo'><span class='glyphicon glyphicon-trash'></span></a></td>
            ";
        }

        $line = "<tr id='row-comentario-$id_comentario'><td>$mensagem</td>
        <td $span $cellid>$line</td>
        <td><a href='#' id='$id_comentario' class='deletar-comentario'><span class='glyphicon glyphicon-trash'></span></a></td></tr>";

        $this->response($line);
    }

    public function verComentarios()
    {
        $user = $_SESSION['user'];
        
        $data = json_decode(json_encode($_POST), true);
        $data = explode('-', $data['dados']);
        $data = explode('_', $data[1]);
        
        $ano = $data[0];
        $mes = $data[1];
        $dia = $data[2];

        $aluno = $data[3];
        $disciplina = $data[4];
        $turma = $data[5];

        $mes = (strlen($mes) == 1) ? "0".$mes : $mes;
        $dia = (strlen($dia) == 1) ? "0".$dia : $dia;

        $data = $ano.'-'.$mes.'-'.$dia;

        try {
            $comentarios = $this->diarioDeClasseStorage->verComentariosDoAlunoDaTurma($aluno, $disciplina, $turma, $data, $user->professor);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $output = '';
        foreach ($comentarios as $comentario) {
            $output .= "<tr id='row-comentario-$comentario->id'><td>$comentario->observacao</td>";
            
            try {
                $arquivo = $this->arquivoStorage->verArquivosDoDiario($comentario->id);
            } catch (\Exception $ex) {
                $this->throwError($ex);
            }

            $span = 'colspan=2';
            $line = 'Essa observação não contem arquivos anexados';
            $cellid = '';
            if ($arquivo) {
                $span = '';
                $line = '';
                $nome = (strlen($arquivo->nome) > 30) ? substr($arquivo->nome, 0, 25) . '...' : $arquivo->nome;

                $cellid = "id='cell-file-head-$arquivo->id'";
                $line .= "
                    <a href='../../../../../$arquivo->endereco'>$nome</a></td>
                    <td id='cell-file-remove-$arquivo->id'><a href='#' class='deletar-arquivo' id='$arquivo->id'><span class='glyphicon glyphicon-trash'></span></a></td>
                ";
            }
            $output .= "<td $span $cellid>$line</td>";
            $output .= "<td><a href='#' id='$comentario->id' class='deletar-comentario'><span class='glyphicon glyphicon-trash'></span></a></td></tr>";
        }

        $this->response($output);
    }
    
    public function deletarComentario($idComentario)
    {
        $arquivo = null;

        try {
            $arquivo = $this->arquivoStorage->verArquivoDoDiario($idComentario);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }
        
        if ($arquivo) {
            if ($arquivo->endereco_thumb) {
                unlink('../' . $arquivo->endereco_thumb);
            }
            unlink('../' . $arquivo->endereco);

            try {
                $this->arquivoStorage->removerArquivoDoComentario($arquivo->id);
            } catch (\Exception $ex) {
                $this->throwError($ex);
            }
        }

        try {
            $this->diarioDeClasseStorage->removerComentario($idComentario);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $this->response();
    }
    
    public function deletarArquivoDeComentario($idArquivo)
    {
        try {
            $arquivo = $this->arquivoStorage->verArquivoPorId($idArquivo);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        if ($arquivo->endereco_thumb) {
            unlink('../' . $arquivo->endereco_thumb);
        }
        unlink('../' . $arquivo->endereco);

        try {
            $this->arquivoStorage->removerArquivoDoComentario($idArquivo);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $this->response();
    }
}
