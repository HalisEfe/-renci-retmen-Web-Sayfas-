<?php
session_start(); 


include 'db.php';

$mesaj = "";


$kullanici_id = $_SESSION['id'] ?? null;
$kullanici_rol = $_SESSION['rol'] ?? null;
$ogretmen_brans = $_SESSION['brans'] ?? null;


if (!$ogretmen_brans && $kullanici_rol === 'yönetici') {
    $stmt = $pdo->prepare("SELECT brans FROM kullanicilar WHERE id = ?");
    $stmt->execute([$kullanici_id]);
    $sonuc = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($sonuc) {
        $ogretmen_brans = $sonuc['brans'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ogrenci_id = $_POST['ogrenci_id'];
    $ders_id = $_POST['ders_id'];
    $not_puan = $_POST['not_puan'];

    try {
        $sql = "INSERT INTO notlar (ogrenci_id, ders_id, not_degeri) 
                VALUES (:ogrenci_id, :ders_id, :not_puan)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':ogrenci_id' => $ogrenci_id,
            ':ders_id' => $ders_id,
            ':not_puan' => $not_puan
        ]);

        $mesaj = "Not başarıyla eklendi!";
    } catch (PDOException $e) {
        $mesaj = "Hata: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Not Ekle</title>
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

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-image: url('arkaplan.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .container { width: 100%; max-width: 480px; }
        .header { margin-bottom: 30px; text-align: center; }
        .header h1 {
            font-size: 24px;
            color: var(--primary);
            background-color: rgba(0, 0, 0, 0.5);
            padding: 8px 16px;
            border-radius: 8px;
            display: inline-block;
        }

        .panel {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .panel h2 {
            font-size: 20px;
            margin-bottom: 15px;
            font-weight: 600;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        label {
            font-weight: 600;
            margin-bottom: 5px;
            align-self: flex-start;
            margin-left: 8%;
        }

        select, input {
            width: 80%;
            padding: 12px;
            margin-bottom: 18px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        select:focus, input:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 6px #00000055;
        }

        button {
            background-color: var(--accent);
            color: white;
            padding: 12px;
            width: 80%;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: var(--accent-hover);
        }

        .message {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            font-weight: 600;
        }

        .back-btn {
            margin-top: 20px;
            text-decoration: none;
            display: inline-block;
            background: #888;
            color: white;
            padding: 8px 20px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: red;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Not Ekle</h1>
    </div>

    <div class="panel">
        <h2>Yeni Not Ekle</h2>
        <?php if (!empty($mesaj)) echo '<p class="message">' . $mesaj . '</p>'; ?>

        <form method="POST" action="">
            <label for="ogrenci_id">Öğrenci Seçin:</label>
            <select name="ogrenci_id" id="ogrenci_id" required>
                <?php
                $sql = "SELECT id, ad_soyad FROM kullanicilar WHERE rol = 'ogrenci'";
                $stmt = $pdo->query($sql);
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $ogrenci) {
                    echo "<option value='" . $ogrenci['id'] . "'>" . $ogrenci['ad_soyad'] . "</option>";
                }
                ?>
            </select>

            <label for="ders_id">Ders Seçin:</label>
<select name="ders_id" id="ders_id" required>
    <?php
    if ($ogretmen_brans) {
        $stmt = $pdo->prepare("SELECT id, ders_adi FROM dersler WHERE brans = :brans");
        $stmt->execute([':brans' => $ogretmen_brans]);
        $dersler = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($dersler) {
            foreach ($dersler as $ders) {
                echo "<option value='" . $ders['id'] . "'>" . $ders['ders_adi'] . " (" . htmlspecialchars($ogretmen_brans) . ")</option>";
            }
        } else {
            echo "<option disabled>Branşınıza ait ders bulunamadı</option>";
        }
    } else {
        echo "<option disabled>Branş bilgisi alınamadı</option>";
    }
    ?>
</select>


            <label for="not_puan">Not Puanı:</label>
            <input type="number" name="not_puan" id="not_puan" required min="0" max="100">

            <button type="submit"> Notu Ekle</button>
        </form>

        <a href="danisman_panel.php" class="back-btn">Panele Dön</a>
    </div>
</div>

</body>
</html>
