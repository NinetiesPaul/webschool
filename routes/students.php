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
});
