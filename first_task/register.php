<?php 
include('layouts/header.php'); 
include('server/connection.php'); 
?>

<?php
// If user is already logged in, redirect to account page
if(isset($_SESSION['logged_in'])) {
    header('location: account.php');
    exit();
}

// Handle registration form submission
if(isset($_POST['register_btn'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate fields
    if(empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "Todos os campos são obrigatórios!";
    } else if($password !== $confirm_password) {
        $error_message = "As senhas não coincidem!";
    } else if(strlen($password) < 6) {
        $error_message = "A senha deve ter no mínimo 6 caracteres!";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        
        if($stmt->num_rows() > 0) {
            $error_message = "Este email já está cadastrado!";
        } else {
            // Register user
            $hashed_password = md5($password);
            $stmt = $conn->prepare("INSERT INTO users (user_name, user_email, user_password) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $name, $email, $hashed_password);
            
            if($stmt->execute()) {
                $user_id = $stmt->insert_id;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['logged_in'] = true;
                
                header('location: account.php?register_success=Cadastro realizado com sucesso!');
                exit();
            } else {
                $error_message = "Erro ao criar conta. Tente novamente.";
            }
        }
    }
}
?>

<!-- Register Section -->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="font-weight-bold">Criar Conta</h2>
        <hr class="mx-auto">
    </div>
    
    <div class="mx-auto container">
        <form id="register-form" method="POST" action="register.php">
            <?php if(isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label>Nome Completo</label>
                <input type="text" class="form-control" id="register-name" name="name" placeholder="Digite seu nome completo" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" id="register-email" name="email" placeholder="Digite seu email" required>
            </div>
            
            <div class="form-group">
                <label>Senha</label>
                <input type="password" class="form-control" id="register-password" name="password" placeholder="Digite sua senha (mínimo 6 caracteres)" required>
            </div>
            
            <div class="form-group">
                <label>Confirmar Senha</label>
                <input type="password" class="form-control" id="register-confirm-password" name="confirm_password" placeholder="Confirme sua senha" required>
            </div>
            
            <div class="form-group">
                <input type="submit" class="btn btn-primary" id="register-btn" name="register_btn" value="Cadastrar-se">
            </div>
            
            <div class="form-group">
                <a id="login-url" href="login.php" class="btn btn-link">Já possui conta? Faça login</a>
            </div>
        </form>
    </div>
</section>

<style>
#register-form {
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
