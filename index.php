<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/User.php';

use CarmineMM\QueryCraft\Connection;

Connection::connect('default', [
    'driver' => 'pgsql',
    'host' => '127.0.0.1',
    'username' => 'postgres',
    'password' => 'admin',
    'database' => 'cordelia',
]);

$user = new User();
var_dump($user->all());
