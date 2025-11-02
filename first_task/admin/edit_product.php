<?php include('header.php'); ?>

<?php
// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit();
}

// Check if product ID is provided
if(!isset($_GET['id'])) {
    header('location: products.php');
    exit();
}

$product_id = $_GET['id'];

// Handle form submission
if(isset($_POST['update_product'])) {
    $product_name = $_POST['product_name'];
    $product_category = $_POST['product_category'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_special_offer = $_POST['product_special_offer'];
    $product_color = $_POST['product_color'];
    
    $stmt = $conn->prepare("UPDATE products SET product_name = ?, product_category = ?, product_description = ?, product_price = ?, product_special_offer = ?, product_color = ? WHERE product_id = ?");
    $stmt->bind_param('sssdisd', $product_name, $product_category, $product_description, $product_price, $product_special_offer, $product_color, $product_id);
    
    if($stmt->execute()) {
        $success_message = "Produto atualizado com sucesso!";
    } else {
        $error_message = "Erro ao atualizar produto.";
    }
}

// Get product details
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    header('location: products.php');
    exit();
}

$product = $result->fetch_assoc();
?>

<div class="container-fluid">
    <div class="row">
        
        <?php include('sidemenu.php'); ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Editar Produto #<?php echo $product['product_id']; ?></h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="products.php" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <a href="edit_images.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-info">
                        <i class="bi bi-images"></i> Editar Imagens
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
                    <form method="POST" action="">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Informações do Produto</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="product_name" class="form-label">Nome do Produto</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="product_category" class="form-label">Categoria</label>
                                    <input type="text" class="form-control" id="product_category" name="product_category" value="<?php echo htmlspecialchars($product['product_category']); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="product_description" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="product_description" name="product_description" rows="4"><?php echo htmlspecialchars($product['product_description']); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="product_price" class="form-label">Preço (R$)</label>
                                        <input type="number" step="0.01" class="form-control" id="product_price" name="product_price" value="<?php echo $product['product_price']; ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="product_special_offer" class="form-label">Oferta Especial (%)</label>
                                        <input type="number" class="form-control" id="product_special_offer" name="product_special_offer" value="<?php echo $product['product_special_offer']; ?>" min="0" max="100">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="product_color" class="form-label">Cor</label>
                                    <input type="text" class="form-control" id="product_color" name="product_color" value="<?php echo htmlspecialchars($product['product_color']); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mb-4">
                            <button type="submit" name="update_product" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> Atualizar Produto
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Imagem Principal</h5>
                        </div>
                        <div class="card-body">
                            <?php if($product['product_image']): ?>
                                <img src="../assets/imgs/<?php echo $product['product_image']; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            <?php else: ?>
                                <div class="alert alert-info">Nenhuma imagem</div>
                            <?php endif; ?>
                            <a href="edit_images.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-info w-100 mt-2">
                                Editar Imagens
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
        </main>
    </div>
</div>

</body>
</html>
