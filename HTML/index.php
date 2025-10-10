<?php
session_start();
include_once '../include/dbh.inc.php'; // твой файл с $pdo

// === ЧТЕНИЕ ФИЛЬТРОВ ===
$type = $_GET['type'] ?? '';
$rating = $_GET['rating'] ?? '';
$sort = $_GET['sort'] ?? '';
$search = $_GET['search'] ?? '';

// === SQL ДЛЯ КАТАЛОГА (с фильтрами) ===
$sql = "
  SELECT 
    p.*,
    COALESCE(AVG(r.Stars), 0) AS Stars
  FROM place p
  LEFT JOIN reviews r ON p.Place_id = r.Place_id
  WHERE 1
";
$params = [];

if (!empty($type)) {
    $sql .= " AND p.Description LIKE :type";
    $params[':type'] = "%$type%";
}

if (!empty($search)) {
    $sql .= " AND (p.Name LIKE :search OR p.Description LIKE :search OR p.Adress LIKE :search)";
    $params[':search'] = "%$search%";
}

$sql .= " GROUP BY p.Place_id";

if (!empty($rating)) {
    $sql .= " HAVING Stars >= :rating";
    $params[':rating'] = (float)$rating;
}

switch ($sort) {
    case 'price_low':
        $sql .= " ORDER BY p.Price ASC";
        break;
    case 'rating_high':
        $sql .= " ORDER BY Stars DESC";
        break;
    default:
        $sql .= " ORDER BY p.Place_id DESC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$places = $stmt->fetchAll(PDO::FETCH_ASSOC);

// === SQL ДЛЯ ПОПУЛЯРНЫХ ===
$popularSql = "
  SELECT 
    p.*, 
    COALESCE(AVG(r.Stars), 0) AS Stars
  FROM place p
  LEFT JOIN reviews r ON p.Place_id = r.Place_id
  GROUP BY p.Place_id
  ORDER BY Stars DESC
  LIMIT 4
";
$popularStmt = $pdo->query($popularSql);
$popularPlaces = $popularStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>RentIT</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <link rel="stylesheet" href="../css/index.css">
  <link rel="stylesheet" href="../css/topup.css">

  <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
  <!-- HEADER -->
  <?php if (isset($_SESSION['user_id'])) { ?>
    <?php if ($_SESSION['is_admin'] == 1) { ?>
      <header class="header">
        <div class="brand"><strong>RentIT</strong></div>
        <nav class="nav">
          <a href="index.php" class="nav-link active">Home</a>
          <a href="popular.php" class="nav-link">Popular</a>
          <a href="offers.php" class="nav-link">Catalog</a>
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
          <a href="index.php" class="nav-link active">Home</a>
          <a href="popular.php" class="nav-link">Popular</a>
          <a href="offers.php" class="nav-link">Catalog</a>
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
        <a href="index.php" class="nav-link active">Home</a>
        <a href="popular.php" class="nav-link">Popular</a>
        <a href="offers.php" class="nav-link">Catalog</a>
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
    <header class="header-block">
      <div class="header-left">
        <h1>Book the Perfect Spot with RentIT</h1>
        <p>Restaurants, bars, gyms, clubs — discover, compare, and reserve instantly.</p>

        <!-- === ФИЛЬТРЫ === -->
        <form method="get" class="filters">
          <input type="text" name="search" placeholder="Try: Restaurant, Club..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" />
          
          <select name="type">
            <option value="">All</option>
            <option value="Restaurant" <?= (($_GET['type'] ?? '') == 'Restaurant') ? 'selected' : '' ?>>Restaurant</option>
            <option value="Bar" <?= (($_GET['type'] ?? '') == 'Bar') ? 'selected' : '' ?>>Bar</option>
            <option value="Gym" <?= (($_GET['type'] ?? '') == 'Gym') ? 'selected' : '' ?>>Gym</option>
            <option value="Club" <?= (($_GET['type'] ?? '') == 'Club') ? 'selected' : '' ?>>Club</option>
          </select>

          <select name="rating">
            <option value="">All Ratings</option>
            <option value="4" <?= (($_GET['rating'] ?? '') == '4') ? 'selected' : '' ?>>★ 4+</option>
            <option value="3" <?= (($_GET['rating'] ?? '') == '3') ? 'selected' : '' ?>>★ 3+</option>
          </select>

          <select name="sort">
            <option value="">Popular</option>
            <option value="rating_high" <?= (($_GET['sort'] ?? '') == 'rating_high') ? 'selected' : '' ?>>Rating High → Low</option>
            <option value="price_low" <?= (($_GET['sort'] ?? '') == 'price_low') ? 'selected' : '' ?>>Price Low → High</option>
          </select>

          <button class="btn primary" type="submit">Apply Filters</button>
          <a href="index.php">Reset</a>
        </form>

        <div class="verified-info">
          <span>⭐ Verified Reviews</span>
          <span>📍 Map View</span>
          <span>📄 PDF Receipts</span>
        </div>
      </div>

      <div class="header-right">
        <img src="../img/situations.jpg" alt="Restaurant" />
      </div>
    </header>

    <!-- === POPULAR === -->
    <section class="section">
      <h3 id="popular-h">Most Popular Venues</h3>
      <div id="popular" class="popular">
        <?php foreach ($popularPlaces as $p) { ?>
          <div class="pop-card">
            <img src="<?php echo '../img/' . htmlspecialchars($p['Foto']) ?>" alt="<?php echo htmlspecialchars($p['Name']); ?>">
            <div class="info">
              <h4><?php echo htmlspecialchars($p['Name']); ?></h4>
              <div class="meta"><?php echo htmlspecialchars($p['Description']); ?> • ⭐<?= number_format($p['Stars'], 1) ?></div>
              <div class="meta"><?php echo htmlspecialchars($p['Adress']); ?></div>
            </div>
          </div>
        <?php } ?>
      </div>
    </section>

    <!-- === CATALOG === -->
    <section class="section">
      <h3>Venue Catalog</h3>
      <div id="catalog" class="catalog">
        <?php if (count($places) > 0): ?>
          <?php foreach ($places as $p): ?>
            <div class="card">
              <img src="<?php echo '../img/' . htmlspecialchars($p['Foto']); ?>" alt="<?php echo htmlspecialchars($p['Name']); ?>">
              <div class="content">
                <div>
                  <h4><?php echo htmlspecialchars($p['Name']); ?></h4>
                  <div class="meta"><?php echo htmlspecialchars($p['Description']); ?> • <?php echo htmlspecialchars($p['Adress']); ?></div>
                  <div class="meta">⭐ <?= number_format($p['Stars'], 1) ?></div>
                </div>
                <div class="bottom">
                  <div class="price">€<?php echo number_format($p['Price'], 2); ?> / hr</div>
                  <a href="aboutOffer.php?id=<?php echo htmlspecialchars($p['Place_id']); ?>" class="btn primary">Book</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No venues found for your filters.</p>
        <?php endif; ?>
      </div>
    </section>

    <!-- === MAP === -->
    <section class="map-wrap">
      <div id="map" style="height:400px;"></div>
      <aside class="nearby">
        <h4>Map & Nearby</h4>
        <div id="nearbyList">
          <?php foreach ($places as $p) { ?>
            <div class="item">
              <img src="<?php echo '../img/' . htmlspecialchars($p['Foto']) ?>" alt="<?php echo htmlspecialchars($p['Name']); ?>">
              <div>
                <div style="font-weight:600"><?php echo htmlspecialchars($p['Name']); ?></div>
                <div class="meta"><?php echo htmlspecialchars($p['Description']); ?> • ⭐<?= number_format($p['Stars'], 1) ?></div>
                <div class="meta"><?php echo htmlspecialchars($p['Adress']); ?></div>
              </div>
            </div>
          <?php } ?>
        </div>
      </aside>
    </section>
  </div>


  <footer>© 2025 RentIT. All rights reserved.</footer>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    function initMap(){
      const map=L.map('map').setView([56.952807, 24.120549],12);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
      <?php foreach ($places as $p) { ?>
        L.marker([<?php echo htmlspecialchars($p['Coordinates']); ?>]).addTo(map)
          .bindPopup(`<b><?php echo htmlspecialchars($p['Name']); ?></b><br><?php echo htmlspecialchars($p['Description']); ?> • ⭐<?php echo number_format($p['Stars'], 1); ?>`);
      <?php } ?>
    }
    document.addEventListener("DOMContentLoaded",()=>{initMap();});


     
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