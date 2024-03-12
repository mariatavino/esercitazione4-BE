<?php 
session_start(); 

// Aggiungi questo all'inizio
if (isset($_SESSION['error'])): ?>
<p><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
<?php endif;

if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']); // Rimuovi il messaggio dopo averlo visualizzato
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    if (preg_match('/^[a-zA-Z0-9_]{5,}$/', $username) && preg_match('/^.{8,}$/', $password)) {
        // Continua con il tuo codice...
    } else {
        $_SESSION['error'] = "Invalid username or password.";
        header('Location: /default/login.php');
    }
}
?>

<style>
nav {
  background-color: #333;
  overflow: hidden;
  margin-bottom: 2%;
}

nav ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
}

nav li {
  float: left;
}

nav a {
  display: block;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

nav a:hover {
  background-color: #111;
}
</style>

<nav>
  <ul>
    <li><a href="/default/login.php">Login</a></li>
    <li><a href="/default/register_process.php">Registrati</a></li>
    <li><a href="/default/admin_panel.php">Admin Panel</a></li>
  </ul>
</nav>
<form method="POST" action="/default/login.php">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    <input type="submit" value="Login">
</form>
