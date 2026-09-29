<?php
// Connect to the MySQL database.
function connect_to_database(): PDO
{
    $host = '127.0.0.1';
    $database = 'waste_food_db';
    $username = 'root';
    $password = '';

    $connectionString = "mysql:host=$host;dbname=$database;charset=utf8mb4";

    return new PDO($connectionString, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
}