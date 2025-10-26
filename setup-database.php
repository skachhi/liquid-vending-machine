<?php
// Database setup script for Liquid Vending Machine
// Compatible with PHP 5.6.12

$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to MySQL server successfully.\n";
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS liquid_vending");
    echo "Database 'liquid_vending' created successfully.\n";
    
    // Use the database
    $pdo->exec("USE liquid_vending");
    
    // Create products table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            category VARCHAR(50) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            stock INT NOT NULL DEFAULT 0,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "Products table created successfully.\n";
    
    // Create orders table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id INT NOT NULL,
            product_name VARCHAR(100) NOT NULL,
            quantity INT NOT NULL,
            total_price DECIMAL(10,2) NOT NULL,
            payment_method ENUM('cash', 'card', 'upi') NOT NULL,
            status ENUM('pending', 'completed', 'cancelled') DEFAULT 'completed',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id)
        )
    ");
    echo "Orders table created successfully.\n";
    
    // Create admins table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Admins table created successfully.\n";
    
    // Insert sample products
    $products = [
        ['Coffee', 'Coffee', 25.00, 50, 'Fresh brewed coffee'],
        ['Tea', 'Tea', 15.00, 30, 'Hot tea'],
        ['Orange Juice', 'Juice', 35.00, 25, 'Fresh orange juice'],
        ['Apple Juice', 'Juice', 30.00, 20, 'Fresh apple juice'],
        ['Mineral Water', 'Water', 20.00, 100, 'Pure mineral water'],
        ['Coca Cola', 'Soda', 30.00, 40, 'Classic Coca Cola'],
        ['Pepsi', 'Soda', 30.00, 35, 'Classic Pepsi'],
        ['Red Bull', 'Energy Drink', 50.00, 15, 'Energy drink']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, description) VALUES (?, ?, ?, ?, ?)");
    foreach ($products as $product) {
        $stmt->execute($product);
    }
    echo "Sample products inserted successfully.\n";
    
    // Insert default admin
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admins (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Admin User', 'admin@vending.com', $adminPassword, 'admin']);
    echo "Default admin created successfully.\n";
    
    echo "\nDatabase setup completed successfully!\n";
    echo "Default admin credentials:\n";
    echo "Email: admin@vending.com\n";
    echo "Password: admin123\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
