<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\Admin\AlunoController as AdminAlunoController;

SimpleRouter::get('/admin/aluno', function() {
    $admin = new AdminAlunoController();
    $admin->criarAluno();
});

SimpleRouter::get('/admin/alunos', function() {
    $admin = new AdminAlunoController();
    $admin->verAlunos();
})->name('admin_alunos');

SimpleRouter::get('/admin/aluno/{idAluno}', function($idAluno) {
    $admin = new AdminAlunoController();
    $admin->verAluno($idAluno);
});

SimpleRouter::delete('/admin/aluno/{idAluno}/delete', function($idAluno) {
    $adminAjax = new AdminAlunoController();
    $adminAjax->removerAluno($idAluno);
});

SimpleRouter::put('/admin/aluno/desativar', function() {
    $ajaxAdmin = new AdminAlunoController();
    $ajaxAdmin->desativarAluno();
});

SimpleRouter::put('/admin/aluno', function() {
    $admin = new AdminAlunoController();
    $admin->atualizarAluno();
});

SimpleRouter::post('/admin/aluno', function() {
    $admin = new AdminAlunoController();
    $admin->adicionarAluno();
});