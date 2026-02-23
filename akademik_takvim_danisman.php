<?php
session_start();
if (!isset($_SESSION['kullanici_id']) || 
    ($_SESSION['rol'] !== 'ogretmen' && $_SESSION['rol'] !== 'yönetici' && $_SESSION['rol'] !== 'ogrenci')) {
    header("Location: login.php");
    exit;
}
include 'db.php';


$mesaj = "";
if (isset($_SESSION['mesaj'])) {
    $mesaj = $_SESSION['mesaj'];
    unset($_SESSION['mesaj']);
}


if ($_SESSION['rol'] === 'yönetici' && isset($_GET['sil'])) {
    $id = intval($_GET['sil']);
    $stmt = $conn->prepare("DELETE FROM akademik_takvim WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['mesaj'] = "Satır silindi.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


if ($_SESSION['rol'] === 'yönetici' && isset($_POST['ekle'])) {
    $tarih = $_POST['tarih'];
    $aciklama = $_POST['aciklama'];
    $stmt = $conn->prepare("INSERT INTO akademik_takvim (tarih, aciklama) VALUES (?, ?)");
    $stmt->execute([$tarih, $aciklama]);
    $_SESSION['mesaj'] = "Satır eklendi.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
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
            color:rgb(0, 0, 0);
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color:rgb(251, 251, 251);
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
            font-weight: bold;
            text-align: center;
        }
        .form-box {
            margin-top: 30px;
            background:rgba(255, 255, 255, 0.85);
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .form-box input, .form-box textarea {
            width: 100%;
            padding: 5px;
            margin-top: 10px;
            margin-bottom: 15px;
            border: none;
            background: rgba(255, 255, 255, 0.85);
            color:rgba(5, 4, 4, 0.85);
            border-radius: 5px;
        }
        .form-box button {
            padding: 10px 20px;
            background-color:rgb(0, 0, 0);
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
             transition: background-color 0.3s ease;
        }
        .form-box button:hover{
              background-color: rgba(49, 11, 11, 1);
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

<h1>Yıllık Akademik Takvim</h1>

<?php if ($mesaj): ?>
    <p class="mesaj"><?= htmlspecialchars($mesaj) ?></p>
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

<?php if ($_SESSION['rol'] === 'yönetici'): ?>
    <div class="form-box">
        <h2>Akademik Takvime Ekle</h2>
        <form method="POST">
            <input type="date" name="tarih" required>
            <textarea name="aciklama" placeholder="Etkinlik açıklaması" required></textarea>
            <button type="submit" name="ekle">Ekle</button>
        </form>
    </div>
<?php endif; ?>
<a href="danisman_panel.php" class="panel-btn">Panele Dön</a>
</body>
</html>