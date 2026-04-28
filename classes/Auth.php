<?php
/**
 * Auth.php
 * Handles session-based authentication
 * Smart Order Management System
 */
class Auth {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Login: verify credentials and start session
    public function login($username, $password) {
        require_once 'User.php';
        $user = new User($this->conn);
        $user->username = $username;

        if ($user->usernameExists()) {
            if (password_verify($password, $user->password)) {
                $_SESSION['user_id']   = $user->id;
                $_SESSION['username']  = $user->username;
                $_SESSION['full_name'] = $user->full_name;
                $_SESSION['role']      = $user->role;
                return true;
            }
        }
        return false;
    }

    // Logout: destroy session
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: login.php');
        exit;
    }

    // Check if user is logged in (protect pages)
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
    }

    // Check admin role
    public static function requireAdmin() {
        self::check();
        if ($_SESSION['role'] !== 'admin') {
            header('Location: dashboard.php?error=Access denied. Admin only.');
            exit;
        }
    }
}
?>
