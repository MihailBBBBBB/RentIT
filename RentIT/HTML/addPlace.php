<?php
session_start();
require_once '../include/dbh.inc.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] !== 1) {
    header("Location: index.php?error=" . urlencode("Please log in as an admin to add a place."));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Place | RentIT</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/topup.css">
    
    <style>
        /* ====== Global Styles ====== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
            background: #f4f7fb;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ====== Header ====== */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .nav {
            display: flex;
            gap: 20px;
        }

        .nav-link {
            text-decoration: none;
            color: #555;
            font-weight: 500;
        }

        .nav-link.active {
            color: #007bff;
        }

        .account {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .balance {
            font-size: 0.9rem;
            color: #555;
        }

        /* ====== Main Content ====== */
        .main-content {
            flex: 1 0 auto;
            padding-top: 60px; /* Adjust based on header height */
            padding-bottom: 60px; /* Adjust based on footer height */
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 30px 40px;
            width: 450px;
            max-width: 95%;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
        }

        .message {
            color: #27ae60;
            background: #e9f7ef;
            border: 1px solid #27ae60;
            padding: 8px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 15px;
        }

        .error {
            color: #e74c3c;
            background: #fdecea;
            border: 1px solid #e74c3c;
            padding: 8px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 0.95rem;
            margin-bottom: 6px;
            color: #555;
        }

        input[type="text"],
        textarea,
        input[type="file"],
        input[type="number"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 0.95rem;
            outline: none;
            transition: 0.2s ease;
        }

        input:focus,
        textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        textarea {
            resize: vertical;
        }

        /* ====== Footer ====== */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #fff;
            text-align: center;
            padding: 10px 20px;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            font-size: 0.9rem;
            color: #555;
            flex-shrink: 0;
        }

        /* ====== Responsive Design ====== */
        @media (max-width: 500px) {
            .container {
                padding: 20px;
            }
            .header {
                flex-direction: column;
                gap: 10px;
            }
            .nav {
                flex-direction: column;
                gap: 10px;
            }
            .account {
                flex-direction: column;
                gap: 10px;
            }
        }

        .submit {
    width: 100%;
    padding: 12px;
    background-color: #007bff; /* Primary blue from RentIT design */
    color: #fff; /* White text for contrast */
    border: none;
    border-radius: 8px; /* Rounded corners to match container */
    font-size: 1rem;
    font-weight: 500;
    font-family: "Poppins", sans-serif; /* Consistent with body font */
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease; /* Smooth hover and click effects */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    text-align: center;
    text-transform: uppercase; /* Optional: for a bold look */
    letter-spacing: 0.5px; /* Slight spacing for readability */
}

.submit:hover {
    background-color: #0056b3; /* Darker blue on hover */
    transform: translateY(-2px); /* Lift effect on hover */
}

.submit:active {
    background-color: #004085; /* Even darker on click */
    transform: translateY(0); /* Reset lift on click */
}

.submit:disabled {
    background-color: #cccccc; /* Grayed out when disabled */
    cursor: not-allowed;
    transform: none;
}
.form-group {
    margin-bottom: 18px;
    position: relative;
}

.custom-file-input {
    display: none; /* Hide the default file input */
}

.custom-file-label {
    width: 100%;
    padding: 10px 12px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 0.95rem;
    font-family: "Poppins", sans-serif;
    color: #555;
    cursor: pointer;
    text-align: left;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.custom-file-label:hover {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
}

.custom-file-label span {
    display: inline-block;
    max-width: 70%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: middle;
}

.custom-file-label .browse-button {
    display: inline-block;
    padding: 5px 15px;
    background-color: #007bff;
    color: #fff;
    border-radius: 5px;
    font-size: 0.9rem;
    font-weight: 500;
    margin-left: 10px;
    transition: background-color 0.3s ease;
}

.custom-file-label .browse-button:hover {
    background-color: #0056b3;
}

.custom-file-label .file-name {
    color: #666;
    font-style: italic;
}

/* ====== Responsive Design ====== */
@media (max-width: 500px) {
    .submit {
        padding: 10px;
        font-size: 0.95rem;
    }
}
    </style>
</head>
<body>
    <?php
    // Generate CSRF token if not set
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    ?>

  <!-- HEADER -->
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
                    <a href="addPlace.php" class="nav-link active">Add Place</a>
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



    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <?php
            // Display messages
            if (isset($_GET['message'])) {
                echo '<p class="message">' . htmlspecialchars($_GET['message']) . '</p>';
            }
            if (isset($_GET['error'])) {
                echo '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
            }
            ?>

            <h2>Add New Place</h2>
            <form action="../include/addPlace.inc.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" required>
                </div>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="owner">Owner:</label>
                    <input type="text" id="owner" name="owner" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Price (€/hr):</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                  <label for="foto">Photo (JPEG, PNG, GIF, max 5MB):</label>
                  <div class="custom-file-input">
                    <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/gif" required>
                    <label class="custom-file-label" for="foto">
                      <span class="file-name">No file selected</span>
                      <span class="browse-button">Browse</span>
                    </label>
                  </div>
                </div>
                <div class="form-group">
                    <label for="coordinates">Coordinates (lat,lng):</label>
                    <input type="text" id="coordinates" name="coordinates" placeholder="e.g., 40.7128,-74.0060" required>
                </div>
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button class="submit" type="submit">Add Place</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
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