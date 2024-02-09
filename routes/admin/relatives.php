<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\Admin\ResponsavelController as AdminResponsavelController;

SimpleRouter::get('/admin/responsavel', function() {
    $admin = new AdminResponsavelController();
    $admin->criarResponsavel();
});

SimpleRouter::get('/admin/responsaveis', function() {
    $admin = new AdminResponsavelController();
    $admin->verResponsaveis();
})->name('admin_responsaveis');

SimpleRouter::get('/admin/responsavel/{idResponsavel}', function($idResponsavel) {
    $admin = new AdminResponsavelController();
    $admin->verResponsavel($idResponsavel);
});

SimpleRouter::delete('/admin/responsavel/{idResponsavel}/delete', function($idResponsavel) {
    $adminAjax = new AdminResponsavelController();
    $adminAjax->removerResponsavel($idResponsavel);
});

SimpleRouter::put('/admin/responsavel/desativar', function() {
    $adminAjax = new AdminResponsavelController();
    $adminAjax->desativarResponsavel();
});

SimpleRouter::put('/admin/responsavel', function() {
    $admin = new AdminResponsavelController();
    $admin->atualizarResponsavel();
});

SimpleRouter::post('/admin/responsavel', function() {
    $admin = new AdminResponsavelController();
    $admin->adicionarResponsavel();
});

SimpleRouter::post('/admin/alunoPorResponsavel', function() {
    $admin = new AdminResponsavelController();
    $admin->adicionarAlunoPorResponsavel();
});

SimpleRouter::delete('/admin/alunoPorResponsavel/{id}', function($id) {
    $adminAjax = new AdminResponsavelController();
    $adminAjax->removerAlunoPorResponsavel($id);
});