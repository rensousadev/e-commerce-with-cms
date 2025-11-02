<?php
session_start();
include('server/connection.php');

// Verify if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Verify if order_id exists
if (!isset($_POST['order_id']) && !isset($_GET['order_id'])) {
    header('Location: account.php');
    exit;
}

$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Get order details
$stmt_order = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt_order->bind_param('ii', $order_id, $user_id);
$stmt_order->execute();
$order_result = $stmt_order->get_result();

if ($order_result->num_rows == 0) {
    header('Location: account.php');
    exit;
}

$order = $order_result->fetch_assoc();

// Get order items with product details
$stmt_items = $conn->prepare("SELECT oi.*, p.product_name, p.product_price 
                               FROM order_items oi 
                               LEFT JOIN products p ON oi.product_id = p.product_id 
                               WHERE oi.order_id = ?");
$stmt_items->bind_param('i', $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();

include('layouts/header.php');
?>

<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Detalhes do Pedido</h2>
        <hr class="mx-auto">
    </div>
    
    <div class="mx-auto container">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Informações do Pedido</h5>
                <p><strong>Pedido #:</strong> <?php echo $order['order_id']; ?></p>
                <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></p>
                <p><strong>Status:</strong> 
                    <?php if ($order['order_status'] == 'paid') { ?>
                        <span class="badge bg-success">Pago</span>
                    <?php } else { ?>
                        <span class="badge bg-warning">Não Pago</span>
                    <?php } ?>
                </p>
                <p><strong>Total:</strong> R$ <?php echo number_format($order['order_cost'], 2, ',', '.'); ?></p>
            </div>
            
            <div class="col-md-6">
                <h5>Informações de Entrega</h5>
                <p><strong>Telefone:</strong> <?php echo $order['user_phone']; ?></p>
                <p><strong>Cidade:</strong> <?php echo $order['user_city']; ?></p>
                <p><strong>Endereço:</strong> <?php echo $order['user_address']; ?></p>
            </div>
        </div>
        
        <h5>Itens do Pedido</h5>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items_result->fetch_assoc()) { ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="assets/imgs/<?php echo $item['product_image']; ?>" 
                                     alt="<?php echo $item['product_name']; ?>" 
                                     style="width: 60px; height: 60px; object-fit: cover; margin-right: 15px;">
                                <span><?php echo $item['product_name']; ?></span>
                            </div>
                        </td>
                        <td>R$ <?php echo number_format($item['product_price'], 2, ',', '.'); ?></td>
                        <td><?php echo $item['product_quantity']; ?></td>
                        <td>R$ <?php echo number_format($item['product_price'] * $item['product_quantity'], 2, ',', '.'); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td><strong>R$ <?php echo number_format($order['order_cost'], 2, ',', '.'); ?></strong></td>
                </tr>
            </tfoot>
        </table>
        
        <div class="mt-4">
            <?php if ($order['order_status'] == 'not paid') { ?>
                <form method="GET" action="payment.php">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-credit-card"></i> Pagar Agora
                    </button>
                </form>
            <?php } else { ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Este pedido já foi pago!
                </div>
            <?php } ?>
            
            <a href="account.php" class="btn btn-secondary mt-3">
                <i class="fas fa-arrow-left"></i> Voltar para Minha Conta
            </a>
        </div>
    </div>
</section>

<?php include('layouts/footer.php'); ?>
