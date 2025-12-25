<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "php-telegram";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Ulanishda xatolik: " . $conn->connect_error);
}


$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL
)";
$conn->query($sql_users);


$sql_msgs = "CREATE TABLE IF NOT EXISTS messages (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql_msgs);
?>