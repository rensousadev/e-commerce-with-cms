<?php
session_start();
include('../server/connection.php');

// Verify if transaction_id and order_id exist
if (!isset($_GET['transaction_id']) || !isset($_GET['order_id'])) {
    header('Location: ../account.php');
    exit;
}

$transaction_id = $_GET['transaction_id'];
$order_id = $_GET['order_id'];
$payment_date = date('Y-m-d H:i:s');

// Verify if user is logged in and order belongs to user
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Verify order exists and belongs to user
$stmt_verify = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt_verify->bind_param('ii', $order_id, $user_id);
$stmt_verify->execute();
$result = $stmt_verify->get_result();

if ($result->num_rows == 0) {
    header('Location: ../account.php?message=Pedido não encontrado');
    exit;
}

$order = $result->fetch_assoc();

// Check if already paid
if ($order['order_status'] == 'paid') {
    header('Location: ../account.php?message=Pedido já foi pago anteriormente');
    exit;
}

// Update order status to paid
$stmt_update = $conn->prepare("UPDATE orders SET order_status = 'paid' WHERE order_id = ?");
$stmt_update->bind_param('i', $order_id);

if ($stmt_update->execute()) {
    
    // Insert payment record
    $stmt_payment = $conn->prepare("INSERT INTO payments (order_id, user_id, transaction_id, payment_date) VALUES (?, ?, ?, ?)");
    $stmt_payment->bind_param('iiss', $order_id, $user_id, $transaction_id, $payment_date);
    
    if ($stmt_payment->execute()) {
        // Clear order_id from session
        unset($_SESSION['order_id']);
        
        // Redirect to account with success message
        header('Location: ../account.php?payment_message=Pagamento confirmado com sucesso! Pedido #' . $order_id);
        exit;
    } else {
        header('Location: ../account.php?payment_message=Erro ao registrar pagamento. Contate o suporte.');
        exit;
    }
    
} else {
    header('Location: ../account.php?payment_message=Erro ao atualizar status do pedido. Tente novamente.');
    exit;
}
?>
