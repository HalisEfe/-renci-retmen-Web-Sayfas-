<?php
session_start();
include 'db.php';

if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'ogrenci') {
    header('Location: login.php');
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];
$hata = '';
$basari = '';


$stmt = $conn->prepare("SELECT ad_soyad, email, sifre FROM kullanicilar WHERE id = ?");
$stmt->execute([$kullanici_id]);
$kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kullanici) {
    die("Kullanıcı bulunamadı.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad_soyad = trim($_POST['ad_soyad'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $eski_sifre = $_POST['eski_sifre'] ?? '';
    $yeni_sifre = $_POST['yeni_sifre'] ?? '';
    $yeni_sifre_tekrar = $_POST['yeni_sifre_tekrar'] ?? '';

    if ($ad_soyad === '' || $email === '') {
        $hata = "Ad Soyad ve Email boş bırakılamaz.";
    } else {
        if ($eski_sifre !== '' || $yeni_sifre !== '' || $yeni_sifre_tekrar !== '') {
            if ($eski_sifre !== $kullanici['sifre']) {
                $hata = "Eski şifreniz yanlış.";
            } elseif ($yeni_sifre === '') {
                $hata = "Yeni şifre boş olamaz.";
            } elseif ($yeni_sifre !== $yeni_sifre_tekrar) {
                $hata = "Yeni şifreler eşleşmiyor.";
            } else {
                $stmt = $conn->prepare("UPDATE kullanicilar SET ad_soyad = ?, email = ?, sifre = ? WHERE id = ?");
                $guncelle = $stmt->execute([$ad_soyad, $email, $yeni_sifre, $kullanici_id]);
                if ($guncelle) {
                    $basari = "Profil ve şifreniz başarıyla güncellendi.";
                    $_SESSION['ad_soyad'] = $ad_soyad;
                } else {
                    $hata = "Güncelleme sırasında hata oluştu.";
                }
            }
        } else {
            $stmt = $conn->prepare("UPDATE kullanicilar SET ad_soyad = ?, email = ? WHERE id = ?");
            $guncelle = $stmt->execute([$ad_soyad, $email, $kullanici_id]);
            if ($guncelle) {
                $basari = "Profil bilgileriniz başarıyla güncellendi.";
                $_SESSION['ad_soyad'] = $ad_soyad;
            } else {
                $hata = "Güncelleme sırasında hata oluştu.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Profil Düzenle</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #74ebd5, #acb6e5);
            --primary: #000000;
            --text-dark: #2c3e50;
            --white: #fff;
            --secondary: #f1f1f1;
            --hover-bg: #333333;
            --error-bg: #ffdddd;
            --error-color: #b00000;
            --success-bg: #ddffdd;
            --success-color: #007700;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color:rgba(49, 11, 11, 1);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--text-dark);
        }

        .container {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
            max-width: 420px;
            width: 100%;
            animation: fadeIn 0.8s ease;
        }

        h2 {
            color: var(--primary);
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 25px;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            font-size: 14px;
        }

        input[type=text],
        input[type=email],
        input[type=password] {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type=text]:focus,
        input[type=email]:focus,
        input[type=password]:focus {
            border-color: var(--primary);
            background-color: var(--secondary);
        }

        input::placeholder {
            color: #aaa;
        }

        hr {
            margin: 25px 0;
            border: 0;
            border-top: 1px solid #ddd;
        }

        button {
            margin-top: 25px;
            width: 100%;
            padding: 14px;
            background-color: var(--primary);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            color: var(--white);
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s;
        }

        button:hover {
            background-color: red;
            transform: translateY(-2px);
        }

        .message {
            margin-bottom: 15px;
            padding: 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
        }

        .error {
            background-color: var(--error-bg);
            color: var(--error-color);
        }

        .success {
            background-color: var(--success-bg);
            color: var(--success-color);
        }

        a.back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s;
        }

        a.back-link:hover {
            color: red;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
                border-radius: 15px;
            }

            h2 {
                font-size: 24px;
            }

            button {
                font-size: 14px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Profil Düzenle</h2>

        <?php if ($hata): ?>
            <div class="message error"><?= htmlspecialchars($hata) ?></div>
        <?php endif; ?>

        <?php if ($basari): ?>
            <div class="message success"><?= htmlspecialchars($basari) ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <label>Ad Soyad</label>
            <input type="text" name="ad_soyad" required value="<?= htmlspecialchars($_POST['ad_soyad'] ?? $kullanici['ad_soyad']) ?>" />

            <label>Email</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? $kullanici['email']) ?>" />

            <hr />

            <label>Eski Şifre (Şifre değiştirmek için giriniz)</label>
            <input type="password" name="eski_sifre" placeholder="Eski şifrenizi girin" />

            <label>Yeni Şifre</label>
            <input type="password" name="yeni_sifre" placeholder="Yeni şifre" />

            <label>Yeni Şifre Tekrar</label>
            <input type="password" name="yeni_sifre_tekrar" placeholder="Yeni şifre tekrar" />

            <button type="submit">Güncelle</button>
        </form>

        <a href="ogrenci_panel.php" class="back-link">Geri dön</a>
    </div>
</body>
</html>
