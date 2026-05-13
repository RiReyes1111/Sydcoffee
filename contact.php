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
  <title>SYD Coffee — Contact</title>
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
      position: fixed; top: 0; left: 0; right: 0;
      z-index: 1000; height: var(--nav-h); padding: 0 6%;
      display: flex; align-items: center;
      background: rgba(248,245,239,0.97);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
      box-shadow: 0 2px 20px rgba(30,18,10,0.08);
    }
    .nav-logo-link { line-height: 0; flex-shrink: 0; }
    .logo-nav { height: 44px; width: auto; }
    .nav-links {
      display: flex; align-items: center;
      gap: 32px; list-style: none; margin-left: 36px;
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
    .nav-auth { margin-left: auto; display: flex; align-items: center; gap: 12px; }
    .nav-user { font-size: 12px; font-weight: 600; letter-spacing: 0.5px; color: var(--brown-mid); }
    .btn-nav {
      font-family: 'Montserrat', sans-serif;
      font-size: 11px; font-weight: 700;
      letter-spacing: 1.5px; text-transform: uppercase;
      text-decoration: none; padding: 8px 22px;
      border-radius: 30px; transition: var(--ease);
    }
    .btn-nav-login { background: var(--espresso); color: var(--cream); border: 1.5px solid var(--espresso); }
    .btn-nav-login:hover { background: var(--brown-mid); border-color: var(--brown-mid); }
    .btn-nav-logout { background: transparent; color: var(--espresso); border: 1.5px solid rgba(30,18,10,0.25); }
    .btn-nav-logout:hover { background: var(--espresso); color: var(--cream); border-color: var(--espresso); }

    .hamburger {
      display: none; flex-direction: column; gap: 5px;
      cursor: pointer; padding: 6px; margin-left: auto;
      background: none; border: none; z-index: 10002;
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
      font-size: 13px; font-weight: 700; letter-spacing: 3px;
      text-transform: uppercase; color: var(--espresso);
      text-decoration: none; transition: color var(--ease);
    }
    .mobile-menu a:hover { color: var(--gold); }
    .mobile-divider { width: 40px; height: 1px; background: var(--cream-dark); }

    /* ── HERO ── */
    .contact-hero {
      position: relative;
      padding: calc(var(--nav-h) + 80px) 6% 100px;
      background: var(--espresso);
      text-align: center; overflow: hidden;
    }
    .contact-hero-bg {
      position: absolute; inset: 0;
      background-image: url('images/bg9213.jpg');
      background-size: cover; background-position: center;
      opacity: 0.20;
    }
    .contact-hero-content { position: relative; z-index: 2; }
    .hero-eyebrow {
      display: inline-block; font-size: 10px; font-weight: 700;
      letter-spacing: 5px; text-transform: uppercase;
      color: var(--gold-light); margin-bottom: 16px;
    }
    .contact-hero h1 {
      font-family: 'Playfair Display', serif;
      font-size: clamp(2.6rem, 5.5vw, 4.5rem);
      font-weight: 400; color: var(--white);
      line-height: 1.1; margin-bottom: 20px;
    }
    .contact-hero h1 em { font-style: italic; color: var(--gold-light); }
    .contact-hero p {
      font-size: 15px; color: rgba(255,255,255,0.65);
      line-height: 1.8; max-width: 500px; margin: 0 auto;
    }

    /* ── MAIN CONTENT ── */
    .contact-body {
      padding: 80px 6%;
    }
    .contact-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 32px;
      max-width: 1100px;
      margin: 0 auto;
    }

    /* ── INFO CARD ── */
    .info-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
      border: 1.5px solid var(--cream-dark);
    }
    .info-card-header {
      background: var(--espresso);
      padding: 28px 32px;
    }
    .info-card-header .eyebrow {
      display: block; font-size: 9px; font-weight: 700;
      letter-spacing: 3px; text-transform: uppercase;
      color: var(--gold-light); margin-bottom: 8px;
    }
    .info-card-header h2 {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem; font-weight: 400;
      color: var(--white); letter-spacing: 0;
      text-transform: none; text-align: left;
      margin: 0;
    }
    .info-card-body { padding: 32px; }

    .map-wrap {
      border-radius: var(--radius-md);
      overflow: hidden;
      margin-bottom: 28px;
      box-shadow: var(--shadow-sm);
    }
    .map-wrap iframe { display: block; width: 100%; height: 240px; border: 0; }

    .address-box {
      background: var(--cream);
      border-radius: var(--radius-md);
      padding: 18px 20px;
      margin-bottom: 24px;
      border: 1px solid var(--cream-dark);
    }
    .address-box p {
      font-size: 13px; color: var(--text-muted);
      line-height: 1.75; text-align: left;
      margin: 0; max-width: none;
    }
    .address-box strong { color: var(--espresso); }

    .contact-items { display: flex; flex-direction: column; gap: 16px; }
    .contact-item {
      display: flex; align-items: flex-start; gap: 14px;
    }
    .contact-icon {
      width: 38px; height: 38px; border-radius: 10px;
      background: var(--cream); border: 1px solid var(--cream-dark);
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; flex-shrink: 0;
    }
    .contact-item-text { flex: 1; }
    .contact-item-label {
      font-size: 9px; font-weight: 700;
      letter-spacing: 2px; text-transform: uppercase;
      color: var(--text-muted); margin-bottom: 3px;
    }
    .contact-item-val {
      font-size: 13px; font-weight: 600;
      color: var(--espresso); line-height: 1.5;
    }
    .contact-item-val a {
      color: var(--gold); text-decoration: none;
      transition: color var(--ease);
    }
    .contact-item-val a:hover { color: var(--espresso); }

    /* ── FORM CARD ── */
    .form-card {
      background: var(--white);
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
      border: 1.5px solid var(--cream-dark);
    }
    .form-card-header {
      background: linear-gradient(135deg, var(--brown-mid), var(--espresso));
      padding: 28px 32px;
    }
    .form-card-header .eyebrow {
      display: block; font-size: 9px; font-weight: 700;
      letter-spacing: 3px; text-transform: uppercase;
      color: var(--gold-light); margin-bottom: 8px;
    }
    .form-card-header h2 {
      font-family: 'Playfair Display', serif;
      font-size: 1.5rem; font-weight: 400;
      color: var(--white); letter-spacing: 0;
      text-transform: none; text-align: left; margin: 0;
    }
    .form-card-body { padding: 32px; }

    .form-group { margin-bottom: 18px; }
    .form-group label {
      display: block; font-size: 10px; font-weight: 700;
      letter-spacing: 1.5px; text-transform: uppercase;
      color: var(--text-muted); margin-bottom: 7px;
    }
    .form-group input,
    .form-group textarea {
      width: 100%; padding: 12px 16px;
      background: var(--cream); border: 1.5px solid var(--cream-dark);
      border-radius: var(--radius-md);
      font-family: 'Montserrat', sans-serif;
      font-size: 13px; color: var(--espresso);
      transition: border-color var(--ease), box-shadow var(--ease);
      outline: none; resize: none;
    }
    .form-group input:focus,
    .form-group textarea:focus {
      border-color: var(--gold);
      box-shadow: 0 0 0 3px rgba(200,151,58,0.12);
      background: var(--white);
    }
    .form-group textarea { height: 130px; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    .form-actions { display: flex; gap: 12px; margin-top: 6px; }

    .btn-submit {
      flex: 1; padding: 13px 20px;
      background: var(--espresso); color: var(--cream);
      border: 2px solid var(--espresso);
      border-radius: var(--radius-md);
      font-family: 'Montserrat', sans-serif;
      font-size: 11px; font-weight: 700;
      letter-spacing: 1.5px; text-transform: uppercase;
      cursor: pointer; transition: var(--ease);
    }
    .btn-submit:hover { background: var(--brown-mid); border-color: var(--brown-mid); transform: translateY(-1px); }

    .btn-reset {
      padding: 13px 20px;
      background: transparent; color: var(--text-muted);
      border: 1.5px solid var(--cream-dark);
      border-radius: var(--radius-md);
      font-family: 'Montserrat', sans-serif;
      font-size: 11px; font-weight: 700;
      letter-spacing: 1.5px; text-transform: uppercase;
      cursor: pointer; transition: var(--ease);
    }
    .btn-reset:hover { border-color: var(--espresso); color: var(--espresso); }

    /* ── FOOTER ── */
    .site-footer { background: var(--espresso); padding: 68px 6% 28px; }
    .footer-grid {
      max-width: 1080px; margin: 0 auto;
      display: grid; grid-template-columns: 2fr 1fr 1fr;
      gap: 56px; padding-bottom: 44px;
      border-bottom: 1px solid rgba(255,255,255,0.07);
    }
    .footer-logo { height: 52px; width: auto; filter: brightness(0) invert(1); opacity: 0.80; margin-bottom: 14px; }
    .footer-tagline { font-size: 13px; color: rgba(248,245,239,0.40); line-height: 1.8; max-width: 240px; }
    .footer-col h4 { font-size: 9px; font-weight: 800; letter-spacing: 3px; text-transform: uppercase; color: var(--gold); margin-bottom: 20px; }
    .footer-links { list-style: none; display: flex; flex-direction: column; gap: 11px; }
    .footer-links a { font-size: 13px; color: rgba(248,245,239,0.50); text-decoration: none; display: inline-block; transition: var(--ease); }
    .footer-links a:hover { color: var(--cream); transform: translateX(4px); }
    .footer-bottom { max-width: 1080px; margin: 24px auto 0; text-align: center; }
    .footer-bottom p { font-size: 11px; letter-spacing: 1.5px; color: rgba(248,245,239,0.22); }

    /* ── REVEAL ── */
    .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.7s ease, transform 0.7s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }
    .delay-1 { transition-delay: 0.10s; }
    .delay-2 { transition-delay: 0.20s; }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
      .contact-grid { grid-template-columns: 1fr; }
      .footer-grid { grid-template-columns: 1fr 1fr; }
      .footer-brand { grid-column: 1 / -1; }
    }
    @media (max-width: 768px) {
      :root { --nav-h: 62px; }
      .navbar { padding: 0 20px; }
      .nav-links, .nav-auth { display: none !important; }
      .hamburger { display: flex; }
      .contact-hero { padding: calc(var(--nav-h) + 50px) 20px 70px; }
      .contact-body { padding: 56px 20px; }
      .info-card-body, .form-card-body { padding: 24px 20px; }
      .form-row { grid-template-columns: 1fr; gap: 0; }
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
    <li><a href="about.php">About</a></li>
    <li><a href="contact.php" class="active">Contact</a></li>
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
<section class="contact-hero">
  <div class="contact-hero-bg"></div>
  <div class="contact-hero-content">
    <span class="hero-eyebrow">We'd love to hear from you</span>
    <h1>Get <em>in touch</em></h1>
    <p>Visit our booth, send us a message, or find us at your next local event.</p>
  </div>
</section>

<!-- BODY -->
<div class="contact-body">
  <div class="contact-grid">

    <!-- INFO -->
    <div class="info-card reveal">
      <div class="info-card-header">
        <span class="eyebrow">Find Us</span>
        <h2>Our Location</h2>
      </div>
      <div class="info-card-body">
        <div class="map-wrap">
          <iframe
            src="https://www.google.com/maps?q=Col.%20S.%20Cruz%20St.%20San%20Rafael%20corner%20Greenrose%20Subd.%20(infront%20of%20ATF%20Builders%20Construction%20Supply)&output=embed"
            allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="SYD Coffee Location">
          </iframe>
        </div>
        <div class="address-box">
          <p><strong>SYD Coffee</strong><br>
          Col. S. Cruz St. San Rafael corner Greenrose Subd.<br>
          (In front of ATF Builders Construction Supply)</p>
        </div>
        <div class="contact-items">
          <div class="contact-item">
            <div class="contact-icon">📞</div>
            <div class="contact-item-text">
              <div class="contact-item-label">Phone</div>
              <div class="contact-item-val">+63 912 345 6789</div>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-icon">✉️</div>
            <div class="contact-item-text">
              <div class="contact-item-label">Email</div>
              <div class="contact-item-val"><a href="mailto:hello@sydcoffee.com">hello@sydcoffee.com</a></div>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-icon">🕐</div>
            <div class="contact-item-text">
              <div class="contact-item-label">Hours</div>
              <div class="contact-item-val">7:00 AM – 10:00 PM · Everyday</div>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-icon">📲</div>
            <div class="contact-item-text">
              <div class="contact-item-label">Follow Us</div>
              <div class="contact-item-val">
                <a href="https://www.facebook.com/sydcoffee" target="_blank">Facebook</a> &nbsp;·&nbsp;
                <a href="#" target="_blank">Instagram</a> &nbsp;·&nbsp;
                <a href="#" target="_blank">TikTok</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- FORM -->
    <div class="form-card reveal delay-1">
      <div class="form-card-header">
        <span class="eyebrow">Drop Us a Line</span>
        <h2>Send a Message</h2>
      </div>
      <div class="form-card-body">
        <form action="mailto:hello@sydcoffee.com" method="post" enctype="text/plain">
          <div class="form-row">
            <div class="form-group">
              <label for="name">Full Name *</label>
              <input type="text" id="name" name="name" required placeholder="Your name">
            </div>
            <div class="form-group">
              <label for="email">Email *</label>
              <input type="email" id="email" name="email" required placeholder="you@example.com">
            </div>
          </div>
          <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" placeholder="What is this regarding?">
          </div>
          <div class="form-group">
            <label for="message">Message *</label>
            <textarea id="message" name="message" required placeholder="Tell us what's on your mind..."></textarea>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-submit">Send Message</button>
            <button type="reset" class="btn-reset">Clear</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

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