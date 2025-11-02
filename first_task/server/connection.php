<?php
/**
 * Database connection file for e-commerce project
 * 
 * Connection parameters for Docker MySQL:
 * - Server: localhost (or mysql container name if running in Docker)
 * - User: root
 * - Password: root123
 * - Database: project_db
 */

// Try to connect to Docker MySQL first, then fallback to local MySQL
// Use 127.0.0.1 instead of localhost to force TCP/IP connection
$conn = mysqli_connect("127.0.0.1", "root", "root123", "project_db", 3306)
or die("Couldn't connect to database: " . mysqli_connect_error());

// Set charset to utf8mb4 for better unicode support
mysqli_set_charset($conn, "utf8mb4");
?>