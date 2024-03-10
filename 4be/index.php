<?php
// Avvia la sessione
session_start();

// Include il file di connessione al database
require __DIR__ . '/db_connect.php';

// Include le classi necessarie
require __DIR__ . '/classi.php';

try {
    // Creo un nuovo oggetto Database
    $db = new Database($host, $db, $user, $pass);
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
    switch ($path) {
        case '/default/':
        case '/default/login.php':
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
            break;
        case '/default/register_process.php':
            require __DIR__ . '/register_process.php';
            break;
        case '/default/logout':
            require __DIR__ . '/logout_process.php';
            break;
        case '/default/admin_panel.php':
            if(!isset($_SESSION['user_id'])){
                header('Location: /default/login.php');
                exit;
            }
            require __DIR__ . '/admin_panel.php';
            break;
        default:
            require __DIR__ . '/default/404.php';
            break;
    }
} catch (Exception $e) {
    // Log dell'errore e mostra la pagina di errore
    error_log($e->getMessage());
    header('Location: /500.php');
    exit;
}
