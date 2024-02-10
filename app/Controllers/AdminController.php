<?php

namespace App\Controllers;

use App\Enum;
use App\Templates;
use App\Util;
use App\DB\Storage\LogStorage;

class AdminController
{
    public function __construct()
    {
        Util::userPermission(Enum::TIPO_ADMIN);
        new LogStorage();
    }
    
    public function index()
    {
        new Templates('admin/index.html');
    }
}
