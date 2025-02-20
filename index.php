<?php

require_once __DIR__ . '/vendor/autoload.php';

use CarmineMM\QueryCraft\Connection;
use CarmineMM\QueryCraft\Draft\User;

Connection::connect('default', [
    'driver' => 'pgsql',
    'host' => '127.0.0.1',
    'username' => 'postgres',
    'password' => 'admin',
    'database' => 'captive_portal',
]);

$user = new User();
var_dump($user->where('id', 1)->first());
