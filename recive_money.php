<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$error = '';
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    $user_id = $_SESSION['user_id'];

    // Validate input
    if ($amount <= 0) {
        $error = "Amount must be greater than zero.";
    } else {
        try {
            // Verify user exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            if (!$stmt->fetch()) {
                $error = "User not found.";
            } else {
                $conn->beginTransaction();
                $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                $stmt->execute([$amount, $user_id]);
                $stmt = $conn->prepare("INSERT INTO wallet (user_id, amount, transaction_type) VALUES (?, ?, 'deposit')");
                $stmt->execute([$user_id, $amount]);
                $conn->commit();
                $message = "Successfully added $" . number_format($amount, 2) . " to your balance.";
            }
        } catch(PDOException $e) {
            $conn->rollBack();
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Funds - PayPal Clone</title>
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
        .receive-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 400px;
            text-align: center;
        }
        .receive-container h2 {
            margin-bottom: 20px;
            color: #0070ba;
        }
        .receive-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .receive-container button {
            width: 100%;
            padding: 12px;
            background-color: #0070ba;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .receive-container button:hover {
            background-color: #00a1d6;
        }
        .error {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }
        .message {
            color: green;
            margin-top: 10px;
            font-size: 14px;
        }
        @media (max-width: 480px) {
            .receive-container { width: 90%; }
        }
    </style>
</head>
<body>
    <div class="receive-container">
        <h2>Add Funds</h2>
        <form method="POST">
            <input type="number" name="amount" placeholder="Amount" step="0.01" min="0.01" value="<?php echo isset($_POST['amount']) ? htmlspecialchars($_POST['amount']) : ''; ?>" required>
            <button type="submit">Add Funds</button>
        </form>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if ($message) echo "<p class='message'>$message</p>"; ?>
        <p><a href="#" onclick="navigate('dashboard.php')">Back to Dashboard</a></p>
    </div>
    <script>
        function navigate(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
