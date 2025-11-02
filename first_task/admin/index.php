<?php include('header.php'); ?>

<?php
// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit();
}

// Handle order deletion
if(isset($_GET['delete_order'])) {
    $order_id = $_GET['delete_order'];
    
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->bind_param('i', $order_id);
    
    if($stmt->execute()) {
        $success_message = "Pedido deletado com sucesso!";
    } else {
        $error_message = "Erro ao deletar pedido.";
    }
}

// Pagination logic
$items_per_page = 5;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Get total number of orders
$total_orders_query = "SELECT COUNT(*) as total FROM orders";
$total_result = mysqli_query($conn, $total_orders_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_orders = $total_row['total'];
$total_pages = ceil($total_orders / $items_per_page);

// Get orders for current page with user information
$stmt = $conn->prepare("SELECT o.order_id, o.order_cost, o.order_status, o.order_date, o.shipping_city, o.shipping_uf, o.shipping_address, u.user_name, u.user_email 
                        FROM orders o 
                        LEFT JOIN users u ON o.user_id = u.user_id 
                        ORDER BY o.order_date DESC 
                        LIMIT ? OFFSET ?");
$stmt->bind_param('ii', $items_per_page, $offset);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<div class="container-fluid">
    <div class="row">
        
        <?php include('sidemenu.php'); ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard - Pedidos</h1>
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
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#ID</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Email</th>
                            <th scope="col">Valor</th>
                            <th scope="col">Status</th>
                            <th scope="col">Endereço</th>
                            <th scope="col">Data</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($orders_result->num_rows > 0): ?>
                            <?php while($order = $orders_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $order['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                                    <td>R$ <?php echo number_format($order['order_cost'], 2, ',', '.'); ?></td>
                                    <td>
                                        <?php 
                                        $status_class = '';
                                        $status_text = '';
                                        switch($order['order_status']) {
                                            case 'on_hold':
                                                $status_class = 'warning';
                                                $status_text = 'Em análise';
                                                break;
                                            case 'paid':
                                                $status_class = 'info';
                                                $status_text = 'Pago';
                                                break;
                                            case 'shipped':
                                                $status_class = 'primary';
                                                $status_text = 'Enviado';
                                                break;
                                            case 'delivered':
                                                $status_class = 'success';
                                                $status_text = 'Entregue';
                                                break;
                                            default:
                                                $status_class = 'secondary';
                                                $status_text = $order['order_status'];
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($order['shipping_address']) . ', ' . htmlspecialchars($order['shipping_city']) . '/' . htmlspecialchars($order['shipping_uf']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                    <td>
                                        <a href="edit_order.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <a href="index.php?delete_order=<?php echo $order['order_id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Tem certeza que deseja deletar este pedido?');">
                                            <i class="bi bi-trash"></i> Deletar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Nenhum pedido encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
                <nav aria-label="Navegação de páginas">
                    <ul class="pagination justify-content-center">
                        <!-- Previous button -->
                        <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">Anterior</a>
                        </li>
                        
                        <!-- Page numbers -->
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <!-- Next button -->
                        <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Próximo</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
            
        </main>
    </div>
</div>

</body>
</html>
