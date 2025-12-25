<?php
session_start();
include '../db.php';
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $phone_input = trim($_POST['phone']);
    $full_phone = "+998" . $phone_input;

    if (empty($username) || empty($phone_input)) {
        $error = "Barcha maydonlarni to'ldiring!";
    } elseif (!preg_match("/^\+998\d{9}$/", $full_phone)) {
        $error = "Telefon raqami noto'g'ri kiritildi!";
    } else {

        $sql = "SELECT * FROM users WHERE phone = '$full_phone'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
        } else {

            $sql = "INSERT INTO users (username, phone) VALUES ('$username', '$full_phone')";
            if ($conn->query($sql) === TRUE) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['username'] = $username;
            } else {
                $error = "Xatolik: " . $conn->error;
            }
        }

        if (empty($error)) {
            header("Location: /index.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./style.css">
</head>

<body>

    <div class="login-card">
        <h2>Login</h2>

        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="field">
                <label>Username</label>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="field">
                <label>Phone number</label>
                <div class="phone-input-wrapper">
                    <span class="prefix">+998</span>
                    <input type="tel" name="phone" placeholder="901234567" pattern="\d{9}" maxlength="9" required>
                </div>
            </div>

            <button type="submit" class="submit-btn">Login</button>
        </form>
    </div>

</body>

</html>