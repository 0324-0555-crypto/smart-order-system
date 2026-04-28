<?php
/**
 * Customer.php
 * Handles customer CRUD operations (Main Entity)
 * Smart Order Management System
 */
class Customer {
    private $conn;
    private $table = 'customers';

    public $id;
    public $name;
    public $email;
    public $phone;
    public $address;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all customers
    public function readAll() {
        $query = "SELECT c.*, COUNT(o.id) AS total_orders
                  FROM {$this->table} c
                  LEFT JOIN orders o ON c.id = o.customer_id
                  GROUP BY c.id
                  ORDER BY c.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read one customer
    public function readOne() {
        $query = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
        $row = $stmt->fetch();
        if ($row) {
            $this->name    = $row['name'];
            $this->email   = $row['email'];
            $this->phone   = $row['phone'];
            $this->address = $row['address'];
        }
    }

    // Create customer
    public function create() {
        $query = "INSERT INTO {$this->table} (name, email, phone, address)
                  VALUES (:name, :email, :phone, :address)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name',    htmlspecialchars(strip_tags($this->name)));
        $stmt->bindParam(':email',   htmlspecialchars(strip_tags($this->email)));
        $stmt->bindParam(':phone',   htmlspecialchars(strip_tags($this->phone)));
        $stmt->bindParam(':address', htmlspecialchars(strip_tags($this->address)));
        return $stmt->execute();
    }

    // Update customer
    public function update() {
        $query = "UPDATE {$this->table}
                  SET name=:name, email=:email, phone=:phone, address=:address
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name',    htmlspecialchars(strip_tags($this->name)));
        $stmt->bindParam(':email',   htmlspecialchars(strip_tags($this->email)));
        $stmt->bindParam(':phone',   htmlspecialchars(strip_tags($this->phone)));
        $stmt->bindParam(':address', htmlspecialchars(strip_tags($this->address)));
        $stmt->bindParam(':id',      $this->id);
        return $stmt->execute();
    }

    // Delete customer
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Count total customers
    public function countAll() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM {$this->table}");
        return $stmt->fetchColumn();
    }

    // Get customers for dropdown
    public function getDropdown() {
        $stmt = $this->conn->query("SELECT id, name FROM {$this->table} ORDER BY name ASC");
        return $stmt->fetchAll();
    }
}
?>
