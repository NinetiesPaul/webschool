<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers;

use App\DB\DB;
use PDO;
use App\Util;
use tFPDF;
use DateTime;

class GeneralController
{
    protected $connection;
    
    protected $util;

    public function __construct()
    {
        $this->connection = new DB;
        $this->util = new Util();
    }
    
    public function gerarBoletim()
    {
        $aluno = $_POST["aluno-pdf"];
        $turma = $_POST["turma-pdf"];

        $notasQuery = $this->connection->query("select * from nota_por_aluno where aluno=$aluno and turma=$turma order by disciplina");
        $notas = $notasQuery->fetchAll(PDO::FETCH_OBJ);

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
            $pdf->Cell(40, 6, $this->util->pegarNomeDaDisciplina($row->disciplina), 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->nota1, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->rec1, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->nota2, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->rec2, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->nota3, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->rec3, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->nota4, 'LR', 0, 'L', $fill);
            $pdf->Cell(10, 6, $row->rec4, 'LR', 0, 'L', $fill);
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
   
        $usuarioQuery = $this->connection->query("select * from aluno where usuario=$aluno[1]");
        $usuario = $usuarioQuery->fetch(PDO::FETCH_OBJ);

        $turmasQuery = $this->connection->query("select turma from nota_por_aluno where aluno=$aluno[0] group by turma");
        $turmas = $turmasQuery->fetchAll(PDO::FETCH_OBJ);

        $pdf = new tFPDF();

        foreach ($turmas as $turma) {
            $notasQuery = $this->connection->query("select * from nota_por_aluno where aluno=$aluno[0] and turma=$turma->turma order by disciplina");
            $notas = $notasQuery->fetchAll(PDO::FETCH_OBJ);

            $pdf->AddPage();

            // Colors, line width and bold font
            $pdf->SetFillColor(43, 74, 92);
            $pdf->SetTextColor(255);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineWidth(.3);
            $pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
            $pdf->SetFont('DejaVu', '', 8);
            // Header

            $pdf->Cell(120, 7, $this->util->pegarTurmaDoAlunoPorTurma($turma->turma), 1, 0, 'C', true);
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
                $pdf->Cell(40, 6, $this->util->pegarNomeDaDisciplina($row->disciplina), 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->nota1, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->rec1, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->nota2, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->rec2, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->nota3, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->rec3, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->nota4, 'LR', 0, 'L', $fill);
                $pdf->Cell(10, 6, $row->rec4, 'LR', 0, 'L', $fill);
                $pdf->Ln();
                $fill = !$fill;
            }
            // Closing line
            $pdf->Cell(120, 0, '', 'T');
        }

        $pdf->Output('historico.pdf', 'D');
        exit();
    }
    
    public function pesquisarFaltas()
    {
        $aluno = $_POST["aluno"];
        $turma = $_POST["turma"];
        $disciplina = $_POST["disciplina"];

        echo '<p/>';

        $faltasQuery = $this->connection->query("
            select * from diario_de_classe where turma=$turma and aluno=$aluno and disciplina=$disciplina and presenca = 1 order by data
        ");
        $faltas = $faltasQuery->fetchAll(PDO::FETCH_OBJ);

        echo "Este aluno possui ".$faltasQuery->rowCount()." falta(s) nesta matéria:<p/>";

        foreach ($faltas as $falta) {
            $data = new DateTime($falta->data);
            echo $data->format('d/m/Y') . "<br/>";
        }

        $comentariosQuery = $this->connection->query("
            select * from diario_de_classe where turma=$turma and aluno=$aluno and disciplina=$disciplina and contexto='observacao' order by data
        ");
        $comentarios = $comentariosQuery->fetchAll(PDO::FETCH_OBJ);

        echo "<p/>Este aluno possui ".$comentariosQuery->rowCount()." comentários(s) por professores:<p/>";

        foreach ($comentarios as $comentario) {
            $data = new DateTime($comentario->data);
            echo $data->format('d/m/Y') . "<br/>$comentario->observacao<br/>";
        }
    }
}
