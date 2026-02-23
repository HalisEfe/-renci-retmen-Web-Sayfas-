<?php
session_start();
if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'yönetici') {
    header('Location: login.php');
    exit;
}
include 'db.php';
$stmt = $conn->query("SELECT id, ad_soyad, email FROM kullanicilar WHERE rol = 'ogrenci'");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Öğrenci Listesi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0f0f, #1c1c1c);
            color: #fff;
            padding: 40px 20px;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
            margin: auto;
            background: rgba(255,255,255,0.02);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 0 15px rgba(255, 0, 0, 0.2);
        }
        h2 {
            color: #ff1a1a;
            margin-bottom: 20px;
            text-align: center;
            font-size: 28px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: rgba(255,255,255,0.03);
            border-radius: 12px;
            overflow: hidden;
        }
        th, td {
            padding: 14px 18px;
            text-align: left;
        }
        th {
            background-color: rgba(255, 0, 0, 0.2);
            color: #fff;
            font-weight: 600;
        }
        td {
            background-color: rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        tr:hover td {
            background-color: rgba(255, 0, 0, 0.1);
        }
        a.back-button {
            display: inline-block;
            margin-top: 30px;
            text-decoration: none;
            background-color: #e60000;
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: bold;
        }
        a.back-button:hover {
            background-color: #ff1a1a;
        }
        .edit-link {
            color: #ff4d4d;
            text-decoration: none;
            font-weight: bold;
        }
        .edit-link:hover {
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }
            th { display: none; }
            td {
                position: relative;
                padding-left: 50%;
            }
            td::before {
                content: attr(data-label);
                position: absolute;
                left: 18px;
                top: 14px;
                font-weight: bold;
                color: #ccc;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Öğrenci Listesi</h2>
        <table>
            <thead>
                <tr>
                    <th>Ad Soyad</th>
                    <th>Email</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td data-label="Ad Soyad"><?= htmlspecialchars($row['ad_soyad']) ?></td>
                    <td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>
                    <td data-label="İşlem">
                        <a href="ogrenci_duzenle.php?id=<?= $row['id'] ?>" class="edit-link">Düzenle</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="danisman_panel.php" class="back-button">← Geri Dön</a>
    </div>
</body>
</html>
