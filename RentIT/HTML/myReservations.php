<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once '../include/dbh.inc.php';

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

$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $stmt = $pdo->prepare("SELECT r.Res_start, r.Res_finish, p.Name, p.Adress, p.Price, r.Res_id 
                           FROM reservation r 
                           JOIN place p ON r.Place_id = p.Place_id 
                           WHERE r.User_id = ? 
                           ORDER BY r.Res_start DESC");
    $stmt->execute([$user_id]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Query failed: " . $e->getMessage());
    $reservations = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>My Reservations | RentIT</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/topup.css">
    <link rel="stylesheet" href="../css/offers.css">

    <style>
        :root {
            --accent: #2563eb;
            --muted: #6b7280;
            --bg: #f8fafc;
            --card-bg: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            --radius: 14px;
            --shadow: 0 6px 20px rgba(16,24,40,0.08);
            --highlight: #424242ff;
            --danger: #ef4444;
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
            background: #ffffffff;
            box-shadow: var(--shadow);
            border-radius: 0 0 var(--radius) var(--radius);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 18px;
            color: #111827;
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
            padding: 8px 12px;
        }

        nav a.active {
            color: var(--accent);
            background: rgba(37, 99, 235, 0.1);
            border-radius: var(--radius);
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

        .btn.danger {
            background: var(--danger);
            color: #fff;
            border: none;
        }

        .btn.danger:hover {
            background: #dc2626;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Reservation Section */
        .section h3 {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--accent);
            padding-bottom: 8px;
            display: inline-block;
        }

        .reservation-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 24px;
        }

        .reservation-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px;
            transition: transform 0.2s ease-in-out;
            border-left: 4px solid var(--highlight);
            position: relative;
        }

        .reservation-card:hover {
            transform: translateY(-5px);
        }

        .reservation-meta {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 12px;
            font-weight: 500;
        }

        .reservation-details {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .reservation-details strong {
            color: #1f2937;
            font-weight: 600;
        }

        .price {
            font-size: 18px;
            font-weight: 700;
            color: #10b981;
            margin-top: 10px;
        }

        .total-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--accent);
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            margin-top: 10px;
        }

        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--danger);
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 12px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .delete-btn:hover {
            background: #dc2626;
        }

        .no-reservations {
            text-align: center;
            color: var(--muted);
            padding: 40px;
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .reservation-list {
                grid-template-columns: 1fr;
            }
            .section h3 {
                font-size: 20px;
            }
            .delete-btn {
                top: 5px;
                right: 5px;
                padding: 4px 8px;
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
        <section class="section">
            <h3>My Reservations</h3>
            <div class="reservation-list">
                <?php if (empty($reservations)) { ?>
                    <div class="no-reservations">You have no reservations yet.</div>
                <?php } else { ?>
                    <?php foreach ($reservations as $r) { ?>
                        <div class="reservation-card">
                            <div class="reservation-meta">
                                <?php echo htmlspecialchars($r['Name']); ?> • <?php echo htmlspecialchars($r['Adress']); ?>
                            </div>
                            <div class="reservation-details">
                                <strong>Start:</strong> <?php echo (new DateTime($r['Res_start']))->format('Y-m-d H:i'); ?><br>
                                <strong>Finish:</strong> <?php echo (new DateTime($r['Res_finish']))->format('Y-m-d H:i'); ?><br>
                                <strong>Price per Hour:</strong> <span class="price">€<?php echo number_format($r['Price'], 2); ?></span><br>
                                <?php
                                $start = new DateTime($r['Res_start']);
                                $finish = new DateTime($r['Res_finish']);
                                $duration = $start->diff($finish)->h + ($start->diff($finish)->days * 24);
                                $total_price = $r['Price'] * $duration;
                                ?>
                                <div class="total-price">
                                    <strong>Total Price:</strong> €<?php echo number_format($total_price, 2); ?>
                                </div>
                            </div>
                            <form action="../include/deleteReservation.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="res_id" value="<?php echo htmlspecialchars($r['Res_id']); ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </section>
    </div>

    <footer>© 2025 RentIT. All rights reserved.</footer>

    <script>
        const topupBtn = document.getElementById('topupBtn');
        const bottomSheet = document.getElementById('bottomSheet');
        const closeSheet = document.getElementById('closeSheet');
        const deleteButtons = document.querySelectorAll('.delete-btn');

        topupBtn.addEventListener('click', () => {
            bottomSheet.classList.add('active');
        });

        closeSheet.addEventListener('click', () => {
            bottomSheet.classList.remove('active');
        });

        window.addEventListener('click', (e) => {
            if (e.target === bottomSheet) {
                bottomSheet.classList.remove('active');
            }
        });

        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                if (!confirm('Are you sure you want to delete this reservation? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>