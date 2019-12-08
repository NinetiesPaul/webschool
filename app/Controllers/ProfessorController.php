<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers;

use App\Templates;
use App\DB\DB;
use PDO;
use App\Util;
use DateTime;

class ProfessorController
{
    protected $template;
    
    protected $connection;
    
    protected $util;

    public function __construct()
    {
        $this->template = new Templates();
        $this->connection = new DB;
        $this->util = new Util();
        $this->util->userPermission('professor');
    }
    
    public function index()
    {
        $user = $_SESSION['user'];
        
        $args = [
            'LOGADO' => $user->nome
        ];
        
        $template 	= $this->template->getTemplate('professor/index.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
    }
    
    public function verTurmas()
    {
        $user = $_SESSION['user'];
        
        $disciplinaQuery = $this->connection->query("select * from disciplina_por_professor where professor=$user->professor");
        $disciplinas = $disciplinaQuery->fetchAll(PDO::FETCH_OBJ);

        
        $turmas = '';
        
        foreach ($disciplinas as $disciplina){
            $turmas .= $this->util->pegarNomeDaDisciplina($disciplina->disciplina).', '.$this->util->pegarNomeDaTurmaPorIdTurma($disciplina->turma);
            $turmas .= "<a href='turma/$disciplina->id' class='btn-sm btn-info' id='btn_disciplina' '><span class='glyphicon glyphicon-eye-open'></span> Visualizar</a>";

            $alunosQuery = $this->connection->query("
                select distinct usuario.* from usuario, aluno, turma, disciplina_por_professor
                where usuario.id=aluno.usuario
                and aluno.turma=disciplina_por_professor.turma
                and disciplina_por_professor.turma=$disciplina->turma
                and disciplina_por_professor.disciplina=$disciplina->disciplina
                and disciplina_por_professor.professor=$user->professor
                order by usuario.nome
            ");
            $alunosQuery = $alunosQuery->fetchAll(PDO::FETCH_OBJ);

            $text = '<br/>Sem alunos cadastrados nessa turma no momento';
            if (!empty($alunosQuery)) {
                $text = '';
                foreach ($alunosQuery as $aluno) {
                    $text .= '<br/>'.$aluno->nome;
                }
            }
            
            $turmas .= $text;
                    
            $args = [
                'LOGADO' => $user->nome,
                'TURMAS' => $turmas
            ];

            $template 	= $this->template->getTemplate('professor/turmas.html');
            $templateFinal = $this->template->parseTemplate($template, $args);
            echo $templateFinal;
        }
    }
    
    public function verTurma(int $id)
    {
        $user = $_SESSION['user'];
        
        $disciplinaQuery = $this->connection->query("select * from disciplina_por_professor where id=$id");
        $result = $disciplinaQuery->fetchObject();

        $disciplina = $result->disciplina;
        $turma = $result->turma;

        $detalhes = '';
        
        $detalhes .= $this->util->pegarNomeDaDisciplina($disciplina).', '.$this->util->pegarNomeDaTurmaPorIdTurma($turma).'<br/>';
        $detalhes .= "<a href='../diariodeclasse/$turma"."_"."$disciplina' class='btn-sm btn-info' id='btn_diario'><span class='glyphicon glyphicon-pencil'></span> Diário de classe</a><p/>";

        $alunosQuery = $this->connection->query("
            select usuario.id, usuario.nome, aluno.id as aluno, nota_por_aluno.nota1, nota_por_aluno.nota2, nota_por_aluno.nota3, nota_por_aluno.nota4, nota_por_aluno.rec1, nota_por_aluno.rec2, nota_por_aluno.rec3, nota_por_aluno.rec4
            from usuario
            inner join aluno on aluno.usuario = usuario.id
            inner join nota_por_aluno on nota_por_aluno.aluno = aluno.id and nota_por_aluno.disciplina=$disciplina and nota_por_aluno.turma=$turma
            group by usuario.nome
            order by usuario.nome
        ");
        $alunosQuery = $alunosQuery->fetchAll(PDO::FETCH_OBJ);

        $detalhes .= "<table style='margin-left: auto; margin-right: auto; font-size: 13;' class='table table-sm table-hover table-striped'>
        <thead><tr><th></th><th>Nota 1</th><th>Rec. 1</th><th>Nota 2:</th><th>Rec. 2</th><th>Nota 3</th><th>Rec. 3</th><th>Nota 4</th><th>Rec. 4</th></tr></thead><tbody>";
        foreach ($alunosQuery as $aluno){
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
        
        $template 	= $this->template->getTemplate('professor/turma.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
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
        
        $template 	= $this->template->getTemplate('professor/diariodeclasse.html');
        $templateFinal = $this->template->parseTemplate($template, $args);
        echo $templateFinal;
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

        $alunosQuery = $this->connection->query("
            select diario_de_classe.*, usuario.nome
            from usuario
            inner join aluno on aluno.usuario = usuario.id
            inner join diario_de_classe on diario_de_classe.aluno = aluno.id
            and diario_de_classe.disciplina = $disc and diario_de_classe.turma = $turma
            group by usuario.nome
            order by usuario.nome 
        ");
        $alunos = $alunosQuery->fetchAll(PDO::FETCH_OBJ);

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
                $diarioQuery = $this->connection->query("
                    select *
                    from diario_de_classe
                    where aluno = $aluno->aluno
                    and diario_de_classe.disciplina = $disc and diario_de_classe.turma = $turma
                    and data = '".$date->format('Y-m-d')."'
                ");
                $diario = $diarioQuery->fetch(PDO::FETCH_OBJ);

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
        $data = json_decode(json_encode($_POST), true);
        
        $aluno = $data['aluno'];
        $turma = $data['turma'];
        $disciplina = $data['disciplina'];
        $notaNum = $data['tipo'];
        $nota = $data['nota'];
                
        $user = $this->connection->prepare("
            UPDATE nota_por_aluno
            SET ".$notaNum."=:nota
            where aluno=:idAluno and disciplina=:idDisciplina and turma=:idTurma
        ");

        $user->execute([
            'nota' => $nota,
            'idAluno' => $aluno,
            'idDisciplina' => $disciplina,
            'idTurma' => $turma,
        ]);
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

        $diarioQuery = $this->connection->query("
        select id, presenca
        from diario_de_classe
        where aluno = $aluno
        and disciplina = $disciplina
        and turma = $turma
        and data = '$data'
        ");
        $diario = $diarioQuery->fetch(PDO::FETCH_OBJ);

        if (!$diario) {
            $user = $this->connection->prepare("
                INSERT INTO diario_de_classe (aluno, disciplina, turma, data, presenca, contexto) 
                values (:aluno, :disciplina, :turma, :data, 1, 'presenca')
            ");

            $user->execute([
                'aluno' => $aluno,
                'disciplina' => $disciplina,
                'turma' => $turma,
                'data' => $data,
            ]);
            
            echo "<span class='glyphicon glyphicon-ok'></span>";
            
            
        } else {
            $presenca = $diario->presenca;

            $span = '';
            if ($presenca == false) {
                $presenca = 1;
                $span = "<span class='glyphicon glyphicon-ok'></span>";;
            } else {
                $presenca = 0;
                $span = "<span class='glyphicon glyphicon-remove'></span>";;
            }

            $user = $this->connection->prepare("UPDATE diario_de_classe SET presenca = :presenca WHERE id=:id");

            $user->execute([
                'presenca' => $presenca,
                'id' => $diario->id,
            ]);
            
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
        
         $comentarioQuery = $this->connection->query("
            select *
            from diario_de_classe
            where aluno = $aluno
            and disciplina = $disciplina
            and turma = $turma
            and data = '$data'
            and professor = $user->professor
            and contexto = 'observacao'
        ");
        $comentarios = $comentarioQuery->fetchAll(PDO::FETCH_OBJ);

        foreach ($comentarios as $comentario) {
            echo "<tr id='row-comentario-$comentario->id'><td>$comentario->observacao</td>";

            $arquivoQuery = $this->connection->query("
                select *
                from arquivos
                where contexto = 'ddc'
                and diario = $comentario->id
            ");
            $arquivos = $arquivoQuery->fetchAll(PDO::FETCH_OBJ);

            $span = 'colspan=2';
            $line = 'Essa observação não contem arquivos anexados';
            $cellid = '';
            if ($arquivos) {
                $span = '';
                $line = '';
                foreach ($arquivos as $arquivo) {
                    $cellid = "id='cell-file-head-$arquivo->id'";
                    $line .= "
                        <a href='../../../../../$arquivo->endereco'>$arquivo->nome</a></td>
                        <td id='cell-file-remove-$arquivo->id'><a href='#' class='deletar-arquivo' id='$arquivo->id'><span class='glyphicon glyphicon-trash'></span></a></td>
                    ";
                }
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

        $user = $this->connection->prepare("
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
        
        $id_comentario = $this->connection->lastInsertId();

        $id_arquivo = '';
        $endereco = '1';
        
        if ($arquivos) {
            $id = (int) $this->connection->lastInsertId();

            $fileQuery = $this->connection->prepare("
                INSERT INTO arquivos (nome, endereco_thumb, endereco, contexto, diario, descricao, data) VALUES (:nome, :endereco_thumb, :endereco, 'ddc', $id, '', :data)
            ");

            $fileQuery->execute([
                'nome' => $file_name,
                'endereco_thumb' => $urlThumbFinal,
                'endereco' => $urlFinal,
                'data' => $dataComentario,
            ]);
            
            $id_arquivo = $this->connection->lastInsertId();
            
            $arquivoQuery = $this->connection->query("
                select endereco, nome
                from arquivos
                where id = $id_arquivo
            ");
            $arquivo = $arquivoQuery->fetch(PDO::FETCH_OBJ);
        }
        
        echo "<tr id='row-comentario-$id_comentario'><td>$mensagem</td>";

        $span = 'colspan=2';
        $line = 'Essa observação não contem arquivos anexados';
        $cellid = '';
        if (empty($_FILES['file_btn']['error'])) {
            $span = '';
            $line = '';
            $cellid = "id='cell-file-head-$id_arquivo'";
            $line .= "
                <a href='../../../../../$arquivo->endereco'>$arquivo->nome</a></td>
                <td id='cell-file-remove-$id_arquivo'><a href='#' class='deletar-arquivo' id='$id_arquivo'><span class='glyphicon glyphicon-trash'></span></a></td>
            ";
        }
        echo "<td $span $cellid>$line</td>";
        echo "<td><a href='#' id='$id_comentario' class='deletar-comentario'><span class='glyphicon glyphicon-trash'></span></a></td></tr>";
    }
    
    public function deletarComentario(int $idComentario)
    {
        $arquivoQuery = $this->connection->query("
        select *
        from arquivos
        where contexto = 'ddc'
        and diario = $idComentario
        ");
        $arquivo = $arquivoQuery->fetch(PDO::FETCH_OBJ);

        if ($arquivo) {
            if ($arquivo->endereco_thumb) {
                unlink('../' . $arquivo->endereco_thumb);
            }
            unlink('../' . $arquivo->endereco);

            $statement = $this->connection->prepare("DELETE FROM arquivos where id=:id");
            $statement->execute([
                'id' => $arquivo->id,
            ]);
        }
        
        $comentario = $this->connection->prepare("DELETE FROM diario_de_classe where contexto = 'observacao' and id=:id");
        $comentario->execute([
            'id' => $idComentario,
        ]);
    }
    
    public function deletarArquivoDeComentario($idArquivo)
    {
        $arquivoQuery = $this->connection->query("
        select *
        from arquivos
        where id = $idArquivo
        ");
        $arquivo = $arquivoQuery->fetch(PDO::FETCH_OBJ);

        if ($arquivo->endereco_thumb) {
            unlink('../' . $arquivo->endereco_thumb);
        }
        unlink('../' . $arquivo->endereco);

        $statement = $this->connection->prepare("DELETE FROM arquivos where id=:id");
        $statement->execute([
            'id' => $idArquivo,
        ]);
    }
}
