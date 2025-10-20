<?php
session_start();
include_once '../include/places.php';
include_once '../include/reviews.inc.php';

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

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get the venue ID from the URL
$place_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate $places array and find the place by Place_id
if (!is_array($places) || empty($places)) {
    header("Location: popular.php?error=" . urlencode("No venues available"));
    exit;
}
$place = null;
foreach ($places as $p) {
    if (isset($p['Place_id']) && $p['Place_id'] == $place_id) {
        $place = $p;
        break;
    }
}
if (!$place) {
    header("Location: popular.php?error=" . urlencode("Venue not found"));
    exit;
}

// Filter reviews for this specific place
$place_reviews = [];
if (is_array($reviews)) {
    $place_reviews = array_filter($reviews, function($review) use ($place_id) {
        return isset($review['Place_id']) && $review['Place_id'] == $place_id;
    });
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($place['Name']); ?> | RentIT</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/topup.css">

    <style>
        :root {
            --accent: #2563eb;
            --muted: #6b7280;
            --bg: #f8fafc;
            --radius: 12px;
            --shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        body {
            margin: 0;
            background: var(--bg);
            color: #111827;
            font-family: Inter, system-ui, sans-serif;
        }

        .container {
            max-width: 1100px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: #111827;
        }

        .venue-details {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
        }

        .venue-img {
            flex: 1;
            max-width: 500px;
        }

        .venue-img img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: var(--radius);
            transition: transform 0.3s ease;
        }

        .venue-img img:hover {
            transform: scale(1.02);
        }

        .venue-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .venue-info h2 {
            margin: 0;
            font-size: 1.5rem;
            color: #111827;
        }

        .meta {
            color: var(--muted);
            font-size: 0.9rem;
        }

        .price {
            font-weight: 600;
            font-size: 1.2rem;
            color: #111827;
        }

        .reviews-section {
            margin-top: 2rem;
        }

        .review-card {
            background: #fff;
            border-radius: var(--radius);
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: var(--shadow);
            transition: transform 0.3s ease;
        }

        .review-card:hover {
            transform: translateY(-4px);
        }

        .review-card .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .review-form, .rent-form {
            background: #fff;
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-top: 2rem;
        }

        .review-form label, .rent-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #111827;
        }

        .review-form select, .rent-form select,
        .review-form textarea, .rent-form input {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9rem;
            box-sizing: border-box;
        }

        .review-form textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn.primary {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s;
        }

        .btn.primary:hover {
            background: #1d4ed8;
        }

        .error {
            color: #dc2626;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .success {
            color: #15803d;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        footer {
            text-align: center;
            padding: 1.5rem;
            margin-top: 3rem;
            color: var(--muted);
            background: #fff;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.05);
        }

        @media (max-width: 768px) {
            .venue-details {
                flex-direction: column;
            }
            .venue-img {
                max-width: 100%;
            }
            .venue-img img {
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
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
        <?php if (isset($_GET['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <a href="offers.php" class="btn">Back to Catalog</a>
        <h1><?php echo htmlspecialchars($place['Name']); ?></h1>
        <div class="venue-details">
            <div class="venue-img">
                <img src="<?php echo !empty($place['Foto']) ? '../img/' . htmlspecialchars($place['Foto']) : '../img/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($place['Name']); ?>">
            </div>
            <div class="venue-info">
                <h2><?php echo htmlspecialchars($place['Name']); ?></h2>
                <div class="meta"><?php echo htmlspecialchars($place['Description'] ?? 'No description available'); ?> • <?php echo htmlspecialchars($place['Adress'] ?? 'No address available'); ?></div>
                <div class="meta">Owner: <?php echo htmlspecialchars($place['Owner'] ?? 'Unknown'); ?></div>
                <div class="meta"><?php echo isset($place['Stars']) && $place['Stars'] !== null ? htmlspecialchars(round($place['Stars'], 1)) : 'No rating'; ?> ⭐</div>
                <div class="price">€<?php echo number_format($place['Price'] ?? 0, 2); ?> / hr</div>
            </div>
        </div>

        <div class="reviews-section">
            <h2>Reviews</h2>
            <?php if (empty($place_reviews)) { ?>
                <p>No reviews yet. Be the first to leave one!</p>
            <?php } else { ?>
                <?php foreach ($place_reviews as $review) { ?>
                    <div class="review-card">
                        <div class="meta">
                            <span><?php echo htmlspecialchars($review['Name'] ?? 'Anonymous'); ?> • <?php echo htmlspecialchars($review['Stars'] ?? '0'); ?> ⭐</span>
                            <span><?php echo htmlspecialchars($review['Date'] ?? 'Unknown'); ?></span>
                        </div>
                        <p><?php echo htmlspecialchars($review['Comment'] ?? 'No comment'); ?></p>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>

        <div class="review-form">
            <h2>Leave a Review</h2>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <form action="../include/postReview.php" method="POST" onsubmit="return validateReviewForm()">
                    <input type="hidden" name="place_id" value="<?php echo $place_id; ?>">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <label for="rating">Rating</label>
                    <select id="rating" name="rating" required>
                        <option value="" disabled selected>Select rating</option>
                        <option value="1">1 ⭐</option>
                        <option value="2">2 ⭐</option>
                        <option value="3">3 ⭐</option>
                        <option value="4">4 ⭐</option>
                        <option value="5">5 ⭐</option>
                    </select>
                    <label for="comment">Comment</label>
                    <textarea id="comment" name="comment" required></textarea>
                    <button type="submit" class="btn primary">Submit Review</button>
                </form>
            <?php } else { ?>
                <p>Please <a href="login.php">log in</a> to leave a review.</p>
            <?php } ?>
        </div>

        <div class="rent-form">
            <h2>Rent a Table</h2>
            <?php if (isset($_SESSION['user_id'])) { ?>
                <form action="../include/rent.php" method="POST" onsubmit="return validateRentForm()">
                    <input type="hidden" name="place_id" value="<?php echo $place_id; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required min="<?php echo date('Y-m-d'); ?>">
                    <label for="time">Time</label>
                    <input type="time" id="time" name="time" required>
                    <label for="duration">Duration (hours)</label>
                    <select id="duration" name="duration" required>
                        <option value="" disabled selected>Select duration</option>
                        <option value="1">1 hour</option>
                        <option value="2">2 hours</option>
                        <option value="3">3 hours</option>
                        <option value="4">4 hours</option>
                    </select>
                    <button type="submit" class="btn primary">Book Now</button>
                </form>
            <?php } else { ?>
                <p>Please <a href="login.php">log in</a> to book a table.</p>
            <?php } ?>
        </div>
    </div>

    <footer>© <?php echo date('Y'); ?> RentIT. All rights reserved.</footer>

    <script>
        function validateReviewForm() {
            const comment = document.getElementById('comment').value.trim();
            if (comment.length < 10) {
                alert('Comment must be at least 10 characters long.');
                return false;
            }
            return true;
        }

        function validateRentForm() {
            const date = document.getElementById('date').value;
            const today = new Date().toISOString().split('T')[0];
            if (date < today) {
                alert('Cannot book a date in the past.');
                return false;
            }
            return true;
        }

  
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