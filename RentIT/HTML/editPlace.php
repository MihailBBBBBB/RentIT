<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: offers.php");
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

$place_id = $_GET['id'] ?? 0;
$csrf_token = $_GET['csrf_token'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM place WHERE Place_id = ?");
$stmt->execute([$place_id]);
$place = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$place) {
    header("Location: offers.php?error=placenotfound");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Edit Place | RentIT</title>
    <link rel="stylesheet" href="../css/index.css">
    <style>
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(16,24,40,0.08);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        button {
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            cursor: pointer;
        }
        button:hover {
            background: #1d4ed8;
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
        <h2>Edit <?php echo htmlspecialchars($place['Name']); ?></h2>
        <form action="../include/editPlace.inc.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="place_id" value="<?php echo htmlspecialchars($place_id); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($place['Name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($place['Adress']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($place['Description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price per Hour (€):</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($place['Price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="foto">Photo (JPEG, PNG, GIF, max 5MB):</label>
                <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/gif">
                <p>Current photo: <?php echo htmlspecialchars($place['Foto']); ?></p>
            </div>
            <button type="submit">Update Place</button>
        </form>
    </div>
</body>
</html>