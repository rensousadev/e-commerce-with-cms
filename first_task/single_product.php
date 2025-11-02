<?php include('layouts/header.php'); ?>
<?php include('server/connection.php'); ?>

<?php
// Check if product ID is provided
if(!isset($_GET['product_id'])) {
    header('location: products.php');
    exit();
}

$product_id = $_GET['product_id'];

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

<!-- Single Product Section -->
<section class="single-product my-5 pt-5">
    <div class="container mt-5">
        <div class="row">
            <!-- Product Images -->
            <div class="col-lg-6 col-md-6 col-sm-12">
                <img class="img-fluid w-100 pb-1" id="mainImg" src="assets/imgs/<?php echo $product['product_image'] ? $product['product_image'] : 'placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                
                <div class="small-img-group">
                    <div class="small-img-col">
                        <img src="assets/imgs/<?php echo $product['product_image'] ? $product['product_image'] : 'placeholder.jpg'; ?>" width="100%" class="small-img" alt="Image 1">
                    </div>
                    <?php if($product['product_image2']): ?>
                        <div class="small-img-col">
                            <img src="assets/imgs/<?php echo $product['product_image2']; ?>" width="100%" class="small-img" alt="Image 2">
                        </div>
                    <?php endif; ?>
                    <?php if($product['product_image3']): ?>
                        <div class="small-img-col">
                            <img src="assets/imgs/<?php echo $product['product_image3']; ?>" width="100%" class="small-img" alt="Image 3">
                        </div>
                    <?php endif; ?>
                    <?php if($product['product_image4']): ?>
                        <div class="small-img-col">
                            <img src="assets/imgs/<?php echo $product['product_image4']; ?>" width="100%" class="small-img" alt="Image 4">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Product Details -->
            <div class="col-lg-6 col-md-6 col-sm-12">
                <h6 class="text-muted"><?php echo htmlspecialchars($product['product_category']); ?></h6>
                <h3 class="py-3"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                <h2>R$ <?php echo number_format($product['product_price'], 2, ',', '.'); ?></h2>
                
                <?php if($product['product_special_offer'] > 0): ?>
                    <div class="alert alert-success" role="alert">
                        <strong>Oferta Especial!</strong> <?php echo $product['product_special_offer']; ?>% de desconto!
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="cart.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    <input type="hidden" name="product_name" value="<?php echo $product['product_name']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $product['product_price']; ?>">
                    <input type="hidden" name="product_image" value="<?php echo $product['product_image']; ?>">
                    
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantidade</label>
                        <input type="number" class="form-control" id="quantity" name="product_quantity" value="1" min="1" max="99">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" name="add_to_cart">Adicionar ao Carrinho</button>
                </form>
                
                <h4 class="mt-5 mb-3">Detalhes do Produto</h4>
                <p><?php echo nl2br(htmlspecialchars($product['product_description'])); ?></p>
                
                <?php if($product['product_color']): ?>
                    <div class="mt-3">
                        <strong>Cor:</strong> <?php echo htmlspecialchars($product['product_color']); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.single-product img {
    border-radius: 8px;
}

.small-img-group {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.small-img-col {
    flex-basis: 24%;
    cursor: pointer;
    transition: transform 0.3s;
}

.small-img-col:hover {
    transform: scale(1.05);
}

.small-img {
    border: 2px solid #e0e0e0;
    border-radius: 5px;
}

.small-img:hover {
    border-color: #007bff;
}
</style>

<script>
// Image switching functionality
var mainImg = document.getElementById('mainImg');
var smallImgs = document.getElementsByClassName('small-img');

for(var i = 0; i < smallImgs.length; i++) {
    smallImgs[i].onclick = function() {
        mainImg.src = this.src;
    }
}
</script>

<?php include('layouts/footer.php'); ?>
