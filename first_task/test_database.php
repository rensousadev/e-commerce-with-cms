<?php
/**
 * Database Connection Test Script
 * Tests the connection to the project_db database and verifies table structure
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Conexão - Database</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        table { border-collapse: collapse; width: 100%; margin: 10px 0; }
        th { background-color: #f2f2f2; }
        hr { margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Database Connection Test</h1>
        <hr>

        <?php
        // Include the connection file
        require_once 'server/connection.php';

        // Test 1: Check connection
        echo "<h2>1. Testing Database Connection</h2>\n";
        if ($conn) {
            echo "<p>✅ <span class='success'>Connected to database successfully!</span></p>\n";
            
            // Get connection info
            echo "<p>📊 Database Host: " . mysqli_get_host_info($conn) . "</p>\n";
            echo "<p>📊 Database Version: " . mysqli_get_server_info($conn) . "</p>\n";
        } else {
            echo "<p>❌ <span class='error'>Failed to connect to database: " . mysqli_connect_error() . "</span></p>\n";
            exit();
        }

        echo "<hr>\n";

        // Test 2: Check if database exists and is selected
        echo "<h2>2. Verifying Database Selection</h2>\n";
        $result = mysqli_query($conn, "SELECT DATABASE() as current_db");
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row['current_db'] === 'project_db') {
                echo "<p>✅ <span class='success'>project_db database is selected</span></p>\n";
            } else {
                echo "<p>⚠️ <span class='warning'>Current database: " . ($row['current_db'] ?? 'None') . "</span></p>\n";
            }
        } else {
            echo "<p>❌ <span class='error'>Could not verify database selection</span></p>\n";
        }

        echo "<hr>\n";

        // Test 3: Check if all tables exist
        echo "<h2>3. Verifying Table Structure</h2>\n";
        $expected_tables = ['products', 'users', 'orders', 'order_items', 'payments', 'admins'];

        $result = mysqli_query($conn, "SHOW TABLES");
        $existing_tables = [];

        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $existing_tables[] = $row[0];
            }
            
            echo "<h3>Expected tables:</h3>\n";
            foreach ($expected_tables as $table) {
                if (in_array($table, $existing_tables)) {
                    echo "<p>✅ <span class='success'>$table</span></p>\n";
                } else {
                    echo "<p>❌ <span class='error'>$table (missing)</span></p>\n";
                }
            }
            
            echo "<h3>All tables in database:</h3>\n";
            foreach ($existing_tables as $table) {
                echo "<p>📋 $table</p>\n";
            }
        } else {
            echo "<p>❌ <span class='error'>Could not retrieve table list: " . mysqli_error($conn) . "</span></p>\n";
        }

        echo "<hr>\n";

        // Test 4: Check admin user
        echo "<h2>4. Verifying Admin User</h2>\n";
        $result = mysqli_query($conn, "SELECT * FROM admins WHERE admin_email = 'admin@shop.com.br'");
        if ($result && mysqli_num_rows($result) > 0) {
            $admin = mysqli_fetch_assoc($result);
            echo "<p>✅ <span class='success'>Admin user found</span></p>\n";
            echo "<p>📧 Email: " . htmlspecialchars($admin['admin_email']) . "</p>\n";
            echo "<p>👤 Name: " . htmlspecialchars($admin['admin_name']) . "</p>\n";
            echo "<p>🔐 Password Hash: " . substr($admin['admin_password'], 0, 10) . "...</p>\n";
        } else {
            echo "<p>❌ <span class='error'>Admin user not found or table doesn't exist</span></p>\n";
            if (mysqli_error($conn)) {
                echo "<p>Error: " . mysqli_error($conn) . "</p>\n";
            }
        }

        echo "<hr>\n";

        // Test 5: Test table structure for products
        echo "<h2>5. Testing Products Table Structure</h2>\n";
        $result = mysqli_query($conn, "DESCRIBE products");
        if ($result) {
            echo "<table class='table table-bordered'>\n";
            echo "<thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr></thead>\n";
            echo "<tbody>\n";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
                echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
                echo "</tr>\n";
            }
            echo "</tbody></table>\n";
            echo "<p>✅ <span class='success'>Products table structure verified</span></p>\n";
        } else {
            echo "<p>❌ <span class='error'>Could not describe products table: " . mysqli_error($conn) . "</span></p>\n";
        }

        echo "<hr>\n";
        echo "<h2>Test Summary</h2>\n";
        echo "<p>🔧 Database connection script completed.</p>\n";
        echo "<p>📅 Test run at: " . date('Y-m-d H:i:s') . "</p>\n";

        // Close connection
        mysqli_close($conn);
        echo "<p>🔌 Database connection closed.</p>\n";
        ?>

        <div class="mt-4">
            <a href="/" class="btn btn-primary">← Voltar para Home</a>
            <a href="http://localhost:8080" target="_blank" class="btn btn-secondary">Abrir phpMyAdmin</a>
        </div>
    </div>
</body>
</html>