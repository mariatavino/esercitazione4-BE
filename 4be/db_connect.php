<?php
$host = 'localhost'; // sostituisci con il tuo hostname
$db = 'esempi'; // sostituisci con il nome del tuo database
$user = 'root'; // sostituisci con il tuo username del DB
$pass = ''; // sostituisci con la tua password del DB

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connessione fallita: " . $e->getMessage());
}
