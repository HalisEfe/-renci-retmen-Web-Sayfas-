<?php
session_start();
if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'ogretmen') {
    header('Location: login.php');
    exit;
}

include 'db.php';

$ogretmen_id = $_SESSION['kullanici_id'];


if (isset($_GET['sil']) && is_numeric($_GET['sil'])) {
    $sil_id = (int)$_GET['sil'];

    $kontrol = $conn->prepare("SELECT * FROM mesajlar WHERE id = ? AND alici_id = ?");
    $kontrol->execute([$sil_id, $ogretmen_id]);
    if ($kontrol->rowCount() > 0) {
        $conn->prepare("DELETE FROM mesajlar WHERE parent_id = ?")->execute([$sil_id]);
        $conn->prepare("DELETE FROM mesajlar WHERE id = ?")->execute([$sil_id]);
        $_SESSION['basarili'] = "Mesaj başarıyla silindi.";
    } else {
        $_SESSION['hata'] = "Mesaj bulunamadı veya yetkiniz yok.";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['parent_id'], $_POST['cevap_mesaj'])) {
    $parent_id = (int)$_POST['parent_id'];
    $cevap_mesaj = trim($_POST['cevap_mesaj']);

    $stmt = $conn->prepare("SELECT * FROM mesajlar WHERE id = ? AND alici_id = ?");
    $stmt->execute([$parent_id, $ogretmen_id]);
    $ana_mesaj = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ana_mesaj && $cevap_mesaj !== '') {
        $ekle = $conn->prepare("INSERT INTO mesajlar (gonderen_id, alici_id, konu, mesaj, parent_id, tarih) VALUES (?, ?, 'Cevap', ?, ?, NOW())");
        $ekle->execute([$ogretmen_id, $ana_mesaj['gonderen_id'], $cevap_mesaj, $parent_id]);
        $_SESSION['basarili'] = "Cevabınız gönderildi.";
        
        $_SESSION['son_cevaplanan_mesaj'] = $parent_id;
    } else {
        $_SESSION['hata'] = "Cevap gönderilemedi. Lütfen mesaj yazdığınızdan emin olun.";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


$stmt = $conn->prepare("
    SELECT m.*, k.ad_soyad as gonderen_adi 
    FROM mesajlar m 
    JOIN kullanicilar k ON m.gonderen_id = k.id 
    WHERE m.alici_id = :ogretmen_id AND m.parent_id IS NULL 
    ORDER BY m.tarih DESC
");
$stmt->execute(['ogretmen_id' => $ogretmen_id]);
$mesajlar = $stmt->fetchAll(PDO::FETCH_ASSOC);


function getReplies($conn, $parent_id) {
    $stmt = $conn->prepare("SELECT m.*, k.ad_soyad as gonderen_adi FROM mesajlar m JOIN kullanicilar k ON m.gonderen_id = k.id WHERE m.parent_id = ? ORDER BY m.tarih ASC");
    $stmt->execute([$parent_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$basarili = $_SESSION['basarili'] ?? '';
$hata = $_SESSION['hata'] ?? '';
unset($_SESSION['basarili'], $_SESSION['hata']);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8" />
<title>Gelen Mesajlar Kutusu</title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');
    body {
        font-family: 'Roboto', sans-serif;
        background-color: rgba(49, 11, 11, 1);
        background-size: cover;
        color: #eee;
        margin: 20px auto;
        max-width: 900px;
        min-height: 100vh;
        padding: 20px;
       
        box-sizing: border-box;
    }
    h1 {
        color:rgb(255, 255, 255);
        margin-bottom: 25px;
        text-align: center;
        text-shadow: 0 0 6pxrgba(255, 0, 0, 0);
        font-weight: 900;
        font-size: 2.8rem;
    }
    .mesaj-listesi {
        max-width: 800px;
        margin: auto;
    }
    .mesaj, .cevap {
        background: rgba(0,0,0,0.75);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 0 10pxrgba(230, 0, 0, 0);
        position: relative;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    
    .mesaj {
        border-left: 6px solid #e60000;
    }
    .cevap {
        margin-left: 50px;
        border-left: 4px solid #ff6666;
        background: rgba(30,30,30,0.9);
        border-radius: 10px;
        box-shadow: 0 0 8pxrgba(0, 0, 0, 0);
    }
    .mesaj h3, .cevap h4 {
        margin-bottom: 12px;
        font-weight: 700;
        color: #ff4d4d;
        text-shadow: 0 0 5pxrgba(255, 102, 102, 0);
    }
    small {
        color: #bbb;
        display: block;
        margin-bottom: 15px;
        font-style: italic;
    }
    p {
        line-height: 1.7;
        font-size: 1.05rem;
        white-space: pre-wrap;
    }
    form textarea {
        width: 100%;
        border-radius: 12px;
        border: none;
        padding: 12px;
        font-size: 15px;
        margin-top: 12px;
        resize: vertical;
        background-color: #222;
        color: #eee;
        box-sizing: border-box;
        box-shadow: inset 0 0 8pxrgba(24, 7, 7, 0);
        transition: box-shadow 0.3s ease;
    }
    

    form button {
        background-color:rgba(0, 0, 0, 0.44);
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 14px;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 0 8pxrgba(230, 0, 0, 0);
        font-size: 1rem;
    }
    form button:hover {
        background-color: #ff3333;
        box-shadow: 0 0 14pxrgba(255, 102, 102, 0);
    }
    .sil-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        background-color: #bb4444;
        border: none;
        color: white;
        padding: 6px 16px;
        border-radius: 10px;
        font-size: 13px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        text-decoration: none;
        font-weight: 700;
        box-shadow: 0 0 6pxrgba(187, 68, 68, 0);
    }
    .sil-btn:hover {
        background-color: #ff4444;
        box-shadow: 0 0 10pxrgba(255, 102, 102, 0);
    }
    .alert {
        padding: 14px 20px;
        background-color: #44bb44;
        margin-bottom: 28px;
        border-radius: 12px;
        color: white;
        font-weight: 700;
        text-align: center;
        box-shadow: 0 0 10px #44bb44cc;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }
    .alert-error {
        background-color: #bb4444;
        box-shadow: 0 0 10pxrgba(187, 68, 68, 0);
    }
    p.no-messages {
        text-align: center;
        font-size: 1.3rem;
        color: #ff6666;
        font-weight: 600;
        margin-top: 50px;
        text-shadow: 0 0 6pxrgba(230, 0, 0, 0);
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
        background-color: red;
    }
</style>
</head>
<body>

<h1>Gelen Mesajlar</h1>

<?php if (!empty($basarili)): ?>
    <div class="alert"><?= htmlspecialchars($basarili) ?></div>
<?php endif; ?>
<?php if (!empty($hata)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($hata) ?></div>
<?php endif; ?>

<div class="mesaj-listesi">
<?php if (count($mesajlar) === 0): ?>
    <p class="no-messages">Hiç mesajınız yok.</p>
<?php else: ?>
    <?php foreach ($mesajlar as $mesaj): ?>
        <div class="mesaj">
            <h3><?= htmlspecialchars($mesaj['konu'] ?: 'Konu yok') ?></h3>
            <small>Gönderen: <?= htmlspecialchars($mesaj['gonderen_adi']) ?> | <?= date('d.m.Y H:i', strtotime($mesaj['tarih'])) ?></small>
            <p><?= nl2br(htmlspecialchars($mesaj['mesaj'])) ?></p>

            <a href="?sil=<?= $mesaj['id'] ?>" class="sil-btn" onclick="return confirm('Mesajı silmek istediğinize emin misiniz?');">Sil</a>

            <?php
            $cevaplar = getReplies($conn, $mesaj['id']);
            foreach ($cevaplar as $cevap): ?>
                <div class="cevap">
                    <h4>Cevap - <?= htmlspecialchars($cevap['gonderen_adi']) ?></h4>
                    <small><?= date('d.m.Y H:i', strtotime($cevap['tarih'])) ?></small>
                    <p><?= nl2br(htmlspecialchars($cevap['mesaj'])) ?></p>
                </div>
            <?php endforeach; ?>

            <?php
            
            if (!isset($_SESSION['son_cevaplanan_mesaj']) || $_SESSION['son_cevaplanan_mesaj'] !== $mesaj['id']):
            ?>
                <form method="post" action="">
                    <input type="hidden" name="parent_id" value="<?= $mesaj['id'] ?>">
                    <textarea name="cevap_mesaj" placeholder="Cevabınızı yazın..." required></textarea>
                    <button type="submit">Cevap Gönder</button>
                </form>
            <?php else: ?>
                <p style="color:#ff6666; font-weight:700; margin-top:12px;">Bu mesaja yeni bir cevap gönderildi.</p>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>

<?php

unset($_SESSION['son_cevaplanan_mesaj']);
?>
 <a href="ogretmen_panel.php" class="panel-btn"> Panele Dön</a>
</body>
</html>