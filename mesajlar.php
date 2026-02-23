<?php
session_start();
if (!isset($_SESSION['kullanici_id'])) {
    header('Location: login.php');
    exit;
}

include 'db.php';

$kullanici_id = $_SESSION['kullanici_id'];
$rol = $_SESSION['rol'];

$basarili = '';
$hata = '';

if (isset($_GET['basarili'])) {
    $basarili = "Mesajınız gönderildi.";
}
if (isset($_GET['cevap'])) {
    $basarili = "Cevabınız gönderildi.";
}
if (isset($_GET['silindi'])) {
    $basarili = "Mesaj ve cevapları başarıyla silindi.";
}
if (isset($_GET['hata'])) {
    $hata = "Bir hata oluştu. Lütfen tekrar deneyin.";
}
if (isset($_GET['yetki'])) {
    $hata = "Bu mesajı silme yetkiniz yok.";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sil_mesaj_id'])) {
    $sil_id = (int)$_POST['sil_mesaj_id'];

    $stmt = $conn->prepare("SELECT * FROM mesajlar WHERE id = ? AND alici_id = ?");
    $stmt->execute([$sil_id, $kullanici_id]);
    $silinecek_mesaj = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($silinecek_mesaj) {
        $stmt = $conn->prepare("DELETE FROM mesajlar WHERE parent_id = ?");
        $stmt->execute([$sil_id]);

        $stmt = $conn->prepare("DELETE FROM mesajlar WHERE id = ?");
        $stmt->execute([$sil_id]);

        header("Location: mesajlar.php?silindi=1");
        exit;
    } else {
        header("Location: mesajlar.php?yetki=1");
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['alici_id'], $_POST['konu'], $_POST['mesaj']) && !isset($_POST['parent_id']) && !isset($_POST['sil_mesaj_id'])) {
        $alici_id = (int)$_POST['alici_id'];
        $konu = trim($_POST['konu']);
        $mesaj = trim($_POST['mesaj']);
        if ($mesaj !== '') {
            $stmt = $conn->prepare("INSERT INTO mesajlar (gonderen_id, alici_id, konu, mesaj, tarih) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$kullanici_id, $alici_id, $konu, $mesaj]);
            header("Location: mesajlar.php?basarili=1");
            exit;
        } else {
            header("Location: mesajlar.php?hata=1");
            exit;
        }
    } elseif (isset($_POST['parent_id'], $_POST['cevap_mesaj'])) {
        $parent_id = (int)$_POST['parent_id'];
        $cevap_mesaj = trim($_POST['cevap_mesaj']);

        $stmt = $conn->prepare("SELECT * FROM mesajlar WHERE id = ? AND alici_id = ?");
        $stmt->execute([$parent_id, $kullanici_id]);
        $ana_mesaj = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ana_mesaj && $cevap_mesaj !== '') {
            $stmt = $conn->prepare("INSERT INTO mesajlar (gonderen_id, alici_id, konu, mesaj, parent_id, tarih) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$kullanici_id, $ana_mesaj['gonderen_id'], 'Cevap', $cevap_mesaj, $parent_id]);
            header("Location: mesajlar.php?cevap=1");
            exit;
        } else {
            header("Location: mesajlar.php?hata=1");
            exit;
        }
    }
}


