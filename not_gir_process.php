<?php
include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $odev_id = $_POST['odev_id'];
    $ogrenci_id = $_POST['ogrenci_id'];
    $not = $_POST['not'];

   
    $sql = "INSERT INTO notlar (odev_id, ogrenci_id, not) VALUES (:odev_id, :ogrenci_id, :not)";
    $stmt = $pdo->prepare($sql);

   
    $stmt->bindParam(':odev_id', $odev_id);
    $stmt->bindParam(':ogrenci_id', $ogrenci_id);
    $stmt->bindParam(':not', $not);

    
    if ($stmt->execute()) {
        echo "Not başarılı bir şekilde kaydedildi!";
    } else {
        echo "Bir hata oluştu, lütfen tekrar deneyin.";
    }
}
?>
