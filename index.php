<?php
// --- DB config: update these ---
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

// --- Fetch all gossips ---
$stmt = $pdo->query("SELECT * FROM gossips ORDER BY created_at DESC");
$gossips = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GossipHub - Main Page</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h1>THALLIOLA</h1>
    <p>hen you fell useless remember there is "Browse Gossips"</p>
</header>

<nav class="nav-bar">
    <a href="login.html">Login</a>
    <a href="content.html">Browse Gossips</a>
    <button id="submit-gossip" class="submit-btn" onclick="window.location.href='input.html'">Submit Gossip</button>
</nav>

<main>
    <section id="featured-gossip">
        <?php if (count($gossips)): ?>
            <div class="gossip-grid">
                    <?php foreach ($gossips as $g): ?>
                    <div class="gossip-card">
        <a href="content.php?id=<?= $g['id'] ?>" style="text-decoration:none; color:inherit; display:block;">
            <h2><?= htmlspecialchars($g['heading']) ?></h2>
            <p><?= nl2br(htmlspecialchars($g['gossip_text'])) ?></p>
            <?php if ($g['image_path']): ?>
                <img src="<?= htmlspecialchars($g['image_path']) ?>" alt="gossip image">
            <?php endif; ?>
        </a>
    </div>

                <?php endforeach ?>
            </div>
        <?php else: ?>
            <p>No gossips yet. <a href="input.html">Submit now!</a></p>
        <?php endif ?>
    </section>
</main>

<footer>
    <p>&copy;2025 No heavens were hurt,A fallen angel initiative | Built at Uselessproject2.0</p>
</footer>
</body>
</html>
