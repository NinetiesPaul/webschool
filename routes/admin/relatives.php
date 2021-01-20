<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\AdminController;
use App\Controllers\AjaxControllers\AdminController as AjaxAdmin;

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