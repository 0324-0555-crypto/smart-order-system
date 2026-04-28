<?php
/**
 * Order.php
 * Handles order CRUD operations (Transaction Class)
 * Smart Order Management System
 */
class Order {
    private $conn;
    private $table = 'orders';

    public $id;
    public $customer_id;
    public $user_id;
    public $product_name;
    public $quantity;
    public $price;
    public $status;
    public $notes;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Read all orders with JOIN (Transaction Flow)
    public function readAll() {
        $query = "SELECT o.id, o.product_name, o.quantity, o.price, o.status, o.notes, o.created_at,
                         c.name AS customer_name, c.email AS customer_email,
                         u.full_name AS created_by, u.username
                  FROM {$this->table} o
                  INNER JOIN customers c ON o.customer_id = c.id
                  INNER JOIN users u ON o.user_id = u.id
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Read one order
    public function readOne() {
        $query = "SELECT o.*, c.name AS customer_name, u.full_name AS created_by
                  FROM {$this->table} o
                  INNER JOIN customers c ON o.customer_id = c.id
                  INNER JOIN users u ON o.user_id = u.id
                  WHERE o.id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->id]);
        $row = $stmt->fetch();
        if ($row) {
            $this->customer_id  = $row['customer_id'];
            $this->user_id      = $row['user_id'];
            $this->product_name = $row['product_name'];
            $this->quantity     = $row['quantity'];
            $this->price        = $row['price'];
            $this->status       = $row['status'];
            $this->notes        = $row['notes'];
        }
        return $row;
    }

    // Create order
    public function create() {
        $query = "INSERT INTO {$this->table} (customer_id, user_id, product_name, quantity, price, status, notes)
                  VALUES (:customer_id, :user_id, :product_name, :quantity, :price, :status, :notes)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id',  $this->customer_id);
        $stmt->bindParam(':user_id',      $this->user_id);
        $stmt->bindParam(':product_name', htmlspecialchars(strip_tags($this->product_name)));
        $stmt->bindParam(':quantity',     $this->quantity);
        $stmt->bindParam(':price',        $this->price);
        $stmt->bindParam(':status',       $this->status);
        $stmt->bindParam(':notes',        htmlspecialchars(strip_tags($this->notes)));
        return $stmt->execute();
    }

    // Update order
    public function update() {
        $query = "UPDATE {$this->table}
                  SET customer_id=:customer_id, product_name=:product_name,
                      quantity=:quantity, price=:price, status=:status, notes=:notes
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id',  $this->customer_id);
        $stmt->bindParam(':product_name', htmlspecialchars(strip_tags($this->product_name)));
        $stmt->bindParam(':quantity',     $this->quantity);
        $stmt->bindParam(':price',        $this->price);
        $stmt->bindParam(':status',       $this->status);
        $stmt->bindParam(':notes',        htmlspecialchars(strip_tags($this->notes)));
        $stmt->bindParam(':id',           $this->id);
        return $stmt->execute();
    }

    // Delete order
    public function delete() {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    // Count total orders
    public function countAll() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM {$this->table}");
        return $stmt->fetchColumn();
    }

    // Sum total revenue
    public function totalRevenue() {
        $stmt = $this->conn->query("SELECT SUM(price * quantity) FROM {$this->table} WHERE status = 'Completed'");
        return $stmt->fetchColumn() ?? 0;
    }

    // Count by status
    public function countByStatus($status) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM {$this->table} WHERE status = ?");
        $stmt->execute([$status]);
        return $stmt->fetchColumn();
    }

    // Recent orders (dashboard)
    public function recentOrders($limit = 5) {
        $query = "SELECT o.id, o.product_name, o.price, o.quantity, o.status, o.created_at,
                         c.name AS customer_name, u.full_name AS created_by
                  FROM {$this->table} o
                  INNER JOIN customers c ON o.customer_id = c.id
                  INNER JOIN users u ON o.user_id = u.id
                  ORDER BY o.created_at DESC LIMIT :lim";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }
}
?>
