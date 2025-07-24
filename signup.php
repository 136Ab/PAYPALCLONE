<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        $_SESSION['user_id'] = $conn->lastInsertId();
        header("Location: dashboard.php");
        exit();
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - PayPal Clone</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .signup-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 400px;
            text-align: center;
        }
        .signup-container h2 {
            margin-bottom: 20px;
            color: #0070ba;
        }
        .signup-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .signup-container button {
            width: 100%;
            padding: 12px;
            background-color: #0070ba;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .signup-container button:hover {
            background-color: #00a1d6;
        }
        .signup-container a {
            color: #0070ba;
            text-decoration: none;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        @media (max-width: 480px) {
            .signup-container { width: 90%; }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="#" onclick="navigate('login.php')">Log In</a></p>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    </div>
    <script>
        function navigate(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
