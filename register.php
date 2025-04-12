<?php
session_start();
require 'db.php';

$error = ""; // Variabel error untuk PHP
$success = ""; // Variabel untuk pesan sukses

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validasi panjang password minimal 8 karakter di PHP
    if (strlen($password) < 8) {
        $error = "Huruf kurang dari 8";
    } else {
        // Hash password dan simpan ke database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);

        // Simpan pesan sukses ke dalam session
        $_SESSION['success'] = "Registrasi berhasil!";
        
        // Redirect ke halaman yang sama agar refresh tidak mengulang post data
        header("Location: register.php");
        exit();
    }
}

// Ambil pesan sukses dari session dan hapus agar tidak muncul setelah refresh
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Palatino;
            background: linear-gradient(to right, #bce7fd, #e2fcbf, #55c2ff);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background: rgb(253, 254, 254);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 250px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            color: #333333;
        }

        form {
            text-align: left;
        }

        form label {
            display: block;
            margin: 10px 0 5px;
            color: rgb(85, 85, 85);
        }

        form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
            display: block;
        }

        .success {
            color: green;
            font-size: 14px;
            margin-bottom: 10px;
            display: block;
        }

        form button {
            width: 100%;
            padding: 10px;
            background-color: rgb(108, 207, 214);
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        form button:hover {
            background: linear-gradient(to right, #bce7fd, #e2fcbf, #55c2ff);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Register</h1>

        <!-- Menampilkan pesan sukses jika ada -->
        <?php if (!empty($success)): ?>
            <span class="success"><?php echo $success; ?></span>
        <?php endif; ?>

        <!-- Form Register -->
        <form method="POST" onsubmit="return validateForm()">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required onkeyup="checkPassword()">
            <span id="password-error" class="error"><?php echo $error; ?></span>

            <button type="submit">Register</button>

            <p>Sudah punya akun? <a href="login.php">Login</a></p>
        </form>
    </div>

    <script>
        function checkPassword() {
            let password = document.getElementById("password").value;
            let errorText = document.getElementById("password-error");

            if (password.length < 8) {
                errorText.textContent = "Huruf kurang dari 8";
            } else {
                errorText.textContent = "";
            }
        }

        function validateForm() {
            let password = document.getElementById("password").value;
            if (password.length < 8) {
                alert("Password harus minimal 8 karakter!");
                return false; // Mencegah form terkirim
            }
            return true;
        }
    </script>
</body>
</html>
