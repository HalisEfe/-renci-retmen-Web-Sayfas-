<?php
session_start();


include 'db.php';

if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'yönetici') {
    header('Location: login.php');
    exit;
}

$mesaj = '';  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ders_adi = $_POST['ders_adi'];
$ders_kodu = $_POST['ders_kodu'];
$brans = $_POST['brans'];


   
   $sql = "INSERT INTO dersler (ders_adi, ders_kodu, brans) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);

if ($stmt->execute([$ders_adi, $ders_kodu, $brans])) {
    $mesaj = "<p class='success-message'>✅ Ders başarıyla eklendi!</p>";
} else {
    $mesaj = "<p class='error-message'>❌ Bir hata oluştu!</p>";
}
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ders Ekleme Paneli</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #ffffff;
            --secondary: #f0f0f0;
            --text: #1a1a1a;
            --card-bg: rgba(255, 255, 255, 0.85);
            --accent: #6a82fb;
            --accent-hover: #4361ee;
            --background-start: #74ebd5;
            --background-end: #9face6;
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
            max-width: 900px;
        }

        .header {
            margin-bottom: 40px;
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
            text-align: center;
        }

        .panel h2 {
            font-size: 20px;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--text);
        }

        .panel p {
            font-size: 14px;
            color: #333;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            font-size: 16px;
            color: #555;
            margin-bottom: 5px;
            align-self: flex-start;
            margin-left: 10%;
        }

        input {
            padding: 12px;
            width: 80%;
            margin-bottom: 20px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.4);
        }

        button {
            padding: 12px 25px;
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

        .panel-btn {
    position:absolute;
    left:705px;
 top:600px;
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
            <br><br><br><br>
        </div>

        <div class="panel">
            <h2>Ders Ekleme Paneli</h2>
            

            <form method="POST" action="">
                <label for="ders_adi">Ders Adı:</label>
                <input type="text" name="ders_adi" id="ders_adi" required>
                <label for="brans">Branş:</label>
<input type="text" name="brans" id="brans" required>
                <label for="ders_kodu">Ders Kodu:</label>
                <input type="text" name="ders_kodu" id="ders_kodu" required>

                <button type="submit"> Dersi Ekle</button>
            </form>

            <?= $mesaj ?>

         
        </div>
    </div>
 <a href="danisman_panel.php" class="panel-btn"> Panele Dön</a>
</body>
</html>
