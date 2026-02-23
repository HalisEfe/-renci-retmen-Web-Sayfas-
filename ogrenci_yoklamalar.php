<?php
session_start();
include 'db.php';

if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'ogrenci') {
    header('Location: login.php');
    exit;
}

$kullanici_id = $_SESSION['kullanici_id'];


$sql = "
    SELECT y.tarih, d.ders_adi, yd.durum
    FROM yoklama_detay yd
    INNER JOIN yoklamalar y ON yd.yoklama_id = y.yoklama_id
    INNER JOIN dersler d ON y.ders_id = d.id
    WHERE yd.ogrenci_id = :ogrenci_id
    ORDER BY y.tarih DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute(['ogrenci_id' => $kullanici_id]);
$yoklamalar = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8" />
<title>Yoklamalarım</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
<style>
    :root {
        --bg-gradient: linear-gradient(135deg, #74ebd5, #acb6e5);
        --primary: #000000;
        --text-dark: #2c3e50;
        --white: #fff;
        --secondary: #f1f1f1;
        --hover-bg: #333333;
        --green: #28a745;
        --red: #dc3545;
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

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table th, table td {
        padding: 14px;
        text-align: center;
        border: 1px solid #ddd;
        font-size: 18px;
    }

    table th {
        background-color: var(--primary);
        color: var(--white);
    }

    table tr:nth-child(even) {
        background-color: var(--secondary);
    }

    table tr:hover {
        background-color: #f1f1f1;
    }

    .status {
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 6px;
        display: inline-block;
        font-size: 14px;
        color: white;
    }
    .status.geldi {
        background-color: var(--green);
    }
    .status.gelmedi {
        background-color: var(--red);
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

        table th, table td {
            font-size: 16px;
            padding: 10px;
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
        <h2>Yoklamalarım</h2>
        <?php if (!empty($yoklamalar)): ?>
        <table>
            <thead>
                <tr>
                    <th>Ders</th>
                    <th>Tarih</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($yoklamalar as $y): ?>
                <tr>
                    <td><?= htmlspecialchars($y['ders_adi']) ?></td>
                    <td><?= htmlspecialchars($y['tarih']) ?></td>
                    <td>
                        <?php if ($y['durum'] == 1): ?>
                            <span class="status geldi">Geldi</span>
                        <?php else: ?>
                            <span class="status gelmedi">Gelmedi</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>Henüz yoklama kaydınız bulunmamaktadır.</p>
        <?php endif; ?>

        <a href="ogrenci_panel.php" class="btn">Panele Dön</a>
    </div>
</body>
</html>
