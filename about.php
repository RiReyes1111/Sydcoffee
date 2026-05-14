<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

$loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $loggedIn ? $_SESSION['name'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>SYD Coffee — About</title>
  <link rel="icon" type="image/png" href="images/logosydnobg.png">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/aboutcss.css">
  <script>
    window.addEventListener("pageshow", function(e) { if (e.persisted) window.location.reload(); });
  </script>
</head>
<body class="light-btt">

<nav class="navbar">
  <a href="index.php" class="nav-logo-link">
    <img src="images/logosydnobg.png" alt="SYD Coffee" class="logo-nav">
  </a>
  <ul class="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="menu.php">Menu</a></li>
    <li><a href="about.php" class="active">About</a></li>
    <li><a href="contact.php">Contact</a></li>
  </ul>
  <div class="nav-auth">
    <?php if ($loggedIn): ?>
      <span class="nav-user">Hi, <?= htmlspecialchars($userName) ?>!</span>
      <a href="logout.php" class="btn-nav btn-nav-logout">Logout</a>
    <?php else: ?>
      <a href="login.php" class="btn-nav btn-nav-login">Login</a>
    <?php endif; ?>
  </div>
  <button class="hamburger" id="hamburger" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</nav>

<div class="mobile-menu" id="mobileMenu">
  <a href="index.php">Home</a>
  <a href="menu.php">Menu</a>
  <a href="about.php">About</a>
  <a href="contact.php">Contact</a>
  <div class="mobile-divider"></div>
  <?php if ($loggedIn): ?>
    <span class="nav-user" style="color:var(--brown-mid)">Hi, <?= htmlspecialchars($userName) ?>!</span>
    <a href="logout.php">Logout</a>
  <?php else: ?>
    <a href="login.php">Login</a>
  <?php endif; ?>
</div>

<section class="about-hero">
  <div class="about-hero-bg"></div>
  <div class="about-hero-content">
    <span class="hero-eyebrow">Who We Are</span>
    <h1>Brewed with <em>heart</em></h1>
    <p>A small booth born from tough times — now a meaningful mission serving every cup with warmth and purpose.</p>
  </div>
</section>

<section class="story-section">
  <div class="story-inner">
    <div class="story-text reveal">
      <span class="eyebrow">Our Story</span>
      <h2>From struggle<br>to <em>something great</em></h2>
      <p>SYD Coffee started during one of the toughest times — the pandemic. Many faced uncertainty, and finding ways to earn a living became a daily challenge. Out of that struggle came a simple idea: to start a small coffee booth that could bring comfort to others while becoming a new source of income.</p>
      <p>What began as a humble setup slowly turned into something meaningful. Through local events and pop-up markets, SYD Coffee became more than just a place to get coffee — it became a reminder that good things can grow even in hard times.</p>
      <p>Today, SYD Coffee continues to brew with heart and purpose, serving each cup with warmth, hope, and the belief that even small beginnings can make a difference.</p>
    </div>
    <div class="story-img-wrap reveal delay-2">
      <img src="images/booth1.jpg" alt="SYD Coffee Booth" class="story-img-main">
      <img src="images/booth2.jpg" alt="SYD Coffee" class="story-img-accent">
    </div>
  </div>
</section>

<section class="values-section">
  <div class="values-header">
    <span class="values-eyebrow">What Drives Us</span>
    <h2>The SYD Promise</h2>
  </div>
  <div class="values-grid">
    <div class="value-card reveal delay-1">
      <div class="value-letter">S</div>
      <h3>Solidarity</h3>
      <p>Standing with our community through thick and thin. We started when times were tough, and we haven't forgotten our roots. We are committed to being a reliable presence for our neighbors and local partners.</p>
    </div>
    <div class="value-card reveal delay-2">
      <div class="value-letter">Y</div>
      <h3>Yield</h3>
      <p>Producing the best possible quality from humble resources. We believe you don't need a massive factory to create a masterpiece. We maximize every bean and every tool to bring you a premium experience.</p>
    </div>
    <div class="value-card reveal delay-3">
      <div class="value-letter">D</div>
      <h3>Determination</h3>
      <p>The drive that turned a small booth into a meaningful mission. Resilience is our secret ingredient. We brew with the same grit and passion today as we did on day one.</p>
    </div>
  </div>
</section>

<section class="promise-section">
  <span class="eyebrow">Come Find Us</span>
  <h2>Ready to taste the story?</h2>
  <p>Every cup we serve carries the spirit of where we started. Come visit our booth, try our menu, and be part of the SYD Coffee community.</p>
  <a href="menu.php" class="btn-promise">Explore Our Menu</a>
</section>

<footer class="site-footer">
  <div class="footer-grid">
    <div class="footer-brand">
      <img src="images/logosydnobg.png" alt="SYD Coffee" class="footer-logo">
      <p class="footer-tagline">Handcrafted coffee served with care — wherever you find us.</p>
    </div>
    <div class="footer-col">
      <h4>Explore</h4>
      <ul class="footer-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="menu.php">Menu</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Connect</h4>
      <ul class="footer-links">
        <li><a href="https://www.facebook.com/sydcoffee">Facebook</a></li>
        <li><a href="mailto:hello@sydcoffee.com">hello@sydcoffee.com</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; 2026 SYD Coffee</p>
  </div>
</footer>

<script>
  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobileMenu');
  hamburger.addEventListener('click', function() {
    const open = mobileMenu.classList.toggle('open');
    hamburger.classList.toggle('active', open);
    document.body.style.overflow = open ? 'hidden' : '';
  });
  mobileMenu.querySelectorAll('a').forEach(function(l) {
    l.addEventListener('click', function() {
      mobileMenu.classList.remove('open');
      hamburger.classList.remove('active');
      document.body.style.overflow = '';
    });
  });

  const observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(e) {
      if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); }
    });
  }, { threshold: 0.10 });
  document.querySelectorAll('.reveal').forEach(function(el) { observer.observe(el); });
</script>
<?php include 'includes/backtotop.php'; ?>
</body>
</html>