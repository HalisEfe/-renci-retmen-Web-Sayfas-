<?php
session_start();
include 'db.php';

if (isset($_POST['kaydet'])) {
    $yoklama_id = intval($_POST['yoklama_id']);
    $yoklama = $_POST['yoklama']; 

    foreach ($yoklama as $ogrenci_id => $durum) {
        $ogrenci_id = intval($ogrenci_id);
        $durum = intval($durum);

       
        $stmt = $conn->prepare("SELECT * FROM yoklama_detay WHERE yoklama_id = :yoklama_id AND ogrenci_id = :ogrenci_id");
        $stmt->execute(['yoklama_id' => $yoklama_id, 'ogrenci_id' => $ogrenci_id]);
        $varmi = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($varmi) {
        
            $stmt = $conn->prepare("UPDATE yoklama_detay SET durum = :durum WHERE id = :id");
            $stmt->execute(['durum' => $durum, 'id' => $varmi['id']]);
        } else {
      
            $stmt = $conn->prepare("INSERT INTO yoklama_detay (yoklama_id, ogrenci_id, durum) VALUES (:yoklama_id, :ogrenci_id, :durum)");
            $stmt->execute(['yoklama_id' => $yoklama_id, 'ogrenci_id' => $ogrenci_id, 'durum' => $durum]);
        }
    }

    echo "Yoklama başarıyla kaydedildi.";
}
?>
