<?php include('layouts/header.php'); ?>
<?php include('server/connection.php'); ?>

<?php
// Pagination logic
$items_per_page = 8;
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

<!-- Products Section -->
<section id="products" class="my-5 pb-5">
    <div class="container text-center mt-5 py-5">
        <h3>Nossos Produtos</h3>
        <hr class="mx-auto">
        <p>Confira nossa seleção de produtos</p>
    </div>
    
    <div class="container">
        <div class="row mx-auto">
            <?php if($products_result->num_rows > 0): ?>
                <?php while($product = $products_result->fetch_assoc()): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="product text-center" onclick="window.location.href='single_product.php?product_id=<?php echo $product['product_id']; ?>';" style="cursor: pointer;">
                            <?php if($product['product_image']): ?>
                                <img class="img-fluid mb-3" src="assets/imgs/<?php echo $product['product_image']; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            <?php else: ?>
                                <div class="bg-secondary" style="height: 200px; margin-bottom: 1rem;"></div>
                            <?php endif; ?>
                            
                            <div class="star">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            
                            <h5 class="p-name"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                            <h4 class="p-price">R$ <?php echo number_format($product['product_price'], 2, ',', '.'); ?></h4>
                            
                            <?php if($product['product_special_offer'] > 0): ?>
                                <span class="badge bg-danger">-<?php echo $product['product_special_offer']; ?>%</span>
                            <?php endif; ?>
                            
                            <a href="single_product.php?product_id=<?php echo $product['product_id']; ?>" class="btn btn-primary mt-2">Ver Detalhes</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        Nenhum produto disponível no momento.
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
            <nav aria-label="Navegação de páginas" class="mt-5">
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
    </div>
</section>

<style>
.product {
    border: 1px solid #e0e0e0;
    padding: 20px;
    border-radius: 8px;
    transition: transform 0.3s, box-shadow 0.3s;
}

.product:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.product img {
    max-height: 200px;
    object-fit: cover;
}

.star i {
    color: #ffc107;
    font-size: 0.8rem;
}

.p-name {
    font-size: 1rem;
    font-weight: bold;
    margin-top: 10px;
}

.p-price {
    color: #28a745;
    font-weight: bold;
}
</style>

<?php include('layouts/footer.php'); ?>
