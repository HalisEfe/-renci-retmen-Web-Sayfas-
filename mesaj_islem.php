<?php
session_start();
include 'db.php';

if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'yönetici') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    if (isset($_POST['onayla'])) {
        $ad_soyad = $_POST['ad_soyad'];
        $email = $_POST['email'];

      
        $stmt = $conn->prepare("SELECT dogum_tarihi FROM basvuru WHERE id = ?");
        $stmt->execute([$id]);
        $basvuru = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$basvuru) {
            $_SESSION['mesaj'] = "Başvuru bulunamadı.";
            header('Location: basvurular.php');
            exit;
        }

        
        $dogum_tarihi = $basvuru['dogum_tarihi'];
        $dogum_yili = substr($dogum_tarihi, 0, 4);

       
        $check = $conn->prepare("SELECT * FROM kullanicilar WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() === 0) {
           
            $sifre = $dogum_yili;

            $stmt = $conn->prepare("INSERT INTO kullanicilar (ad_soyad, email, sifre, rol) VALUES (?, ?, ?, 'ogrenci')");
            $stmt->execute([$ad_soyad, $email, $sifre]);
        }

       
        $delete = $conn->prepare("DELETE FROM basvuru WHERE id = ?");
        $delete->execute([$id]);

        $_SESSION['mesaj'] = 'Öğrenci başarıyla eklendi.';
        header('Location: basvurular.php');
        exit;
    }

    if (isset($_POST['reddet'])) {
       
        $delete = $conn->prepare("DELETE FROM basvuru WHERE id = ?");
        $delete->execute([$id]);

        $_SESSION['mesaj'] = 'Başvuru reddedildi ve silindi.';
        header('Location: basvurular.php');
        exit;
    }
}
?>
