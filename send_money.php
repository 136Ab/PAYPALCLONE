<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $receiver = trim($_POST['receiver']); // Remove whitespace
    $amount = floatval($_POST['amount']); // Convert to float
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];

    // Validate input
    if (empty($receiver)) {
        $error = "Please enter a receiver email or username.";
    } elseif ($amount <= 0) {
        $error = "Amount must be greater than zero.";
    } else {
        try {
            // Fetch receiver by email or username (case-insensitive)
            $stmt = $conn->prepare("SELECT id, email, username FROM users WHERE LOWER(email) = LOWER(?) OR LOWER(username) = LOWER(?)");
            $stmt->execute([$receiver, $receiver]);
            $receiver_user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Fetch sender's balance
            $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $sender = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$receiver_user) {
                $error = "Receiver not found. Please check the email or username.";
            } elseif ($receiver_user['id'] == $user_id) {
                $error = "You cannot send money to yourself.";
            } elseif ($sender['balance'] < $amount) {
                $error = "Insufficient balance. Your current balance is $" . number_format($sender['balance'], 2) . ".";
            } else {
                // Process transaction
                $conn->beginTransaction();
                $stmt = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
                $stmt->execute([$amount, $user_id]);
                $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
                $stmt->execute([$amount, $receiver_user['id']]);
                $stmt = $conn->prepare("INSERT INTO transactions (sender_id, receiver_id, amount, description) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $receiver_user['id'], $amount, $description]);
                $conn->commit();
                // Simulate email notification
                mail($receiver_user['email'], "Payment Received", "You received $" . number_format($amount, 2) . " from user ID $user_id.");
                header("Location: dashboard.php");
                exit();
            }
        } catch(PDOException $e) {
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
    <title>Send Money - PayPal Clone</title>
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
        .send-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 400px;
            text-align: center;
        }
        .send-container h2 {
            margin-bottom: 20px;
            color: #0070ba;
        }
        .send-container input, .send-container textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .send-container button {
            width: 100%;
            padding: 12px;
            background-color: #0070ba;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .send-container button:hover {
            background-color: #00a1d6;
        }
        .error {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }
        @media (max-width: 480px) {
            .send-container { width: 90%; }
        }
    </style>
</head>
<body>
    <div class="send-container">
        <h2>Send Money</h2>
        <form method="POST">
            <input type="text" name="receiver" placeholder="Email or Username" value="<?php echo isset($receiver) ? htmlspecialchars($receiver) : ''; ?>" required>
            <input type="number" name="amount" placeholder="Amount" step="0.01" min="0.01" value="<?php echo isset($amount) ? htmlspecialchars($amount) : ''; ?>" required>
            <textarea name="description" placeholder="Description (optional)"><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
            <button type="submit">Send</button>
        </form>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <p><a href="#" onclick="navigate('dashboard.php')">Back to Dashboard</a></p>
    </div>
    <script>
        function navigate(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
