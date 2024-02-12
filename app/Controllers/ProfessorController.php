<?php

namespace App\Controllers;

use App\DB\Storage\LogStorage;
use App\DB\Storage\MateriaStorage;
use App\DB\Storage\ProfessorStorage;
use App\DB\Storage\DiarioDeClasseStorage;
use App\DB\Storage\NotaStorage;
use App\DB\Storage\ArquivoStorage;
use App\Enum;
use App\ResponseHandler;
use App\Templates;
use App\Util;
use DateTime;

class ProfessorController
{
    protected $materiaStorage;
    protected $professorStorage;
    protected $diarioDeClasseStorage;
    protected $arquivoStorage;
    protected $notaStorage;

    public function __construct()
    {
        Util::userPermission(Enum::TIPO_PROFESSOR);
        new LogStorage();

        $this->materiaStorage = new MateriaStorage();
        $this->professorStorage = new ProfessorStorage();
        $this->diarioDeClasseStorage = new DiarioDeClasseStorage();
        $this->arquivoStorage = new ArquivoStorage();
        $this->notaStorage = new NotaStorage();
    }
    
    public function index()
    {
        $user = $_SESSION['user'];
        
        $args = [
            'LOGADO' => $user->nome
        ];

        new Templates('professor/index.html', $args);
    }
    
    public function verTurmas()
    {
        $user = $_SESSION['user'];

        $disciplinas = $this->materiaStorage->verMateriasDoProfessor($user->professor);

        $turmas = '';
        
        foreach ($disciplinas as $disciplina) {
            $turmas .= "<p><a href='turma/$disciplina->id' class='btn btn-sm btn-primary' id='btn_disciplina' '>" . $disciplina->nomeDisciplina . ", $disciplina->nome ($disciplina->ano)</a><p/>";
        }
                    
        $args = [
            'LOGADO' => $user->nome,
            'TURMAS' => $turmas
        ];

        new Templates('professor/turmas.html', $args);
    }
    
    public function verTurma($id)
    {
        $user = $_SESSION['user'];
        $result = $this->materiaStorage->verMateriaDoProfessor($id);

        $disciplina = $result->disciplina;
        $turma = $result->turma;
        $diario = "
            <p>
                $result->nomeDisciplina<br/>
                $result->nome ($result->ano)<br/>
                <a href='../diariodeclasse/$turma"."_"."$disciplina' class='btn btn-sm btn-primary' id='btn_diario'><span class='glyphicon glyphicon-pencil'></span> Diário de classe</a>
            </p>
        ";

        $alunosQuery = $this->notaStorage->verNotasPorAlunosDaDisciplinaETurma($disciplina, $result->turma);

        $detalhes = "";
        foreach ($alunosQuery as $aluno) {
            $detalhes .= "
                <tr data-aluno='$aluno->aluno'>
                    <td>$aluno->nome</td>
                    <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' data-tipo='nota1'>$aluno->nota1</a> </td>
                    <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' data-tipo='rec1'>$aluno->rec1</a> </td>
                    <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' data-tipo='nota2'>$aluno->nota2</a> </td>
                    <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' data-tipo='rec2'>$aluno->rec2</a> </td>
                    <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' data-tipo='nota3'>$aluno->nota3</a> </td>
                    <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' data-tipo='rec3'>$aluno->rec3</a> </td>
                    <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' data-tipo='nota4'>$aluno->nota4</a> </td>
                    <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' data-tipo='rec4'>$aluno->rec4</a> </td>
                </tr>
            ";
        }
        
        $detalhes .= '</tbody></table>';
        
        $args = [
            'DIARIO_BUTTON' => $diario,
            'LOGADO' => $user->nome,
            'DETALHES' => $detalhes,
            'ALUNO' => $aluno->aluno,
            'DISCIPLINA' => $disciplina,
            'TURMA' => $turma,
        ];

        new Templates('professor/turma.html', $args, '../');
    }
    
    public function verDiarioDeClasse($id)
    {
        $user = $_SESSION['user'];
        
        $id = explode('_', $id);
        
        $args = [
            'LOGADO' => $user->nome,
            'DISCIPLINA' => $id[1],
            'TURMA' => $id[0]
        ];

        new Templates('professor/diariodeclasse.html', $args, '../');
    }

    public function inserirNota()
    {
        try {
            $this->notaStorage->adicionarNota(json_decode(json_encode($_POST), true));
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        ResponseHandler::response();
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
            ResponseHandler::throwError($ex);
        }

        $output = "<table style='margin-left: auto; margin-right: auto; font-size: 13px;' class='table table-striped table-responsive'>";
        $output .= "<thead class='thead-dark'><tr><th scope='col'>  </th>";
        foreach ($dates as $date) {
            $output .= "<th scope='col'>".$date->format('d')."</th>";
        }
        $output .= "</tr></thead><tbody>";
        foreach ($alunos as $aluno) {
            $output .= "<tr><td>$aluno->nome</td>";
            foreach ($dates as $date) {
                try {
                    $diario = $this->diarioDeClasseStorage->verFaltasDoAlunoDaturmaPorData($aluno->aluno, $turma, $disc, $date->format('Y-m-d'));
                } catch (\Exception $ex) {
                    ResponseHandler::throwError($ex);
                }

                $spanPresenca = ($diario && $diario->presenca == 1) ? "<span class='glyphicon glyphicon-ok'></span>" : "<span class='glyphicon glyphicon-remove'></span>";

                $output .= "
                    <td>
                        <a href='#' data-date='" . $date->format('Y-m-d') . "' data-aluno='$aluno->aluno' class='presenca'>$spanPresenca</a>
                        <a href='#' data-date='" . $date->format('Y-m-d') . "' data-aluno='$aluno->aluno' class='comentarios' data-toggle='modal' data-target='#modalExemplo' ><span class='glyphicon glyphicon-comment'></span></a>
                    </td>
                ";
            }
        }
        $output .= "</tr></tbody></table>";

        ResponseHandler::response($output);
    }

