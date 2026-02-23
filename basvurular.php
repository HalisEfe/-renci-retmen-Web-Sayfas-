<?php
session_start();
include 'db.php';

if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'yönetici') {
    header('Location: login.php');
    exit;
}


$stmt = $conn->query("SELECT * FROM basvuru ORDER BY tarih DESC");

$basvurular = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8" />
<title>Öğrenci Başvuruları - Danışman Öğretmen Paneli</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
<style>
    :root {
        --sidebar-bg: #1a1a1a;
        --primary-red: #e60000;
        --hover-red: #ff1a1a;
        --white: #ffffff;
        --text-color: #f2f2f2;
        --card-bg: rgba(0, 0, 0, 0.7);
        --shadow-red: rgba(255, 0, 0, 0.4);
        --table-border: #444;
        --table-bg: rgba(255, 255, 255, 0.84);
        --button-approve: #4CAF50;
        --button-reject: #f44336;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Inter', sans-serif;
        background-color: rgba(49, 11, 11, 1);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        color: var(--text-color);
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        padding: 40px 20px;
    }
    h2 {
        color: var(--white);
        margin-bottom: 20px;
        font-weight: 700;
        text-align: center;
        width: 100%;
    }
    table {
        width: 100%;
        max-width: 900px;
        border-collapse: collapse;
        background: rgba(255, 255, 255, 0.85);
        border-radius: 12px;
        overflow: hidden;
       
    }
    th, td {
        border: 1px solid var(--table-border);
        padding: 12px 15px;
        
        text-align: left;
        font-size: 15px;
        color: rgb(8, 7, 7);
    }
    th {
        
        background: var(--table-bg);
        font-weight: 600;
        user-select: none;
    }
    .action-btn {
        padding: 8px 14px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        border-radius: 6px;
        font-size: 16px;
        transition: background-color 0.3s;
        color: var(--white);
    }
    .approve { background-color: var(--button-approve); margin-right: 6px; }
    .approve:hover { background-color: #45a049; }
    .reject { background-color: var(--button-reject); }
    .reject:hover { background-color: #da190b; }

    .mesaj-uyari {
        max-width: 900px;
        background-color: rgba(0, 128, 0, 0.85);
        padding: 15px 20px;
        border-radius: 12px;
        font-weight: 600;
        color: #e0ffe0;
        text-align: center;
        box-shadow: 0 0 12px #00cc00;
        margin-bottom: 20px;
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
       
        transition: background-color 0.3s ease;
    }
    .panel-btn:hover {
        background-color: rgb(255, 0, 0);
    }
</style>
</head>
<body>

<div style="width:100%; max-width: 900px;">
    
    <h2>Öğrenci Başvuruları</h2>

    <?php if (isset($_SESSION['mesaj'])): ?>
        <div class="mesaj-uyari">
            <?= htmlspecialchars($_SESSION['mesaj']) ?>
        </div>
        <?php unset($_SESSION['mesaj']); ?>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Ad Soyad</th>
                <th>Email</th>
                <th>Mesaj</th>
                <th>Tarih</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($basvurular as $basvuru): ?>
            <tr>
                <td><?= htmlspecialchars($basvuru['ad_soyad']) ?></td>
                <td><?= htmlspecialchars($basvuru['email']) ?></td>
                <td style="white-space: pre-wrap;"><?= nl2br(htmlspecialchars($basvuru['neden_basvuru'])) ?></td>
                <td><?= htmlspecialchars($basvuru['tarih']) ?></td>
                <td>
                    <form method="post" action="mesaj_islem.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $basvuru['id'] ?>">
                        <input type="hidden" name="ad_soyad" value="<?= $basvuru['ad_soyad'] ?>">
                        <input type="hidden" name="email" value="<?= $basvuru['email'] ?>">
                        <button type="submit" name="onayla" class="action-btn approve" title="Onayla">✅ Kabul Et</button>
                        <button type="submit" name="reddet" class="action-btn reject" title="Reddet">❌ Reddet</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
   <a href="danisman_panel.php" class="panel-btn">Panele Dön</a>
</div>


</body>
</html>
