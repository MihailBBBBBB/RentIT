<?php
session_start();
include_once '../include/places.php';

$balance = 0.0; // дефолтное значение

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT Balance FROM users WHERE User_id = ?");
    $stmt->execute([$user_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $balance = (float)$row['Balance'];
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>RentIT Support</title>
  <link rel="stylesheet" href="../css/index.css">
  <link rel="stylesheet" href="../css/support.css">
  <link rel="stylesheet" href="../css/topup.css">
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
                    <a href="profile_settings.php" class="nav-link">My profile</a>
                    <a href="support.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'support.php' ? 'active' : ''; ?>">Support</a>
                    <a href="https://www.paypal.com/donate/?hosted_button_id=4264QAURH9QKC" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'https://www.paypal.com/donate/?hosted_button_id=4264QAURH9QKC' ? 'active' : ''; ?>">Donate</a>
                </nav>
                <div class="account">
                    <div class="balance"><?= number_format($balance, 2) ?> €</div>
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
                    <a href="profile_settings.php" class="nav-link">My profile</a>
                    <a href="support.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'support.php' ? 'active' : ''; ?>">Support</a>
                    <a href="https://www.paypal.com/donate/?hosted_button_id=4264QAURH9QKC" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'https://www.paypal.com/donate/?hosted_button_id=4264QAURH9QKC' ? 'active' : ''; ?>">Donate</a>
                </nav>
                <div class="account">
                    <div class="balance"><?= number_format($balance, 2) ?> €</div>
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
                <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">Home</a>
                <a href="popular.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'popular.php' ? 'active' : ''; ?>">Popular</a>
                <a href="offers.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'offers.php' ? 'active' : ''; ?>">Catalog</a>
                <a href="support.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'support.php' ? 'active' : ''; ?>">Support</a>
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

  <div class="container">
    <h1>Frequently Asked Questions</h1>

    <?php
    $faqs = [
      "How do I top up my wallet?" => "You can top up your wallet by clicking the “Top up” button in the header and following the payment instructions via Stripe.",
      "How can I book a venue?" => "Browse the catalog, select a venue, and click the “Book” button. You can choose your time slot and confirm your reservation online.",
      "Can I cancel or reschedule my booking?" => "Yes, you can cancel or reschedule your booking from your account dashboard, as long as it is done at least 24 hours before the scheduled time.",
      "How do I contact support?" => "You can submit a support ticket using the “Support” page, or email us directly at <strong>support@rentit.com</strong>. We will respond within 24 hours.",
      "Is there a refund policy?" => "Refunds are available for cancellations made within the allowed period (at least 24 hours before the event). For special cases, contact support.",
      "Can I change my account information?" => "Yes, you can update your name, email, and password in your profile settings under the “Account” section.",
      "How can I view my booking history?" => "All your previous and upcoming bookings are listed in the “My Bookings” section of your account dashboard.",
      "Do you support business accounts?" => "Yes! RentIT offers business accounts with invoice-based payments and dedicated support. Contact us for more information.",
      "Is my payment information secure?" => "All transactions are processed securely through Stripe. RentIT does not store or have access to your payment data.",
      "Can I rent multiple venues at once?" => "Yes, you can add multiple venues to your booking list and confirm them together at checkout."
    ];

    foreach ($faqs as $question => $answer): ?>
      <div class="faq-item">
        <div class="faq-question">
          <?= htmlspecialchars($question) ?>
          <span class="arrow">▶</span>
        </div>
        <div class="faq-answer">
          <p><?= $answer ?></p>
        </div>
      </div>
    <?php endforeach; ?>

    <div class="contact-box">
      <h2>Still have questions?</h2>
      <p>If you didn’t find the answer you were looking for, feel free to contact us directly at:</p>
      <p><a href="mailto:rentit@rentit.com">rentit@rentit.com</a></p>
      <button onclick="window.location.href='mailto:rentit@rentit.com'">Send Email</button>
    </div>
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