    public function alterarFrequencia()
    {
        $data = json_decode(json_encode($_POST), true);

        $aluno = $data['aluno'];
        $disciplina = $data['disciplina'];
        $turma = $data['turma'];
        $data = $data['data'];

        $diario = false;
        try {
            $diario = $this->diarioDeClasseStorage->verFaltasDoAlunoDaturmaPorData($aluno, $turma, $disciplina, $data);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        $span = '';

        if (!$diario) {
            try {
                $this->diarioDeClasseStorage->adicionarFalta($aluno, $turma, $disciplina, $data);
            } catch (\Exception $ex) {
                ResponseHandler::throwError($ex);
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
                $span = "<span class='glyphicon glyphicon-remove'></span>";
            }

            try {
                $this->diarioDeClasseStorage->alterarFalta($presenca, $diario->id);
            } catch (\Exception $ex) {
                ResponseHandler::throwError($ex);
            }
        }

        ResponseHandler::response($span);
    }

    public function adicionarComentario()
    {
        $user = $_SESSION['user'];

        $data = json_decode(json_encode($_POST), true);

        $mensagem = $data['comentario'];
        $aluno = $data['aluno'];
        $disciplina = $data['disciplina'];
        $turma = $data['turma'];
        $dataComentario = $data['data'];

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
                $urlFinal = 'uploads/files/'.$file_name;
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

                $urlFinal = 'uploads/images/'.$file_name;
                $urlThumbFinal = 'uploads/images/thumbs/'.$file_name_thumb;
            }
        }

        $prof = $user->professor;

        try {
            $id_comentario = $this->diarioDeClasseStorage->adicionarComentario($turma, $aluno, $disciplina, $mensagem, $dataComentario, $prof);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        $id_arquivo = '';
        if ($arquivos) {
            try {
                $id_arquivo = $this->arquivoStorage->adicionarArquivo($file_name, $urlThumbFinal, $urlFinal, $dataComentario, $id_comentario);
            } catch (\Exception $ex) {
                ResponseHandler::throwError($ex);
            }
        }

        $line = '';
        if (empty($_FILES['file_btn']['error'])) {
            $nome = (strlen($file_name) > 15) ? substr($file_name, 0, 15) . '...' : $file_name;

            $line = '';
            $line .= "
                <a href='../../../../../$urlFinal'>$nome</a></td>
                <a href='#' class='deletar-arquivo' id='$id_arquivo'><span class='glyphicon glyphicon-trash'></span></a></td>
            ";
        }

        $output = "
            <tr data-comentario='$id_comentario'>
                <td>$mensagem<br/>$line</td>
                <td><a href='#' data-id='$id_comentario' class='deletar-comentario'><span class='glyphicon glyphicon-trash'></span></a></td>
            </tr>
        ";

        ResponseHandler::response($output);
    }

    public function verComentarios()
    {
        $user = $_SESSION['user'];

        $data = json_decode(json_encode($_POST), true);

        $aluno = $data['aluno'];
        $disciplina = $data['disciplina'];
        $turma = $data['turma'];
        $data = $data['data'];

        try {
            $comentarios = $this->diarioDeClasseStorage->verComentariosDoAlunoDaTurma($aluno, $disciplina, $turma, $data, $user->professor);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        $output = '';
        foreach ($comentarios as $comentario) {
            $arquivo = $this->arquivoStorage->verArquivosDoDiario($comentario->id);

            $line = '';
            if ($arquivo) {

                $nome = (strlen($arquivo->nome) > 15) ? substr($arquivo->nome, 0, 15) . '...' : $arquivo->nome;
                $line = "
                    <span class='comentario-arquivo' data-arquivo='$arquivo->id'>
                        <a href='../../../../../$arquivo->endereco'>$nome</a>
                        <a href='#' class='deletar-arquivo' data-id='$arquivo->id'><span class='glyphicon glyphicon-trash'></span></a>
                    </span>
                ";
            }

            $output .= "
                <tr data-comentario='$comentario->id'>
                    <td>$comentario->observacao<br/>$line</td>
                    <td><a href='#' data-id='$comentario->id' class='deletar-comentario'><span class='glyphicon glyphicon-trash'></span></a></td>
                </tr>
            ";
        }

        ResponseHandler::response($output);
    }

    public function deletarComentario($idComentario)
    {
        $arquivo = null;

        try {
            $arquivo = $this->arquivoStorage->verArquivoDoDiario($idComentario);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        if ($arquivo) {
            if ($arquivo->endereco_thumb) {
                unlink($arquivo->endereco_thumb);
            }
            unlink($arquivo->endereco);

            try {
                $this->arquivoStorage->removerArquivoDoComentario($arquivo->id);
            } catch (\Exception $ex) {
                ResponseHandler::throwError($ex);
            }
        }

        try {
            $this->diarioDeClasseStorage->removerComentario($idComentario);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        ResponseHandler::response();
    }

    public function deletarArquivoDeComentario($idArquivo)
    {
        try {
            $arquivo = $this->arquivoStorage->verArquivoPorId($idArquivo);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        if ($arquivo->endereco_thumb) {
            unlink($arquivo->endereco_thumb);
        }
        unlink($arquivo->endereco);

        try {
            $this->arquivoStorage->removerArquivoDoComentario($idArquivo);
        } catch (\Exception $ex) {
            ResponseHandler::throwError($ex);
        }

        ResponseHandler::response();
    }
}
