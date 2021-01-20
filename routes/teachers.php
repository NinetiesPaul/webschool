<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\ProfessorController;
use App\Controllers\AjaxControllers\ProfessorController as AjaxProfessor;

SimpleRouter::get('/professor/home', function() {
    $professor = new ProfessorController();
    $professor->index();
});

SimpleRouter::get('/professor/turmas', function() {
    $professor = new ProfessorController();
    $professor->verTurmas();
});

SimpleRouter::get('/professor/turma/{idTurma}', function($idTurma) {
    $professor = new ProfessorController();
    $professor->verTurma($idTurma);
});

SimpleRouter::get('/professor/diariodeclasse/{idTurma}', function($idTurma) {
    $professor = new ProfessorController();
    $professor->verDiarioDeClasse($idTurma);
});

SimpleRouter::post('/professor/inserirNota', function() {
    $ajaxProfessor = new AjaxProfessor();
    $ajaxProfessor->inserirNota();
});

SimpleRouter::post('/professor/pesquisarFrequencia', function() {
    $ajaxProfessor = new AjaxProfessor();
    $ajaxProfessor->pesquisarFrequencia();
});

SimpleRouter::post('/professor/alterarFrequencia', function() {
    $ajaxProfessor = new AjaxProfessor();
    $ajaxProfessor->alterarFrequencia();
});

SimpleRouter::post('/professor/visualizarComentarios', function() {
    $ajaxProfessor = new AjaxProfessor();
    $ajaxProfessor->verComentarios();
});

SimpleRouter::post('/professor/comentario', function() {
    $ajaxProfessor = new AjaxProfessor();
    $ajaxProfessor->adicionarComentario();
});

SimpleRouter::delete('/professor/comentario/{idComentario}', function($idComentario) {
    $ajaxProfessor = new AjaxProfessor();
    $ajaxProfessor->deletarComentario($idComentario);
});

SimpleRouter::delete('/professor/arquivo/{idArquivo}', function($idArquivo) {
    $ajaxProfessor = new AjaxProfessor();
    $ajaxProfessor->deletarArquivoDeComentario($idArquivo);
});