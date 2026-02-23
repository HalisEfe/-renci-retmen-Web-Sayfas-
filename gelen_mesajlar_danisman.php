<?php
session_start();
if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'yönetici') {
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
    <meta charset="UTF-8">
    <title>Gelen Mesajlar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff3c3c;
            --secondary-color: #2c3e50;
            --bg-color: #1a1a1a;
            --light-bg: #2d2d2d;
            --text-color: #f5f5f5;
            --border-color: #444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
           background-color:rgba(49, 11, 11, 1);
            background-size: cover;
            color: var(--text-color);
            padding: 40px;
            backdrop-filter: blur(2px);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background:rgba(255, 255, 255, 0.85);
            border-radius: 16px;
            border: 1px solid white;
           
            overflow: hidden;
        }

        h2 {
            color: black;
            padding: 20px;
            text-align: center;
           
            border-bottom: 1px solid white;
            margin-bottom: 0;
        }

        .alert {
            padding: 15px;
            margin: 0;
            text-align: center;
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

        .mesaj-listesi {
            padding: 20px;
        }

        .mesaj {
            background: rgba(0, 0, 0, 0.7);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
          
            
            position: relative;
        }

        .mesaj-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
           
        }

        .mesaj-baslik {
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .mesaj-gonderen {
            font-weight: 500;
            color: red;
        }

        .mesaj-tarih {
            
            color: #aaa;
            font-size: 0.9rem;
        }

        .mesaj-icerik {
            padding: 15px 0;
          
        }

        .sil-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(145deg, #dc3545, #a71d2a);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            font-weight: 600;
        }

        .sil-btn:hover {
            background: linear-gradient(145deg, #a71d2a, #7a0d1a);
            transform: scale(1.05);
        }

        .cevaplar {
            margin-top: 20px;
            padding-top: 15px;
            
        }

        .cevap {
            background: rgba(255, 60, 60, 0.1);
            padding: 15px;
            margin-bottom: 15px;
           
           
        }

        .cevap-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            color: var(--primary-color);
            font-weight: 500;
        }

        .cevap-tarih {
            color: #aaa;
            font-size: 0.8rem;
        }

        .cevap-form {
            margin-top: 20px;
            padding: 15px;
           
            border-radius: 8px;
           
        }

        .cevap-textarea {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid white;
            background: var(--bg-color);
            color: var(--text-color);
            margin-bottom: 10px;
            resize: vertical;
            min-height: 100px;
        }

        .cevap-textarea:focus {
            outline: none;
            box-shadow: 0 0 10px rgba(255, 60, 60, 0.6);
        }

        .btn {
            padding: 10px 20px;
            background: rgb(0, 0, 0);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn:hover {
            background: rgba(49, 11, 11, 1);
            transform: translateY(-2px);
        }

        .no-messages {
            text-align: center;
            padding: 40px;
            color: #aaa;
            font-size: 1.1rem;
        }

        .back-btn {
            display: inline-block;
            margin: 20px;
            padding: 10px 20px;
            background: rgb(0, 0, 0);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s;
            text-align: center;
        }

        .back-btn:hover {
            background-color: rgb(255, 0, 0);
        }

        @media (max-width: 768px) {
            body {
                padding: 20px;
            }
            
            .mesaj-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .mesaj-tarih {
                margin-top: 5px;
            }
            
            .sil-btn {
                position: static;
                margin-top: 10px;
                display: inline-block;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-envelope"></i> Gelen Mesajlar</h2>

        <?php if (!empty($basarili)): ?>
            <div class="alert success"><?= htmlspecialchars($basarili) ?></div>
        <?php endif; ?>
        <?php if (!empty($hata)): ?>
            <div class="alert error"><?= htmlspecialchars($hata) ?></div>
        <?php endif; ?>

        <div class="mesaj-listesi">
            <?php if (empty($mesajlar)): ?>
                <p class="no-messages"><i class="fas fa-comment-slash"></i> Henüz mesajınız bulunmamaktadır.</p>
            <?php else: ?>
                <?php foreach ($mesajlar as $mesaj): ?>
                    <div class="mesaj">
                        <div class="mesaj-header">
                            <div>
                                 <div class="mesaj-gonderen">Gönderen: <?= htmlspecialchars($mesaj['gonderen_adi']) ?></div>
                                <div class="mesaj-baslik"><?= htmlspecialchars($mesaj['konu'] ?: 'Konu Belirtilmemiş') ?></div>
                               
                            </div>
                            
                        </div>
                        
                        <a href="?sil=<?= $mesaj['id'] ?>" class="sil-btn" onclick="return confirm('Mesajı silmek istediğinize emin misiniz?');">
                            <i class="fas fa-trash"></i> Sil
                        </a>
                        
                        <div class="mesaj-icerik">
                            <?= nl2br(htmlspecialchars($mesaj['mesaj'])) ?>
                        </div>
                        
                        <?php $cevaplar = getReplies($conn, $mesaj['id']); ?>
                        <?php if (!empty($cevaplar)): ?>
                            <div class="cevaplar">
                                <h4><i class="fas fa-reply"></i> Cevaplar</h4>
                                <?php foreach ($cevaplar as $cevap): ?>
                                    <div class="cevap">
                                        <div class="cevap-header">
                                            <span><?= htmlspecialchars($cevap['gonderen_adi']) ?></span>
                                            <span class="cevap-tarih"><?= date('d.m.Y H:i', strtotime($cevap['tarih'])) ?></span>
                                        </div>
                                        <div><?= nl2br(htmlspecialchars($cevap['mesaj'])) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="cevap-form">
                            <form method="post">
                                <input type="hidden" name="parent_id" value="<?= $mesaj['id'] ?>">
                                <textarea name="cevap_mesaj" class="cevap-textarea" placeholder="Cevabınızı buraya yazın..." required></textarea>
                                <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Yanıt Gönder</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <center>
        <a href="danisman_panel.php" class="back-btn"> Panele Dön</a>
    </center>
</body>
</html>