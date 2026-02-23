<?php
include 'db.php';

$mesaj = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ad_soyad = $_POST['ad_soyad'];
    $email = $_POST['email'];
    $sifre = $_POST['sifre']; 
    $rol = $_POST['rol'];

    $sql = "INSERT INTO kullanicilar (ad_soyad, email, sifre, rol) VALUES (:ad_soyad, :email, :sifre, :rol)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':ad_soyad', $ad_soyad);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':sifre', $sifre);
    $stmt->bindParam(':rol', $rol);

    if ($stmt->execute()) {
        $mesaj = "✅ Kayıt başarılı! <a href='login.php'>Giriş yap</a>";
    } else {
        $mesaj = "❌ Bir hata oluştu, lütfen tekrar deneyin.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .form-container h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 0.9em;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Kayıt Ol</h2>
        <form method="POST" action="">
            <input type="text" name="ad_soyad" placeholder="Ad Soyad" required>
            <input type="email" name="email" placeholder="E-Posta" required>
            <input type="password" name="sifre" placeholder="Şifre" required>
            <select name="rol" required>
                <option value="">Rol Seçiniz</option>
                <option value="ogrenci">Öğrenci</option>
                <option value="ogretmen">Öğretmen</option>
            </select>
            <button type="submit">Kayıt Ol</button>
        </form>
        <div class="message"><?= $mesaj ?></div>
        <div class="login-link">
            Zaten hesabınız var mı? <a href="login.php">Giriş Yap</a>
        </div>
    </div>
</body>
</html>
