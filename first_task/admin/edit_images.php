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

// Handle image upload
if(isset($_POST['upload_images'])) {
    $target_dir = "../assets/imgs/";
    $updated = false;
    
    // Get current product images
    $stmt = $conn->prepare("SELECT product_image, product_image2, product_image3, product_image4 FROM products WHERE product_id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_product = $result->fetch_assoc();
    
    $images = [
        'product_image' => $current_product['product_image'],
        'product_image2' => $current_product['product_image2'],
        'product_image3' => $current_product['product_image3'],
        'product_image4' => $current_product['product_image4']
    ];
    
    // Process each image
    foreach(['product_image', 'product_image2', 'product_image3', 'product_image4'] as $key) {
        if(isset($_FILES[$key]) && $_FILES[$key]['error'] == 0) {
            // Delete old image if exists
            if($images[$key] && file_exists($target_dir . $images[$key])) {
                unlink($target_dir . $images[$key]);
            }
            
            // Upload new image
            $image_name = time() . '_' . basename($_FILES[$key]['name']);
            if(move_uploaded_file($_FILES[$key]['tmp_name'], $target_dir . $image_name)) {
                $images[$key] = $image_name;
                $updated = true;
            }
        }
    }
    
    // Update database
    if($updated) {
        $stmt = $conn->prepare("UPDATE products SET product_image = ?, product_image2 = ?, product_image3 = ?, product_image4 = ? WHERE product_id = ?");
        $stmt->bind_param('ssssi', $images['product_image'], $images['product_image2'], $images['product_image3'], $images['product_image4'], $product_id);
        
        if($stmt->execute()) {
            $success_message = "Imagens atualizadas com sucesso!";
        } else {
            $error_message = "Erro ao atualizar imagens.";
        }
    } else {
        $error_message = "Nenhuma imagem foi enviada.";
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
                <h1 class="h2">Editar Imagens - <?php echo htmlspecialchars($product['product_name']); ?></h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="products.php" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-pencil"></i> Editar Produto
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
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Gerenciar Imagens do Produto</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Image 1 -->
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold">Imagem Principal</label>
                                        <?php if($product['product_image']): ?>
                                            <img src="../assets/imgs/<?php echo $product['product_image']; ?>" class="img-fluid mb-2 border" alt="Imagem 1">
                                        <?php else: ?>
                                            <div class="alert alert-secondary mb-2">Nenhuma imagem</div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control" name="product_image" accept="image/*">
                                    </div>
                                    
                                    <!-- Image 2 -->
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold">Imagem 2</label>
                                        <?php if($product['product_image2']): ?>
                                            <img src="../assets/imgs/<?php echo $product['product_image2']; ?>" class="img-fluid mb-2 border" alt="Imagem 2">
                                        <?php else: ?>
                                            <div class="alert alert-secondary mb-2">Nenhuma imagem</div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control" name="product_image2" accept="image/*">
                                    </div>
                                    
                                    <!-- Image 3 -->
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold">Imagem 3</label>
                                        <?php if($product['product_image3']): ?>
                                            <img src="../assets/imgs/<?php echo $product['product_image3']; ?>" class="img-fluid mb-2 border" alt="Imagem 3">
                                        <?php else: ?>
                                            <div class="alert alert-secondary mb-2">Nenhuma imagem</div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control" name="product_image3" accept="image/*">
                                    </div>
                                    
                                    <!-- Image 4 -->
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label fw-bold">Imagem 4</label>
                                        <?php if($product['product_image4']): ?>
                                            <img src="../assets/imgs/<?php echo $product['product_image4']; ?>" class="img-fluid mb-2 border" alt="Imagem 4">
                                        <?php else: ?>
                                            <div class="alert alert-secondary mb-2">Nenhuma imagem</div>
                                        <?php endif; ?>
                                        <input type="file" class="form-control" name="product_image4" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mb-4">
                            <button type="submit" name="upload_images" class="btn btn-primary btn-lg">
                                <i class="bi bi-upload"></i> Atualizar Imagens
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </main>
    </div>
</div>

</body>
</html>
