<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\AdminController;
use App\Controllers\AjaxControllers\AdminController as AjaxAdmin;

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