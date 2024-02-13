<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\AlunoController;

SimpleRouter::get('/aluno/home', function() {
    $aluno = new AlunoController();
    $aluno->index();
});

SimpleRouter::get('/aluno/turmas', function() {
    $aluno = new AlunoController();
    $aluno->verTurmas();
})->name('aluno_turmas');

SimpleRouter::get('/aluno/turma/{idTurma}', function($idTurma) {
    $aluno = new AlunoController();
    $aluno->verTurma($idTurma);
});
