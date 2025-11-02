<?php
session_start();

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

include('layouts/header.php');
?>

<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Checkout</h2>
        <hr class="mx-auto">
    </div>
    <div class="mx-auto container">
        <form id="checkout-form" method="POST" action="server/place_order.php">
            <p class="text-center" style="color: red;">
                <?php if(isset($_GET['message'])) { echo $_GET['message']; } ?>
                <?php if(isset($_GET['message'])) { ?>
                    <a href="login.php" class="btn btn-primary">Login</a>
                <?php } ?>
            </p>
            
            <div class="form-group checkout-small-element">
                <label>Nome Completo</label>
                <input type="text" class="form-control" id="checkout-name" name="name" placeholder="Nome Completo" required>
            </div>
            
            <div class="form-group checkout-small-element">
                <label>Email</label>
                <input type="email" class="form-control" id="checkout-email" name="email" placeholder="Email" required>
            </div>
            
            <div class="form-group checkout-small-element">
                <label>Telefone</label>
                <input type="tel" class="form-control" id="checkout-phone" name="phone" placeholder="Telefone" required>
            </div>
            
            <div class="form-group checkout-small-element">
                <label>Cidade</label>
                <input type="text" class="form-control" id="checkout-city" name="city" placeholder="Cidade" required>
            </div>
            
            <div class="form-group checkout-small-element">
                <label>Estado (UF)</label>
                <select class="form-control" id="checkout-state" name="state" required>
                    <option value="">Selecione o Estado</option>
                    <option value="AC">Acre</option>
                    <option value="AL">Alagoas</option>
                    <option value="AP">Amapá</option>
                    <option value="AM">Amazonas</option>
                    <option value="BA">Bahia</option>
                    <option value="CE">Ceará</option>
                    <option value="DF">Distrito Federal</option>
                    <option value="ES">Espírito Santo</option>
                    <option value="GO">Goiás</option>
                    <option value="MA">Maranhão</option>
                    <option value="MT">Mato Grosso</option>
                    <option value="MS">Mato Grosso do Sul</option>
                    <option value="MG">Minas Gerais</option>
                    <option value="PA">Pará</option>
                    <option value="PB">Paraíba</option>
                    <option value="PR">Paraná</option>
                    <option value="PE">Pernambuco</option>
                    <option value="PI">Piauí</option>
                    <option value="RJ">Rio de Janeiro</option>
                    <option value="RN">Rio Grande do Norte</option>
                    <option value="RS">Rio Grande do Sul</option>
                    <option value="RO">Rondônia</option>
                    <option value="RR">Roraima</option>
                    <option value="SC">Santa Catarina</option>
                    <option value="SP">São Paulo</option>
                    <option value="SE">Sergipe</option>
                    <option value="TO">Tocantins</option>
                </select>
            </div>
            
            <div class="form-group checkout-large-element">
                <label>Endereço</label>
                <input type="text" class="form-control" id="checkout-address" name="address" placeholder="Rua, Número, Complemento" required>
            </div>

            <div class="form-group checkout-btn-container">
                <p>Total a Pagar: R$ <?php echo number_format($_SESSION['total'], 2, ',', '.'); ?></p>
                <input type="submit" class="btn btn-primary" id="checkout-btn" value="Finalizar Pedido">
            </div>
        </form>
    </div>
</section>

<style>
.checkout-small-element {
    margin: 15px 0;
}

.checkout-large-element {
    margin: 15px 0;
}

.checkout-btn-container {
    margin-top: 30px;
    text-align: center;
}

.checkout-btn-container p {
    font-size: 22px;
    font-weight: bold;
    color: coral;
    margin-bottom: 20px;
}

#checkout-btn {
    padding: 12px 40px;
    font-size: 18px;
}
</style>

<?php include('layouts/footer.php'); ?>