$stmt = $conn->prepare("
    SELECT m.*, k.ad_soyad as gonderen_adi 
    FROM mesajlar m 
    JOIN kullanicilar k ON m.gonderen_id = k.id
    WHERE m.alici_id = :kullanici_id AND m.parent_id IS NULL
    ORDER BY m.tarih DESC
");
$stmt->execute(['kullanici_id' => $kullanici_id]);
$mesajlar = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $conn->prepare("
    SELECT m.*, k.ad_soyad as alici_adi 
    FROM mesajlar m 
    JOIN kullanicilar k ON m.alici_id = k.id
    WHERE m.gonderen_id = :kullanici_id AND m.parent_id IS NULL
    ORDER BY m.tarih DESC
");
$stmt->execute(['kullanici_id' => $kullanici_id]);
$gonderilen_mesajlar = $stmt->fetchAll(PDO::FETCH_ASSOC);


function getReplies($conn, $parent_id) {
    $stmt = $conn->prepare("
        SELECT m.*, k.ad_soyad as gonderen_adi 
        FROM mesajlar m 
        JOIN kullanicilar k ON m.gonderen_id = k.id
        WHERE m.parent_id = ?
        ORDER BY m.tarih ASC
    ");
    $stmt->execute([$parent_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$stmt = $conn->prepare("SELECT id, ad_soyad FROM kullanicilar WHERE (rol = 'ogretmen' OR rol = 'yönetici') AND id != :id");
$stmt->execute(['id' => $kullanici_id]);
$kullanicilar = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Mesajlar</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: rgba(49, 11, 11, 1);
            background-size: cover;
            color:rgb(0, 0, 0);
            backdrop-filter: blur(2px);
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .left-column {
            flex: 1;
            min-width: 300px;
            background:rgba(255, 255, 255, 0.85);
            padding: 20px;
            border-radius: 16px;
            border: 1px solidrgb(255, 255, 255);
          color:white
            height: fit-content;
        }

        .right-column {
            flex: 2;
            min-width: 400px;
        }

        h2, h3 {
            color: #ff3c3c;
            text-shadow: 1px 1px 2px black;
            margin-top: 0;
        }

        .mesaj {
            background: rgba(255, 255, 255, 0.85);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 16px;
            border: 1px solid #ff3c3c;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }

        .mesaj:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(255, 60, 60, 0.3);
        }

        .cevap {
            background: rgba(255, 60, 60, 0.1);
            margin: 15px 0 10px 25px;
            padding: 15px;
            border-left: 4px solid #ff3c3c;
            border-radius: 10px;
        }

        .mesaj h4 {
            margin: 0 0 10px 0;
            color: #ff3c3c;
        }

        .alert {
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            backdrop-filter: blur(5px);
        }

        .success {
            background-color: rgba(40, 167, 69, 0.15);
            border-left: 6px solid #28a745;
            color: #c8e6c9;
        }

        .error {
            background-color: rgba(220, 53, 69, 0.15);
            border-left: 6px solid #dc3545;
            color: #f5b7b1;
        }

        .btn {
            padding: 10px 20px;
            cursor: pointer;
            background: linear-gradient(145deg, #ff3c3c, #cc0000);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.3s ease, transform 0.2s ease;
            margin-top: 10px;
        }

        .btn:hover {
            background: linear-gradient(145deg, #cc0000, #990000);
            transform: scale(1.05);
        }

        .btn-sil {
            background: linear-gradient(145deg, #dc3545, #a71d2a);
        }

        .btn-sil:hover {
            background: linear-gradient(145deg, #a71d2a, #7a0d1a);
        }

        textarea, input[type="text"], select {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #ff3c3c;
            border-radius: 8px;
            font-size: 15px;
            background: #1a1a1a;
            color: #f0f0f0;
        }

        textarea:focus, input[type="text"]:focus, select:focus {
            outline: none;
            border-color: #ff3c3c;
          
        }

        small {
            color: #aaa;
            font-size: 13px;
            display: block;
            margin-top: 5px;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .message-actions {
            display: flex;
            gap: 10px;
        }

        .tab-container {
            margin-bottom: 20px;
        }

        .tab-buttons {
            display: flex;
            border-bottom: 1px solid #ff3c3c;
            margin-bottom: 15px;
        }

        .tab-button {
            padding: 10px 20px;
            background: transparent;
            border: none;
            color: #f5f5f5;
            cursor: pointer;
            font-weight: 600;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab-button.active {
            border-bottom: 3px solid #ff3c3c;
            color: #ff3c3c;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .left-column, .right-column {
                width: 100%;
            }
        }

         .panel-btn {
    position:absolute;
    left:700px;
 
        display: inline-block;
        margin-top: 30px;
        padding: 12px 25px;
        background-color: black;
        color: white;
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
    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tabbuttons;
            
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }
            
            tabbuttons = document.getElementsByClassName("tab-button");
            for (i = 0; i < tabbuttons.length; i++) {
                tabbuttons[i].classList.remove("active");
            }
            
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }
    </script>
</head>
<body>

    <div class="container">
       
        <div class="left-column">
            <h3>Yeni Mesaj Gönder</h3>
            <form method="post">
                <label for="alici_id">Alıcı:</label>
                <select name="alici_id" required>
                    <?php foreach ($kullanicilar as $kullanici): ?>
                        <option value="<?= $kullanici['id'] ?>"><?= htmlspecialchars($kullanici['ad_soyad']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="konu">Konu:</label>
                <input type="text" name="konu" required>

                <label for="mesaj">Mesaj:</label>
                <textarea name="mesaj" rows="4" required></textarea>

                <button type="submit" class="btn">Gönder</button>
            </form>
        </div>

        
        <div class="right-column">
            <h2>Mesajlarım</h2>

            <?php if ($basarili): ?>
                <div class="alert success"><?= htmlspecialchars($basarili) ?></div>
            <?php endif; ?>
            <?php if ($hata): ?>
                <div class="alert error"><?= htmlspecialchars($hata) ?></div>
            <?php endif; ?>

            <div class="tab-container">
                <div class="tab-buttons">
                    <button class="tab-button active" onclick="openTab(event, 'gelen')">Gelen Kutusu</button>
                    <button class="tab-button" onclick="openTab(event, 'gonderilen')">Gönderilenler</button>
                </div>

               
                <div id="gelen" class="tab-content active">
                    <?php if (empty($mesajlar)): ?>
                        <p></p>
                    <?php else: ?>
                        <?php foreach ($mesajlar as $mesaj): ?>
                            <div class="mesaj">
                                <div class="message-header">
                                    <h4><?= htmlspecialchars($mesaj['gonderen_adi']) ?> - <?= htmlspecialchars($mesaj['konu']) ?></h4>
                                    <small><?= $mesaj['tarih'] ?></small>
                                </div>
                                <p><?= nl2br(htmlspecialchars($mesaj['mesaj'])) ?></p>
                                
                                <form method="post" class="message-actions">
                                    <input type="hidden" name="sil_mesaj_id" value="<?= $mesaj['id'] ?>">
                                    <button type="submit" class="btn btn-sil" onclick="return confirm('Mesaj ve tüm cevapları silinsin mi?')">Sil</button>
                                </form>

                                <form method="post">
                                    <input type="hidden" name="parent_id" value="<?= $mesaj['id'] ?>">
                                    <textarea name="cevap_mesaj" rows="2" placeholder="Bu mesaja cevap yaz..."></textarea>
                                    <button type="submit" class="btn">Cevap Gönder</button>
                                </form>

                                <?php $cevaplar = getReplies($conn, $mesaj['id']); ?>
                                <?php foreach ($cevaplar as $cevap): ?>
                                    <div class="cevap">
                                        <strong><?= htmlspecialchars($cevap['gonderen_adi']) ?>:</strong><br>
                                        <?= nl2br(htmlspecialchars($cevap['mesaj'])) ?><br>
                                        <small><?= $cevap['tarih'] ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

             
                <div id="gonderilen" class="tab-content">
                    <?php if (empty($gonderilen_mesajlar)): ?>
                        <p></p>
                    <?php else: ?>
                        <?php foreach ($gonderilen_mesajlar as $mesaj): ?>
                            <div class="mesaj">
                                <div class="message-header">
                                    <h4>Alıcı: <?= htmlspecialchars($mesaj['alici_adi']) ?> - <?= htmlspecialchars($mesaj['konu']) ?></h4>
                                    <small><?= $mesaj['tarih'] ?></small>
                                </div>
                                <p><?= nl2br(htmlspecialchars($mesaj['mesaj'])) ?></p>
                                
                                <?php $cevaplar = getReplies($conn, $mesaj['id']); ?>
                                <?php foreach ($cevaplar as $cevap): ?>
                                    <div class="cevap">
                                        <strong><?= htmlspecialchars($cevap['gonderen_adi']) ?>:</strong><br>
                                        <?= nl2br(htmlspecialchars($cevap['mesaj'])) ?><br>
                                        <small><?= $cevap['tarih'] ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <a href="ogrenci_panel.php" class="panel-btn">Panele Dön</a>
</body>
</html>