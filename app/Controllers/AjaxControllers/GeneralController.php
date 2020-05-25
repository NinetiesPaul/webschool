<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controllers\AjaxControllers;

use App\DB\DB;
use App\DB\Storage\DiarioDeClasseStorage;
use App\Templates;
use App\Util;
use tFPDF;
use DateTime;

class GeneralController extends AjaxController
{
    protected $diarioStorage;

    public function __construct()
    {
        parent::__construct();

        $this->diarioStorage = new DiarioDeClasseStorage();
    }
    
    public function pesquisarFaltas()
    {
        $aluno = $_POST["aluno"];
        $turma = $_POST["turma"];
        $disciplina = $_POST["disciplina"];

        try {
            $faltas = $this->diarioStorage->verFaltasPorAlunoDaMateriaETurma($turma, $aluno, $disciplina);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $message =  "Este aluno possui " . count($faltas) . " falta(s) nesta matéria:<p/>";

        foreach ($faltas as $falta) {
            $data = new DateTime($falta->data);
            $message .= $data->format('d/m/Y') . "<br/>";
        }

        try {
            $comentarios = $this->diarioStorage->verComentariosPorAlunoDaMateriaETurma($turma, $aluno, $disciplina);
        } catch (\Exception $ex) {
            $this->throwError($ex);
        }

        $message .= "<p/>Este aluno possui " . count($comentarios) . " comentários(s) por professores:<p/>";

        foreach ($comentarios as $comentario) {
            $data = new DateTime($comentario->data);
            $message .= $data->format('d/m/Y') . "<br/>$comentario->observacao<br/>";
        }

        $this->response($message);
    }
}
