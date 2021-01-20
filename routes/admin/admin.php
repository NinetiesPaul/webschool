<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\AdminController;

SimpleRouter::get('/admin/home', function() {
    $admin = new AdminController();
    $admin->index();
});
