<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notlarım</title>
</head>
<body>
    <h1>Notlarım</h1>

    <?php
    include 'db.php'; 
    session_start();

    
    if ($_SESSION['rol'] == 'ogrenci') {
       
        $ogrenci_id = $_SESSION['id'];
        $sql = "SELECT o.baslik, n.not 
                FROM notlar n 
                JOIN odevler o ON n.odev_id = o.id 
                WHERE n.ogrenci_id = :ogrenci_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ogrenci_id', $ogrenci_id);
    } else if ($_SESSION['rol'] == 'ogretmen') {
    
        $ogretmen_id = $_SESSION['id'];
        $sql = "SELECT o.baslik, n.not, k.ad_soyad 
                FROM notlar n 
                JOIN odevler o ON n.odev_id = o.id 
                JOIN kullanicilar k ON n.ogrenci_id = k.id 
                WHERE o.ogretmen_id = :ogretmen_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ogretmen_id', $ogretmen_id);
    }

    
    $stmt->execute();
    $notlar = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($notlar) > 0) {
        foreach ($notlar as $not) {
            echo "<p><strong>Ödev Başlığı:</strong> " . $not['baslik'] . "<br><strong>Not:</strong> " . $not['not'] . "</p>";
        }
    } else {
        echo "Henüz not girilmemiş.";
    }
    ?>

</body>
</html>
