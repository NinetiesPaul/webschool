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
});

SimpleRouter::get('/responsavel/aluno/{idAluno}', function($idAluno) {
    $responsavel = new ResponsavelController();
    $responsavel->verAluno($idAluno);
});