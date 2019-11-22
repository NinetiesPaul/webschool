<?php

if (! empty($_POST)) {
    include('conn.php');
    include('functions.php');
    require('lib/tfpdf/tfpdf.php');
    
    $aluno = $_POST["aluno-pdf"];
    $historico = $_POST["historico-pdf"];
    
    if ($historico == 'boletim') {
        
        $turma = $_POST["turma-pdf"];

        $notasQuery = $db->query("select * from nota_por_aluno where aluno=$aluno and turma=$turma order by disciplina");
        $notas = $notasQuery->fetchAll(PDO::FETCH_OBJ);

        $pdf = new tFPDF();
        $pdf->AddPage();

        // Colors, line width and bold font
        $pdf->SetFillColor(43,74,92);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
        $pdf->SetFont('DejaVu','',8);
        
        // Header

        $headers = ['Matéria','Nota 1', 'Rec. 1', 'Nota 2', 'Rec. 2', 'Nota 3', 'Rec. 3', 'Nota 4', 'Rec. 4'];

        foreach($headers as $header) {
            $width = ($header === 'Matéria') ? 40 : 10;
            $pdf->Cell($width,7,$header,1,0,'C',true);
        }
        $pdf->Ln();
        // Color and font restoration
        $pdf->SetFillColor(224,235,255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('DejaVu','',8);
        // Data
        $fill = false;
        foreach($notas as $row)
        {
            $pdf->Cell(40,6,pegarDisciplina($row->disciplina),'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->nota1,'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->rec1,'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->nota2,'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->rec2,'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->nota3,'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->rec3,'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->nota4,'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->rec4,'LR',0,'L',$fill);
            $pdf->Ln();
            $fill = !$fill;
        }
        // Closing line
        $pdf->Cell(120,0,'','T');

        $pdf->Output('boletim.pdf', 'D');
        exit();
    }    
    
    $aluno = explode('.', $aluno);
   
    $usuarioQuery = $db->query("select * from aluno where usuario=$aluno[1]");
    $usuario = $usuarioQuery->fetch(PDO::FETCH_OBJ);

    $turmasQuery = $db->query("select turma from nota_por_aluno where aluno=$aluno[0] group by turma");
    $turmas = $turmasQuery->fetchAll(PDO::FETCH_OBJ);

    $pdf = new tFPDF();
    
    foreach ($turmas as $turma) {

        $notasQuery = $db->query("select * from nota_por_aluno where aluno=$aluno[0] and turma=$turma->turma order by disciplina");
        $notas = $notasQuery->fetchAll(PDO::FETCH_OBJ);

        $pdf->AddPage();

        // Colors, line width and bold font
        $pdf->SetFillColor(43,74,92);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
        $pdf->SetFont('DejaVu','',8);
        // Header

        $pdf->Cell(120,7,pegarTurma($turma->turma),1,0,'C',true);
        $pdf->Ln();
        $headers = ['Matéria','Nota 1', 'Rec. 1', 'Nota 2', 'Rec. 2', 'Nota 3', 'Rec. 3', 'Nota 4', 'Rec. 4'];

        foreach($headers as $header) {
            $width = ($header === 'Matéria') ? 40 : 10;
            $pdf->Cell($width,7,$header,1,0,'C',true);
        }
        $pdf->Ln();
        // Color and font restoration
        $pdf->SetFillColor(224,235,255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('DejaVu','',8);
        // Data
        $fill = false;
        foreach($notas as $row)
        {
            $pdf->Cell(40,6,pegarDisciplina($row->disciplina),'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->nota1,'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->rec1,'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->nota2,'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->rec2,'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->nota3,'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->rec3,'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->nota4,'LR',0,'L',$fill);
            $pdf->Cell(10,6,$row->rec4,'LR',0,'L',$fill);
            $pdf->Ln();
            $fill = !$fill;
        }
        // Closing line
        $pdf->Cell(120,0,'','T');
    } 
    
    $pdf->Output('historico.pdf', 'D');
    exit();
}
