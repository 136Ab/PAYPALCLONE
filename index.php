<?php
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Clone - Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
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
        .hero {
            text-align: center;
            padding: 50px 20px;
            background: linear-gradient(135deg, #0070ba, #00a1d6);
            color: white;
        }
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
        }
        .btn {
            padding: 15px 30px;
            background-color: #00a1d6;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0070ba;
        }
        .features {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 50px 20px;
            flex-wrap: wrap;
        }
        .feature-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 300px;
            text-align: center;
        }
        @media (max-width: 768px) {
            .hero h1 { font-size: 32px; }
            .hero p { font-size: 16px; }
            .feature-card { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="#" onclick="navigate('signup.php')">Sign Up</a>
        <a href="#" onclick="navigate('login.php')">Log In</a>
    </div>
    <div class="hero">
        <h1>Welcome to PayPal Clone</h1>
        <p>Send, receive, and manage your money with ease and security.</p>
        <button class="btn" onclick="navigate('signup.php')">Get Started</button>
    </div>
    <div class="features">
        <div class="feature-card">
            <h3>Send Money</h3>
            <p>Transfer funds to anyone, anywhere, instantly.</p>
        </div>
        <div class="feature-card">
            <h3>Secure Wallet</h3>
            <p>Store and manage your funds with top-notch security.</p>
        </div>
        <div class="feature-card">
            <h3>Track Transactions</h3>
            <p>Keep track of all your transactions in real-time.</p>
        </div>
    </div>
    <script>
        function navigate(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
