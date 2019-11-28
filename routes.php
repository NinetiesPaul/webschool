<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\IndexController as Index;
use App\Controllers\AuthController as Auth;

SimpleRouter::get('/webschool/', function() {
    Index::index();
});

SimpleRouter::get('/webschool/listing', function() {
    Index::list();
});

SimpleRouter::get('/webschool/form', function() {
    Index::form();
});

SimpleRouter::post('/webschool/fromForm', function() {
    Index::fromForm();
});

SimpleRouter::post('/webschool/login', function() {
    Auth::login();
});

