<?php
session_start();
include('server/connection.php');

// Verify if order_id exists
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header('Location: account.php');
    exit;
}

$order_id = $_GET['order_id'];

// Get order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param('ii', $order_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: account.php');
    exit;
}

$order = $result->fetch_assoc();

// Check if order is already paid
if ($order['order_status'] == 'paid') {
    header('Location: account.php?message=Pedido já foi pago');
    exit;
}

$amount = $order['order_cost'];

include('layouts/header.php');
?>

<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="form-weight-bold">Pagamento</h2>
        <hr class="mx-auto">
    </div>
    <div class="mx-auto container text-center">
        
        <?php if ($order['order_status'] == 'not paid') { ?>
            
            <p>Total a pagar: <strong>R$ <?php echo number_format($amount, 2, ',', '.'); ?></strong></p>
            <p>Pedido #<?php echo $order_id; ?></p>
            
            <div id="paypal-button-container" class="my-4"></div>
            
            <div class="alert alert-info mt-4">
                <h5>Informações de Teste PayPal</h5>
                <p>Para testar o pagamento, use as credenciais de teste do PayPal Sandbox.</p>
                <p>Acesse: <a href="https://developer.paypal.com/developer/accounts" target="_blank">PayPal Developer</a></p>
            </div>
            
        <?php } else { ?>
            
            <p class="alert alert-warning">Este pedido não está disponível para pagamento.</p>
            <a href="account.php" class="btn btn-primary">Voltar para Minha Conta</a>
            
        <?php } ?>
        
    </div>
</section>

<!-- PayPal SDK -->
<!-- IMPORTANTE: Substitua YOUR_CLIENT_ID pelo seu Client ID do PayPal -->
<!-- Obtenha em: https://developer.paypal.com/developer/applications -->
<script src="https://www.paypal.com/sdk/js?client-id=YOUR_CLIENT_ID&currency=BRL"></script>

<script>
    paypal.Buttons({
        // Sets up the transaction when a payment button is clicked
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?php echo $amount; ?>'
                    }
                }]
            });
        },
        
        // Finalize the transaction after payer approval
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(orderData) {
                // Successful capture! For dev/demo purposes:
                console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                var transaction = orderData.purchase_units[0].payments.captures[0];
                
                // Show success message
                alert('Pagamento realizado com sucesso!\nTransação: ' + transaction.status + '\nID: ' + transaction.id);
                
                // Redirect to complete payment
                window.location.href = "server/complete_payment.php?transaction_id=" + transaction.id + "&order_id=<?php echo $order_id; ?>";
            });
        },
        
        // Handle errors
        onError: function(err) {
            console.error('PayPal Error:', err);
            alert('Ocorreu um erro ao processar o pagamento. Por favor, tente novamente.');
        }
        
    }).render('#paypal-button-container');
</script>

<style>
#paypal-button-container {
    max-width: 400px;
    margin: 0 auto;
}
</style>

<?php include('layouts/footer.php'); ?>
