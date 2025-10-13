<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/index.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('../img/situations.jpg') no-repeat center center fixed;
            backdrop-filter: blur(10px);
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        .container {
            background: linear-gradient(135deg, #6b48ff, #a73bff);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            color: white;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            color: white;
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .btn {
            background: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            width: 100%;
        }
        .btn:hover {
            background: #e0e0e0;
        }
        .error {
            color: #ff4444;
            margin-bottom: 10px;
        }
        .success {
            color: #44ff44;
            margin-bottom: 10px;
        }
        .switch-form {
            color: white;
            margin-top: 15px;
        }
        .switch-form a {
            color: #fff;
            text-decoration: underline;
        }
        .invalid {
            border: 2px solid #ff4444;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <form action="../include/login.inc.php" method="POST" onsubmit="return validateLogin()">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <p class="switch-form">Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <script>
        function validateLogin() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            let isValid = true;

            // email dermo
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                document.getElementById('email').classList.add('invalid');
                alert('Please enter a valid email address.');
                isValid = false;
            } else {
                document.getElementById('email').classList.remove('invalid');
            }

            // minimum 6 
            if (password.length < 6) {
                document.getElementById('password').classList.add('invalid');
                alert('Password must be at least 6 characters long.');
                isValid = false;
            } else {
                document.getElementById('password').classList.remove('invalid');
            }

            return isValid;
        }
    </script>

</body>
</html>