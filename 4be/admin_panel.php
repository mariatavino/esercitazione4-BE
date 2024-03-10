<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Per favore, effettua il login prima di accedere al pannello di configurazione.";
    header('Location: /default/login.php');
    exit;
}
// Display the admin panel
?>
<h1>Welcome to the Admin Panel</h1>
