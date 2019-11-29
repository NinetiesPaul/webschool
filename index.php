<?php

use Pecee\SimpleRouter\SimpleRouter;

include 'vendor/autoload.php';

require_once 'helpers.php';
require_once 'routes.php';

SimpleRouter::setDefaultNamespace('Controllers');

SimpleRouter::start();