<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\IndexController;
use App\Controllers\AuthController;
use App\Controllers\AlunoController;
use App\Controllers\AdminController;
use App\Controllers\GeneralController;
use App\Controllers\ResponsavelController;
use App\Controllers\ProfessorController;
use App\Controllers\AjaxControllers\AdminController as AjaxAdmin;
use App\Controllers\AjaxControllers\GeneralController as AjaxGeneral;
use App\Controllers\AjaxControllers\ProfessorController as AjaxProfessor;

//general

SimpleRouter::get('/', function() {
    $general = new GeneralController;
    $general->index();
});

SimpleRouter::post('/gerarBoletim', function() {
    $general = new GeneralController;
    $general->gerarBoletim();
});

SimpleRouter::post('/gerarHistorico', function() {
    $general = new GeneralController;
    $general->gerarHistorico();
});

SimpleRouter::post('/pesquisarFaltas', function() {
    $generalAjax = new AjaxGeneral();
    $generalAjax->pesquisarFaltas();
});

SimpleRouter::get('/perfil', function() {
    $general = new GeneralController;
    $general->visualizarPerfil();
});

SimpleRouter::put('/atualizarPerfil', function() {
    $general = new GeneralController;
    $general->alterarPerfil();
});

//alunos

SimpleRouter::get('/aluno/home', function() {
    $aluno = new AlunoController();
    $aluno->index();
});

SimpleRouter::get('/aluno/turmas', function() {
    $aluno = new AlunoController();
    $aluno->verTurmas();
});

//responsavel

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

//professor

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

//admin - alunos

SimpleRouter::get('/admin/home', function() {
    $admin = new AdminController();
    $admin->index();
});

SimpleRouter::get('/admin/alunos', function() {
    $admin = new AdminController();
    $admin->verAlunos();
});

SimpleRouter::get('/admin/aluno/{idAluno}', function($idAluno) {
    $admin = new AdminController();
    $admin->verAluno($idAluno);
});

SimpleRouter::delete('/admin/aluno/{idAluno}/delete', function($idAluno) {
    $adminAjax = new AjaxAdmin();
    $adminAjax->removerAluno($idAluno);
});

SimpleRouter::put('/admin/aluno/{idAluno}/desativar', function($idAluno) {
    $ajaxAdmin = new AjaxAdmin();
    $ajaxAdmin->desativarAluno($idAluno);
});

SimpleRouter::put('/admin/aluno', function() {
    $admin = new AdminController();
    $admin->atualizarAluno();
});

SimpleRouter::post('/admin/aluno', function() {
    $admin = new AdminController();
    $admin->adicionarAluno();
});

// admin - professores

SimpleRouter::get('/admin/professores', function() {
    $admin = new AdminController();
    $admin->verProfessores();
});

SimpleRouter::get('/admin/professor/{idProfessor}', function($idProfessor) {
    $admin = new AdminController();
    $admin->verProfessor($idProfessor);
});

SimpleRouter::put('/admin/professor/{idProfessor}/desativar', function($idProfessor) {
    $adminAjax = new AjaxAdmin();
    $adminAjax->desativarProfessor($idProfessor);
});

SimpleRouter::delete('/admin/professor/{idProfessor}/delete', function($idProfessor) {
    $adminAjax = new AjaxAdmin();
    $adminAjax->removerProfessor($idProfessor);
});

SimpleRouter::put('/admin/professor', function() {
    $admin = new AdminController();
    $admin->atualizarProfessor();
});

SimpleRouter::post('/admin/professor', function() {
    $admin = new AdminController();
    $admin->adicionarProfessor();
});

SimpleRouter::post('/admin/professorPorMateria', function() {
    $admin = new AdminController();
    $admin->adicionarProfessorPorMateria();
});

SimpleRouter::delete('/admin/professorPorMateria/{id}', function($id) {
    $adminAjax = new AjaxAdmin();
    $adminAjax->removerProfessorPorMateria($id);
});

//admin - responsaveis

SimpleRouter::get('/admin/responsaveis', function() {
    $admin = new AdminController();
    $admin->verResponsaveis();
});

SimpleRouter::get('/admin/responsavel/{idResponsavel}', function($idResponsavel) {
    $admin = new AdminController();
    $admin->verResponsavel($idResponsavel);
});

SimpleRouter::delete('/admin/responsavel/{idResponsavel}/delete', function($idResponsavel) {
    $adminAjax = new AjaxAdmin();
    $adminAjax->removerResponsavel($idResponsavel);
});

SimpleRouter::put('/admin/responsavel/{idResponsavel}/desativar', function($idResponsavel) {
    $adminAjax = new AjaxAdmin();
    $adminAjax->desativarResponsavel($idResponsavel);
});

SimpleRouter::put('/admin/responsavel', function() {
    $admin = new AdminController();
    $admin->atualizarResponsavel();
});

SimpleRouter::post('/admin/responsavel', function() {
    $admin = new AdminController();
    $admin->adicionarResponsavel();
});

SimpleRouter::post('/admin/alunoPorResponsavel', function() {
    $admin = new AdminController();
    $admin->adicionarAlunoPorResponsavel();
});

SimpleRouter::delete('/admin/alunoPorResponsavel/{id}', function($id) {
    $adminAjax = new AjaxAdmin();
    $adminAjax->removerAlunoPorResponsavel($id);
});

//admin - disciplinas

SimpleRouter::get('/admin/disciplinas', function() {
    $admin = new AdminController();
    $admin->verMaterias();
});

SimpleRouter::get('/admin/disciplina/{idDisciplina}', function($idDisciplina) {
    $admin = new AdminController();
    $admin->verMateria($idDisciplina);
});

SimpleRouter::delete('/admin/disciplina/{idDisciplina}/delete', function($idDisciplina) {
    $ajaxAdmin = new AjaxAdmin();
    $ajaxAdmin->removerMateria($idDisciplina);
});

SimpleRouter::put('/admin/disciplina', function() {
    $admin = new AdminController();
    $admin->atualizarMateria();
});

SimpleRouter::post('/admin/disciplina', function() {
    $admin = new AdminController();
    $admin->adicionarMateria();
});

//admin - turmas

SimpleRouter::get('/admin/turmas', function() {
    $admin = new AdminController();
    $admin->verTurmas();
});

SimpleRouter::get('/admin/turma/{idTurma}', function($idTurma) {
    $admin = new AdminController();
    $admin->verTurma($idTurma);
});

SimpleRouter::delete('/admin/turma/{idTurma}/delete', function($idTurma) {
    $ajaxAdmin = new AjaxAdmin();
    $ajaxAdmin->removerTurma($idTurma);
});

SimpleRouter::put('/admin/turma', function() {
    $admin = new AdminController();
    $admin->atualizarTurma();
});

SimpleRouter::post('/admin/turma', function() {
    $admin = new AdminController();
    $admin->adicionarTurma();
});

//auth e logout

SimpleRouter::post('/login', function() {
    $auth = new AuthController;
    $auth->login();
});

SimpleRouter::get('/logout', function() {
    $auth = new AuthController;
    $auth->logout();
});

SimpleRouter::post('/verificarLogin', function() {
    $auth = new AuthController;
    $auth->loginTaken();
});