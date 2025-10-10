<?php
session_start();
include_once '../include/places.php';
?> 

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>All Offers | RentIT</title>
  <link rel="stylesheet" href="../css/index.css">
  <link rel="stylesheet" href="../css/topup.css">
  <link rel="stylesheet" href="../css/offers.css">

  <style>
    :root {
      --accent: #2563eb;
      --muted: #6b7280;
      --bg: #f8fafc;
      --radius: 14px;
      --shadow: 0 6px 20px rgba(16,24,40,0.08);
      font-family: Inter, system-ui, sans-serif;
    }

    body {
      margin: 0;
      background: var(--bg);
      color: #111827;
    }

    /* Header */
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 24px;
      background: #fff;
      box-shadow: var(--shadow);
      border-radius: 0 0 var(--radius) var(--radius);
    }

    .brand {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 700;
      font-size: 18px;
      color: #111827;
    }

    .brand img {
      width: 32px;
      height: 32px;
      border-radius: 50%;
    }

    nav {
      display: flex;
      gap: 20px;
    }

    nav a {
      text-decoration: none;
      color: #4b5563;
      font-weight: 500;
      transition: 0.2s;
    }

    nav a:hover {
      color: var(--accent);
    }

    .account {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .btn {
      cursor: pointer;
      border-radius: 8px;
      border: 1px solid #ddd;
      padding: 8px 14px;
      font-weight: 500;
      font-size: 14px;
      background: #fff;
      color: #111827;
      transition: 0.2s;
    }

    .btn.primary {
      background: var(--accent);
      color: #fff;
      border: none;
    }

    .btn.primary:hover {
      background: #1d4ed8;
    }

    /* Container */
    .container {
      max-width: 1200px;
      margin: 30px auto;
      padding: 0 20px;
    }

    /* Catalog */
    .section h3 {
      font-size: 22px;
      margin-bottom: 16px;
    }

    .catalog {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 18px;
    }

    .card {
      background: #fff;
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: transform 0.15s ease-in-out;
    }

    .card:hover {
      transform: scale(1.03);
    }

    .card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
    }

    .content {
      padding: 14px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      flex: 1;
    }

    .meta {
      font-size: 13px;
      color: var(--muted);
    }

    .bottom {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 10px;
    }

    .price {
      font-weight: 600;
    }

    footer {
      text-align: center;
      padding: 1.5rem;
      margin-top: 3rem;
      color: #666;
      background: #fff;
      box-shadow: 0 -2px 4px rgba(0,0,0,0.05);
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

  <div class="container">
    <section class="section">
      <h3>All Available Venues</h3>
      <div id="catalog" class="catalog">
        <?php foreach ($places as $p) { ?>
          <div class="card">
            <img src="<?php echo '../img/' . htmlspecialchars($p['Foto']); ?>" alt="<?php echo htmlspecialchars($p['Name']); ?>">
            <div class="content">
              <div>
                <h4><?php echo htmlspecialchars($p['Name']); ?></h4>
                <div class="meta"><?php echo htmlspecialchars($p['Description']); ?> • <?php echo htmlspecialchars($p['Adress']); ?></div>
              </div>
              <div class="bottom">
                <div class="price">€<?php echo number_format($p['Price'], 2); ?> / hr</div>
                <a href="aboutOffer.php?id=<?php echo htmlspecialchars($p['Place_id']); ?>" class="btn primary">Book</a>
              </div>
            </div>
          </div>
          <?php } ?>
        </div>
      </section>
    </div>

  <footer>© 2025 RentIT. All rights reserved.</footer>


    <script>
    const faqItems = document.querySelectorAll('.faq-item');
    faqItems.forEach(item => {
      item.querySelector('.faq-question').addEventListener('click', () => {
        faqItems.forEach(el => {
          if (el !== item) el.classList.remove('open');
        });
        item.classList.toggle('open');
      });
    });

    const topupBtn = document.getElementById('topupBtn');
    const bottomSheet = document.getElementById('bottomSheet');
    const closeSheet = document.getElementById('closeSheet');

    topupBtn.addEventListener('click', () => {
      bottomSheet.classList.add('active');
    });

    closeSheet.addEventListener('click', () => {
      bottomSheet.classList.remove('active');
    });

    // Закрытие при клике вне контента
    window.addEventListener('click', (e) => {
      if (e.target === bottomSheet) {
        bottomSheet.classList.remove('active');
      }
    });
  </script>
</body>
</html>