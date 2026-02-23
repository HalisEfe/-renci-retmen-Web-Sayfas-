<?php
session_start();
include 'db.php';


$ogretmen_id = $_SESSION['kullanici_id'];
$sql = "SELECT brans FROM kullanicilar WHERE id = ? AND rol IN ('ogretmen', 'yönetici')";
$stmt = $pdo->prepare($sql);
$stmt->execute([$ogretmen_id]);
$ogretmen = $stmt->fetch(PDO::FETCH_ASSOC);

$brans = $ogretmen['brans'] ?? '';

if (!$brans) {
    die("Branş bilgisi bulunamadı.");
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Ver</title>
</head>
<body>
    <h1>Öğrenciye Not Ver</h1>
    <form action="not_gir_process.php" method="POST">
        <label for="odev_id">Ödev:</label>
        <select id="odev_id" name="odev_id" required>
            <?php
            
            $sql = "SELECT * FROM odevler WHERE brans = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$brans]);
            $odevler = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($odevler as $odev) {
                echo "<option value='" . $odev['id'] . "'>" . $odev['baslik'] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="ogrenci_id">Öğrenci:</label>
        <select id="ogrenci_id" name="ogrenci_id" required>
            <?php
          
            $sql = "SELECT * FROM kullanicilar WHERE rol = 'ogrenci'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $ogrenciler = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($ogrenciler as $ogrenci) {
                echo "<option value='" . $ogrenci['id'] . "'>" . $ogrenci['ad_soyad'] . "</option>";
            }
            ?>
        </select><br><br>

        <label for="not">Not:</label>
        <input type="number" id="not" name="not" required><br><br>

        <button type="submit">Notu Kaydet</button>
    </form>
</body>
</html>
