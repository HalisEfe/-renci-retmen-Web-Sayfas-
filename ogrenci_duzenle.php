<?php
session_start();
if (!isset($_SESSION['kullanici_id']) || !in_array($_SESSION['rol'], ['ogretmen', 'yönetici'])) {
    header('Location: login.php');
    exit;
}
include 'db.php';

if (!isset($_GET['id'])) {
    header('Location: ogrenci_listesi.php');
    exit;
}

$id = intval($_GET['id']);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_soyad = $_POST['ad_soyad'];
    $email = $_POST['email'];
    $sifre = $_POST['sifre']; 

    $stmt = $conn->prepare("UPDATE kullanicilar SET ad_soyad = ?, email = ?, sifre = ? WHERE id = ?");
    $stmt->execute([$ad_soyad, $email, $sifre, $id]);

    header("Location: ogrenci_listesi.php");
    exit;
}


$stmt = $conn->prepare("SELECT ad_soyad, email, sifre FROM kullanicilar WHERE id = ?");
$stmt->execute([$id]);
$ogrenci = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ogrenci) {
    echo "Öğrenci bulunamadı.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Öğrenci Düzenle</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #121212;
            color: #fff;
            padding: 40px;
        }
        .form-container {
            max-width: 500px;
            margin: auto;
            background-color: #1f1f1f;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ff0000;
        }
        h2 {
            text-align: center;
            color: #ff1a1a;
        }
        label {
            display: block;
            margin-top: 20px;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: none;
            background-color: #2a2a2a;
            color: #fff;
        }
        button {
            margin-top: 25px;
            width: 100%;
            padding: 12px;
            background-color: #e60000;
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #ff1a1a;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #ccc;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Öğrenci Bilgilerini Düzenle</h2>
        <form method="post">
            <label for="ad_soyad">Ad Soyad:</label>
            <input type="text" name="ad_soyad" id="ad_soyad" value="<?= htmlspecialchars($ogrenci['ad_soyad']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($ogrenci['email']) ?>" required>

            <label for="sifre">Şifre (Açık):</label>
            <input type="text" name="sifre" id="sifre" value="<?= htmlspecialchars($ogrenci['sifre']) ?>" required>

            <button type="submit">Kaydet</button>
        </form>
        <a href="ogrenci_listesi_danisman.php">← Listeye Geri Dön</a>
    </div>
</body>
</html>
