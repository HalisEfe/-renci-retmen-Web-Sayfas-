<?php
include 'db.php'; 


$sql = "SELECT * FROM kullanicilar";
$stmt = $pdo->query($sql);

if ($stmt) {
    $kullanicilar = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($kullanicilar as $kullanici) {
        echo "Ad Soyad: " . $kullanici['ad_soyad'] . "<br>";
        echo "E-posta: " . $kullanici['email'] . "<br>";
        echo "Rol: " . $kullanici['rol'] . "<br><br>";
    }
}
?>
