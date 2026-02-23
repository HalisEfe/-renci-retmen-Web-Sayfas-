<?php
include 'db.php';

$basvuruGonderildi = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad_soyad = $_POST['ad_soyad'];
    $dogum_tarihi = $_POST['dogum_tarihi'];
    $telefon = $_POST['telefon'];
    $email = $_POST['email'];
    
    $neden_basvuru = $_POST['neden_basvuru'];

    $sql = "INSERT INTO basvuru 
        (ad_soyad, dogum_tarihi, telefon, email, neden_basvuru)
        VALUES
        (:ad_soyad, :dogum_tarihi, :telefon, :email,  :neden_basvuru)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':ad_soyad', $ad_soyad);
    $stmt->bindParam(':dogum_tarihi', $dogum_tarihi);
    $stmt->bindParam(':telefon', $telefon);
    $stmt->bindParam(':email', $email);
   
    $stmt->bindParam(':neden_basvuru', $neden_basvuru);

    if ($stmt->execute()) {
        $basvuruGonderildi = true;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>- Okula Başvuru -</title>
    <?php if ($basvuruGonderildi): ?>
        <meta http-equiv="refresh" content="10;url=login.php">
    <?php endif; ?>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            background-image: url('arkaplan.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form-container, .message-box {
            background: white;
            padding: 40px 40px;
            border-radius: 6px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        h1 {
            margin-bottom: 20px;
            color: #000;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        textarea {
            height: 80px;
            resize: vertical;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 12px;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #e60000;
        }
        .tick-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .tick-center {
            font-size: 50px;
            color: #28a745;
            animation: pulse 2.5s infinite;
        }
        @keyframes pulse {
            0%, 100% {transform: scale(1);}
            50% {transform: scale(1.2);}
        }
        .success-message {
            font-size: 18px;
            color: #28a745;
        }
        .redirect-msg {
            margin-top: 8px;
            font-size: 13px;
            color: #555;
        }
        header {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 100;
        }
        .logo {
            width: 100px;
            height: auto;
            background-color: transparent; 
            animation: logoAnimation 2s ease-in-out;
        }
        @keyframes logoAnimation {
            0% {transform: scale(0);}
            50% {transform: scale(1.2);}
            100% {transform: scale(1);}
        }
    </style>
</head>
<body>
    <header>
        <a href="login.php"> 
            <img src="logo5.png" alt="Logo" class="logo">
        </a>
    </header>

    <?php if ($basvuruGonderildi): ?>
        <div class="message-box">
            <div class="tick-wrapper">
                <div class="tick-center">✔</div>
            </div>
            <div class="success-message">Başvurunuz başarıyla alındı.</div>
            <div class="redirect-msg">
                Giriş sayfasına yönlendirme <span id="gerisayim">10</span> saniye içinde gerçekleşecek...
            </div>
        </div>

        <script>
            let timeLeft = 10;
            const countdownElement = document.getElementById('gerisayim');
            const interval = setInterval(() => {
                timeLeft--;
                countdownElement.textContent = timeLeft;
                if (timeLeft <= 0) clearInterval(interval);
            }, 1000);
        </script>
    <?php else: ?>
        <div class="form-container">
            <h1>Okula Başvuru Formu</h1>
            <form method="POST" action="">
                <input type="text" name="ad_soyad" placeholder="Ad Soyad" required>
                <input type="date" name="dogum_tarihi" placeholder="Doğum Tarihi" required>
                <input type="tel" name="telefon" placeholder="Telefon Numarası" required pattern="[0-9\s\-\+\(\)]{10,}">
                <input type="email" name="email" placeholder="E-Posta" required>
               
                <textarea name="neden_basvuru" placeholder="Neden başvuruyorsunuz?" required></textarea>
                <button type="submit">Başvuruyu Gönder</button>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>
