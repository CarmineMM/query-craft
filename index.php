<?php

use CarmineMM\QueryCraft\Connection;
use CarmineMM\QueryCraft\Data\Model;

require_once __DIR__ . '/vendor/autoload.php';

Connection::connect('star_client', [
    'driver' => 'pgsql',
    'host' => '3.136.210.194',
    'username' => 'user_star_client',
    'password' => 'YaddBxKCmnwjkuJS',
    'database' => 'star_client',
]);

$client = new Model('star_client');
$client->setTable('clients')->setReturnType('array');

var_dump($client->all());
