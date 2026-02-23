<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>- Bilgi Sistemleri -</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container1 {
            position: absolute;
            top: 170px;
            left: 35%;
            transform: translateX(-50%);
            background-color: rgb(255, 255, 255);
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 40%;
            max-width: 200px;
            min-height: 380px;
        }

        .container {
            position: absolute;
            top: 170px;
            left: 57%;
            transform: translateX(-50%);
            background-color: rgb(255, 255, 255);
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 8px 20px rgba(255, 255, 255, 0.1);
            width: 90%;
            max-width: 400px;
        }

        h1, h2, h4 {
            text-align: center;
            color: #333;
        }

        label {
            font-weight: 600;
            color: #444;
            display: block;
            margin-top: 15px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background-color: rgb(0, 0, 0);
            border: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.5s ease, transform 0.5s ease;
        }

        button:hover {
            background-color: rgb(0, 0, 0);
            transform: scale(1.1);
        }

        .back-link {
            text-align: center;
            margin-top: 15px;
        }

        .back-link a {
            color: rgb(255, 0, 0);
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .kvkk-row {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 12px;
            font-size: 14px;
        }

        .kvkk-link {
            color: #4CAF50;
            text-decoration: underline;
            cursor: pointer;
            font-size: 13px;
        }

        
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
        }

        .modal-icerik {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .kapat {
            float: right;
            font-size: 22px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
        }
       
        .kvkk-link {
    color: rgb(255, 0, 0); 
    text-decoration: underline;
    cursor: pointer;
    font-size: 13px;
}
.logo {
    position: absolute;
    left:7px;
    top:150px;
    width: 192px;
    height: 165px;
    border-radius: 15px;
}

body {
    background-image: url('arkaplan.jpg'); 
    background-size: cover; 
    background-position: center; 
    background-repeat: no-repeat; 
}



    </style>
</head>
<body>
    <div class="background">
        <img src
</div>
<div class="container1">
    <h2>T.C.</h2>
    <h4>TÜRKİYE ÜNİVERSİTESİ</h4>
    <img src="logo1.jpg" alt="Logo" class="logo">

</div>

    <div class="container">
        <form action="login_process.php" method="POST">
            <label for="email">E-posta</label>
            <input type="email" id="email" name="email" required>

            <label for="sifre">Şifre</label>
            <input type="password" id="sifre" name="sifre" required>

            <button type="submit">Giriş</button>

            <div class="kvkk-row">
                <input type="checkbox" id="onay" name="onay" required>
                <span class="kvkk-link" onclick="modalAc() "><h3>KVKK metnini okudum, kabul ediyorum.</h3></span>
            </div>
        </form>

        <div class="back-link">
            <p>Terchin Bizsek <a href="register.php"> Başvur!</a></p>
            <p>Soru ve destek için: destek@turkiye.edu.tr </p>
        </div>
    </div>

  
    <div id="kvkkModal" class="modal">
        <div class="modal-icerik">
            <span class="kapat" onclick="modalKapat()">&times;</span>
            <h3>KVKK Aydınlatma Metni</h3>
            <p>
                6698 sayılı Kişisel Verilerin Korunması Kanunu (“KVKK”) uyarınca, kişisel verileriniz;
                veri sorumlusu sıfatıyla tarafımızca aşağıda açıklanan kapsamda işlenebilecektir.
            </p>
            <p><strong>1. Kişisel Verilerin İşlenme Amacı:</strong> Kayıt, sistem içi hizmetler ve kullanıcı destek süreçleri.</p>
            <p><strong>2. Aktarım:</strong> Yalnızca yasal yükümlülükler çerçevesinde ilgili kurumlara.</p>
            <p><strong>3. Toplama Yöntemi:</strong> Elektronik ortamda, kayıt esnasında.</p>
            <p><strong>4. Haklarınız:</strong> Bilgi talep etme, silme, düzeltme ve itiraz haklarınız bulunmaktadır.</p>
            <p><strong>5. Onay:</strong> Bu metni okuyarak, kişisel verilerinizin belirtilen şekilde işlenmesini kabul etmiş olursunuz.</p>
        </div>
    </div>

    <script>
        function modalAc() {
            document.getElementById("kvkkModal").style.display = "block";
        }

        function modalKapat() {
            document.getElementById("kvkkModal").style.display = "none";
        }

        window.onclick = function(event) {
            const modal = document.getElementById("kvkkModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>