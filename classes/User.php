<?php
/**
 * User.php
 * Handles user data operations
 * Smart Order Management System
 */
class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $username;
    public $password;
    public $full_name;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all users
    public function readAll() {
        $query = "SELECT id, username, full_name, role, created_at FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read single user
    public function readOne() {
        $query = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
        $row = $stmt->fetch();
        if ($row) {
            $this->username  = $row['username'];
            $this->full_name = $row['full_name'];
            $this->role      = $row['role'];
        }
    }

    // Create user
    public function create() {
        $query = "INSERT INTO {$this->table} (username, password, full_name, role)
                  VALUES (:username, :password, :full_name, :role)";
        $stmt = $this->conn->prepare($query);
        $hashed = password_hash($this->password, PASSWORD_DEFAULT);
        $stmt->bindParam(':username',  htmlspecialchars(strip_tags($this->username)));
        $stmt->bindParam(':password',  $hashed);
        $stmt->bindParam(':full_name', htmlspecialchars(strip_tags($this->full_name)));
        $stmt->bindParam(':role',      htmlspecialchars(strip_tags($this->role)));
        return $stmt->execute();
    }

    // Update user
    public function update() {
        if (!empty($this->password)) {
            $query = "UPDATE {$this->table} SET username=:username, password=:password, full_name=:full_name, role=:role WHERE id=:id";
        } else {
            $query = "UPDATE {$this->table} SET username=:username, full_name=:full_name, role=:role WHERE id=:id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username',  htmlspecialchars(strip_tags($this->username)));
        $stmt->bindParam(':full_name', htmlspecialchars(strip_tags($this->full_name)));
        $stmt->bindParam(':role',      htmlspecialchars(strip_tags($this->role)));
        $stmt->bindParam(':id',        $this->id);
        if (!empty($this->password)) {
            $hashed = password_hash($this->password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashed);
        }
        return $stmt->execute();
    }

    // Delete user
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Check if username exists
    public function usernameExists() {
        $query = "SELECT id, full_name, password, role FROM {$this->table} WHERE username = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->username]);
        $row = $stmt->fetch();
        if ($row) {
            $this->id        = $row['id'];
            $this->full_name = $row['full_name'];
            $this->password  = $row['password'];
            $this->role      = $row['role'];
            return true;
        }
        return false;
    }
}
?>
