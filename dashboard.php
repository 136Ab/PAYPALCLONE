<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$error = '';
$transactions = [];
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        $error = "User not found.";
        header("Location: login.php");
        exit();
    }
    // Check if transactions table exists
    $stmt = $conn->query("SHOW TABLES LIKE 'transactions'");
    if ($stmt->rowCount() > 0) {
        $stmt = $conn->prepare("SELECT * FROM transactions WHERE sender_id = ? OR receiver_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$user_id, $user_id]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error = "Transactions table not found. Please contact the administrator.";
    }
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PayPal Clone</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }
        .navbar {
            background-color: #0070ba;
            padding: 15px;
            text-align: center;
        }
        .navbar a {
            color: white;
            margin: 0 20px;
            text-decoration: none;
            font-size: 18px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
        .dashboard {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .balance {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            margin-bottom: 20px;
        }
        .balance h2 {
            color: #0070ba;
        }
        .transactions {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .transactions table {
            width: 100%;
            border-collapse: collapse;
        }
        .transactions th, .transactions td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        .btn {
            padding: 10px 20px;
            background-color: #0070ba;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #00a1d6;
        }
        .error {
            color: red;
            margin-top: 10px;
            font-size: 14px;
            text-align: center;
        }
        .no-transactions {
            color: #555;
            text-align: center;
            margin-top: 20px;
        }
        @media (max-width: 768px) {
            .dashboard { padding: 10px; }
            .transactions table { font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="#" onclick="navigate('send_money.php')">Send Money</a>
        <a href="#" onclick="navigate('recive_money.php')">Add Funds</a>
        <a href="#" onclick="navigate('transactions.php')">Transactions</a>
        <a href="#" onclick="navigate('login.php')">Log Out</a>
    </div>
    <div class="dashboard">
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <div class="balance">
            <h2>Balance: $<?php echo number_format($user['balance'], 2); ?></h2>
            <button class="btn" onclick="navigate('send_money.php')">Send Money</button>
            <button class="btn" onclick="navigate('recive_money.php')">Add Funds</button>
        </div>
        <div class="transactions">
            <h3>Recent Transactions</h3>
            <?php if (empty($transactions) && !$error): ?>
                <p class="no-transactions">No transactions found.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>From/To</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach ($transactions as $t): ?>
                    <tr>
                        <td><?php echo $t['created_at']; ?></td>
                        <td><?php echo $t['sender_id'] == $user_id ? 'To: ' . $t['receiver_id'] : 'From: ' . $t['sender_id']; ?></td>
                        <td>$<?php echo number_format($t['amount'], 2); ?></td>
                        <td><?php echo $t['status']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <script>
        function navigate(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
