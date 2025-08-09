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
    die('Database connection failed: ' . $e->getMessage());
}

// --- Process the form ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $heading = trim($_POST['gossip-title'] ?? '');
    $gossip = trim($_POST['gossip-text'] ?? '');
    $imagePath = null;

    // Validate required fields
    if (!$heading || !$gossip) {
        die('Please fill in all fields.');
    }

    // DEBUG: See what PHP received for the uploaded file
    echo '<pre>';
    var_dump($_FILES['gossip-image']);
    echo '</pre>';

    // Check for upload errors
    if (!empty($_FILES['gossip-image']['name'])) {
        if ($_FILES['gossip-image']['error'] !== UPLOAD_ERR_OK) {
            die('Upload error code: ' . $_FILES['gossip-image']['error'] . ' (check PHP upload_max_filesize and post_max_size in php.ini)');
        }

        $img = $_FILES['gossip-image'];

        // Validate image type
        $info = @getimagesize($img['tmp_name']);
        if ($info === false) {
            die('Invalid image file.');
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $type = $info['mime'];
        if (!in_array($type, $allowedTypes)) {
            die('Only JPG, PNG, GIF allowed.');
        }

        // Make sure uploads folder exists and is writable
        $uploadDir = __DIR__ . "/uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Full permissions for debugging
        }

        $name = uniqid("img_") . "." . pathinfo($img['name'], PATHINFO_EXTENSION);
        $dest = $uploadDir . $name;

        if (!move_uploaded_file($img['tmp_name'], $dest)) {
            die('Failed to move uploaded file — check folder permissions for "uploads"');
        }

        $imagePath = "uploads/$name";
    }

    // Store in database
    $stmt = $pdo->prepare("INSERT INTO gossips (heading, gossip_text, image_path) VALUES (?, ?, ?)");
    $stmt->execute([$heading, $gossip, $imagePath]);

    echo "<p>✅ Gossip saved successfully!</p>";
    echo "<p><a href='index.php'>Go back to homepage</a></p>";
}
