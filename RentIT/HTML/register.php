<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
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
        .invalid {
            border: 2px solid #ff4444;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registration Form</h2>
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <form action="../include/registration.inc.php" method="POST" onsubmit="return validateRegister()">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="surname">Surname</label>
                <input type="text" id="surname" name="surname" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="per_kod">Personal Code</label>
                <input type="text" id="per_kod" name="per_kod" required oninput="formatPersonalCode(this)">
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
    </div>

    <script>
        function validateRegister() {
            const name = document.getElementById('name').value;
            const surname = document.getElementById('surname').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const perKod = document.getElementById('per_kod').value;
            let isValid = true;

            // Imja toka bukvi 
            const nameRegex = /^[a-zA-Z\s]+$/;
            if (!nameRegex.test(name)) {
                document.getElementById('name').classList.add('invalid');
                alert('Name must contain only letters and spaces.');
                isValid = false;
            } else {
                document.getElementById('name').classList.remove('invalid');
            }

            // Bukvi
            if (!nameRegex.test(surname)) {
                document.getElementById('surname').classList.add('invalid');
                alert('Surname must contain only letters and spaces.');
                isValid = false;
            } else {
                document.getElementById('surname').classList.remove('invalid');
            }

            // po sutji a@a.com +
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
            if (!emailRegex.test(email) || email === 'a@a.lv') {
                document.getElementById('email').classList.add('invalid');
                alert('Please enter a valid email address (e.g., example@domain.com).');
                isValid = false;
            } else {
                document.getElementById('email').classList.remove('invalid');
            }

            // parol min 6 
            if (password.length < 6) {
                document.getElementById('password').classList.add('invalid');
                alert('Password must be at least 6 characters long.');
                isValid = false;
            } else {
                document.getElementById('password').classList.remove('invalid');
            }

            // pers kod 123456-12345
            const perKodRegex = /^\d{6}-\d{5}$/;
            if (!perKodRegex.test(perKod)) {
                document.getElementById('per_kod').classList.add('invalid');
                alert('Personal Code must be in the format 123456-12345 .');
                isValid = false;
            } else {
                document.getElementById('per_kod').classList.remove('invalid');
            }

            return isValid;
        }

        function formatPersonalCode(input) {
            let value = input.value.replace(/[^0-9]/g, ''); // toka bukvi 
            if (value.length > 6) {
                value = value.substring(0, 6) + '-' + value.substring(6, 11);
            }
            input.value = value;
        }
    </script>
</body>
</html>