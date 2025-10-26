<?php
// Simple PHP API for Liquid Vending Machine
// Compatible with PHP 5.6.12

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Database configuration
$host = 'localhost';
$dbname = 'liquid_vending';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api/', '', $path);

// Route handling
switch ($method) {
    case 'GET':
        if ($path === 'products') {
            getProducts($pdo);
        } elseif ($path === 'orders') {
            getOrders($pdo);
        } else {
            echo json_encode(['error' => 'Not found']);
        }
        break;
        
    case 'POST':
        if ($path === 'orders') {
            createOrder($pdo);
        } elseif ($path === 'admin/login') {
            adminLogin($pdo);
        } else {
            echo json_encode(['error' => 'Not found']);
        }
        break;
        
    default:
        echo json_encode(['error' => 'Method not allowed']);
}

// Functions
function getProducts($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM products WHERE stock > 0 ORDER BY category, name");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($products);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function createOrder($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['product_id']) || !isset($input['quantity'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        return;
    }
    
    try {
        $pdo->beginTransaction();
        
        // Get product details
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$input['product_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            return;
        }
        
        // Check stock
        if ($product['stock'] < $input['quantity']) {
            echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
            return;
        }
        
        $totalPrice = $product['price'] * $input['quantity'];
        
        // Create order
        $stmt = $pdo->prepare("INSERT INTO orders (product_id, product_name, quantity, total_price, payment_method, status) VALUES (?, ?, ?, ?, ?, 'completed')");
        $stmt->execute([
            $input['product_id'],
            $product['name'],
            $input['quantity'],
            $totalPrice,
            $input['payment_method'] ?? 'cash'
        ]);
        
        // Update stock
        $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->execute([$input['quantity'], $input['product_id']]);
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Order created successfully']);
        
    } catch(PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function getOrders($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 100");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($orders);
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function adminLogin($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['email']) || !isset($input['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$input['email']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($input['password'], $admin['password'])) {
            // Simple token generation
            $token = base64_encode($admin['email'] . ':' . time());
            echo json_encode(['success' => true, 'token' => $token, 'admin' => $admin]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
