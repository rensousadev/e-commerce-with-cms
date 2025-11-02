<?php include('header.php'); ?>

<?php
// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit();
}

// Check if order ID is provided
if(!isset($_GET['id'])) {
    header('location: index.php');
    exit();
}

$order_id = $_GET['id'];

// Handle form submission to update order status
if(isset($_POST['update_order'])) {
    $new_status = $_POST['order_status'];
    
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param('si', $new_status, $order_id);
    
    if($stmt->execute()) {
        $success_message = "Status do pedido atualizado com sucesso!";
    } else {
        $error_message = "Erro ao atualizar status do pedido.";
    }
}

// Get order details
$stmt = $conn->prepare("SELECT o.order_id, o.order_cost, o.order_status, o.order_date, o.shipping_city, o.shipping_uf, o.shipping_address, u.user_name, u.user_email 
                        FROM orders o 
                        LEFT JOIN users u ON o.user_id = u.user_id 
                        WHERE o.order_id = ?");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    header('location: index.php');
    exit();
}

$order = $result->fetch_assoc();
?>

<div class="container-fluid">
    <div class="row">
        
        <?php include('sidemenu.php'); ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Editar Pedido #<?php echo $order['order_id']; ?></h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            
            <?php if(isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if(isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informações do Pedido</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">ID do Pedido:</label>
                                    <p><?php echo $order['order_id']; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Data do Pedido:</label>
                                    <p><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></p>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Cliente:</label>
                                    <p><?php echo htmlspecialchars($order['user_name']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Email:</label>
                                    <p><?php echo htmlspecialchars($order['user_email']); ?></p>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Endereço de Entrega:</label>
                                    <p><?php echo htmlspecialchars($order['shipping_address']) . ', ' . htmlspecialchars($order['shipping_city']) . '/' . htmlspecialchars($order['shipping_uf']); ?></p>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Valor Total:</label>
                                    <p class="fs-5 text-success">R$ <?php echo number_format($order['order_cost'], 2, ',', '.'); ?></p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="order_status" class="form-label fw-bold">Status do Pedido:</label>
                                    <select class="form-select" id="order_status" name="order_status" required>
                                        <option value="on_hold" <?php echo $order['order_status'] == 'on_hold' ? 'selected' : ''; ?>>Em análise</option>
                                        <option value="paid" <?php echo $order['order_status'] == 'paid' ? 'selected' : ''; ?>>Pago</option>
                                        <option value="shipped" <?php echo $order['order_status'] == 'shipped' ? 'selected' : ''; ?>>Enviado</option>
                                        <option value="delivered" <?php echo $order['order_status'] == 'delivered' ? 'selected' : ''; ?>>Entregue</option>
                                    </select>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" name="update_order" class="btn btn-primary btn-lg">
                                        <i class="bi bi-save"></i> Atualizar Status
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Status Atual</h5>
                        </div>
                        <div class="card-body text-center">
                            <?php 
                            $status_class = '';
                            $status_text = '';
                            $status_icon = '';
                            switch($order['order_status']) {
                                case 'on_hold':
                                    $status_class = 'warning';
                                    $status_text = 'Em análise';
                                    $status_icon = 'clock';
                                    break;
                                case 'paid':
                                    $status_class = 'info';
                                    $status_text = 'Pago';
                                    $status_icon = 'credit-card';
                                    break;
                                case 'shipped':
                                    $status_class = 'primary';
                                    $status_text = 'Enviado';
                                    $status_icon = 'truck';
                                    break;
                                case 'delivered':
                                    $status_class = 'success';
                                    $status_text = 'Entregue';
                                    $status_icon = 'check-circle';
                                    break;
                                default:
                                    $status_class = 'secondary';
                                    $status_text = $order['order_status'];
                                    $status_icon = 'question-circle';
                            }
                            ?>
                            <i class="bi bi-<?php echo $status_icon; ?> text-<?php echo $status_class; ?>" style="font-size: 3rem;"></i>
                            <h3 class="mt-3">
                                <span class="badge bg-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            
        </main>
    </div>
</div>

</body>
</html>
