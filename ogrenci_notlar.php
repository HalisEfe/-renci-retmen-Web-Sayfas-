<?php
session_start();
include 'db.php';


if ($_SESSION['rol'] != 'ogrenci') {
    header('Location: login.php');
    exit();
}

$ogrenci_id = $_SESSION['id'];


$sql = "SELECT o.baslik, n.not 
        FROM notlar n 
        JOIN odevler o ON n.odev_id = o.id 
        WHERE n.ogrenci_id = :ogrenci_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':ogrenci_id', $ogrenci_id);
$stmt->execute();
$notlar = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notlar</title>
</head>
<body>
    <h1>Notlarım</h1>

    <?php
    if (count($notlar) > 0) {
        foreach ($notlar as $not) {
            echo "<p><strong>Ödev Başlığı:</strong> " . $not['baslik'] . "<br><strong>Not:</strong> " . $not['not'] . "</p>";
        }
    } else {
        echo "Henüz notlarınız girilmemiş.";
    }
    ?>
</body>
</html>
