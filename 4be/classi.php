<?php
class User {
    private $id;
    private $username;
    private $password;
    private $db;

    public function __construct($username, $password, $db) {
        $this->username = $this->sanitizeInput($username);
        $this->password = $password;
        $this->db = $db;
    }

    private function sanitizeInput($input) {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    public function authenticate() {
        $this->validateUsername($this->username);
        $this->validatePassword($this->password);
        $user = $this->db->select('users', ['username' => $this->username]);

        if ($user && password_verify($this->password, $user['password'])) {
            // Memorizza l'ID dell'utente
            $this->id = $user['id'];
            $_SESSION['user_id'] = $this->id;
            return true;
        }

        // Se l'autenticazione fallisce, incrementa il conteggio dei tentativi falliti
        $this->db->incrementFailedAttempts($this->username);
        return false;
    }

    private function validateUsername($username) {
        if (!preg_match('/^[a-zA-Z0-9_]{5,}$/', $username)) {
            throw new Exception('Invalid username');
        }
    }

    private function validatePassword($password) {
        if (!preg_match('/^.{8,}$/', $password)) {
            throw new Exception('Invalid password');
        }
    }

    public function register($username, $email, $password) {
        $username = $this->sanitizeInput($username);
        $this->validateUsername($username);
        $this->validateEmail($email);
        $this->validatePassword($password);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $this->db->insert('users', [
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
        ]);
    }

    private function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email');
        }
    }

    public function update($username, $email, $password) {
        $username = $this->sanitizeInput($username);
        $this->validateUsername($username);
        $this->validateEmail($email);
        $this->validatePassword($password);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $this->db->update('users', [
            'email' => $email,
            'password' => $hashedPassword,
        ], [
            'username' => $username,
        ]);
    }

    public function getId() {
        // Supponendo che $this->id contenga l'ID dell'utente
        return $this->id;
    }

    public function logout() {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
    }
}



class Database {
    private $connection;

    public function __construct($host, $db, $user, $pass) {
        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        } catch(PDOException $e) {
            $this->handleException($e);
        }
    }

    private function handleException($e) {
        error_log($e->getMessage());
        die('An error occurred');
    }

    public function insert($table, $data) {
        // Build SQL query
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($fields) VALUES ($placeholders)";

        // Prepare and execute SQL statement
        return $this->executeStatement($sql, $data);
    }

    public function select($table, $conditions = []) {
        // Build SQL query
        $sql = "SELECT * FROM $table";
        if (!empty($conditions)) {
            $fieldConditions = array_map(function ($field) {
                return "$field = :$field";
            }, array_keys($conditions));
            $sql .= ' WHERE ' . implode(' AND ', $fieldConditions);
        }

        // Prepare and execute SQL statement
        return $this->executeStatement($sql, $conditions)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($table, $data, $conditions) {
        // Build SQL query
        $fieldUpdates = array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($data));
        $sql = "UPDATE $table SET " . implode(', ', $fieldUpdates);
        if (!empty($conditions)) {
            $fieldConditions = array_map(function ($field) {
                return "$field = :where_$field";
            }, array_keys($conditions));
            $sql .= ' WHERE ' . implode(' AND ', $fieldConditions);
        }

        // Merge data and conditions arrays
        $data = array_merge($data, array_combine(
            array_map(function ($field) { return "where_$field"; }, array_keys($conditions)),
            $conditions
        ));

        // Prepare and execute SQL statement
        return $this->executeStatement($sql, $data)->rowCount();
    }

    public function delete($table, $conditions) {
        // Build SQL query
        $fieldConditions = array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($conditions));
        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $fieldConditions);

        // Prepare and execute SQL statement
        return $this->executeStatement($sql, $conditions)->rowCount();
    }

    private function executeStatement($sql, $params) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->handleException($e);
        }
    }
}
