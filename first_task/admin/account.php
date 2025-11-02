<?php include('header.php'); ?>

<?php
// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit();
}

// Pagination logic
$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Get total number of users
$total_users_query = "SELECT COUNT(*) as total FROM users";
$total_result = mysqli_query($conn, $total_users_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_users = $total_row['total'];
$total_pages = ceil($total_users / $items_per_page);

// Get users for current page
$stmt = $conn->prepare("SELECT user_id, user_name, user_email FROM users ORDER BY user_id DESC LIMIT ? OFFSET ?");
$stmt->bind_param('ii', $items_per_page, $offset);
$stmt->execute();
$users_result = $stmt->get_result();
?>

<div class="container-fluid">
    <div class="row">
        
        <?php include('sidemenu.php'); ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Usuários Cadastrados</h1>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#ID</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Email</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($users_result->num_rows > 0): ?>
                            <?php while($user = $users_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $user['user_id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['user_email']); ?></td>
                                    <td>
                                        <a href="user_orders.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-info">
                                            <i class="bi bi-bag"></i> Ver Pedidos
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Nenhum usuário encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
                <nav aria-label="Navegação de páginas">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">Anterior</a>
                        </li>
                        
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
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
