<?php
session_start();
include 'db.php';

if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'yönetici') {
    header('Location: login.php');
    exit;
}


$ogrenci_sorgu = $pdo->query("SELECT id, ad_soyad FROM kullanicilar WHERE rol = 'ogrenci'");
$ogrenciler = $ogrenci_sorgu->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baslik = $_POST['baslik'];
    $icerik = $_POST['icerik'];
    $hedef = $_POST['hedef'];

    $sql = "INSERT INTO duyurular (baslik, icerik, hedef, tarih) VALUES (?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$baslik, $icerik, $hedef])) {
        echo "<p class='success-message'>Duyuru başarıyla eklendi!</p>";
    } else {
        echo "<p class='error-message'>Bir hata oluştu!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Duyuru Ekleme</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
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
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 700px; 
        }

        .header {
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 26px; 
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
        }

        .panel {
            background: rgba(255, 255, 255, 0.85);
            padding: 30px; 
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .panel h2 {
            font-size: 18px; 
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
        }

        .panel p {
            font-size: 14px;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            font-size: 14px; 
            color: #555;
            margin-bottom: 5px;
            align-self: flex-start;
            margin-left: 10%;
        }

        input, textarea, select {
            padding: 10px;
            width: 80%;
            margin-bottom: 15px; 
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.4);
        }

        button {
            padding: 12px 20px; 
            background-color:rgb(0, 0, 0);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color:rgba(49, 11, 11, 1);
        }

        .success-message, .error-message {
            font-size: 16px;
            font-weight: 600;
            margin-top: 15px;
        }

        .success-message {
            color: #4CAF50;
        }

        .error-message {
            color: #e74c3c;
        }

        .back-link {
            margin-top: 20px; 
        }

        .panel-btn {
    position:absolute;
    left:695px;
 top:625px;
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
            <br><br>
        </div>

        <div class="panel">
            <h2>Duyuru Gönder</h2>
            <p>Yeni bir duyuru göndermek için aşağıdaki formu doldurun.</p>

            <form method="POST" action="">
                <label for="baslik">Duyuru Başlığı</label>
                <input type="text" name="baslik" id="baslik" required>

                <label for="icerik">Duyuru İçeriği</label>
                <textarea name="icerik" id="icerik" rows="5" required></textarea>

                <label for="hedef">Hedef Kitle</label>
                <select name="hedef" id="hedef" required>
                    <option value="tum_ogrenciler">Tüm Öğrenciler</option>
                    <?php foreach ($ogrenciler as $ogrenci): ?>
                        <option value="ogrenci_<?php echo $ogrenci['id']; ?>">
                            <?php echo htmlspecialchars($ogrenci['ad_soyad']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit">Duyuruyu Gönder!</button>
            </form>

            
              
         
        </div>
    </div>
     <a href="danisman_panel.php" class="panel-btn"> Panele Dön</a>
</body>
</html>
