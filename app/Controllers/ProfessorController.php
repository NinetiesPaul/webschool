<?php

namespace App\Controllers;

use App\DB\Storage\MateriaStorage;
use App\DB\Storage\ProfessorStorage;
use App\DB\Storage\DiarioDeClasseStorage;
use App\DB\Storage\NotaStorage;
use App\DB\Storage\ArquivoStorage;
use App\Enum;
use App\Templates;
use App\Util;
use DateTime;

class ProfessorController
{
    protected $template;
        
    protected $util;

    protected $materiaStorage;

    protected $professorStorage;
    
    protected $diarioDeClasseStorage;
    
    protected $arquivoStorage;
    
    protected $notaStorage;

    public function __construct()
    {
        $this->template = new Templates();
        $this->util = new Util();
        $this->materiaStorage = new MateriaStorage();
        $this->professorStorage = new ProfessorStorage();
        $this->diarioDeClasseStorage = new DiarioDeClasseStorage();
        $this->arquivoStorage = new ArquivoStorage();
        $this->notaStorage = new NotaStorage();
        $this->util->userPermission(Enum::TIPO_PROFESSOR);
    }
    
    public function index()
    {
        $user = $_SESSION['user'];
        
        $args = [
            'LOGADO' => $user->nome
        ];

        $this->util->loadTemplate('professor/index.html', $args);
    }
    
    public function verTurmas()
    {
        $user = $_SESSION['user'];

        $disciplinas = $this->materiaStorage->verMateriaPorProfessorDoProfessor($user->professor);

        $turmas = '';
        
        foreach ($disciplinas as $disciplina) {
            $turmas .= $disciplina->nomeDisciplina.', '.$disciplina->serie.'º Série '.$disciplina->nome;
            $turmas .= "<p/><a href='turma/$disciplina->id' class='btn btn-sm btn-primary' id='btn_disciplina' '><span class='glyphicon glyphicon-eye-open'></span> Visualizar</a><br/>";

            $alunosQuery = $this->professorStorage->verMeusAlunos($disciplina->turma, $disciplina->disciplina, $user->professor);

            $text = '<br/>Sem alunos cadastrados nessa turma no momento';
            if (!empty($alunosQuery)) {
                $text = '';
                foreach ($alunosQuery as $aluno) {
                    $text .= $aluno->nome . '<br/>';
                }
            }
            
            $turmas .= $text;
        }
                    
        $args = [
            'LOGADO' => $user->nome,
            'TURMAS' => $turmas
        ];

        $this->util->loadTemplate('professor/turmas.html', $args);
    }
    
    public function verTurma(int $id)
    {
        $user = $_SESSION['user'];
        $result = $this->materiaStorage->verMateriaPorProfessorSingle($id);

        $disciplina = $result->disciplina;
        $turma = $result->turma;

        $detalhes = '';
        
        $detalhes .= $result->nomeDisciplina.', '.$result->serie.'º Série '.$result->nome;
        $detalhes .= "<a href='../diariodeclasse/$turma"."_"."$disciplina' class='btn btn-sm btn-primary' id='btn_diario'><span class='glyphicon glyphicon-pencil'></span> Diário de classe</a><p/>";

        $alunosQuery = $this->notaStorage->verNotasPorAlunosDaDisciplinaETurma($disciplina, $turma);

        $detalhes .= "<table style='margin-left: auto; margin-right: auto; font-size: 13;' class='table table-sm table-hover table-striped'>
        <thead><tr><th></th><th>Nota 1</th><th>Rec. 1</th><th>Nota 2:</th><th>Rec. 2</th><th>Nota 3</th><th>Rec. 3</th><th>Nota 4</th><th>Rec. 4</th></tr></thead><tbody>";
        foreach ($alunosQuery as $aluno) {
            $detalhes .= "<tr><td>$aluno->nome</td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."nota1'>$aluno->nota1</a> </td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."rec1'>$aluno->rec1</a> </td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."nota2'>$aluno->nota2</a> </td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."rec2'>$aluno->rec2</a> </td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."nota3'>$aluno->nota3</a> </td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."rec3'>$aluno->rec3</a> </td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."nota4'>$aluno->nota4</a> </td>
            <td> <a href='#' class='nota' data-toggle='modal' data-target='#modalExemplo' id='id-$aluno->aluno".'_'."$disciplina".'_'."$turma".'_'."rec4'>$aluno->rec4</a> </td>
            </tr>";
        }
        
        $detalhes .= '</tbody></table>';
        
        $args = [
            'LOGADO' => $user->nome,
            'DETALHES' => $detalhes
        ];

        $this->util->loadTemplate('professor/turma.html', $args);
    }
    
