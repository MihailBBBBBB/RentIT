<?php
session_start();
include_once '../include/dbh.inc.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Загружаем данные пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE User_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['first_name'] ?? '');
  $surname = trim($_POST['last_name'] ?? '');
  $mail = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');
  $avatar_path = $user['Avatar'];

  // === Обработка загрузки аватарки ===
  if (!empty($_FILES['avatar']['name'])) {
    $upload_dir = realpath(__DIR__ . '/../img') . '/';

    if (!$upload_dir || !is_dir($upload_dir)) {
        $error = 'Folder img not found. Path: ' . htmlspecialchars(__DIR__ . '/../img');
    } elseif (!is_writable($upload_dir)) {
        $error = 'The img folder is not writable: ' . $upload_dir;
    } else {
        $tmp = $_FILES['avatar']['tmp_name'];
        $filename = basename($_FILES['avatar']['name']);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ext, $allowed)) {
            $new_name = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
            $target = $upload_dir . $new_name;

            if (move_uploaded_file($tmp, $target)) {
                // Удаляем старую аватарку
                if ($user['Avatar'] && $user['Avatar'] !== 'img/default-avatar.png' && file_exists(__DIR__ . '/../' . $user['Avatar'])) {
                    unlink(__DIR__ . '/../' . $user['Avatar']);
                }
                $avatar_path = 'img/' . $new_name;
            } else {
                $error = 'Error moving file. Code: ' . $_FILES['avatar']['error'];
            }
        } else {
            $error = 'Invalid file format (jpg, png, gif, webp).';
        }
    }
}

  // === Обновление профиля ===
  if (empty($error)) {
    if (!empty($password)) {
      $stmt = $pdo->prepare("
        UPDATE users 
        SET Name=?, Surname=?, Mail=?, Password=?, Avatar=? 
        WHERE User_id=?
      ");
      $stmt->execute([$name, $surname, $mail, $password, $avatar_path, $user_id]);
    } else {
      $stmt = $pdo->prepare("
        UPDATE users 
        SET Name=?, Surname=?, Mail=?, Avatar=? 
        WHERE User_id=?
      ");
      $stmt->execute([$name, $surname, $mail, $avatar_path, $user_id]);
    }

    $success = 'Profile updated successfully!';
    // Обновляем данные
    $stmt = $pdo->prepare("SELECT * FROM users WHERE User_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Profile Settings — RentIT</title>
  <link rel="stylesheet" href="../css/index.css">
  <link rel="stylesheet" href="../css/topup.css">
  <link rel="stylesheet" href="../css/profile.css">
</head>
<body>
    <?php if (isset($_SESSION['user_id'])) { ?>
        <?php if ($_SESSION['is_admin'] == 1) { ?>
            <header class="header">
                <div class="brand"><strong>RentIT</strong></div>
                <nav class="nav">
                    <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">Home</a>
                    <a href="popular.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'popular.php' ? 'active' : ''; ?>">Popular</a>
                    <a href="offers.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'offers.php' ? 'active' : ''; ?>">Catalog</a>
                    <a href="profile_settings.php" class="nav-link active">My profile</a>
                    <a href="support.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'support.php' ? 'active' : ''; ?>">Support</a>
                    <a href="https://www.paypal.com/donate/?hosted_button_id=4264QAURH9QKC" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'https://www.paypal.com/donate/?hosted_button_id=4264QAURH9QKC' ? 'active' : ''; ?>">Donate</a>
                </nav>
                <div class="account">
                    <div class="balance">0.00 €</div>
                    <button id="topupBtn" style="background-color:#22c55e;color:white;padding:8px 16px;
                        border-radius:6px;border:1px solid #16a34a;cursor:pointer;
                        font-weight:500;">Top up</button>
                    <div id="bottomSheet" class="bottom-sheet">
                        <div class="sheet-content">
                            <div class="sheet-header">
                                <h3>Add Funds</h3>
                                <span id="closeSheet">&times;</span>
                            </div>
                            <form method="POST" action="../checkout.php">
                                <label for="amount">Enter amount (USD)</label>
                                <input type="number" id="amount" name="amount" min="1" placeholder="10" required>
                                <button type="submit">Proceed to Payment</button>
                            </form>
                        </div>
                    </div>
                    <a href="addPlace.php" class="nav-link">Add Place</a>
                    <a href="myReservations.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'myReservations.php' ? 'active' : ''; ?>">My Reservations</a>
                    <a href="../include/logOut.php"><button class="btn primary login">Log Out</button></a>
                </div>
            </header>
        <?php } else { ?>
            <header class="header">
                <div class="brand"><strong>RentIT</strong></div>
                <nav class="nav">
                    <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">Home</a>
                    <a href="popular.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'popular.php' ? 'active' : ''; ?>">Popular</a>
                    <a href="offers.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'offers.php' ? 'active' : ''; ?>">Catalog</a>
                    <a href="profile_settings.php" class="nav-link active">My profile</a>
                    <a href="support.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'support.php' ? 'active' : ''; ?>">Support</a>
                    <a href="https://www.paypal.com/donate/?hosted_button_id=4264QAURH9QKC" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'https://www.paypal.com/donate/?hosted_button_id=4264QAURH9QKC' ? 'active' : ''; ?>">Donate</a>
                </nav>
                <div class="account">
                    <div class="balance">0.00 €</div>
                    <button id="topupBtn" style="background-color:#22c55e;color:white;padding:8px 16px;
                        border-radius:6px;border:1px solid #16a34a;cursor:pointer;
                        font-weight:500;">Top up</button>
                    <div id="bottomSheet" class="bottom-sheet">
                        <div class="sheet-content">
                            <div class="sheet-header">
                                <h3>Add Funds</h3>
                                <span id="closeSheet">&times;</span>
                            </div>
                            <form method="POST" action="../checkout.php">
                                <label for="amount">Enter amount (USD)</label>
                                <input type="number" id="amount" name="amount" min="1" placeholder="10" required>
                                <button type="submit">Proceed to Payment</button>
                            </form>
                        </div>
                    </div>
                    <a href="myReservations.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'myReservations.php' ? 'active' : ''; ?>">My Reservations</a>
                    <a href="../include/logOut.php"><button class="btn primary login">Log Out</button></a>
                </div>
            </header>
        <?php } ?>
    <?php } else { ?>
        <header class="header">
            <div class="brand"><strong>RentIT</strong></div>
            <nav class="nav">
                <a href="index.php" class="nav-link">Home</a>
                <a href="popular.php" class="nav-link">Popular</a>
                <a href="offers.php" class="nav-link">Catalog</a>
                <a href="support.php" class="nav-link">Support</a>
                <a href="https://www.paypal.com/donate/?hosted_button_id=4264QAURH9QKC" class="nav-link">Donate</a>
            </nav>
            <div class="account">
                <button id="topupBtn" style="background-color:#22c55e;color:white;padding:8px 16px;
                    border-radius:6px;border:1px solid #16a34a;cursor:pointer;
                    font-weight:500;">Top up</button>
                <div id="bottomSheet" class="bottom-sheet">
                    <div class="sheet-content">
                        <div class="sheet-header">
                            <h3>Please login!</h3>
                            <span id="closeSheet">&times;</span>
                        </div>
                    </div>
                </div>
                <a href="login.php"><button class="btn primary login">Login / Register</button></a>
            </div>
        </header>
    <?php } ?>

  <div class="profile-container">
    <!-- Левая колонка — информация о пользователе -->
    <div class="profile-info">
      <img src="../<?= htmlspecialchars($user['Avatar'] ?? 'img/default-avatar.png') ?>" alt="User Avatar">
      <h2><?= htmlspecialchars($user['Name'] . ' ' . $user['Surname']) ?></h2>
      <p class="email"><?= htmlspecialchars($user['Mail']) ?></p>
    </div>

    <!-- Правая колонка — форма редактирования -->
    <form method="POST" enctype="multipart/form-data" class="profile-form">
      <h1>My Profile Settings</h1>

      <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
      <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

      <label for="avatar">Change Avatar:</label>
      <input type="file" name="avatar" accept="image/*">

      <label for="first_name">First Name:</label>
      <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($user['Name']) ?>" required>

      <label for="last_name">Last Name:</label>
      <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($user['Surname']) ?>" required>

      <label for="email">Email:</label>
      <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['Mail']) ?>" required>

      <label for="password">New Password (optional):</label>
      <input type="password" name="password" id="password" placeholder="••••••••">

      <button type="submit" class="btn" style="margin-top: 20px;">Save Changes</button>
    </form>
  </div>

  <footer>© 2025 RentIT. All rights reserved.</footer>

  <script>
    const topupBtn = document.getElementById('topupBtn');
    const bottomSheet = document.getElementById('bottomSheet');
    const closeSheet = document.getElementById('closeSheet');

    topupBtn.addEventListener('click', () => bottomSheet.classList.add('active'));
    closeSheet.addEventListener('click', () => bottomSheet.classList.remove('active'));
    window.addEventListener('click', (e) => {
      if (e.target === bottomSheet) bottomSheet.classList.remove('active');
    });
  </script>
</body>
</html>
