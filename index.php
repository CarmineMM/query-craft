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
    fromModel: (new Model('star_client'))->setTable('gift_cards'),
    toModel: (new Model('star_client_local'))->setTable('gift_cards')
);

$etl->extractAttributes([
    'id' => 'id',
    'client_id' => 'client_id',
    'amount' => 'amount',
    'type' => 'type',
    'redeemed_at' => 'redeemed_at',
    'status' => 'status',
    'meta' => 'meta',
]);

$etl->processEtl();
