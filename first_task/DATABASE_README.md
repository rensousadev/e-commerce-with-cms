# Database Setup Instructions

## Prerequisites
- MySQL/MariaDB server installed and running
- MySQL root user access

## Setup Steps

1. **Import the database schema:**
   ```bash
   mysql -u root -p < database_schema.sql
   ```

2. **Verify the database was created:**
   ```sql
   SHOW DATABASES;
   USE project_db;
   SHOW TABLES;
   ```

## Database Structure

The e-commerce database includes the following tables:

### Products (`products`)
- `product_id` (INT, Primary Key, Auto Increment)
- `product_name` (VARCHAR 100)
- `product_category` (VARCHAR 100)
- `product_description` (VARCHAR 250)
- `product_image` (VARCHAR 250)
- `product_image2` (VARCHAR 250)
- `product_image3` (VARCHAR 250)
- `product_image4` (VARCHAR 250)
- `product_price` (DECIMAL 6,2)
- `product_special_offer` (INTEGER 2)
- `product_color` (VARCHAR 100)

### Users (`users`)
- `user_id` (INT, Primary Key, Auto Increment)
- `user_name` (VARCHAR 100)
- `user_email` (VARCHAR 100, Unique)
- `user_password` (VARCHAR 100)

### Orders (`orders`)
- `order_id` (INT, Primary Key, Auto Increment)
- `order_cost` (DECIMAL 6,2)
- `order_status` (VARCHAR 100)
- `user_id` (INT, Foreign Key → users.user_id)
- `shipping_city` (VARCHAR 255)
- `shipping_uf` (VARCHAR 2)
- `shipping_address` (VARCHAR 255)
- `order_date` (DATETIME)

### Order Items (`order_items`)
- `item_id` (INT, Primary Key, Auto Increment)
- `order_id` (INT, Foreign Key → orders.order_id)
- `product_id` (INT, Foreign Key → products.product_id)
- `user_id` (INT, Foreign Key → users.user_id)
- `qnt` (INT)
- `order_date` (DATETIME)

### Payments (`payments`)
- `payment_id` (INT, Primary Key, Auto Increment)
- `order_id` (INT, Foreign Key → orders.order_id)
- `user_id` (INT, Foreign Key → users.user_id)
- `transaction_id` (VARCHAR 255)

### Administrators (`admins`)
- `admin_id` (INT, Primary Key, Auto Increment)
- `admin_name` (VARCHAR 255)
- `admin_email` (VARCHAR 255, Unique)
- `admin_password` (VARCHAR 100)

## Default Admin Account
- **Email:** admin@shop.com.br
- **Password:** 123456
- **Name:** admin

## Database Connection
Use the `server/connection.php` file to connect to the database in your PHP files:

```php
<?php
require_once 'server/connection.php';
// $conn variable is now available for database operations
?>
```