<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\IndexController;
use App\Controllers\AuthController;
use App\Controllers\AlunoController;
use App\Controllers\AdminController;
use App\Controllers\GeneralController;
use App\Controllers\ResponsavelController;
use App\Controllers\ProfessorController;
use App\Util;

//general

SimpleRouter::get('/webschool/', function() {
    $index = new IndexController;
    $index->index();
});

SimpleRouter::post('/webschool/gerarBoletim', function() {
    $general = new GeneralController;
    $general->gerarBoletim();
});

SimpleRouter::post('/webschool/gerarHistorico', function() {
    $general = new GeneralController;
    $general->gerarHistorico();
});

SimpleRouter::post('/webschool/pesquisarFaltas', function() {
    $general = new GeneralController;
    $general->pesquisarFaltas();
});

//alunos

SimpleRouter::get('/webschool/aluno/home', function() {
    $aluno = new AlunoController();
    $aluno->index();
});

SimpleRouter::get('/webschool/aluno/turmas', function() {
    $aluno = new AlunoController();
    $aluno->verTurmas();
});

//responsavel

SimpleRouter::get('/webschool/responsavel/home', function() {
    $responsavel = new ResponsavelController();
    $responsavel->index();
});

SimpleRouter::get('/webschool/responsavel/alunos', function() {
    $responsavel = new ResponsavelController();
    $responsavel->verAlunos();
});

SimpleRouter::get('/webschool/responsavel/aluno/{idAluno}', function($idAluno) {
    $responsavel = new ResponsavelController();
    $responsavel->verAluno($idAluno);
});

//professor

SimpleRouter::get('/webschool/professor/home', function() {
    $professor = new ProfessorController();
    $professor->index();
});

SimpleRouter::get('/webschool/professor/turmas', function() {
    $professor = new ProfessorController();
    $professor->verTurmas();
});

SimpleRouter::get('/webschool/professor/turma/{idTurma}', function($idTurma) {
    $professor = new ProfessorController();
    $professor->verTurma($idTurma);
});

SimpleRouter::get('/webschool/professor/diariodeclasse/{idTurma}', function($idTurma) {
    $professor = new ProfessorController();
    $professor->verDiarioDeClasse($idTurma);
});

SimpleRouter::post('/webschool/professor/inserirNota', function() {
    $professor = new ProfessorController();
    $professor->inserirNota();
});

SimpleRouter::post('/webschool/professor/pesquisarFrequencia', function() {
    $professor = new ProfessorController();
    $professor->pesquisarFrequencia();
});

SimpleRouter::post('/webschool/professor/alterarFrequencia', function() {
    $professor = new ProfessorController();
    $professor->alterarFrequencia();
});

SimpleRouter::post('/webschool/professor/visualizarComentarios', function() {
    $professor = new ProfessorController();
    $professor->verComentarios();
});

SimpleRouter::post('/webschool/professor/comentario', function() {
    $professor = new ProfessorController();
    $professor->adicionarComentario();
});

SimpleRouter::delete('/webschool/professor/comentario/{idComentario}', function($idComentario) {
    $professor = new ProfessorController();
    $professor->deletarComentario($idComentario);
});

SimpleRouter::delete('/webschool/professor/arquivo/{idArquivo}', function($idArquivo) {
    $professor = new ProfessorController();
    $professor->deletarArquivoDeComentario($idArquivo);
});

//admin - alunos

SimpleRouter::get('/webschool/admin/home', function() {
    $admin = new AdminController();
    $admin->index();
});

SimpleRouter::get('/webschool/admin/alunos', function() {
    $admin = new AdminController();
    $admin->verAlunos();
});

SimpleRouter::get('/webschool/admin/aluno/{idAluno}', function($idAluno) {
    $admin = new AdminController();
    $admin->verAluno($idAluno);
});

SimpleRouter::delete('/webschool/admin/aluno/{idAluno}/delete', function($idAluno) {
    $admin = new AdminController();
    $admin->removerAluno($idAluno);
});

SimpleRouter::put('/webschool/admin/aluno', function() {
    $admin = new AdminController();
    $admin->atualizarAluno();
});

