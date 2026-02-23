<?php
session_start();
include 'db.php';


if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'ogrenci') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ogrenci_id = $_SESSION['kullanici_id'];
    $ders_id = $_POST['ders_id'];


    $sql = "INSERT INTO ogrenciler_dersler (ogrenci_id, ders_id) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$ogrenci_id, $ders_id]);
        echo "Ders başarıyla seçildi!";
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
} else {
    echo "Lütfen bir ders seçin!";
}
?>
