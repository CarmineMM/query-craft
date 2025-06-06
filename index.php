<?php

use CarmineMM\QueryCraft\Connection;
use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\ETL\Factory;

require_once __DIR__ . '/vendor/autoload.php';

Connection::connect('star_client', [
    'driver' => 'pgsql',
    'host' => '3.136.210.194',
    'username' => 'user_star_client',
    'password' => 'YaddBxKCmnwjkuJS',
    'database' => 'star_client',
]);

Connection::connect('star_client_local', [
    'driver' => 'pgsql',
    'host' => 'localhost',
    'username' => 'postgres',
    'password' => 'admin',
    'database' => 'star_client',
]);

$etl = new Factory(
    (new Model('star_client'))->setTable('clients'),
    (new Model('star_client_local'))->setTable('clients')
);

$etl->extractAttributes([
    'id' => 'id',
    'name' => 'name',
    'identification' => 'identification',
    'email' => 'email',
    'tel' => 'tel',
    'address' => 'address',
    'registered_by' => 'registered_by',
]);

$etl->processEtl();
