<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
try {
    $stmt = $conn->prepare("SELECT * FROM transactions WHERE sender_id = ? OR receiver_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id, $user_id]);
    $transactions = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - PayPal Clone</title>
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
        .transactions {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
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
        @media (max-width: 768px) {
            .transactions { padding: 10px; }
            .transactions table { font-size: 14px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="#" onclick="navigate('dashboard.php')">Dashboard</a>
        <a href="#" onclick="navigate('send_money.php')">Send Money</a>
        <a href="#" onclick="navigate('receive_money.php')">Add Funds</a>
        <a href="#" onclick="navigate('login.php')">Log Out</a>
    </div>
    <div class="transactions">
        <h2>Transaction History</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>From/To</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Status</th>
            </tr>
            <?php foreach ($transactions as $t): ?>
            <tr>
                <td><?php echo $t['created_at']; ?></td>
                <td><?php echo $t['sender_id'] == $user_id ? 'To: ' . $t['receiver_id'] : 'From: ' . $t['sender_id']; ?></td>
                <td>$<?php echo number_format($t['amount'], 2); ?></td>
                <td><?php echo $t['description'] ?: 'N/A'; ?></td>
                <td><?php echo $t['status']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <script>
        function navigate(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
