<?php 
session_start();
include('layouts/header.php'); 
include('server/connection.php'); 
?>

<?php
// If user is already logged in, redirect to account page
if(isset($_SESSION['logged_in'])) {
    header('location: account.php');
    exit();
}

// Handle login form submission
if(isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    
    $stmt = $conn->prepare("SELECT user_id, user_name, user_email FROM users WHERE user_email = ? AND user_password = ? LIMIT 1");
    $stmt->bind_param('ss', $email, $password);
    
    if($stmt->execute()) {
        $stmt->bind_result($user_id, $user_name, $user_email);
        $stmt->store_result();
        
        if($stmt->num_rows() == 1) {
            $stmt->fetch();
            
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user_name;
            $_SESSION['user_email'] = $user_email;
            $_SESSION['logged_in'] = true;
            
            header('location: account.php');
            exit();
        } else {
            $error_message = "Email ou senha incorretos!";
        }
    } else {
        $error_message = "Erro ao processar login. Tente novamente.";
    }
}
?>

<!-- Login Section -->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="font-weight-bold">Login</h2>
        <hr class="mx-auto">
    </div>
    
    <div class="mx-auto container">
        <form id="login-form" method="POST" action="login.php">
            <?php if(isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" id="login-email" name="email" placeholder="Digite seu email" required>
            </div>
            
            <div class="form-group">
                <label>Senha</label>
                <input type="password" class="form-control" id="login-password" name="password" placeholder="Digite sua senha" required>
            </div>
            
            <div class="form-group">
                <input type="submit" class="btn btn-primary" id="login-btn" name="login_btn" value="Entrar">
            </div>
            
            <div class="form-group">
                <a id="register-url" href="register.php" class="btn btn-link">Não possui conta? Cadastre-se aqui</a>
            </div>
        </form>
    </div>
</section>

<style>
#login-form {
    max-width: 600px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    font-weight: bold;
    margin-bottom: 0.5rem;
    display: block;
}

.form-control {
    width: 100%;
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.btn-primary {
    width: 100%;
    padding: 12px;
    font-size: 1rem;
}

.btn-link {
    text-decoration: none;
    padding: 0;
}
</style>

<?php include('layouts/footer.php'); ?>
