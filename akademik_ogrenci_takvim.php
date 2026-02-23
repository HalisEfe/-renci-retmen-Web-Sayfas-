<?php
session_start();
if (!isset($_SESSION['kullanici_id']) || 
    ($_SESSION['rol'] !== 'ogretmen' && $_SESSION['rol'] !== 'yönetici' && $_SESSION['rol'] !== 'ogrenci')) {
    header("Location: login.php");
    exit;
}
include 'db.php';

$mesaj = "";


if ($_SESSION['rol'] === 'yönetici' && isset($_GET['sil'])) {
    $id = intval($_GET['sil']);
    $stmt = $conn->prepare("DELETE FROM akademik_takvim WHERE id = ?");
    $stmt->execute([$id]);
    $mesaj = "Satır silindi.";
}


if ($_SESSION['rol'] === 'yönetici' && isset($_POST['ekle'])) {
    $tarih = $_POST['tarih'];
    $aciklama = $_POST['aciklama'];
    $stmt = $conn->prepare("INSERT INTO akademik_takvim (tarih, aciklama) VALUES (?, ?)");
    $stmt->execute([$tarih, $aciklama]);
    $mesaj = "Satır eklendi.";
}


$stmt = $conn->query("SELECT * FROM akademik_takvim ORDER BY tarih ASC");
$takvim = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Akademik Takvim</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background:rgba(49, 11, 11, 1);
            color: #f0f0f0;
            padding: 40px;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color:rgb(255, 255, 255);

        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1f1f1f;
            
        }
        th, td {
            border: 1px solid #333;
            padding: 12px 16px;
            text-align: left;
        }
        th {
            background-color: #2a2a2a;
            color: #f2f2f2;
        }
        tr:hover {
            background-color: #262626;
        }
        .mesaj {
            color: #66ff66;
            margin-bottom: 20px;
        }
        .form-box {
            margin-top: 30px;
            background: #1a1a1a;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .form-box input, .form-box textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 15px;
            border: none;
            background: #2a2a2a;
            color: #fff;
            border-radius: 5px;
        }
        .form-box button {
            padding: 10px 20px;
            background-color: #e60000;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }
        .sil-link {
            color: #ff4d4d;
            text-decoration: none;
            font-weight: bold;
        }
        .sil-link:hover {
            text-decoration: underline;
        }
         .panel-btn {
    position:absolute;
    left:700px;
 
        display: inline-block;
        margin-top: 30px;
        padding: 12px 25px;
        background-color: black;
        color: var(--white);
        text-decoration: none;
        font-weight: 600;
        border-radius: 8px;
        box-shadow: 0 4px 8px var(--shadow-red);
        transition: background-color 0.3s ease;
    }
    .panel-btn:hover {
        background-color: rgb(255, 0, 0);
    }
    </style>
</head>
<body>

<h2>Yıllık Akademik Takvim</h2>

<?php if ($mesaj): ?>
    <p class="mesaj"><?= $mesaj ?></p>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Tarih</th>
            <th>Açıklama</th>
            <?php if ($_SESSION['rol'] === 'yönetici') echo "<th>İşlem</th>"; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($takvim as $satir): ?>
            <tr>
                <td><?= htmlspecialchars($satir['tarih']) ?></td>
                <td><?= htmlspecialchars($satir['aciklama']) ?></td>
                <?php if ($_SESSION['rol'] === 'yönetici'): ?>
                    <td><a class="sil-link" href="?sil=<?= $satir['id'] ?>" onclick="return confirm('Bu satırı silmek istediğinize emin misiniz?')">Sil</a></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

 <a href="ogrenci_panel.php" class="panel-btn"> Panele Dön</a>
</body>
</html>
