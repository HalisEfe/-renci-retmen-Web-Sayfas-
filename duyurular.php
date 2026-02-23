<?php
session_start();
include 'db.php';

if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'ogrenci') {
    header('Location: login.php');
    exit;
}

$ogrenci_id = $_SESSION['kullanici_id'];


$sql_duyurular = "SELECT * FROM duyurular 
                  WHERE hedef = 'tum_ogrenciler' 
                  OR hedef = :hedef 
                  ORDER BY tarih DESC";
$stmt_duyurular = $pdo->prepare($sql_duyurular);
$stmt_duyurular->execute(['hedef' => 'ogrenci_' . $ogrenci_id]);
$duyurular = $stmt_duyurular->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Duyurular</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #74ebd5, #acb6e5);
            --primary: #000000; 
            --text-dark: #2c3e50;
            --white: #fff;
            --secondary: #f1f1f1;
            --hover-bg: #333333;
            --highlight-color: #ff6b6b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color:rgba(49, 11, 11, 1);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 700px;
            width: 100%;
            animation: fadeIn 0.8s ease;
        }

        h2 {
            color: var(--primary);
            font-size: 32px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        ul {
            list-style: none;
            padding: 0;
            margin-top: 20px;
            text-align: left;
        }

        ul li {
            background-color: var(--secondary);
            border-left: 6px solid var(--primary);
            padding: 15px 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            position: relative;
            transition: all 0.3s ease;
            color: var(--text-dark);
        }

        ul li:hover {
            background-color: var(--hover-bg);
            color: var(--white);
            border-left-color: var(--highlight-color);
            transform: scale(1.03);
        }

        .ozel-etiket {
            position: absolute;
            top: 12px;
            right: 15px;
            background: var(--highlight-color);
            color: var(--white);
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            user-select: none;
        }

        strong {
            display: block;
            font-size: 20px;
            margin-bottom: 8px;
        }

        p {
            font-size: 16px;
            line-height: 1.4;
            white-space: pre-line;
        }

        .btn {
            display: inline-block;
            margin-top: 30px;
            background-color: var(--primary);
            color: var(--white);
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s;
        }

        .btn:hover {
            background-color: red;
            transform: translateY(-2px);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 600px) {
            h2 {
                font-size: 24px;
            }

            ul li {
                font-size: 16px;
                padding: 12px 15px;
            }

            .btn {
                font-size: 16px;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Duyurular</h2>
        <ul>
            <?php if (count($duyurular) > 0): ?>
                <?php foreach ($duyurular as $duyuru): ?>
                    <li>
                        <?php if ($duyuru['hedef'] === 'ogrenci_' . $ogrenci_id): ?>
                            <div class="ozel-etiket">Size Özel</div>
                        <?php endif; ?>
                        <strong><?= htmlspecialchars($duyuru['baslik']) ?></strong>
                        <p><?= nl2br(htmlspecialchars($duyuru['icerik'])) ?></p>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>Şu anda size özel bir duyuru bulunmamaktadır.</li>
            <?php endif; ?>
        </ul>
        <a href="ogrenci_panel.php" class="btn">Panele Dön</a>
    </div>
</body>
</html>
