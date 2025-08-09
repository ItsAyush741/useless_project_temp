<?php
// --- DB config ---
$host = 'localhost';
$db   = 'gossip';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

// --- Connect ---
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    die('Database connection failed');
}

// --- Get the gossip ID from URL ---
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    die('<p style="color:red;">Invalid gossip selected.</p>');
}

// --- Fetch the gossip ---
$stmt = $pdo->prepare("SELECT * FROM gossips WHERE id = ?");
$stmt->execute([$id]);
$gossip = $stmt->fetch();

if (!$gossip) {
    die('<p style="color:red;">Gossip not found.</p>');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($gossip['heading']) ?> | GossipHub</title>
    <link rel="stylesheet" href="styles.css">
    <style>
      .gossip-fullscreen {
          max-width: 700px;
          margin: 3rem auto;
          background: #fff;
          border-radius: 24px;
          box-shadow: 0 6px 36px rgba(163,48,203,.18);
          padding: 2.5rem 2rem 2rem 2rem;
          text-align: center;
          position: relative;
      }
      .gossip-fullscreen h2 {
          font-size: 2.2rem;
          margin-bottom: 1rem;
          color: #9932cc;
          font-family: 'Montserrat', 'Roboto', Arial, sans-serif;
      }
      .gossip-fullscreen img {
          max-width: 98%;
          border-radius: 20px;
          box-shadow: 0 2px 14px rgba(163,48,203,0.13);
          margin: 1.3rem auto 1.3rem auto;
      }
      .gossip-fullscreen p {
          color: #242092;
          font-size: 1.2rem;
          line-height: 1.6;
          margin-bottom: 1.5rem;
      }
      .gossip-date {
          color: #ae89cd;
          font-size: 1rem;
          margin-bottom: 0.8rem;
      }
      .back-btn {
          display:inline-block;
          background:#9932cc;
          color:#fff;
          padding:0.65rem 1.4rem;
          border-radius:30px;
          text-decoration:none;
          font-weight:500;
          box-shadow: 0 2px 8px #9932cc33;
          transition:background 0.18s;
          margin-top:1.2rem;
          font-size:1.1rem;
      }
      .back-btn:hover {
          background:#c15aff;
      }
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="hub-link">
            <h1>GossipHub</h1>
        </a>
        <p>Your daily dose of juicy gossip!</p>
    </header>
    <nav class="nav-bar">
        <a href="index.php">Home</a>
        <a href="input.html">Submit Gossip</a>
        <a href="content.php?id=<?= $gossip['id'] ?>">Refresh</a>
    </nav>
    <main>
        <section class="gossip-fullscreen">
            <div class="gossip-date">
                Posted on <?= date('F j, Y, g:i a', strtotime($gossip['created_at'])) ?>
            </div>
            <h2><?= htmlspecialchars($gossip['heading']) ?></h2>
            <?php if ($gossip['image_path']): ?>
                <img src="<?= htmlspecialchars($gossip['image_path']) ?>" alt="gossip image">
            <?php endif; ?>
            <p><?= nl2br(htmlspecialchars($gossip['gossip_text'])) ?></p>
            <a class="back-btn" href="index.php">&larr; Back to Gossip Grid</a>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 GossipHub | Built at [Your Hackathon Name]</p>
    </footer>
</body>
</html>
