<?php

use Pecee\SimpleRouter\SimpleRouter;

include 'vendor/autoload.php';

/* Load external routes file */
require_once 'helpers.php';
require_once 'routes.php';

/**
 * The default namespace for route-callbacks, so we don't have to specify it each time.
 * Can be overwritten by using the namespace config option on your routes.
 */


SimpleRouter::setDefaultNamespace('Controllers');

// Start the routing
SimpleRouter::start();