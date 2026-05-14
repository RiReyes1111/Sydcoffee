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
  <title>SYD Coffee — Home</title>
  <link rel="icon" type="image/png" href="images/logosydnobg.png">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/indexcss.css">
  <script>
    window.addEventListener("pageshow", function(e) {
      if (e.persisted) window.location.reload();
    });
  </script>
</head>
<body class="light-btt">

<nav class="navbar" id="navbar">
  <a href="index.php" class="nav-logo-link">
    <img src="images/logosydnobg.png" alt="SYD Coffee" class="logo-nav">
  </a>
  <ul class="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="menu.php">Menu</a></li>
    <li><a href="about.php">About</a></li>
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

<section class="hero" id="hero">
  <div class="hero-bg" id="heroBg"></div>
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <span class="hero-eyebrow">Handcrafted with passion</span>
    <h1 class="hero-title">Every cup tells<br><em>a story</em></h1>
    <p class="hero-sub">Premium coffee, carefully sourced and brewed to perfection —<br>served wherever you find us.</p>
    <div class="hero-btns">
      <a href="menu.php" class="btn-primary">Explore Menu</a>
      <a href="about.php" class="btn-outline-white">Our Story</a>
    </div>
  </div>
  <div class="hero-scroll-hint">
    <div class="scroll-bar"></div>
    <span>Scroll</span>
  </div>
</section>

<section class="sec bs-section" id="best-sellers-section">
  <span class="sec-label reveal">Fan Favourites</span>
  <h2 class="sec-title reveal delay-1">Best Sellers</h2>
  <p class="sec-desc reveal delay-2">The drinks our customers keep coming back for — crafted to delight, every single time.</p>
  <div class="bs-grid">
    <div class="bs-card reveal delay-1">
      <div class="bs-img">
        <img src="images/spanish-latte.jpg" alt="Spanish Latte" loading="lazy">
      </div>
      <span class="bs-tag">Spanish Latte</span>
      <p class="bs-text">A velvety blend of espresso and milk with a hint of caramelized sugar.</p>
    </div>
    <div class="bs-card featured reveal delay-2">
      <div class="bs-img">
        <img src="images/caramel-macchiato.jpg" alt="Caramel Macchiato" loading="lazy">
      </div>
      <span class="bs-tag">Caramel Macchiato</span>
      <p class="bs-text">Bold and indulgent with a silky finish — a layered blend of espresso and caramel.</p>
    </div>
    <div class="bs-card reveal delay-3">
      <div class="bs-img">
        <img src="images/matcha-latte.jpg" alt="Matcha Latte" loading="lazy">
      </div>
      <span class="bs-tag">Matcha Latte</span>
      <p class="bs-text">Earthy and refreshing with a creamy finish — a smooth blend of matcha and milk.</p>
    </div>
  </div>
</section>

<section class="sec promo-section" id="promotions">
  <span class="sec-label reveal">Special Offers</span>
  <h2 class="sec-title on-dark reveal delay-1">Current Promotions</h2>
  <p class="sec-desc on-dark reveal delay-2">Enjoy these exclusive deals — sign up for a free account to unlock member benefits.</p>
  <div class="promo-grid">
    <a href="<?= $loggedIn ? 'menu.php' : 'login.php' ?>" class="promo-link reveal delay-1">
      <div class="promo-card">
        <div class="promo-badge">Limited Time</div>
        <h3>Happy Hour Student Rush</h3>
        <p class="promo-when">9AM – 11AM Daily</p>
        <p class="promo-desc">Extra 10% off all cold drinks during happy hour — 20% total for members!</p>
      </div>
    </a>
    <a href="<?= $loggedIn ? 'menu.php' : 'login.php' ?>" class="promo-link reveal delay-2">
      <div class="promo-card">
        <div class="promo-badge">Members Only</div>
        <h3>Member Discount</h3>
        <p class="promo-when">Free to join</p>
        <p class="promo-desc">Get 10% off on all drinks just by signing up for a free account!</p>
      </div>
    </a>
  </div>
</section>

<section class="sec gallery-section" id="gallery">
  <span class="sec-label reveal">Behind the Brew</span>
  <h2 class="sec-title reveal delay-1">Gallery</h2>
  <p class="sec-desc reveal delay-2">A glimpse at our favourite moments — from pop-ups to community events.</p>
  <div class="gallery-grid">
    <div class="gallery-item reveal delay-1">
      <img src="images/Gallery photo1.jpg" alt="Gallery 1" loading="lazy">
    </div>
    <div class="gallery-item reveal delay-2">
      <img src="images/Gallery photo2.jpg" alt="Gallery 2" loading="lazy">
    </div>
    <div class="gallery-item reveal delay-3">
      <img src="images/Gallery photo3.jpg" alt="Gallery 3" loading="lazy">
    </div>
  </div>
</section>

<section class="sec booth-section" id="booth">
  <div class="booth-bg"></div>
  <div class="booth-inner">
    <span class="sec-label reveal" style="color:var(--gold-light)">Find Us</span>
    <h2 class="sec-title on-dark reveal delay-1">Our Coffee Booth</h2>
    <p class="sec-desc on-dark reveal delay-2">We bring SYD Coffee wherever we go — from local events to community pop-ups.</p>
    <div class="booth-grid">
      <div class="booth-item reveal delay-1">
        <img src="images/booth1.jpg" alt="Coffee Booth 1" loading="lazy">
      </div>
      <div class="booth-item reveal delay-2">
        <img src="images/booth2.jpg" alt="Coffee Booth 2" loading="lazy">
      </div>
    </div>
  </div>
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
  window.addEventListener('load', function() {
    document.getElementById('heroBg').classList.add('loaded');
  });

  const navbar = document.getElementById('navbar');
  window.addEventListener('scroll', function() {
    navbar.classList.toggle('scrolled', window.scrollY > 50);
  }, { passive: true });

  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobileMenu');

  hamburger.addEventListener('click', function() {
    const open = mobileMenu.classList.toggle('open');
    hamburger.classList.toggle('active', open);
    document.body.style.overflow = open ? 'hidden' : '';
    if (open) navbar.classList.add('scrolled');
    else if (window.scrollY <= 50) navbar.classList.remove('scrolled');
  });

  mobileMenu.querySelectorAll('a').forEach(function(link) {
    link.addEventListener('click', function() {
      mobileMenu.classList.remove('open');
      hamburger.classList.remove('active');
      document.body.style.overflow = '';
      if (window.scrollY <= 50) navbar.classList.remove('scrolled');
    });
  });

  const observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(e) {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.10 });
  document.querySelectorAll('.reveal').forEach(function(el) { observer.observe(el); });
</script>
<?php include 'includes/backtotop.php'; ?>
</body>
</html>