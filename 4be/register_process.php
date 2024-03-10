<?php
session_start();
require __DIR__ . '/db_connect.php';
require __DIR__ . '/classi.php';

$db = new Database($host, $db, $user, $pass);
$user = new User($_POST['username'] ?? '', $_POST['password'] ?? '', $db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $user->register($_POST['username'], $_POST['email'], $_POST['password']);
        header('Location: /default/login.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: /default/register_process.php');
        exit;
    }
} else {
    // Mostra il modulo di registrazione
    if (isset($_SESSION['error'])): ?>
        <p><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <form method="POST" action="/default/register_process.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <input type="submit" value="Register">
    </form>
<?php
}

