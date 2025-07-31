<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get':
        echo json_encode([
            'success' => true,
            'cart' => $_SESSION['cart'] ?? []
        ]);
        break;

    case 'add':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['product_name'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data']);
            exit;
        }

        // Always get price from DB for security
        $stmt = mysqli_prepare($conn, "SELECT id, price FROM products WHERE name = ?");
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Database error']);
            exit;
        }
        mysqli_stmt_bind_param($stmt, "s", $data['product_name']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($result);

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $_SESSION['cart'][] = [
            'product_id' => $product['id'],
            'product_name' => $data['product_name'],
            'price' => (float)$product['price'],
            'topping' => $data['topping'] ?? '',
            'quantity' => (int)($data['quantity'] ?? 1)
        ];

        echo json_encode(['success' => true, 'message' => 'Item added to cart']);
        break;

    case 'remove':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['index'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $index = $data['index'];
        if (isset($_SESSION['cart'][$index])) {
            array_splice($_SESSION['cart'], $index, 1);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
        }
        break;

    case 'update':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['index']) || !isset($data['quantity'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            exit;
        }

        $index = $data['index'];
        $quantity = max(1, intval($data['quantity']));

        if (isset($_SESSION['cart'][$index])) {
            $_SESSION['cart'][$index]['quantity'] = $quantity;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
        }
        break;

    case 'clear':
        $_SESSION['cart'] = [];
        echo json_encode(['success' => true]);
        break;

    case 'checkout':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Please login to checkout']);
            exit;
        }

        if (empty($_SESSION['cart'])) {
            echo json_encode(['success' => false, 'message' => 'Cart is empty']);
            exit;
        }

        try {
            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                if (!isset($item['product_id'], $item['price'], $item['quantity'])) {
                    throw new Exception('Incomplete cart item data');
                }
                $total += $item['price'] * $item['quantity'];
            }

            // Start transaction
            mysqli_begin_transaction($conn);

            // Create order
            $stmt = mysqli_prepare($conn, "INSERT INTO orders (user_id, total_amount, created_at) VALUES (?, ?, NOW())");
            if (!$stmt) {
                throw new Exception('Failed to create order: ' . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($stmt, "id", $_SESSION['user_id'], $total);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception('Failed to create order: ' . mysqli_stmt_error($stmt));
            }
            $orderId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            // Add order items
            $stmt = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
            if (!$stmt) {
                throw new Exception('Failed to prepare order items: ' . mysqli_error($conn));
            }

            foreach ($_SESSION['cart'] as $item) {
                mysqli_stmt_bind_param($stmt, "iii", $orderId, $item['product_id'], $item['quantity']);
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception('Failed to add order item: ' . mysqli_stmt_error($stmt));
                }
            }
            mysqli_stmt_close($stmt);

            // Commit transaction
            mysqli_commit($conn);

            // Clear cart
            $_SESSION['cart'] = [];
            
            echo json_encode([
                'success' => true, 
                'message' => 'Order placed successfully',
                'order_id' => $orderId
            ]);

        } catch (Exception $e) {
            // Rollback transaction on error
            mysqli_rollback($conn);
            error_log('Checkout error: ' . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to process order: ' . $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>