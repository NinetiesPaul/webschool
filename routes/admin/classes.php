<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\Admin\TurmasController as AdminTurmaController;

SimpleRouter::get('/admin/turmas', function() {
    $admin = new AdminTurmaController();
    $admin->verTurmas();
});

SimpleRouter::get('/admin/turma/{idTurma}', function($idTurma) {
    $admin = new AdminTurmaController();
    $admin->verTurma($idTurma);
});

SimpleRouter::delete('/admin/turma/{idTurma}/delete', function($idTurma) {
    $ajaxAdmin = new AdminTurmaController();
    $ajaxAdmin->removerTurma($idTurma);
});

SimpleRouter::put('/admin/turma', function() {
    $admin = new AdminTurmaController();
    $admin->atualizarTurma();
});

SimpleRouter::post('/admin/turma', function() {
    $admin = new AdminTurmaController();
    $admin->adicionarTurma();
});