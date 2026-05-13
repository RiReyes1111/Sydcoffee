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
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
  <script>
    window.addEventListener("pageshow", function(e) { if (e.persisted) window.location.reload(); });
  </script>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --cream:      #f8f5ef;
      --cream-dark: #ede8de;
      --espresso:   #1e120a;
      --brown-mid:  #4a3020;
      --brown-light:#7a5c3e;
      --gold:       #c8973a;
      --gold-light: #e8b84b;
      --white:      #ffffff;
      --text-muted: #7a6555;
      --nav-h:      72px;
      --radius-lg:  24px;
      --radius-md:  14px;
      --shadow:     0 8px 40px rgba(30,18,10,0.13);
      --shadow-sm:  0 2px 12px rgba(30,18,10,0.08);
      --ease:       0.35s cubic-bezier(0.4,0,0.2,1);
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'Montserrat', sans-serif;
      background: var(--cream);
      color: var(--espresso);
      overflow-x: hidden;
    }

    img { display: block; max-width: 100%; }

    /* ── NAVBAR ── */
    .navbar {
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 1000;
      height: var(--nav-h);
      padding: 0 6%;
      display: flex;
      align-items: center;
      background: rgba(248,245,239,0.97);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      box-shadow: 0 2px 20px rgba(30,18,10,0.08);
    }

    .nav-logo-link { line-height: 0; flex-shrink: 0; }
    .logo-nav { height: 44px; width: auto; }

    .nav-links {
      display: flex; align-items: center;
      gap: 32px; list-style: none;
      margin-left: 36px;
    }
    .nav-links a {
      font-size: 11px; font-weight: 600;
      letter-spacing: 2px; text-transform: uppercase;
      text-decoration: none; color: var(--espresso);
      position: relative; padding-bottom: 3px;
      transition: color var(--ease);
    }
    .nav-links a.active { color: var(--gold); }
    .nav-links a::after {
      content: ''; position: absolute;
      bottom: 0; left: 0; width: 0; height: 1.5px;
      background: var(--gold); transition: width var(--ease);
    }
    .nav-links a:hover::after,
    .nav-links a.active::after { width: 100%; }

    .nav-auth {
      margin-left: auto; display: flex;
      align-items: center; gap: 12px;
    }
    .nav-user {
      font-size: 12px; font-weight: 600;
      letter-spacing: 0.5px; color: var(--brown-mid);
    }
    .btn-nav {
      font-family: 'Montserrat', sans-serif;
      font-size: 11px; font-weight: 700;
      letter-spacing: 1.5px; text-transform: uppercase;
      text-decoration: none; padding: 8px 22px;
      border-radius: 30px; transition: var(--ease);
    }
    .btn-nav-login {
      background: var(--espresso); color: var(--cream);
      border: 1.5px solid var(--espresso);
    }
    .btn-nav-login:hover { background: var(--brown-mid); border-color: var(--brown-mid); }
    .btn-nav-logout {
      background: transparent; color: var(--espresso);
      border: 1.5px solid rgba(30,18,10,0.25);
    }
    .btn-nav-logout:hover { background: var(--espresso); color: var(--cream); border-color: var(--espresso); }

    .hamburger {
      display: none; flex-direction: column;
      gap: 5px; cursor: pointer; padding: 6px;
      margin-left: auto; background: none; border: none;
      z-index: 10002;
    }
    .hamburger span {
      display: block; width: 22px; height: 2px;
      background: var(--espresso); border-radius: 2px;
      transition: var(--ease);
    }
    .hamburger.active span:nth-child(1) { transform: rotate(45deg) translate(5px,5px); }
    .hamburger.active span:nth-child(2) { opacity: 0; }
    .hamburger.active span:nth-child(3) { transform: rotate(-45deg) translate(5px,-5px); }

    .mobile-menu {
      display: none; position: fixed; inset: 0;
      background: var(--cream); z-index: 10001;
      flex-direction: column; align-items: center;
      justify-content: center; gap: 28px;
    }
    .mobile-menu.open { display: flex; }
    .mobile-menu a {
      font-size: 13px; font-weight: 700;
      letter-spacing: 3px; text-transform: uppercase;
      color: var(--espresso); text-decoration: none;
      transition: color var(--ease);
    }
    .mobile-menu a:hover { color: var(--gold); }
    .mobile-divider { width: 40px; height: 1px; background: var(--cream-dark); }

    /* ── HERO ── */
    .about-hero {
      position: relative;
      padding: calc(var(--nav-h) + 80px) 6% 100px;
      background: var(--espresso);
      text-align: center;
      overflow: hidden;
    }
    .about-hero-bg {
      position: absolute; inset: 0;
      background-image: url('images/Gallery\ photo4.jpg');
      background-size: cover; background-position: center;
      opacity: 0.20;
    }
    .about-hero-content { position: relative; z-index: 2; }
    .hero-eyebrow {
      display: inline-block;
      font-size: 10px; font-weight: 700;
      letter-spacing: 5px; text-transform: uppercase;
      color: var(--gold-light); margin-bottom: 16px;
    }
    .about-hero h1 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2.6rem, 5.5vw, 4.5rem);
      font-weight: 400; color: var(--white);
      line-height: 1.1; margin-bottom: 20px;
    }
    .about-hero h1 em { font-style: italic; color: var(--gold-light); }
    .about-hero p {
      font-size: 15px; color: rgba(255,255,255,0.65);
      line-height: 1.8; max-width: 520px; margin: 0 auto;
    }

    /* ── STORY SECTION ── */
    .story-section {
      padding: 96px 6%;
      background: var(--cream);
    }
    .story-inner {
      max-width: 1100px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: center;
    }
    .story-text .eyebrow {
      display: block;
      font-size: 10px; font-weight: 700;
      letter-spacing: 4px; text-transform: uppercase;
      color: var(--gold); margin-bottom: 16px;
    }
    .story-text h2 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(1.9rem, 3.5vw, 2.8rem);
      font-weight: 400; color: var(--espresso);
      line-height: 1.2; margin-bottom: 24px;
      letter-spacing: 0; text-transform: none;
      text-align: left;
    }
    .story-text h2 em { font-style: italic; color: var(--gold); }
    .story-text p {
      font-size: 14px; color: var(--text-muted);
      line-height: 1.9; margin-bottom: 16px;
      text-align: left; max-width: none; margin-left: 0;
    }
    .story-img-wrap {
      position: relative;
    }
    .story-img-main {
      width: 100%;
      aspect-ratio: 4/5;
      object-fit: cover;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
    }
    .story-img-accent {
      position: absolute;
      bottom: -24px; right: -24px;
      width: 55%;
      aspect-ratio: 1;
      object-fit: cover;
      border-radius: var(--radius-md);
      border: 6px solid var(--cream);
      box-shadow: var(--shadow);
    }

    /* ── VALUES ── */
    .values-section {
      padding: 96px 6%;
      background: var(--espresso);
    }
    .values-header {
      text-align: center;
      margin-bottom: 60px;
    }
    .values-eyebrow {
      display: block;
      font-size: 10px; font-weight: 700;
      letter-spacing: 4px; text-transform: uppercase;
      color: var(--gold-light); margin-bottom: 14px;
    }
    .values-header h2 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(1.9rem, 3.5vw, 2.8rem);
      font-weight: 400; color: var(--white);
      letter-spacing: 0; text-transform: none;
    }

    .values-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
      max-width: 1000px;
      margin: 0 auto;
    }
    .value-card {
      background: rgba(255,255,255,0.055);
      border: 1px solid rgba(255,255,255,0.10);
      border-radius: var(--radius-lg);
      padding: 40px 32px;
      text-align: center;
      transition: var(--ease);
    }
    .value-card:hover {
      border-color: rgba(200,151,58,0.40);
      transform: translateY(-5px);
      box-shadow: 0 14px 44px rgba(0,0,0,0.25);
    }
    .value-letter {
      font-family: 'Playfair Display', serif;
      font-size: 4rem; font-weight: 400;
      font-style: italic;
      color: var(--gold-light);
      line-height: 1;
      margin-bottom: 16px;
    }
    .value-card h3 {
      font-size: 12px; font-weight: 800;
      letter-spacing: 3px; text-transform: uppercase;
      color: var(--white); margin-bottom: 14px;
    }
    .value-card p {
      font-size: 13px; color: rgba(255,255,255,0.55);
      line-height: 1.75; text-align: center;
      max-width: none; margin: 0;
    }

    /* ── PROMISE BANNER ── */
    .promise-section {
      padding: 80px 6%;
      background: var(--cream-dark);
      text-align: center;
    }
    .promise-section .eyebrow {
      display: block;
      font-size: 10px; font-weight: 700;
      letter-spacing: 4px; text-transform: uppercase;
      color: var(--gold); margin-bottom: 14px;
    }
    .promise-section h2 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(1.7rem, 3vw, 2.4rem);
      font-weight: 400; color: var(--espresso);
      margin-bottom: 18px;
      letter-spacing: 0; text-transform: none;
    }
    .promise-section p {
      font-size: 14px; color: var(--text-muted);
      line-height: 1.85; max-width: 620px;
      margin: 0 auto 32px;
    }
    .btn-promise {
      display: inline-block;
      font-family: 'Montserrat', sans-serif;
      font-size: 11px; font-weight: 700;
      letter-spacing: 2px; text-transform: uppercase;
      text-decoration: none;
      padding: 14px 38px;
      border-radius: 40px;
      background: var(--espresso); color: var(--cream);
      border: 2px solid var(--espresso);
      transition: var(--ease);
    }
    .btn-promise:hover {
      background: var(--brown-mid);
      border-color: var(--brown-mid);
      transform: translateY(-2px);
    }

    /* ── FOOTER ── */
    .site-footer {
      background: var(--espresso);
      padding: 68px 6% 28px;
    }
    .footer-grid {
      max-width: 1080px; margin: 0 auto;
      display: grid;
      grid-template-columns: 2fr 1fr 1fr;
      gap: 56px; padding-bottom: 44px;
      border-bottom: 1px solid rgba(255,255,255,0.07);
    }
    .footer-logo {
      height: 52px; width: auto;
      filter: brightness(0) invert(1);
      opacity: 0.80; margin-bottom: 14px;
    }
    .footer-tagline {
      font-size: 13px;
      color: rgba(248,245,239,0.40);
      line-height: 1.8; max-width: 240px;
    }
    .footer-col h4 {
      font-size: 9px; font-weight: 800;
      letter-spacing: 3px; text-transform: uppercase;
      color: var(--gold); margin-bottom: 20px;
    }
    .footer-links { list-style: none; display: flex; flex-direction: column; gap: 11px; }
    .footer-links a {
      font-size: 13px; color: rgba(248,245,239,0.50);
      text-decoration: none; display: inline-block;
      transition: var(--ease);
    }
    .footer-links a:hover { color: var(--cream); transform: translateX(4px); }
    .footer-bottom { max-width: 1080px; margin: 24px auto 0; text-align: center; }
    .footer-bottom p { font-size: 11px; letter-spacing: 1.5px; color: rgba(248,245,239,0.22); }

    /* ── REVEAL ── */
    .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.7s ease, transform 0.7s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }
    .delay-1 { transition-delay: 0.10s; }
    .delay-2 { transition-delay: 0.20s; }
    .delay-3 { transition-delay: 0.30s; }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
      .story-inner { grid-template-columns: 1fr; gap: 48px; }
      .story-img-accent { display: none; }
      .values-grid { grid-template-columns: 1fr; max-width: 440px; margin: 0 auto; }
      .footer-grid { grid-template-columns: 1fr 1fr; }
      .footer-brand { grid-column: 1 / -1; }
    }
    @media (max-width: 768px) {
      :root { --nav-h: 62px; }
      .navbar { padding: 0 20px; }
      .nav-links, .nav-auth { display: none !important; }
      .hamburger { display: flex; }
      .about-hero { padding: calc(var(--nav-h) + 50px) 20px 70px; }
      .story-section, .values-section, .promise-section { padding: 64px 20px; }
      .footer-grid { grid-template-columns: 1fr; gap: 28px; text-align: center; }
      .footer-tagline { margin: 0 auto; max-width: 100%; }
      .footer-logo { margin: 0 auto 14px; }
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
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

<!-- HERO -->
<section class="about-hero">
  <div class="about-hero-bg"></div>
  <div class="about-hero-content">
    <span class="hero-eyebrow">Who We Are</span>
    <h1>Brewed with <em>heart</em></h1>
    <p>A small booth born from tough times — now a meaningful mission serving every cup with warmth and purpose.</p>
  </div>
</section>

<!-- STORY -->
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

<!-- VALUES -->
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

<!-- PROMISE CTA -->
<section class="promise-section">
  <span class="eyebrow">Come Find Us</span>
  <h2>Ready to taste the story?</h2>
  <p>Every cup we serve carries the spirit of where we started. Come visit our booth, try our menu, and be part of the SYD Coffee community.</p>
  <a href="menu.php" class="btn-promise">Explore Our Menu</a>
</section>

<!-- FOOTER -->
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
        <li><a href="#">Instagram</a></li>
        <li><a href="https://www.facebook.com/sydcoffee">Facebook</a></li>
        <li><a href="mailto:hello@sydcoffee.com">hello@sydcoffee.com</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; 2025 SYD Coffee</p>
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
</body>
</html>