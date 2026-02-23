<?php
session_start();
include 'db.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'yönetici') {
    header('Location: login.php');
    exit;
}

$mesaj = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ogrenciler'])) {
    $ids = $_POST['ogrenciler'];

    if (!empty($ids)) {
        $ids = array_map('intval', $ids);
        $placeholders = rtrim(str_repeat('?,', count($ids)), ',');

      
        $stmt_not = $conn->prepare("DELETE FROM notlar WHERE ogrenci_id IN ($placeholders)");
        $stmt_not->execute($ids);

       
        $stmt_user = $conn->prepare("DELETE FROM kullanicilar WHERE id IN ($placeholders) AND rol = 'ogrenci'");
        $stmt_user->execute($ids);

        $silinen_sayi = $stmt_user->rowCount();
        $mesaj = "$silinen_sayi öğrenci başarıyla silindi.";
    } else {
        $mesaj = "Lütfen en az bir öğrenci seçin.";
    }
}

$stmt = $conn->prepare("SELECT id, ad_soyad FROM kullanicilar WHERE rol = 'ogrenci' ORDER BY ad_soyad");
$stmt->execute();
$ogrenciler = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8" />
<title>Öğrenci Silme Paneli</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
<style>
    :root {
        --sidebar-bg: #1a1a1a;
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
        background-color: rgba(49, 11, 11, 1);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        color: var(--text-color);
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        padding: 40px 0;
    }
    form {
        background-color: rgba(255, 255, 255, 0.85);
        padding: 30px 40px;
        border-radius: 12px;
        width: 400px;
        position: relative;
    }
    h2 {
        margin-bottom: 25px;
        color: rgb(12, 11, 11);
        font-weight: 700;
        text-align: center;
    }
    .ogrenci-listesi {
        max-height: 400px;
        overflow-y: auto;
        margin-bottom: 20px;
        border: 1px solid ;
        border-radius: 8px;
        padding: 15px;
        background: rgba(49, 11, 11, 1);
    }
    .ogrenci-item {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        font-size: 16px;
        cursor: pointer;
        user-select: none;
    }
    .ogrenci-item input[type="checkbox"] {
        margin-right: 12px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    button {
        background-color: rgb(12, 7, 7);
        color: var(--white);
        border: none;
        padding: 14px 30px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
        width: 100%;
    }
    button:hover {
        background-color: rgba(49, 11, 11, 1);
    }
    .mesaj {
        margin-bottom: 20px;
        background-color: rgba(0, 128, 0, 0.85);
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        color: #e0ffe0;
        text-align: center;
        box-shadow: 0 0 12px #00cc00;
    }
    .bos-mesaj {
        margin-bottom: 20px;
        background-color: rgba(204, 0, 0, 0.85);
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        color:rgb(189, 51, 51);
        text-align: center;
        box-shadow: 0 0 12px #cc0000;
    }

  
   .panel-btn {
    position:absolute;
    left:695px;
 top:300px;
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

<form method="POST" action="">
    <h2>Öğrenci Silme Paneli</h2>

    <?php if ($mesaj): ?>
        <div class="<?= strpos($mesaj, 'silindi') !== false ? 'mesaj' : 'bos-mesaj' ?>">
            <?= htmlspecialchars($mesaj) ?>
        </div>
    <?php endif; ?>

    <?php if (count($ogrenciler) > 0): ?>
        <div class="ogrenci-listesi" tabindex="0" aria-label="Öğrenci Listesi">
            <?php foreach ($ogrenciler as $ogr): ?>
                <label class="ogrenci-item">
                    <input type="checkbox" name="ogrenciler[]" value="<?= (int)$ogr['id'] ?>">
                    <?= htmlspecialchars($ogr['ad_soyad']) ?>
                </label>
            <?php endforeach; ?>
        </div>
        <button type="submit">Seçilen Öğrencileri Sil</button>
    
    <?php else: ?>
        <p style="text-align:center; font-size:18px; color:rgb(245, 5, 5);">Silinecek öğrenci bulunmamaktadır.</p>
   
    <?php endif; ?>
</form>
<a href="danisman_panel.php" class="panel-btn">Panele Dön</a>
</body>
</html>
