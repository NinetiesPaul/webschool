<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\IndexController;
use App\Controllers\AuthController;
use App\Util;

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

//admin - alunos

SimpleRouter::get('/webschool/admin/alunos', function() {
    $admin = new App\Controllers\AdminController();
    $admin->verAlunos();
});

SimpleRouter::get('/webschool/admin/aluno/{idAluno}', function($idAluno) {
    $admin = new App\Controllers\AdminController();
    $admin->verAluno($idAluno);
});

SimpleRouter::delete('/webschool/admin/aluno/{idAluno}/delete', function($idAluno) {
    $admin = new App\Controllers\AdminController();
    $admin->removerAluno($idAluno);
});

SimpleRouter::put('/webschool/admin/aluno', function() {
    $admin = new App\Controllers\AdminController();
    $admin->atualizarAluno();
});

SimpleRouter::post('/webschool/admin/aluno', function() {
    $admin = new App\Controllers\AdminController();
    $admin->adicionarAluno();
});

// admin - professores

SimpleRouter::get('/webschool/admin/professores', function() {
    $admin = new App\Controllers\AdminController();
    $admin->verProfessores();
});

SimpleRouter::get('/webschool/admin/professor/{idProfessor}', function($idProfessor) {
    $admin = new App\Controllers\AdminController();
    $admin->verProfessor($idProfessor);
});

SimpleRouter::delete('/webschool/admin/professor/{idProfessor}/delete', function($idProfessor) {
    $admin = new App\Controllers\AdminController();
    $admin->removerProfessor($idProfessor);
});

SimpleRouter::put('/webschool/admin/professor', function() {
    $admin = new App\Controllers\AdminController();
    $admin->atualizarProfessor();
});

SimpleRouter::post('/webschool/admin/professor', function() {
    $admin = new App\Controllers\AdminController();
    $admin->adicionarProfessor();
});

SimpleRouter::post('/webschool/admin/professorPorMateria', function() {
    $admin = new App\Controllers\AdminController();
    $admin->adicionarProfessorPorMateria();
});

SimpleRouter::delete('/webschool/admin/professorPorMateria/{id}', function($id) {
    $admin = new App\Controllers\AdminController();
    $admin->removerProfessorPorMateria($id);
});

//admin - responsaveis

SimpleRouter::get('/webschool/admin/responsaveis', function() {
    $admin = new App\Controllers\AdminController();
    $admin->verResponsaveis();
});

SimpleRouter::get('/webschool/admin/responsavel/{idResponsavel}', function($idResponsavel) {
    $admin = new App\Controllers\AdminController();
    $admin->verResponsavel($idResponsavel);
});

SimpleRouter::delete('/webschool/admin/responsavel/{idResponsavel}/delete', function($idResponsavel) {
    $admin = new App\Controllers\AdminController();
    $admin->removerResponsavel($idResponsavel);
});

SimpleRouter::put('/webschool/admin/responsavel', function() {
    $admin = new App\Controllers\AdminController();
    $admin->atualizarResponsavel();
});

SimpleRouter::post('/webschool/admin/responsavel', function() {
    $admin = new App\Controllers\AdminController();
    $admin->adicionarResponsavel();
});

SimpleRouter::post('/webschool/admin/alunoPorResponsavel', function() {
    $admin = new App\Controllers\AdminController();
    $admin->adicionarAlunoPorResponsavel();
});

SimpleRouter::delete('/webschool/admin/alunoPorResponsavel/{id}', function($id) {
    $admin = new App\Controllers\AdminController();
    $admin->removerAlunoPorResponsavel($id);
});

//admin - disciplinas

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

//admin - turmas

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

//auth e logout

SimpleRouter::post('/webschool/login', function() {
    $auth = new AuthController;
    $auth->login();
});

SimpleRouter::get('/webschool/logout', function() {
    $auth = new AuthController;
    $auth->logout();
});

SimpleRouter::post('/webschool/verificarLogin', function() {
    $util = new Util;
    $util->loginTakenAjax();
});