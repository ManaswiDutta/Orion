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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Orion Feed</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="icon" type="image/png" href="favicon.png"/>

  <style>
    .share-button {
      position: absolute;
      right: 1.2em;
      bottom: 1.2em;
      display: inline-flex;
      align-items: center;
      gap: 0.4em;
      padding: 0.35em 0.9em;
      background: #181f2a;
      color: #eaf4ff;
      border: none;
      border-radius: 1.5em;
      font-weight: 500;
      font-size: 0.95rem;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(12, 122, 219, 0.10);
      transition: 
        background 0.2s,
        color 0.2s,
        box-shadow 0.2s,
        transform 0.15s;
      outline: none;
      z-index: 2;
    }
    .share-button:hover,
    .share-button:focus {
      background: #25304a;
      color: #7ec4ff;
      box-shadow: 0 4px 16px rgba(12, 122, 219, 0.18);
      transform: translateY(-2px) scale(1.04);
    }
    .share-button svg {
      width: 18px;
      height: 18px;
      stroke: #7ec4ff;
      transition: stroke 0.2s;
    }
    .share-button:hover svg,
    .share-button:focus svg {
      stroke: #eaf4ff;
    }
    .post {
      position: relative;
    }
  </style>
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
    <a href="post.php?q=<?php echo $row['id']; ?>"><div class="post">
      <?php if (preg_match('/\.(mp4|webm|ogg)$/i', $row['file_path'])): ?>
        <video controls src="<?= htmlspecialchars($row['file_path']) ?>"></video>
      <?php else: ?>
        <img src="<?= htmlspecialchars($row['file_path']) ?>" alt="Post">
      <?php endif; ?>
      <div class="caption"><?= htmlspecialchars($row['caption']) ?></div>
      <button 
        class="share-button" 
        title="Share this post"
        data-url="https://<?php echo $_SERVER['HTTP_HOST']; ?>/Orion/post.php?q=<?php echo $row['id']; ?>"
      >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;">
          <circle cx="18" cy="5" r="3"></circle>
          <circle cx="6" cy="12" r="3"></circle>
          <circle cx="18" cy="19" r="3"></circle>
          <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
          <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
        </svg>
        <span>Share</span>
      </button>
    </div></a>
  <?php endwhile; ?>
</div>

<script>
document.querySelectorAll('.share-button').forEach(btn => {
  btn.addEventListener('click', async (e) => {
    e.preventDefault();
    const url = btn.getAttribute('data-url');
    const text = 'Check out this post on Orion!';
    if (navigator.share) {
      try {
        await navigator.share({ title: 'Orion Post', text, url });
      } catch (err) {
        // fallback to copy
        try {
          await navigator.clipboard.writeText(url);
          alert('Link copied to clipboard!');
        } catch {
          alert('Sharing not supported. Please copy the URL manually.');
        }
      }
    } else {
      try {
        await navigator.clipboard.writeText(url);
        alert('Link copied to clipboard!');
      } catch {
        alert('Sharing not supported. Please copy the URL manually.');
      }
    }
  });
});
</script>

</body>
</html>
