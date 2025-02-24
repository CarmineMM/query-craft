<?php

require_once __DIR__ . '/vendor/autoload.php';

use CarmineMM\QueryCraft\Cache;
use CarmineMM\QueryCraft\Connection;
use CarmineMM\QueryCraft\Debug;
use CarmineMM\QueryCraft\Draft\User;

Connection::connect('default', [
    'driver' => 'pgsql',
    'host' => '127.0.0.1',
    'username' => 'postgres',
    'password' => 'admin',
    'database' => 'captive_portal',
]);

Connection::debugMode();

$user = new User();
$user->where('id', 1)->where('name', 'Fulton Avery')->first();
$user->where('id', 1)->first();
$user->where('id', 2)->first();
$user->where('id', 2)->first();

var_dump(Debug::getQueries());
