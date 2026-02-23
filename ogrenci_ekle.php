<?php
session_start();
include 'db.php';

if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'yönetici') {
    header('Location: login.php');
    exit;
}


$mesaj = '';

if (isset($_POST['ekle'])) {
    $adsoyad = $_POST['adsoyad'];
    $email = $_POST['email'];
    $sifre = $_POST['sifre'];

    if (empty($adsoyad) || empty($email) || empty($sifre)) {
        $mesaj = "<p style='color:red;'>❌ Lütfen tüm alanları doldurun!</p>";
    } else {
        try {
        
            $stmt = $pdo->prepare("INSERT INTO kullanicilar (ad_soyad, email, sifre, rol) VALUES (?, ?, ?, 'ogrenci')");
            $stmt->execute([$adsoyad, $email, $sifre]);

            $mesaj = "<p style='color:green;'>✅ Öğrenci başarıyla eklendi!</p>";
        } catch (PDOException $e) {
            $mesaj = "<p style='color:red;'>❌ Hata oluştu: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Öğrenci Ekleme Paneli
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ffffff;
            --secondary: #f0f0f0;
            --text: #1a1a1a;
            --card-bg: rgba(255, 255, 255, 0.85);
            --accent: rgb(0, 0, 0);
            --accent-hover: rgb(252, 0, 0);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: rgba(49, 11, 11, 1);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }

        .container {
            width: 100%;
            max-width: 600px;
        }

        .header {
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            color: var(--primary);
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
        }

        .panel {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .panel h2 {
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--text);
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input, button {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-family: 'Inter', sans-serif;
            font-size: 16px;
        }

        input:focus, button:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 8px #00000055;
        }

        button {
            background-color: var(--accent);
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: rgba(49, 11, 11, 1);
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            font-size: 16px;
        }

       .panel-btn {
    position:absolute;
    left:685px;
 top:500px;
        display: inline-block;
        margin-top: 30px;
        padding: 12px 25px;
        background-color: rgb(0, 0, 0);
        color: white;
        text-decoration: none;
        font-weight: 600;
        border-radius: 8px;
        
        transition: background-color 0.3s ease;
    }
    .panel-btn:hover {
        background-color: red;
    }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <br><br><br>
    </div>

    <div class="panel">
        <h2>Öğrenci Ekleme Paneli</h2>

        <?php if (!empty($mesaj)) echo '<div class="message">' . $mesaj . '</div>'; ?>

        <form action="" method="POST">
            <input type="text" name="adsoyad" placeholder="Ad Soyad" required>
            <input type="email" name="email" placeholder="E-posta" required>
            <input type="password" name="sifre" placeholder="Şifre" required>
            <button type="submit" name="ekle">Öğrenci Ekle</button>
        </form>

        
    </div>
</div>
<a class="panel-btn" href="danisman_panel.php"> Panele Geri Dön</a>
</body>
</html>
