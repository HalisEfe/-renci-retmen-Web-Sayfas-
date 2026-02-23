<?php
$host = 'localhost';
$db = 'ders_yonetimi'; 
$user = 'root';
$pass = '';
$port = 3306;

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8";
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   
    $conn = $pdo;
} catch (PDOException $e) {
    die("Bağlantı başarısız: " . $e->getMessage());
}
?>
