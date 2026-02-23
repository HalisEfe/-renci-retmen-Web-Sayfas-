<?php
session_start();
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $sifre = $_POST['sifre'];

    
    $sql = "SELECT * FROM kullanicilar WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($kullanici && $kullanici['sifre'] === $sifre) {
        $_SESSION['kullanici_id'] = $kullanici['id'];
        $_SESSION['ad_soyad'] = $kullanici['ad_soyad'];
        $_SESSION['rol'] = $kullanici['rol'];

        if ($kullanici['rol'] === 'ogretmen') {
            $_SESSION['brans'] = $kullanici['brans'];
            header('Location: ogretmen_panel.php');
        } elseif ($kullanici['rol'] === 'yönetici') {
            $_SESSION['brans'] = $kullanici['brans'];
            header('Location: danisman_panel.php'); 
        } elseif ($kullanici['rol'] === 'ogrenci') {
            header('Location: ogrenci_panel.php');
        } else {
            echo "Tanımsız rol!";
        }
        exit;
    } else {
        echo "E-posta veya şifre yanlış!";
    }
}
?>