    public function verDiarioDeClasse(string $id)
    {
        $user = $_SESSION['user'];
        
        $id = explode('_', $id);
        
        $args = [
            'LOGADO' => $user->nome,
            'DISCIPLINA' => $id[1],
            'TURMA' => $id[0],
        ];

        $this->util->loadTemplate('professor/diariodeclasse.html', $args);
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

        echo '<p/>';

        $alunos = $this->diarioDeClasseStorage->verFaltasDoAlunoDaTurma($turma, $disc);

        echo "<table style='margin-left: auto; margin-right: auto; font-size: 13;' class='table table-striped table-responsive'>";
        echo "<thead class='thead-dark'>";
        echo "<tr>";
        echo "<th scope='col'>  </th>";
        foreach ($dates as $date) {
            echo "<th scope='col'>".$date->format('d')."</th>";
        }
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        foreach ($alunos as $aluno) {
            echo "<tr>";
            echo "<td>$aluno->nome</td>";
            foreach ($dates as $date) {
                $diario = $this->diarioDeClasseStorage->verFaltasDoAlunoDaturmaPorData($aluno->aluno, $turma, $disc, $date->format('Y-m-d'));

                $date = explode('-', $date->format('Y-m-d'));

                $spanPresenca = "<span class='glyphicon glyphicon-remove'></span>";
                $linkPresenca = "$date[0]".'_'."$date[1]".'_'."$date[2]".'_'."$aluno->aluno".'_'."$aluno->disciplina".'_'."$aluno->turma";

                if ($diario && $diario->presenca == 1) {
                    $spanPresenca = "<span class='glyphicon glyphicon-ok'></span>";
                }

                $linkComentario = "$date[0]".'_'."$date[1]".'_'."$date[2]".'_'."$aluno->aluno".'_'."$aluno->disciplina".'_'."$aluno->turma";

                echo "<td>";
                echo "<a href='#' id='presenca-$linkPresenca' class='alterar-presenca'>$spanPresenca</a> <a href='#' id='comentario-$linkComentario' class='comentarios' data-toggle='modal' data-target='#modalExemplo' ><span class='glyphicon glyphicon-comment'></span></a>";
                echo "</td>";
            }
        }
        echo "</tr>";
        echo "</tbody>";
        echo "</table>";
    }
    
    public function inserirNota()
    {
        $this->notaStorage->adicionarNota(json_decode(json_encode($_POST), true));
    }
    
    public function alterarFrequencia()
    {
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

        $diario = $this->diarioDeClasseStorage->verFaltasDoAlunoDaturmaPorData($aluno, $turma, $disciplina, $data);

        if (!$diario) {
            $this->diarioDeClasseStorage->adicionarFalta($aluno, $turma, $disciplina, $data);
            
            echo "<span class='glyphicon glyphicon-ok'></span>";
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
            
            $this->diarioDeClasseStorage->alterarFalta($presenca, $diario->id);
            
            echo $span;
        }
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
        
        $comentarios = $this->diarioDeClasseStorage->verComentariosDoAlunoDaTurma($aluno, $disciplina, $turma, $data, $user->professor);

        foreach ($comentarios as $comentario) {
            echo "<tr id='row-comentario-$comentario->id'><td>$comentario->observacao</td>";
            
            $arquivo = $this->arquivoStorage->verArquivosDoDiario($comentario->id);

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
            echo "<td $span $cellid>$line</td>";
            echo "<td><a href='#' id='$comentario->id' class='deletar-comentario'><span class='glyphicon glyphicon-trash'></span></a></td></tr>";
        }
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
        
        $id_comentario = $this->diarioDeClasseStorage->adicionarComentario($turma, $aluno, $disciplina, $mensagem, $dataComentario, $prof);

        $id_arquivo = '';
        
        if ($arquivos) {
            $id_arquivo = $this->arquivoStorage->adicionarArquivo($file_name, $urlThumbFinal, $urlFinal, $dataComentario, $id_comentario);
        }
        
        echo "<tr id='row-comentario-$id_comentario'><td>$mensagem</td>";

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
        
        echo "<td $span $cellid>$line</td>";
        echo "<td><a href='#' id='$id_comentario' class='deletar-comentario'><span class='glyphicon glyphicon-trash'></span></a></td></tr>";
    }
    
    public function deletarComentario(int $idComentario)
    {
        $arquivo = $this->arquivoStorage->verArquivoDoDiario($idComentario);
        
        if ($arquivo) {
            try {
                if ($arquivo->endereco_thumb) {
                    unlink('../' . $arquivo->endereco_thumb);
                }
                unlink('../' . $arquivo->endereco);

                $this->arquivoStorage->removerArquivoDoComentario($arquivo->id);
            } catch (\Exception $ex) {
                echo $ex->getMessage();
            }
        }
        
        $this->diarioDeClasseStorage->removerComentario($idComentario);
    }
    
    public function deletarArquivoDeComentario($idArquivo)
    {
        $arquivo = $this->arquivoStorage->verArquivoPorId($idArquivo);

        if ($arquivo->endereco_thumb) {
            unlink('../' . $arquivo->endereco_thumb);
        }
        unlink('../' . $arquivo->endereco);

        $this->arquivoStorage->removerArquivoDoComentario($idArquivo);
    }
}
