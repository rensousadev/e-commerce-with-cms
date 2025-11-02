<?php
session_start();

// Function to calculate cart total
function calculateTotal() {
    $total_price = 0;
    $total_quantity = 0;
    
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $product_id => $product) {
            $total_price += $product['product_price'] * $product['product_quantity'];
            $total_quantity += $product['product_quantity'];
        }
    }
    
    $_SESSION['total'] = $total_price;
    $_SESSION['quantity'] = $total_quantity;
    
    return ['total_price' => $total_price, 'total_quantity' => $total_quantity];
}

// Handle add to cart from product page
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];
    
    // Check if product already exists in cart
    if (isset($_SESSION['cart'][$product_id])) {
        // Update quantity
        $_SESSION['cart'][$product_id]['product_quantity'] += $product_quantity;
    } else {
        // Add new product to cart
        $product_array = array(
            'product_id' => $product_id,
            'product_name' => $product_name,
            'product_price' => $product_price,
            'product_image' => $product_image,
            'product_quantity' => $product_quantity
        );
        $_SESSION['cart'][$product_id] = $product_array;
    }
    
    // Redirect to cart page
    header('Location: cart.php');
    exit;
}

// Handle remove item from cart
if (isset($_POST['remove_product'])) {
    $product_id = $_POST['product_id'];
    
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        header('Location: cart.php');
        exit;
    }
}

// Handle edit quantity
if (isset($_POST['edit_quantity'])) {
    $product_id = $_POST['product_id'];
    $product_quantity = $_POST['product_quantity'];
    
    if (isset($_SESSION['cart'][$product_id]) && $product_quantity > 0) {
        $_SESSION['cart'][$product_id]['product_quantity'] = $product_quantity;
        header('Location: cart.php');
        exit;
    }
}

// Calculate totals
$totals = calculateTotal();

include('layouts/header.php');
?>

<section class="cart container my-5 py-5">
    <div class="container mt-5">
        <h2 class="font-weight-bold">Seu Carrinho</h2>
        <hr>
    </div>

    <table class="mt-5 pt-5">
        <tr>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Subtotal</th>
            <th>Ações</th>
        </tr>

        <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
            <?php foreach ($_SESSION['cart'] as $product_id => $product) { ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="assets/imgs/<?php echo $product['product_image']; ?>" alt="<?php echo $product['product_name']; ?>">
                            <div>
                                <p><?php echo $product['product_name']; ?></p>
                                <small><span>R$</span><?php echo number_format($product['product_price'], 2, ',', '.'); ?></small>
                            </div>
                        </div>
                    </td>

                    <td>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <input type="number" name="product_quantity" value="<?php echo $product['product_quantity']; ?>" min="1" class="form-control" style="width: 80px; display: inline-block;">
                            <button type="submit" name="edit_quantity" class="btn btn-sm btn-primary">Atualizar</button>
                        </form>
                    </td>

                    <td>
                        <span>R$</span>
                        <span class="product-price"><?php echo number_format($product['product_price'] * $product['product_quantity'], 2, ',', '.'); ?></span>
                    </td>

                    <td>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <button type="submit" name="remove_product" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Remover
                            </button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="4" class="text-center">
                    <p class="mt-5">Seu carrinho está vazio</p>
                    <a href="products.php" class="btn btn-primary">Continuar Comprando</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
        <div class="cart-total">
            <table>
                <tr>
                    <td>Total</td>
                    <td>R$ <?php echo number_format($totals['total_price'], 2, ',', '.'); ?></td>
                </tr>
            </table>
        </div>

        <div class="checkout-container">
            <form method="GET" action="checkout.php">
                <button type="submit" class="btn btn-primary">Finalizar Compra</button>
            </form>
        </div>
    <?php } ?>
</section>

<style>
.cart table {
    width: 100%;
    border-collapse: collapse;
}

.cart th {
    border-bottom: 2px solid #ddd;
    padding: 15px;
    text-align: left;
    font-weight: 600;
}

.cart td {
    padding: 20px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.cart .product-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.cart .product-info img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 5px;
}

.cart .product-info p {
    margin: 0;
    font-weight: 500;
}

.cart .product-info small {
    color: coral;
}

.cart-total {
    margin-top: 30px;
    display: flex;
    justify-content: flex-end;
}

.cart-total table {
    width: 300px;
    border-top: 2px solid #ddd;
}

.cart-total td {
    font-size: 18px;
    font-weight: 600;
    padding: 15px;
}

.checkout-container {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}

.checkout-container button {
    padding: 12px 30px;
    font-size: 16px;
}
</style>

<?php include('layouts/footer.php'); ?>
