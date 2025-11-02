<?php
session_start();
include('../server/connection.php');

// Verify if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: ../checkout.php?message=Por favor, faça login para continuar com o pedido');
    exit;
}

// Verify if cart exists
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: ../index.php');
    exit;
}

// Get form data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $address = $_POST['address'];
    
    $user_id = $_SESSION['user_id'];
    $order_cost = $_SESSION['total'];
    $order_status = 'not paid';
    $order_date = date('Y-m-d H:i:s');
    
    // Insert order into orders table
    $stmt = $conn->prepare("INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_city, user_address, order_date) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('dsissss', $order_cost, $order_status, $user_id, $phone, $city, $address, $order_date);
    
    if ($stmt->execute()) {
        // Get the order_id of the just inserted order
        $order_id = $stmt->insert_id;
        
        // Insert each cart item into order_items table
        foreach ($_SESSION['cart'] as $product_id => $product) {
            $product_name = $product['product_name'];
            $product_price = $product['product_price'];
            $product_quantity = $product['product_quantity'];
            $product_image = $product['product_image'];
            
            $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, product_price, product_quantity, user_id, order_date) 
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_items->bind_param('iissdiss', $order_id, $product_id, $product_name, $product_image, $product_price, $product_quantity, $user_id, $order_date);
            $stmt_items->execute();
        }
        
        // Store order_id in session
        $_SESSION['order_id'] = $order_id;
        
        // Clear the cart
        unset($_SESSION['cart']);
        unset($_SESSION['total']);
        unset($_SESSION['quantity']);
        
        // Redirect to payment page
        header('Location: ../payment.php?order_id=' . $order_id);
        exit;
        
    } else {
        header('Location: ../checkout.php?message=Erro ao processar pedido. Tente novamente.');
        exit;
    }
    
} else {
    header('Location: ../checkout.php');
    exit;
}
?>
