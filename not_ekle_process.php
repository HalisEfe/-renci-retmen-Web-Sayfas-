<?php
session_start();
include 'db.php';


if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ogretmen') {
    header('Location: login.php');
    exit();
}


$ogrenci_id = $_POST['ogrenci_id'] ?? null;
$odev_id = $_POST['odev_id'] ?? null;
$not = $_POST['not'] ?? null;


$ogretmen_brans = $_SESSION['brans'] ?? null;

if (!$ogretmen_brans) {
    echo "Branş bilgisi alınamadı. Lütfen tekrar giriş yapınız.";
    exit();
}




$sql = "INSERT INTO notlar (ogrenci_id, odev_id, not) VALUES (:ogrenci_id, :odev_id, :not)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':ogrenci_id', $ogrenci_id);
$stmt->bindParam(':odev_id', $odev_id);
$stmt->bindParam(':not', $not);

if ($stmt->execute()) {
    echo "Not başarıyla kaydedildi!";
} else {
    echo "Not kaydedilemedi.";
}
?>
