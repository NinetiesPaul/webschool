<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\ResponsavelController;

SimpleRouter::get('/responsavel/home', function() {
    $responsavel = new ResponsavelController();
    $responsavel->index();
});

SimpleRouter::get('/responsavel/alunos', function() {
    $responsavel = new ResponsavelController();
    $responsavel->verAlunos();
})->name('responsavel_alunos');

SimpleRouter::get('/responsavel/aluno/{idAluno}', function($idAluno) {
    $responsavel = new ResponsavelController();
    $responsavel->verAluno($idAluno);
});

SimpleRouter::get('/responsavel/aluno/{idAluno}/turma/{idTurma}', function($idAluno, $idTurma) {
    $responsavel = new ResponsavelController();
    $responsavel->verTurmaDoAluno($idAluno, $idTurma);
});