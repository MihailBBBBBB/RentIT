<?php
session_start();
include_once '../include/places.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile Settings — RentIT</title>
  <link rel="stylesheet" href="../css/index.css">
  <link rel="stylesheet" href="../css/topup.css">
  <style>
    body {
      background-color: #f9fafb;
      font-family: 'Inter', sans-serif;
      color: #111827;
    }

    .profile-container {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      gap: 3rem;
      max-width: 1100px;
      margin: 3rem auto;
      padding: 2rem;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    /* Левая часть — карточка профиля */
    .profile-info {
      width: 320px;
      background-color: #f3f4f6;
      padding: 2rem 1.5rem;
      border-radius: 12px;
      text-align: center;
      box-shadow: inset 0 0 6px rgba(0,0,0,0.05);
    }

    .profile-info img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #e5e7eb;
      margin-bottom: 1rem;
    }

    .profile-info h2 {
      font-size: 1.3rem;
      margin-bottom: 0.2rem;
      color: #111827;
    }

    .profile-info p {
      color: #4b5563;
      margin: 0.2rem 0;
    }

    .profile-info .email {
      font-weight: 500;
      color: #2563eb;
    }

    /* Правая часть — форма */
    .profile-form {
      flex: 1;
    }

    .profile-form h1 {
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 1.5rem;
    }

    .profile-form label {
      display: block;
      font-weight: 600;
      margin-top: 1rem;
      color: #374151;
    }

    .profile-form input[type="text"],
    .profile-form input[type="email"],
    .profile-form input[type="password"],
    .profile-form input[type="file"] {
      width: 100%;
      padding: 0.6rem 0.8rem;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      margin-top: 0.3rem;
      font-size: 0.95rem;
    }

    .btn {
      display: inline-block;
      margin-top: 1.5rem;
      background-color: #3b82f6;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    .btn:hover {
      background-color: #2563eb;
    }

    .btn.danger {
      background-color: #ef4444;
    }

    .btn.danger:hover {
      background-color: #dc2626;
    }

    @media (max-width: 850px) {
      .profile-container {
        flex-direction: column;
        align-items: center;
      }
      .profile-info {
        width: 100%;
      }
    }
  </style>
</head>
<body>

<?php if (isset($_SESSION['user_id'])) { ?>
    <?php if ($_SESSION['is_admin'] == 1) { ?>
      <header class="header">
        <div class="brand"><strong>RentIT</strong></div>
        <nav class="nav">
          <a href="index.php" class="nav-link ">Home</a>
          <a href="popular.php" class="nav-link">Popular</a>
          <a href="offers.php" class="nav-link active">Catalog</a>
          <a href="profile_settings.php" class="nav-link">My profile</a>
          <a href="support.php" class="nav-link">Support</a>
          <a href="https://www.paypal.com/donate/?hosted_button_id=4264QAURH9QKC" class="nav-link">Donate</a>
        </nav>
        <div class="account">
          <div class="balance">0.00 €</div>


          <!-- Кнопка Top-up -->
          <button id="topupBtn" style="background-color:#22c55e;color:white;padding:8px 16px;
              border-radius:6px;border:1px solid #16a34a;cursor:pointer;
              font-weight:500;">Top up</button>

          <!-- Popup Window -->
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


          <a href="addPlace.php" class="nav-link ">Add Place</a>
          <a href="../include/logOut.php"><button class="btn primary login">Log Out</button></a>
        </div>
      </header>
    <?php } else { ?>
      <header class="header">
        <div class="brand"><strong>RentIT</strong></div>
        <nav class="nav">
          <a href="index.php" class="nav-link ">Home</a>
          <a href="popular.php" class="nav-link">Popular</a>
          <a href="offers.php" class="nav-link active">Catalog</a>
          <a href="profile_settings.php" class="nav-link">My profile</a>
          <a href="support.php" class="nav-link">Support</a>
          <a href="https://www.paypal.com/donate/?hosted_button_id=4264QAURH9QKC" class="nav-link">Donate</a>
        </nav>
        <div class="account">
          <div class="balance">0.00 €</div>


          <button id="topupBtn" 
                  style="background-color:#22c55e;color:white;padding:8px 16px;
                        border-radius:6px;border:1px solid #16a34a;cursor:pointer;
                        font-weight:500;">Top up</button>

          <!-- Popup Window -->
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


          <a href="../include/logOut.php"><button class="btn primary login">Log Out</button></a>
        </div>
      </header>
    <?php } ?>
  <?php } else { ?>
    <header class="header">
      <div class="brand"><strong>RentIT</strong></div>
      <nav class="nav">
        <a href="index.php" class="nav-link ">Home</a>
        <a href="popular.php" class="nav-link">Popular</a>
        <a href="offers.php" class="nav-link active">Catalog</a>
        <a href="support.php" class="nav-link">Support</a>
        <a href="https://www.paypal.com/donate/?hosted_button_id=4264QAURH9QKC" class="nav-link">Donate</a>
      </nav>
      <div class="account">
        <button id="topupBtn" 
                  style="background-color:#22c55e;color:white;padding:8px 16px;
                        border-radius:6px;border:1px solid #16a34a;cursor:pointer;
                        font-weight:500;">Top up</button>

        <!-- Всплывающее окно -->
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
  <!-- Леваяfff колонка — информация о пользователе -->
  <div class="profile-info">
    <img src="../<?= htmlspecialchars($user['avatar'] ?? 'img/default-avatar.png') ?>" alt="User Avatar">
    <h2><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h2>
    <p class="email"><?= htmlspecialchars($user['email']) ?></p>
  </div>

  <!-- Правая колонка — форма редактирования -->
  <form method="POST" enctype="multipart/form-data" class="profile-form">
    <h1>My Profile Settings</h1>

    <?php if ($success): ?>
      <p style="color:green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
      <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <label for="avatar">Change Avatar:</label>
    <input type="file" name="avatar" accept="image/*">

    <label for="first_name">First Name:</label>
    <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>

    <label for="last_name">Last Name:</label>
    <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>

    <label for="email">Email:</label>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>

    <label for="password">New Password (leave blank to keep current):</label>
    <input type="password" name="password" id="password" placeholder="••••••••">

    <button type="submit" class="btn">Save Changes</button>

    <div style="margin-top:1rem;">
      <form action="../include/delete_account.php" method="POST" onsubmit="return confirm('Are you sure you want to delete your account?');">
        <button type="submit" class="btn danger">Delete Account</button>
      </form>
    </div>
  </form>
</div>

<footer>© 2025 RentIT. All rights reserved.</footer>

</body>
</html>
