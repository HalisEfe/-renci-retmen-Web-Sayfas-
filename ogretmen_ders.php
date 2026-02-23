<?php
session_start();
include 'db.php';


if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] != 'ogretmen') {
    header('Location: login.php');
    exit;
}

$ogretmen_id = $_SESSION['kullanici_id'];


$sql = "SELECT * FROM dersler";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$dersler = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['dersler'])) {
    $selected_dersler = $_POST['dersler'];

  
    foreach ($selected_dersler as $ders_id) {
        $sql = "INSERT INTO ogretmenler_dersler (ogretmen_id, ders_id) VALUES (:ogretmen_id, :ders_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ogretmen_id', $ogretmen_id);
        $stmt->bindParam(':ders_id', $ders_id);
        $stmt->execute();
    }

    echo "Dersler başarıyla seçildi!";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ders Ekle</title>
</head>
<body>
    <h1>Ders Ekle</h1>
    <form method="POST" action="ders_ekle.php">
        <label for="ders_adi">Ders Adı:</label>
        <input type="text" id="ders_adi" name="ders_adi" required><br><br>

        <label for="ders_kodu">Ders Kodu:</label>
        <input type="text" id="ders_kodu" name="ders_kodu" required><br><br>

        <label for="ogretmen_id">Öğretmen ID:</label>
        <input type="number" id="ogretmen_id" name="ogretmen_id" required><br><br>

        <label for="ders_saati">Ders Saati:</label>
        <input type="time" id="ders_saati" name="ders_saati" required><br><br>

        <button type="submit">Ders Ekle</button>
    </form>
</body>
</html>

