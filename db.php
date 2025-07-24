<?php
session_start();
$host = "localhost";
$user = "uhcrnj1vbersg";
$password = "q2hr4nxquppc";
$dbname = "db0dr7ua0hmmzg";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
