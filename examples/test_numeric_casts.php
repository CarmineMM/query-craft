<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Examples\UserEntity;

// Create a new user with numeric values
$user = new UserEntity([
    'id' => '42', // Will be cast to int
    'username' => 'johndoe',
    'email' => 'john@example.com',
    'password' => 'secret',
    'price' => '19.9999', // Will be formatted to 2 decimal places
    'quantity' => '5.67', // Will be cast to int (5)
    'discount' => '0.1256789', // Will be formatted to 4 decimal places
    'createdAt' => '2023-01-01 12:00:00',
    'preferences' => ['theme' => 'dark', 'notifications' => true],
]);

// Display the values after casting
echo "=== After Initialization ===\n";
echo "ID (int): " . $user->id . " (" . gettype($user->id) . ")\n";
echo "Price (float, 2 decimals): " . $user->price . " (" . gettype($user->price) . ")\n";
echo "Quantity (int): " . $user->quantity . " (" . gettype($user->quantity) . ")\n";
echo "Discount (float, 4 decimals): " . $user->discount . " (" . gettype($user->discount) . ")\n\n";

// Update some values
$user->price = '25.5555';
$user->quantity = '10.25';
$user->discount = '0.333333';

echo "=== After Update ===\n";
echo "Price (float, 2 decimals): " . $user->price . " (" . gettype($user->price) . ")\n";
echo "Quantity (int): " . $user->quantity . " (" . gettype($user->quantity) . ")\n";
echo "Discount (float, 4 decimals): " . $user->discount . " (" . gettype($user->discount) . ")\n\n";

// Test with array conversion
$userArray = $user->toArray();
echo "=== Array Representation ===\n";
print_r($userArray);

// The password should be hidden in the array output
echo "\nPassword is hidden in array output: " . (isset($userArray['password']) ? 'No' : 'Yes') . "\n";
