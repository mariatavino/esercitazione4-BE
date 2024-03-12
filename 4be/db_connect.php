<?php
$config = require_once 'config.php';

try {
    $conn = new PDO($config['dsn'].$config['database'], $config['user'], $config['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connessione fallita: " . $e->getMessage());
}
