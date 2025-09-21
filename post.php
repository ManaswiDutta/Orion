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
  <!-- Font Awesome CDN -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  />
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
    
      <button id="shareButton" class="share-button" title="Share this article">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        class="feather feather-share-2"
      >
        <circle cx="18" cy="5" r="3"></circle>
        <circle cx="6" cy="12" r="3"></circle>
        <circle cx="18" cy="19" r="3"></circle>
        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
      </svg>
      <span>Share</span>
      </button>
      </div>


  </main>

<script>
   const handleShare = async () => {
    const url = window.location.href;
    const title = document.querySelector('h2').textContent;
    const text = `Check out this article: ${title}`;

    if (navigator.share) {
      try {
        await navigator.share({
          title: title,
          text: text,
          url: url,
        });
      } catch (err) {
        console.error('Error sharing:', err);
        // Fallback to copy
        try {
          await navigator.clipboard.writeText(url);
          alert('Link copied to clipboard!');
        } catch (copyErr) {
          console.error('Failed to copy:', copyErr);
          alert('Sharing not supported. Please copy the URL manually.');
        }
      }
    } else {
      try {
        await navigator.clipboard.writeText(url);
        alert('Link copied to clipboard!');
      } catch (err) {
        console.error('Failed to copy:', err);
        alert('Sharing not supported. Please copy the URL manually.');
      }
    }
  };
  document.getElementById('shareButton').addEventListener('click', handleShare);
 const audio = document.getElementById('article-audio');
    const playPauseBtn = document.getElementById('play-pause-btn');
    const playIcon = '<i class="fas fa-play"></i>';
    const pauseIcon = '<i class="fas fa-pause"></i>';
    
    const progressContainer = document.getElementById('player-progress-container');
    const progress = document.getElementById('player-progress');
    const timeDisplay = document.getElementById('time-display');

    // Function to toggle play and pause
    function togglePlayPause() {
        if (audio.paused) {
            audio.play();
            playPauseBtn.innerHTML = pauseIcon;
        } else {
            audio.pause();
            playPauseBtn.innerHTML = playIcon;
        }
    }

    // Function to format time from seconds to MM:SS
    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = Math.floor(seconds % 60);
        return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
    }

    // Function to update the progress bar and time display
    function updateProgress() {
        const { duration, currentTime } = audio;
        const progressPercent = (currentTime / duration) * 100;
        progress.style.width = `${progressPercent}%`;
        
        // Update time display if duration is a valid number
        if (!isNaN(duration)) {
             timeDisplay.textContent = `${formatTime(currentTime)} / ${formatTime(duration)}`;
        }
    }

    // Function to set the audio's current time when progress bar is clicked
    function setProgress(e) {
        const width = this.clientWidth;
        const clickX = e.offsetX;
        const duration = audio.duration;
        audio.currentTime = (clickX / width) * duration;
    }

    // Event Listeners
    playPauseBtn.addEventListener('click', togglePlayPause);
    audio.addEventListener('timeupdate', updateProgress);
    progressContainer.addEventListener('click', setProgress);

    // Reset icon when audio finishes
    audio.addEventListener('ended', () => {
        playPauseBtn.innerHTML = playIcon;
        progress.style.width = '0%';
    });
    
    // Set initial duration when metadata loads
    audio.addEventListener('loadedmetadata', () => {
        updateProgress();
    });


</script>
</body>
</html>
