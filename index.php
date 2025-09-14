<?php
include 'db.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM posts WHERE approved = 1 ORDER BY RAND() LIMIT 20");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Orion Feed</title>
  <link rel="stylesheet" href="styles.css">
      <link rel="icon" type="image/png" href="favicon.png"/>


</head>

<body>

<header>
  <div style="display:flex; justify-content:space-between; align-items:center; position:relative;">
    <!-- Empty left side for symmetry -->
    <div></div>

    <!-- Title (centered) -->
    <h1 style="position:absolute; left:50%; transform:translateX(-50%); margin:0;">
      Orion Feed
    </h1>

    <!-- Upload button (right) -->
    <a href="upload.php" 
       style="color:#4da3ff; text-decoration:none; font-weight:bold; margin-right:10px;">
       Upload
    </a>
  </div>
</header>


<div class="feed">
  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="post">
      <?php if (preg_match('/\.(mp4|webm|ogg)$/i', $row['file_path'])): ?>
        <video controls src="<?= htmlspecialchars($row['file_path']) ?>"></video>
      <?php else: ?>
        <img src="<?= htmlspecialchars($row['file_path']) ?>" alt="Post">
      <?php endif; ?>
      <div class="caption"><?= htmlspecialchars($row['caption']) ?></div>
    </div>
  <?php endwhile; ?>
</div>

</body>
</html>
