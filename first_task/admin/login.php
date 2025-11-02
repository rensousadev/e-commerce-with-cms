<?php include('header.php'); ?>

<?php
// If user is already logged in, redirect to dashboard
if(isset($_SESSION['admin_logged_in'])) {
    header('location: index.php');
    exit();
}

// Handle login form submission
if(isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // MD5 hash to match database
    
    // Query to check if admin exists with provided credentials
    $stmt = $conn->prepare("SELECT admin_id, admin_name, admin_email FROM admins WHERE admin_email = ? AND admin_password = ? LIMIT 1");
    $stmt->bind_param('ss', $email, $password);
    
    if($stmt->execute()) {
        $stmt->bind_result($admin_id, $admin_name, $admin_email);
        $stmt->store_result();
        
        if($stmt->num_rows() == 1) {
            $stmt->fetch();
            
            // Store admin data in session
            $_SESSION['admin_id'] = $admin_id;
            $_SESSION['admin_name'] = $admin_name;
            $_SESSION['admin_email'] = $admin_email;
            $_SESSION['admin_logged_in'] = true;
            
            // Redirect to dashboard
            header('location: index.php');
            exit();
        } else {
            // Invalid credentials
            $error_message = "Email ou senha incorretos!";
        }
    } else {
        // Query execution failed
        $error_message = "Erro ao processar login. Tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<body class="text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 mt-5">
                <main class="form-signin">
                    <form method="POST" action="login.php">
                        <h1 class="h3 mb-3 fw-normal">Admin Login</h1>
                        
                        <?php if(isset($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                            <label for="email">Email</label>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Senha" required>
                            <label for="password">Senha</label>
                        </div>
                        
                        <button class="w-100 btn btn-lg btn-primary" type="submit" name="login_btn">Login</button>
                        
                        <p class="mt-5 mb-3 text-muted">
                            <small>Usuário padrão: admin@shop.com.br<br>Senha: 123456</small>
                        </p>
                    </form>
                </main>
            </div>
        </div>
    </div>
    
    <style>
        html, body {
            height: 100%;
        }
        
        body {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }
        
        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }
        
        .form-signin .form-floating:focus-within {
            z-index: 2;
        }
    </style>
</body>
</html>
