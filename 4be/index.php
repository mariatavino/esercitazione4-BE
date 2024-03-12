<?php
// Avvia la sessione
session_start();

// Include il file di connessione al database
require __DIR__ . '/db_connect.php';

// Include le classi necessarie
require __DIR__ . '/classi.php';

// Carica le configurazioni del database
$config = require __DIR__ . '/config.php';

try {
    // Creo un nuovo oggetto Database
    $db = new Database($config['host'], $config['database'], $config['user'], $config['password']);
} catch (PDOException $e) {
    // Log dell'errore e termina lo script
    error_log($e->getMessage());
    header('Location: /500.php');
    exit;
}

// Controllo il percorso richiesto
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Registra il percorso richiesto per il debug
error_log("Percorso richiesto: " . $path);

try {
    if ($path === '/default/login.php') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Creo un nuovo oggetto User
            $user = new User($_POST['username'], $_POST['password'], $db);
            if ($user->authenticate()) {
                $_SESSION['user_id'] = $user->getId();
                // Reindirizzo l'utente al pannello di amministrazione
                header('Location: /default/admin_panel.php');
                exit;
            } else {
                $_SESSION['error'] = "Invalid username or password.";
                header('Location: /default/login.php');
            }
        } else {
            // Mostra il modulo di login
            require __DIR__ . '/login.php';
        }
    } else if ($path === '/default/register_process.php') {
        require __DIR__ . '/register_process.php';
    } else if ($path === '/default/logout') {
        require __DIR__ . '/logout_process.php';
    } else if ($path === '/default/admin_panel.php') {
        if(!isset($_SESSION['user_id'])){
            header('Location: /default/login.php');
            exit;
        }
        require __DIR__ . '/admin_panel.php';
    } else {
        require __DIR__ . '/404.php';
    }
} catch (Exception $e) {
    // Log dell'errore e mostra la pagina di errore
    error_log($e->getMessage());
    header('Location: /500.php');
    exit;
}
