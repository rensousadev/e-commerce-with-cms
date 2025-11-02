<?php 
include('layouts/header.php'); 
include('server/connection.php'); 
?>

<?php
// Check if user is logged in
if(!isset($_SESSION['logged_in'])) {
    header('location: login.php');
    exit();
}

// Handle password change
if(isset($_POST['change_password'])) {
    $current_password = md5($_POST['current_password']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['user_id'];
    
    // Verify current password
    $stmt = $conn->prepare("SELECT user_password FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if($user['user_password'] !== $current_password) {
        $password_error = "Senha atual incorreta!";
    } else if($new_password !== $confirm_password) {
        $password_error = "As senhas não coincidem!";
    } else if(strlen($new_password) < 6) {
        $password_error = "A senha deve ter no mínimo 6 caracteres!";
    } else {
        $hashed_password = md5($new_password);
        $stmt = $conn->prepare("UPDATE users SET user_password = ? WHERE user_id = ?");
        $stmt->bind_param('si', $hashed_password, $user_id);
        
        if($stmt->execute()) {
            $password_success = "Senha alterada com sucesso!";
        } else {
            $password_error = "Erro ao alterar senha!";
        }
    }
}

// Get user orders
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<!-- Account Section -->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="font-weight-bold">Minha Conta</h2>
        <hr class="mx-auto">
    </div>
    
    <div class="mx-auto container">
        <?php if(isset($_GET['payment_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['payment_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <div class="row">
            <!-- Account Info -->
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Informações da Conta</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Nome:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                        <hr>
                        <a href="logout.php" class="btn btn-danger">Sair</a>
                    </div>
                </div>
                
                <!-- Change Password -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Alterar Senha</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($password_success)): ?>
                            <div class="alert alert-success"><?php echo $password_success; ?></div>
                        <?php endif; ?>
                        
                        <?php if(isset($password_error)): ?>
                            <div class="alert alert-danger"><?php echo $password_error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="form-group mb-3">
                                <label>Senha Atual</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>Nova Senha</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label>Confirmar Nova Senha</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                            
                            <button type="submit" name="change_password" class="btn btn-primary">Alterar Senha</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Orders -->
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Meus Pedidos</h4>
                    </div>
                    <div class="card-body">
                        <?php if($orders_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Pedido #</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                            <th>Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($order = $orders_result->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <form method="POST" action="order_details.php" style="display: inline;">
                                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                        <button type="submit" class="btn btn-link btn-sm p-0" style="text-decoration: none;">
                                                            #<?php echo $order['order_id']; ?>
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>R$ <?php echo number_format($order['order_cost'], 2, ',', '.'); ?></td>
                                                <td>
                                                    <?php 
                                                    $status_labels = [
                                                        'not paid' => '<span class="badge bg-warning">Não Pago</span>',
                                                        'on_hold' => '<span class="badge bg-secondary">Em análise</span>',
                                                        'paid' => '<span class="badge bg-success">Pago</span>',
                                                        'shipped' => '<span class="badge bg-primary">Enviado</span>',
                                                        'delivered' => '<span class="badge bg-info">Entregue</span>'
                                                    ];
                                                    echo $status_labels[$order['order_status']] ?? '<span class="badge bg-secondary">' . $order['order_status'] . '</span>';
                                                    ?>
                                                </td>
                                                <td><?php echo date('d/m/Y', strtotime($order['order_date'])); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">Você ainda não realizou nenhum pedido.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
    padding: 15px;
}

.card-header h4 {
    margin: 0;
    font-size: 1.2rem;
}

.form-group label {
    font-weight: bold;
}
</style>

<?php include('layouts/footer.php'); ?>
