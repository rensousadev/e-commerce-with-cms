<?php if(!isset($_SESSION)) { session_start(); } ?>
<!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="assets/imgs/logo.svg" width="100px" alt="Shopping Logo" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto nav-buttons">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php">Produtos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Fale Conosco</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <?php if(isset($_SESSION['logged_in'])): ?>
                            <a class="nav-link" href="account.php" title="Minha Conta">
                                <i class="fa fa-user" aria-hidden="true"></i>
                            </a>
                        <?php else: ?>
                            <a class="nav-link" href="login.php" title="Login">
                                <i class="fa fa-user" aria-hidden="true"></i>
                            </a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>