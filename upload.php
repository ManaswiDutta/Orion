<?php
$conn = new mysqli("localhost", "root", "", "orion");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $caption = $_POST['caption'] ?? '';
    $file = $_FILES['media'];

    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $targetFile = $targetDir . time() . "_" . basename($file["name"]);

    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO posts (file_path, caption, approved) VALUES (?, ?, 0)");
        $stmt->bind_param("ss", $targetFile, $caption);
        $stmt->execute();
        $message = "✅ Uploaded successfully! Waiting for admin approval.";
    } else {
        $message = "❌ Error uploading file.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Upload | Orion</title>
      <link rel="icon" type="image/png" href="favicon.png"/>
    <link rel="stylesheet" href="styles.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #101018;
      color: #f5f5f5;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    
  </style>
</head>
<body>

<div class="upload-box">
  <h2>Upload to Orion</h2>
  <?php if ($message): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>
  <form method="post" enctype="multipart/form-data">
    <input type="file" name="media" accept="image/*,video/*" required>
    <textarea name="caption" placeholder="Write a caption..."></textarea>
    <button type="submit">Upload</button>
  </form>
  <a class="back-link" href="index.php">← Back to Feed</a>
</div>

</body>
</html>
