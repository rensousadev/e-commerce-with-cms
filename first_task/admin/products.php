<?php include('header.php'); ?>

<?php
// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit();
}

// Handle product deletion
if(isset($_GET['delete_product'])) {
    $product_id = $_GET['delete_product'];
    
    // Get product images to delete files
    $stmt = $conn->prepare("SELECT product_image, product_image2, product_image3, product_image4 FROM products WHERE product_id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    // Delete product from database
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param('i', $product_id);
    
    if($stmt->execute()) {
        // Delete image files
        $images = [$product['product_image'], $product['product_image2'], $product['product_image3'], $product['product_image4']];
        foreach($images as $image) {
            if($image && file_exists('../assets/imgs/' . $image)) {
                unlink('../assets/imgs/' . $image);
            }
        }
        $success_message = "Produto deletado com sucesso!";
    } else {
        $error_message = "Erro ao deletar produto.";
    }
}

// Pagination logic
$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Get total number of products
$total_products_query = "SELECT COUNT(*) as total FROM products";
$total_result = mysqli_query($conn, $total_products_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $items_per_page);

// Get products for current page
$stmt = $conn->prepare("SELECT * FROM products ORDER BY product_id DESC LIMIT ? OFFSET ?");
$stmt->bind_param('ii', $items_per_page, $offset);
$stmt->execute();
$products_result = $stmt->get_result();
?>

<div class="container-fluid">
    <div class="row">
        
        <?php include('sidemenu.php'); ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Produtos</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="add_product.php" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle"></i> Adicionar Produto
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
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#ID</th>
                            <th scope="col">Imagem</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Categoria</th>
                            <th scope="col">Preço</th>
                            <th scope="col">Oferta</th>
                            <th scope="col">Cor</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($products_result->num_rows > 0): ?>
                            <?php while($product = $products_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $product['product_id']; ?></td>
                                    <td>
                                        <?php if($product['product_image']): ?>
                                            <img src="../assets/imgs/<?php echo $product['product_image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #ddd;"></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['product_category']); ?></td>
                                    <td>R$ <?php echo number_format($product['product_price'], 2, ',', '.'); ?></td>
                                    <td>
                                        <?php if($product['product_special_offer'] > 0): ?>
                                            <span class="badge bg-success"><?php echo $product['product_special_offer']; ?>%</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Não</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['product_color']); ?></td>
                                    <td>
                                        <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="edit_images.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-info">
                                            <i class="bi bi-images"></i>
                                        </a>
                                        <a href="products.php?delete_product=<?php echo $product['product_id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Tem certeza que deseja deletar este produto?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Nenhum produto encontrado.</td>
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
