<?php

use CarmineMM\QueryCraft\Connection;
use CarmineMM\QueryCraft\Data\Model;
use CarmineMM\QueryCraft\ETL\Factory;
use CarmineMM\UnitsConversion\Base\Dictionary;

require_once __DIR__ . '/vendor/autoload.php';

Connection::connect('star_client', [
    'driver' => 'pgsql',
    'host' => '3.136.210.194',
    'username' => 'user_etl_connection',
    'password' => 'TyolYywRwkQLNRI',
    'database' => 'etl_ecommerce',
]);

Connection::connect('star_client_local', [
    'driver' => 'pgsql',
    'host' => 'localhost',
    'username' => 'postgres',
    'password' => 'admin',
    'database' => 'api_ecommerce',
]);

$etl = new Factory(
    fromModel: (new Model('star_client'))->setTable('oitm'),
    toModel: (new Model('star_client_local'))->setTable('products'),
    splitIn: 5_000
);

$etl->extractAttributes([
    'codigoArticulo' => 'sku',
    'descripcion' => 'name',
    'U_N_COMERCIAL' => 'name_commercial',
    fn($data) => ['slug_commercial', Dictionary::slug($data['U_N_COMERCIAL'])],
    'U_D_COMERCIAL' => 'description_commercial',
    'cubicaje' => 'cubic_volume',
    fn($data) => ['can_recessed', (bool) $data['Empotrable'] ? 'true' : 'false'],
    'ITM1_Price' => 'net_price',
    fn($data) => ['total_taxes', $data['ITM1_Price'] * 0.16],
    fn() => ['total_discounts', 0],
    fn() => ['price_currency', 'USD'],
]);

$etl->processEtl();