SimpleRouter::post('/webschool/admin/aluno', function() {
    $admin = new AdminController();
    $admin->adicionarAluno();
});

// admin - professores

SimpleRouter::get('/webschool/admin/professores', function() {
    $admin = new AdminController();
    $admin->verProfessores();
});

SimpleRouter::get('/webschool/admin/professor/{idProfessor}', function($idProfessor) {
    $admin = new AdminController();
    $admin->verProfessor($idProfessor);
});

SimpleRouter::delete('/webschool/admin/professor/{idProfessor}/delete', function($idProfessor) {
    $admin = new AdminController();
    $admin->removerProfessor($idProfessor);
});

SimpleRouter::put('/webschool/admin/professor', function() {
    $admin = new AdminController();
    $admin->atualizarProfessor();
});

SimpleRouter::post('/webschool/admin/professor', function() {
    $admin = new AdminController();
    $admin->adicionarProfessor();
});

SimpleRouter::post('/webschool/admin/professorPorMateria', function() {
    $admin = new AdminController();
    $admin->adicionarProfessorPorMateria();
});

SimpleRouter::delete('/webschool/admin/professorPorMateria/{id}', function($id) {
    $admin = new AdminController();
    $admin->removerProfessorPorMateria($id);
});

//admin - responsaveis

SimpleRouter::get('/webschool/admin/responsaveis', function() {
    $admin = new AdminController();
    $admin->verResponsaveis();
});

SimpleRouter::get('/webschool/admin/responsavel/{idResponsavel}', function($idResponsavel) {
    $admin = new AdminController();
    $admin->verResponsavel($idResponsavel);
});

SimpleRouter::delete('/webschool/admin/responsavel/{idResponsavel}/delete', function($idResponsavel) {
    $admin = new AdminController();
    $admin->removerResponsavel($idResponsavel);
});

SimpleRouter::put('/webschool/admin/responsavel', function() {
    $admin = new AdminController();
    $admin->atualizarResponsavel();
});

SimpleRouter::post('/webschool/admin/responsavel', function() {
    $admin = new AdminController();
    $admin->adicionarResponsavel();
});

SimpleRouter::post('/webschool/admin/alunoPorResponsavel', function() {
    $admin = new AdminController();
    $admin->adicionarAlunoPorResponsavel();
});

SimpleRouter::delete('/webschool/admin/alunoPorResponsavel/{id}', function($id) {
    $admin = new AdminController();
    $admin->removerAlunoPorResponsavel($id);
});

//admin - disciplinas

SimpleRouter::get('/webschool/admin/disciplinas', function() {
    $admin = new AdminController();
    $admin->verMaterias();
});

SimpleRouter::get('/webschool/admin/disciplina/{idDisciplina}', function($idDisciplina) {
    $admin = new AdminController();
    $admin->verMateria($idDisciplina);
});

SimpleRouter::delete('/webschool/admin/disciplina/{idDisciplina}/delete', function($idDisciplina) {
    $admin = new AdminController();
    $admin->removerMateria($idDisciplina);
});

SimpleRouter::put('/webschool/admin/disciplina', function() {
    $admin = new AdminController();
    $admin->atualizarMateria();
});

SimpleRouter::post('/webschool/admin/disciplina', function() {
    $admin = new AdminController();
    $admin->adicionarMateria();
});

//admin - turmas

SimpleRouter::get('/webschool/admin/turmas', function() {
    $admin = new AdminController();
    $admin->verTurmas();
});

SimpleRouter::get('/webschool/admin/turma/{idTurma}', function($idTurma) {
    $admin = new AdminController();
    $admin->verTurma($idTurma);
});

SimpleRouter::delete('/webschool/admin/turma/{idTurma}/delete', function($idTurma) {
    $admin = new AdminController();
    $admin->removerTurma($idTurma);
});

SimpleRouter::put('/webschool/admin/turma', function() {
    $admin = new AdminController();
    $admin->atualizarTurma();
});

SimpleRouter::post('/webschool/admin/turma', function() {
    $admin = new AdminController();
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