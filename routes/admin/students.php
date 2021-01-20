<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\AdminController;
use App\Controllers\AjaxControllers\AdminController as AjaxAdmin;

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