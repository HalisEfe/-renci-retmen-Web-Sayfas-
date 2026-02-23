<?php
session_start();
if (!isset($_SESSION['kullanici_id']) || 
    ($_SESSION['rol'] !== 'ogretmen' && $_SESSION['rol'] !== 'yönetici')) {
    header("Location: login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8" />
<title>Öğretmen Paneli</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
<style>
    :root {
        --sidebar-bg: #1a1a1a;
        --topbar-bg: #0d0d0d;
        --primary-red: #e60000;
        --hover-red: #ff1a1a;
        --white: #ffffff;
        --text-color: #f2f2f2;
        --card-bg: rgba(0, 0, 0, 0.7);
        --shadow-red: rgba(255, 0, 0, 0.4);
    }
    * {
        margin: 0; padding: 0; box-sizing: border-box;
    }
    body {
        font-family: 'Inter', sans-serif;
        display: flex;
        height: 100vh;
        background-color: rgb(255, 255, 255)
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        color: var(--text-color);
        overflow: hidden;
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
        color: rgb(250, 247, 247);
        font-weight: 700;
    }
    .menu-item {
        margin: 15px 0;
        cursor: pointer;
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
        font-weight: 600;
    }
    .topbar .user-info {
        display: flex;
        align-items: center;
    }
    .topbar .user-info img {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        margin-right: 10px;
        object-fit: cover;
        border: 2px solid var(--primary-red);
    }
     .dashboard {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
        .card {
            background-color: rgba(0,0,0,0.7);
            padding: 15px;
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
    <h2>Öğretmen Sistemi</h2>

   
 
    <a href="not_ekle.php" class="menu-item"> → Not Ekleme</a>
    <a href="akademik_takvim.php" class="menu-item"> → Akademik Takvim</a>

    <a href="ogretmen_yoklama_secim.php"class="menu-item"> → Yoklama Alma</a>
   
    <a href="gelen_mesajlar.php" class="menu-item"> → Gelen Mesajlar</a>
   
    <a href="logout.php" class="menu-item" style="margin-top:auto; background-color: var(--primary-red); text-align:center;">Çıkış Yap</a>
</div>

<div class="content">
    <div class="topbar">
        <div></div>
        <div class="user-info">
            
            <div>Öğretim Görevlisi <?= htmlspecialchars($_SESSION['ad_soyad']) ?></div>
        </div>
    </div>

    <div class="dashboard">
        <div class="card">
            <h3>Hoş geldiniz</h3>
            <p>Sayın <?= htmlspecialchars($_SESSION['ad_soyad']) ?>, Öğretmen Paneline Hoş Geldiniz!</p>
        </div>
        
        <div class="card">
            <h3>Aktif Dönem</h3>
            <p>2024-2025 Bahar</p>
        </div>
       <a href="ogrenci_listesi.php" style="text-decoration: none; color: inherit;">
<div class="card1" style="cursor:pointer;">
    <h3>Toplam Öğrenci</h3>
    <p>
    <?php
        include 'db.php';
        $stmt = $conn->query("SELECT COUNT(*) as toplam FROM kullanicilar WHERE rol = 'ogrenci'");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo $row ? $row['toplam'] : '0';
    ?>
    </p>
</div>
</a>

    </div>
</div>

</body>
</html>