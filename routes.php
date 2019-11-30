<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\IndexController;
use App\Controllers\AuthController;

//index

SimpleRouter::get('/webschool/', function() {
    $index = new IndexController;
    $index->index();
});

//admin

SimpleRouter::get('/webschool/admin/home', function() {
    $admin = new App\Controllers\AdminController();
    $admin->index();
});


SimpleRouter::get('/webschool/admin/alunos', function() {
    $admin = new App\Controllers\AdminController();
    $admin->alunos();
});


SimpleRouter::get('/webschool/admin/professores', function() {
    $admin = new App\Controllers\AdminController();
    $admin->professores();
});


SimpleRouter::get('/webschool/admin/responsaveis', function() {
    $admin = new App\Controllers\AdminController();
    $admin->responsaveis();
});


SimpleRouter::get('/webschool/admin/disciplinas', function() {
    $admin = new App\Controllers\AdminController();
    $admin->verMaterias();
});

SimpleRouter::get('/webschool/admin/disciplina/{idDisciplina}', function($idDisciplina) {
    $admin = new App\Controllers\AdminController();
    $admin->verMateria($idDisciplina);
});

SimpleRouter::delete('/webschool/admin/disciplina/{idDisciplina}/delete', function($idDisciplina) {
    $admin = new App\Controllers\AdminController();
    $admin->removerMateria($idDisciplina);
});

SimpleRouter::put('/webschool/admin/disciplina', function() {
    $admin = new App\Controllers\AdminController();
    $admin->atualizarMateria();
});

SimpleRouter::post('/webschool/admin/disciplina', function() {
    $admin = new App\Controllers\AdminController();
    $admin->adicionarMateria();
});

SimpleRouter::get('/webschool/admin/turmas', function() {
    $admin = new App\Controllers\AdminController();
    $admin->verTurmas();
});

SimpleRouter::get('/webschool/admin/turma/{idTurma}', function($idTurma) {
    $admin = new App\Controllers\AdminController();
    $admin->verTurma($idTurma);
});

SimpleRouter::delete('/webschool/admin/turma/{idTurma}/delete', function($idTurma) {
    $admin = new App\Controllers\AdminController();
    $admin->removerTurma($idTurma);
});

SimpleRouter::put('/webschool/admin/turma', function() {
    $admin = new App\Controllers\AdminController();
    $admin->atualizarTurma();
});

SimpleRouter::post('/webschool/admin/turma', function() {
    $admin = new App\Controllers\AdminController();
    $admin->adicionarTurma();
});

//auth

SimpleRouter::post('/webschool/login', function() {
    $auth = new AuthController;
    $auth->login();
});

SimpleRouter::get('/webschool/logout', function() {
    $auth = new AuthController;
    $auth->logout();
});
