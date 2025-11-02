<?php include('header.php'); ?>

<?php
// Check if admin is logged in
if(!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit();
}

// Handle form submission
if(isset($_POST['create_product'])) {
    $product_name = $_POST['product_name'];
    $product_category = $_POST['product_category'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_special_offer = $_POST['product_special_offer'];
    $product_color = $_POST['product_color'];
    
    // Validate fields
    if(empty($product_name) || empty($product_category) || empty($product_price)) {
        $error_message = "Nome, categoria e preço são obrigatórios!";
    } else {
        // Handle image uploads
        $product_image = '';
        $product_image2 = '';
        $product_image3 = '';
        $product_image4 = '';
        
        // Process main image
        if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $image_name = time() . '_' . $_FILES['product_image']['name'];
            $target_dir = "../assets/imgs/";
            
            if(move_uploaded_file($_FILES['product_image']['tmp_name'], $target_dir . $image_name)) {
                $product_image = $image_name;
            }
        }
        
        // Process additional images
        if(isset($_FILES['product_image2']) && $_FILES['product_image2']['error'] == 0) {
            $image_name = time() . '_2_' . $_FILES['product_image2']['name'];
            if(move_uploaded_file($_FILES['product_image2']['tmp_name'], $target_dir . $image_name)) {
                $product_image2 = $image_name;
            }
        }
        
        if(isset($_FILES['product_image3']) && $_FILES['product_image3']['error'] == 0) {
            $image_name = time() . '_3_' . $_FILES['product_image3']['name'];
            if(move_uploaded_file($_FILES['product_image3']['tmp_name'], $target_dir . $image_name)) {
                $product_image3 = $image_name;
            }
        }
        
        if(isset($_FILES['product_image4']) && $_FILES['product_image4']['error'] == 0) {
            $image_name = time() . '_4_' . $_FILES['product_image4']['name'];
            if(move_uploaded_file($_FILES['product_image4']['tmp_name'], $target_dir . $image_name)) {
                $product_image4 = $image_name;
            }
        }
        
        // Insert product into database
        $stmt = $conn->prepare("INSERT INTO products (product_name, product_category, product_description, product_image, product_image2, product_image3, product_image4, product_price, product_special_offer, product_color) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssssdis', $product_name, $product_category, $product_description, $product_image, $product_image2, $product_image3, $product_image4, $product_price, $product_special_offer, $product_color);
        
        if($stmt->execute()) {
            header('location: products.php?product_created=Produto criado com sucesso!');
            exit();
        } else {
            $error_message = "Erro ao criar produto: " . $stmt->error;
        }
    }
}
?>

<div class="container-fluid">
    <div class="row">
        
        <?php include('sidemenu.php'); ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Adicionar Novo Produto</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="products.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            
            <?php if(isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-8">
                    <form method="POST" action="add_product.php" enctype="multipart/form-data">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Informações do Produto</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="product_name" class="form-label">Nome do Produto *</label>
                                    <input type="text" class="form-control" id="product_name" name="product_name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="product_category" class="form-label">Categoria *</label>
                                    <input type="text" class="form-control" id="product_category" name="product_category" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="product_description" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="product_description" name="product_description" rows="4"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="product_price" class="form-label">Preço (R$) *</label>
                                        <input type="number" step="0.01" class="form-control" id="product_price" name="product_price" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="product_special_offer" class="form-label">Oferta Especial (%)</label>
                                        <input type="number" class="form-control" id="product_special_offer" name="product_special_offer" value="0" min="0" max="100">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="product_color" class="form-label">Cor</label>
                                    <input type="text" class="form-control" id="product_color" name="product_color">
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Imagens do Produto</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="product_image" class="form-label">Imagem Principal</label>
                                    <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="product_image2" class="form-label">Imagem 2</label>
                                    <input type="file" class="form-control" id="product_image2" name="product_image2" accept="image/*">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="product_image3" class="form-label">Imagem 3</label>
                                    <input type="file" class="form-control" id="product_image3" name="product_image3" accept="image/*">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="product_image4" class="form-label">Imagem 4</label>
                                    <input type="file" class="form-control" id="product_image4" name="product_image4" accept="image/*">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 mb-4">
                            <button type="submit" name="create_product" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> Criar Produto
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
