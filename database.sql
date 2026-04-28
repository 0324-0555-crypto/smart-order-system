CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    role VARCHAR(50) DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    user_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Processing', 'Completed', 'Cancelled') DEFAULT 'Pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (username, password, full_name, role) VALUES
('admin', '$2y$10$lWszMLKyiCZpJ4VpyBTrgOZc7IKFvu/fBQhZrdYTHeVe81wpSfN2a', 'System Administrator', 'admin');

INSERT INTO customers (name, email, phone, address) VALUES
('Juan dela Cruz', 'juan@email.com', '09171234567', 'Laguna, Philippines'),
('Maria Santos', 'maria@email.com', '09281234567', 'Calamba, Laguna');

INSERT INTO orders (customer_id, user_id, product_name, quantity, price, status, notes) VALUES
(1, 1, 'Office Chair', 2, 2500.00, 'Completed', 'Rush delivery'),
(2, 1, 'Laptop Stand', 1, 850.00, 'Pending', 'Standard delivery');