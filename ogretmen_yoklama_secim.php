<?php
session_start();
include 'db.php';


$ogretmen_id = $_SESSION['kullanici_id'] ?? null;

if (!$ogretmen_id) {
    die("Giriş yapmanız gerekiyor.");
}


$stmt = $conn->prepare("SELECT brans FROM kullanicilar WHERE id = :id AND rol = 'ogretmen'");
$stmt->execute(['id' => $ogretmen_id]);
$ogretmen = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ogretmen) {
    die("Öğretmen bilgisi bulunamadı.");
}

$ogretmen_brans = $ogretmen['brans'];

$kayitBasarili = false;
$ogrenciler = null;
$yoklama_id = null;


$stmt = $conn->prepare("SELECT id, ders_adi FROM dersler WHERE brans = :brans");
$stmt->execute(['brans' => $ogretmen_brans]);
$dersler = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['secim'])) {
    $ders_id = intval($_POST['ders_id']);
    $tarih = $_POST['tarih'];

    
    $stmt = $conn->prepare("SELECT * FROM dersler WHERE id = :id AND brans = :brans");
    $stmt->execute(['id' => $ders_id, 'brans' => $ogretmen_brans]);
    $ders = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ders) {
        die("Seçilen ders sizin branşınıza ait değil.");
    }

    
    $stmt = $conn->prepare("SELECT * FROM yoklamalar WHERE ders_id = :ders_id AND tarih = :tarih");
    $stmt->execute(['ders_id' => $ders_id, 'tarih' => $tarih]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $stmt = $conn->prepare("INSERT INTO yoklamalar (ders_id, tarih) VALUES (:ders_id, :tarih)");
        $stmt->execute(['ders_id' => $ders_id, 'tarih' => $tarih]);
        $yoklama_id = $conn->lastInsertId();
    } else {
        $yoklama_id = $row['yoklama_id'];
    }

    
    $stmt = $conn->prepare("SELECT id, ad_soyad FROM kullanicilar WHERE rol = 'ogrenci'");
    $stmt->execute();
    $ogrenciler = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST['kaydet'])) {
    $yoklama_id = intval($_POST['yoklama_id']);
    $yoklama = $_POST['yoklama'] ?? [];

    foreach ($yoklama as $ogrenci_id => $durum) {
        $ogrenci_id = intval($ogrenci_id);
        $durum = intval($durum);

      
        $stmt = $conn->prepare("INSERT INTO yoklama_detay (yoklama_id, ogrenci_id, durum) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE durum = VALUES(durum)");
        $stmt->execute([$yoklama_id, $ogrenci_id, $durum]);
    }

    $kayitBasarili = true;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8" />
<title>Yoklama Paneli</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
<style>
    :root {
        --sidebar-bg: #1a1a1a;
        --topbar-bg: #0d0d0d;
        --primary-red: #e60000;
        --hover-red: #ff1a1a;
        --white: #ffffff;
        --text-color: #f2f2f2;
        --card-bg: rgba(0, 0, 0, 0.7);
        --shadow-red: rgba(255, 0, 0, 0.4);
        --input-bg: #2c2c2c;
        --input-border: #444444;
        --input-focus-border: var(--primary-red);
    }
    * {
        margin: 0; padding: 0; box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }
    body {
        background-color: rgba(49, 11, 11, 1);
        color: var(--text-color);
        padding: 30px;
        min-height: 100vh;
    }
    h2 {
        text-align: center;
        margin-bottom: 30px;
        font-weight: 700;
        color: rgb(0, 0, 0);
    }
    form {
        background-color: rgba(255, 255, 255, 0.85);
        max-width: 700px;
        margin: 0 auto 40px;
        padding: 25px 30px;
        border-radius: 10px;
      
    }
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: rgba(0, 0, 0, 0.85);
    }
    select, input[type="date"], input[type="submit"] {
        width: 100%;
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        background-color: var(--input-bg);
        border: 2px solid var(--input-border);
        color: var(--text-color);
        font-size: 16px;
        transition: border-color 0.3s ease, background-color 0.3s ease;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }
    select:focus, input[type="date"]:focus {
        border-color: var(--input-focus-border);
        outline: none;
        background-color: #3a3a3a;
    }
    input[type="submit"] {
        background-color: rgb(0, 0, 0);
        border: none;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    input[type="submit"]:hover {
        background-color:rgba(49, 11, 11, 1);
    }
    table {
        width: 100%;
        border-collapse: collapse;
        max-width: 700px;
        margin: 0 auto;
        background-color: var(--card-bg);
        border-radius: 10px;
        overflow: hidden;
       
        color: var(--white);
    }
    th, td {
        padding: 15px 20px;
        text-align: left;
    }
    th {
        background-color:rgba(49, 11, 11, 1);
        font-weight: 700;
        user-select: none;
    }
    tr:nth-child(even) {
        background-color: rgba(255, 0, 0, 0.1);
    }
   
    select[name^="yoklama"] {
        width: 110px;
        padding: 8px 10px;
        border-radius: 6px;
        border: 1.5px solid white;
        font-weight: 600;
        cursor: pointer;
        background: var(--input-bg);
        color: var(--text-color);
        transition: background-color 0.3s ease;
    }
    select[name^="yoklama"]:hover {
        background-color: rgba(230, 0, 0, 0.3);
    }
    @media (max-width: 768px) {
        form, table {
            width: 90%;
            padding: 20px;
        }
        th, td {
            padding: 12px 10px;
        }
        select[name^="yoklama"] {
            width: 90px;
            font-size: 14px;
        }
    }
 .panel-btn {
    position:absolute;
    left:700px;
 
        display: inline-block;
        margin-top: 30px;
        padding: 12px 25px;
        background-color: rgb(0, 0, 0);
        color: var(--white);
        text-decoration: none;
        font-weight: 600;
        border-radius: 8px;
        
        transition: background-color 0.3s ease;
    }
    .panel-btn:hover {
        background-color: var(--hover-red);
    }
</style>
</head>
<body>

<br><br><br><br><br>

<?php if ($kayitBasarili): ?>
    <p style="text-align:center; font-size: 1.3rem; font-weight: 600; color: var(--primary-red); margin-bottom: 40px;">
        Yoklama alındı.
    </p>
    <div style="text-align:center;">
        <a href="ogretmen_yoklama_secim.php" class="panel-btn" style="position: static; display: inline-block; margin: 0 auto;">Başka Yoklama Al</a>
    </div>
<?php else: ?>

    <form method="post" action="">
        <h2>Yoklama Alma</h2>
        <label>Ders Seç:</label>
        <select name="ders_id" required>
            <?php foreach ($dersler as $row) { ?>
                <option value="<?= htmlspecialchars($row['id']) ?>" <?= (isset($ders_id) && $ders_id == $row['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['ders_adi']) ?>
                </option>
            <?php } ?>
        </select>

        <label>Tarih:</label>
        <input type="date" name="tarih" value="<?= isset($tarih) ? htmlspecialchars($tarih) : '' ?>" required>

        <input type="submit" name="secim" value="Öğrencileri Getir">
    </form>

    <?php if ($ogrenciler) { ?>
        <form method="post" action="">
            <input type="hidden" name="yoklama_id" value="<?= htmlspecialchars($yoklama_id) ?>">
            <table>
                <thead>
                    <tr>
                        <th>Öğrenci</th>
                        <th>Yoklama</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ogrenciler as $ogr) { ?>
                        <tr>
                            <td><?= htmlspecialchars($ogr['ad_soyad']) ?></td>
                            <td>
                                <select name="yoklama[<?= htmlspecialchars($ogr['id']) ?>]">
                                    <option value="1">Geldi</option>
                                    <option value="0">Gelmedi</option>
                                </select>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <br>
            <input type="submit" name="kaydet" value="Yoklamayı Kaydet">
        </form>
    <?php } ?>

<?php endif; ?>

<a href="ogretmen_panel.php" class="panel-btn">Panele Dön</a>

</body>
</html>