<?php
session_start();
include 'db.php';

if (!isset($_SESSION['kullanici_id']) || $_SESSION['rol'] !== 'ogrenci') {
    header('Location: login.php');
    exit;
}

$ogrenci_id = $_SESSION['kullanici_id'];

$sql_notlar = "SELECT d.ders_adi, n.not_degeri 
               FROM notlar n 
               JOIN dersler d ON n.ders_id = d.id 
               WHERE n.ogrenci_id = :ogrenci_id";
$stmt = $pdo->prepare($sql_notlar);
$stmt->execute(['ogrenci_id' => $ogrenci_id]);
$notlar = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($notlar === false) {
    $notlar = [];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Notlarım</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #74ebd5, #acb6e5);
            --primary: #000000; 
            --text-dark: #2c3e50;
            --white: #fff;
            --secondary: #f1f1f1;
            --hover-bg: #333333; 
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
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: var(--secondary);
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
            background-color: rgba(49, 11, 11, 1);
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
        <h2>Notlarım</h2>
        <table>
            <thead>
                <tr>
                    <th>Ders Adı</th>
                    <th>Not</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($notlar)): ?>
                    <?php foreach ($notlar as $not): ?>
                        <tr>
                            <td><?= htmlspecialchars($not['ders_adi']) ?></td>
                            <td><?= htmlspecialchars($not['not_degeri']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">Henüz notunuz yok.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="ogrenci_panel.php" class="btn">Anasayfaya Dön</a>
    </div>
</body>
</html>
