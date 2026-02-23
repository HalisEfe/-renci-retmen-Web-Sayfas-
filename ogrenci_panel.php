<?php
session_start();
include 'db.php';

if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'ogrenci') {
    header('Location: login.php');
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];
$ortalama = null;
$danisman_adi = 'Bilinmiyor';


$stmt = $conn->prepare("SELECT AVG(not_degeri) as ortalama FROM notlar WHERE ogrenci_id = ?");
$stmt->execute([$kullanici_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
if ($result && $result['ortalama'] !== null) {
    $ortalama = number_format($result['ortalama'], 2);
}


$stmt = $conn->prepare("SELECT ad_soyad FROM kullanicilar WHERE rol = 'ogretmen' LIMIT 1");
$stmt->execute();
$danisman_adi = $stmt->fetchColumn() ?: 'Bilinmiyor';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Öğrenci Paneli</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #1a1a1a;
            --topbar-bg: #0d0d0d;
            --primary-red: #e60000;
            --hover-red: #ff1a1a;
            --white: #ffffff;
            --text-color: #f2f2f2;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            height: 100vh;
            overflow: hidden;
            background-color:white;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .sidebar {
            width: 250px;
            background-color: rgba(49, 11, 11, 1);
            color: var(--white);
            display: flex;
            flex-direction: column;
            padding: 20px;
            
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 20px;
            color: white;
        }
        .menu-item {
            margin: 15px 0;
            cursor: pointer;
            position: relative;
            font-size: 16px;
            font-weight: 600;
            color: var(--white);
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 8px;
            transition: background-color 0.3s;
            display: block;
        }
        .menu-item:hover {
            background-color: var(--hover-red);
            color: var(--white);
        }
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(5px);
        }
        .topbar {
            height: 60px;
            background-color:rgba(49, 11, 11, 1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            color: var(--white);
        }
        .topbar .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .topbar .user-info img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .topbar .user-info a {
            background-color: var(--primary-red);
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 14px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        .topbar .user-info a:hover {
            background-color: var(--hover-red);
        }
        .dashboard {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
        .card {
            background-color: rgba(0,0,0,0.7);
            padding: 20px;
            border-radius: 10px;
            color: var(--text-color);
            box-shadow: 0 4px 10px rgba(49, 11, 11, 1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card h3 { margin-bottom: 10px; font-size: 18px; }
        .card p { font-size: 14px; }

        .card1 {
            background-color: rgba(0,0,0,0.7);
            padding: 35px;
            border-radius: 10px;
            color: var(--text-color);
            box-shadow: 0 4px 10px rgba(49, 11, 11, 1);
            transition: transform 0.2s;
        }
        .card1:hover {
            transform: translateY(-5px);
        }
        .card1 h3 { margin-bottom: 10px; font-size: 18px; }
        .card1 p { font-size: 14px; }
        .logo {
    width: 80px;
    height: auto;
    background-color: transparent; 
    animation: logoAnimation 2s ease-in-out;
    margin: 0 auto;
    display: block;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <img src="logo5.png" alt="Logo" class="logo">
    
    <div style="text-align: center; margin-top: 5px; color: var(--text-color); font-weight: 600; font-size: 14px;">
        Türkiye Üniversitesi
    </div>
    <hr style="margin: 10px 0 20px 0; border: none; border-top: 1px solid rgb(255, 255, 255); width: 80%; margin-left: auto; margin-right: auto;" />
    <h2>Öğrenci Sistemi</h2>

    <a href="notlar.php" class="menu-item"> → Notlarım</a>
   <a href="mesajlar.php" class="menu-item"> → Mesaj Gönder</a>
  <a href="akademik_ogrenci_takvim.php" class="menu-item"> → Akademik Takvim</a>
    <a href="duyurular.php" class="menu-item"> → Duyurular</a>
    <a href="ogrenci_yoklamalar.php" class="menu-item"> → Yoklamalarım</a>

    <a href="logout.php" class="menu-item" style="margin-top:auto; background-color: var(--primary-red); text-align:center;">Çıkış Yap</a>
</div>

<div class="content">
    <div class="topbar">
        <div>2024-2025 Bahar Dönemi</div>
        <div class="user-info">
            <div><?= htmlspecialchars($_SESSION['ad_soyad']) ?></div>
            <a href="profil_duzenle.php">Profili Düzenle</a>
        </div>
    </div>

    <div class="dashboard">
        <div class="card">
            <h3>Aktif Dönem</h3>
            <p>2024-2025 Bahar</p>
        </div>
        <div class="card">
            <h3>Danışman</h3>
            <p>Öğr. Göv. <?= htmlspecialchars($danisman_adi) ?></p>
        </div>
        <div class="card">
            <h3>Öğrenim Bilgisi</h3>
            <p>MYO Bilgisayar Teknolojisi<br>1. Sınıf</p>
        </div>
       <a href="notlar.php" style="text-decoration: none; color: inherit;">
<div class="card1" style="cursor:pointer;">
    
            <h3>Not Ortalamanız</h3>
            <p><?= $ortalama !== null ? $ortalama : 'Henüz notunuz yok' ?></p>
        </div>
    </div>
</div>

</body>
</html>
