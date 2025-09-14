<?php

session_start();

if (empty($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}
include 'db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Approve post
if (isset($_GET['approve'])) {
    $id = (int) $_GET['approve'];
    $conn->query("UPDATE posts SET approved = 1 WHERE id = $id");
}

// Delete post
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $res = $conn->query("SELECT file_path FROM posts WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        if (file_exists($row['file_path'])) {
            unlink($row['file_path']);
        }
    }
    $conn->query("DELETE FROM posts WHERE id = $id");
}

$result = $conn->query("SELECT * FROM posts WHERE approved = 0 ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Orion Admin</title>
  <link rel="stylesheet" href="styles.css">
    <link rel="icon" type="image/png" href="favicon.png"/>

</head>
<body>

<header>
  <div style="display:flex; justify-content:space-between; align-items:center; position:relative;">
    <!-- Logout on the left -->
    <a href="logout.php" style="color:#ff7676; text-decoration:none; font-weight:bold;">Logout</a>
    
    <!-- Title centered absolutely -->
    <h1 style="position:absolute; left:50%; transform:translateX(-50%); margin:0;">
      Orion Admin Panel
    </h1>
  </div>
</header>



<div class="pending">
  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="post">
        <?php if (preg_match('/\.(mp4|webm|ogg)$/i', $row['file_path'])): ?>
          <video controls src="<?= htmlspecialchars($row['file_path']) ?>"></video>
        <?php else: ?>
          <img src="<?= htmlspecialchars($row['file_path']) ?>" alt="Pending Post">
        <?php endif; ?>
        <div class="caption"><?= htmlspecialchars($row['caption']) ?></div>
        <div class="actions">
          <a class="approve" href="admin.php?approve=<?= $row['id'] ?>">Approve</a>
          <a class="delete" href="admin.php?delete=<?= $row['id'] ?>">Delete</a>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p style="padding:20px;">No pending posts 🎉</p>
  <?php endif; ?>
</div>

</body>
</html>
