<?php
include 'db.php'; // your DB connection file

if (!isset($_GET['q']) || !is_numeric($_GET['q'])) {
    die("Invalid post ID.");
}

$post_id = intval($_GET['q']);

$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Post not found.");
}

$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orion - Post <?php echo $post['id']; ?></title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header>
    <a href="index.php" style="text-decoration: none;"><h1>Orion</h1></a>
  </header>

  <main class="feed">
    <div class="post">
      <?php if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $post['file_path'])): ?>
        <img src="<?php echo htmlspecialchars($post['file_path']); ?>" alt="Post Image">
      <?php elseif (preg_match('/\.(mp4|webm)$/i', $post['file_path'])): ?>
        <video src="<?php echo htmlspecialchars($post['file_path']); ?>" controls></video>
      <?php endif; ?>
      <p class="caption"><?php echo nl2br(htmlspecialchars($post['caption'])); ?></p>
    </div>
  </main>
</body>
</html>
