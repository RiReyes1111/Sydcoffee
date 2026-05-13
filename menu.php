<?php
session_start();
include "config.php";

header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

$loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $loggedIn ? $_SESSION['name'] : '';
$userRole = $_SESSION['role'] ?? null;

$isUser  = ($loggedIn && $userRole === 'user');
$isAdmin = ($loggedIn && $userRole === 'admin');

$currentHour = (int)date('H');
$isHappyHour = ($currentHour >= 15 && $currentHour < 17);

$discount     = 0.10;
$coldDiscount = ($isUser && $isHappyHour) ? 0.20 : ($isUser ? 0.10 : 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>SYD Coffee — Menu</title>
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
    --green:      #2d7a4f;
    --nav-h:      72px;
    --radius-lg:  20px;
    --radius-md:  12px;
    --shadow:     0 8px 40px rgba(30,18,10,0.13);
    --shadow-sm:  0 2px 12px rgba(30,18,10,0.08);
    --ease:       0.3s cubic-bezier(0.4,0,0.2,1);
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
    display: flex;
    align-items: center;
    gap: 32px;
    list-style: none;
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
    bottom: 0; left: 0;
    width: 0; height: 1.5px;
    background: var(--gold);
    transition: width var(--ease);
  }
  .nav-links a:hover::after,
  .nav-links a.active::after { width: 100%; }

  .nav-auth {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .nav-user {
    font-size: 12px; font-weight: 600;
    letter-spacing: 0.5px; color: var(--brown-mid);
  }

  .btn-nav {
    font-family: 'Montserrat', sans-serif;
    font-size: 11px; font-weight: 700;
    letter-spacing: 1.5px; text-transform: uppercase;
    text-decoration: none;
    padding: 8px 22px;
    border-radius: 30px;
    transition: var(--ease);
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
  .btn-nav-dash {
    background: var(--gold); color: var(--espresso);
    border: 1.5px solid var(--gold);
  }
  .btn-nav-dash:hover { background: var(--gold-light); border-color: var(--gold-light); }

  .hamburger {
    display: none; flex-direction: column;
    gap: 5px; cursor: pointer; padding: 6px;
    margin-left: auto; z-index: 10002;
    background: none; border: none;
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
    display: none;
    position: fixed; inset: 0;
    background: var(--cream);
    z-index: 10001;
    flex-direction: column;
    align-items: center; justify-content: center;
    gap: 28px;
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

  /* ── HAPPY HOUR BANNER ── */
  .happy-banner {
    background: linear-gradient(135deg, var(--brown-mid), var(--espresso));
    color: var(--gold-light);
    text-align: center;
    padding: 12px 20px;
    font-size: 13px; font-weight: 700;
    letter-spacing: 1px;
    margin-top: var(--nav-h);
  }

  /* ── HERO ── */
  .menu-hero {
    position: relative;
    padding: calc(var(--nav-h) + 60px) 6% 80px;
    background: var(--espresso);
    text-align: center;
    overflow: hidden;
  }
  .menu-hero-bg {
    position: absolute; inset: 0;
    background-image: url('images/Gallery\ photo4.jpg');
    background-size: cover; background-position: center;
    opacity: 0.25;
  }
  .menu-hero-content { position: relative; z-index: 2; }
  .menu-hero-eyebrow {
    display: inline-block;
    font-size: 10px; font-weight: 700;
    letter-spacing: 5px; text-transform: uppercase;
    color: var(--gold-light); margin-bottom: 16px;
  }
  .menu-hero h1 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(2.4rem, 5vw, 4rem);
    font-weight: 400; color: var(--white);
    line-height: 1.1; margin-bottom: 16px;
  }
  .menu-hero h1 em { font-style: italic; color: var(--gold-light); }
  .menu-hero p {
    font-size: 14px; color: rgba(255,255,255,0.65);
    line-height: 1.75; max-width: 480px; margin: 0 auto;
  }

  /* ── CATEGORY NAV ── */
  .cat-nav {
    position: sticky;
    top: var(--nav-h);
    z-index: 500;
    background: var(--white);
    border-bottom: 1px solid var(--cream-dark);
    display: flex;
    justify-content: center;
    gap: 0;
    overflow-x: auto;
    scrollbar-width: none;
  }
  .cat-nav::-webkit-scrollbar { display: none; }
  .cat-nav a {
    font-size: 10px; font-weight: 700;
    letter-spacing: 2px; text-transform: uppercase;
    text-decoration: none; color: var(--text-muted);
    padding: 16px 28px;
    border-bottom: 2px solid transparent;
    white-space: nowrap;
    transition: var(--ease);
  }
  .cat-nav a:hover,
  .cat-nav a.active {
    color: var(--espresso);
    border-bottom-color: var(--gold);
  }

  /* ── SECTION ── */
  .menu-section {
    padding: 72px 6%;
  }
  .menu-section:nth-child(even) { background: var(--cream-dark); }

  .section-header {
    text-align: center;
    margin-bottom: 48px;
  }
  .section-tag {
    display: inline-block;
    font-size: 9px; font-weight: 800;
    letter-spacing: 3px; text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 10px;
  }
  .section-header h2 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.7rem, 3.5vw, 2.4rem);
    font-weight: 400; color: var(--espresso);
    letter-spacing: 0; text-transform: none;
    line-height: 1.2;
  }

  /* ── PRODUCT GRID ── */
  .product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 24px;
    max-width: 1200px;
    margin: 0 auto;
  }

  /* ── PRODUCT CARD ── */
  .product-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1.5px solid var(--cream-dark);
    display: flex;
    flex-direction: column;
    transition: box-shadow var(--ease), transform var(--ease), border-color var(--ease);
  }
  .product-card:hover {
    box-shadow: var(--shadow);
    transform: translateY(-4px);
    border-color: #d4bfa0;
  }

  .card-img {
    width: 100%;
    aspect-ratio: 4/3;
    overflow: hidden;
    flex-shrink: 0;
  }
  .card-img img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
  }
  .product-card:hover .card-img img { transform: scale(1.06); }

  .card-body {
    padding: 18px 18px 14px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .card-name {
    font-size: 14px; font-weight: 700;
    color: var(--espresso); line-height: 1.3;
  }

  .price-row {
    display: flex;
    gap: 10px;
    justify-content: center;
  }
  .price-pill {
    background: var(--cream);
    border: 1px solid var(--cream-dark);
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 12px; font-weight: 700;
    color: var(--brown-mid);
    display: flex; flex-direction: column;
    align-items: center; gap: 1px;
  }
  .price-pill .size-label {
    font-size: 9px; font-weight: 700;
    letter-spacing: 1.5px; text-transform: uppercase;
    color: var(--text-muted);
  }

  .discount-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #e8f7ee;
    color: var(--green);
    border: 1px solid rgba(45,122,79,0.20);
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 11px; font-weight: 700;
    letter-spacing: 0.5px;
    align-self: center;
  }

  .card-footer {
    padding: 0 18px 18px;
  }

  .btn-buy {
    width: 100%;
    padding: 11px;
    background: var(--espresso);
    color: var(--cream);
    border: none;
    border-radius: var(--radius-md);
    font-family: 'Montserrat', sans-serif;
    font-size: 11px; font-weight: 700;
    letter-spacing: 1.5px; text-transform: uppercase;
    cursor: pointer;
    transition: var(--ease);
  }
  .btn-buy:hover {
    background: var(--brown-mid);
    transform: translateY(-1px);
  }

  /* ── SIZE POPUP ── */
  .popup-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.55);
    z-index: 3000;
    justify-content: center; align-items: center;
    padding: 20px;
  }
  .popup-overlay.active { display: flex; }

  .popup-box {
    background: var(--white);
    border-radius: var(--radius-lg);
    padding: 36px 28px 28px;
    width: 100%; max-width: 360px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.25);
  }
  .popup-box h3 {
    font-family: 'Playfair Display', serif;
    font-size: 1.3rem; font-weight: 400;
    color: var(--espresso); margin-bottom: 6px;
    letter-spacing: 0; text-transform: none;
  }
  .popup-box p {
    font-size: 12px; color: var(--text-muted);
    margin-bottom: 22px; text-align: center;
  }

  .size-options {
    display: flex; gap: 10px;
    justify-content: center;
    margin-bottom: 22px;
  }
  .size-btn {
    flex: 1;
    padding: 12px 10px;
    border: 2px solid var(--cream-dark);
    border-radius: var(--radius-md);
    background: var(--cream);
    color: var(--espresso);
    font-family: 'Montserrat', sans-serif;
    font-weight: 700; font-size: 12px;
    cursor: pointer;
    transition: var(--ease);
  }
  .size-btn:hover,
  .size-btn.selected {
    background: var(--espresso);
    border-color: var(--espresso);
    color: var(--white);
  }

  .popup-actions {
    display: flex; gap: 10px;
    margin-bottom: 10px;
  }
  .popup-actions button {
    flex: 1; padding: 12px;
    border-radius: var(--radius-md);
    border: none; font-family: 'Montserrat', sans-serif;
    font-weight: 700; font-size: 12px;
    letter-spacing: 0.5px; cursor: pointer;
    transition: var(--ease);
  }
  .btn-add-cart {
    background: var(--cream-dark); color: var(--espresso);
  }
  .btn-add-cart:hover { background: #d4bfa0; }
  .btn-order-now {
    background: var(--espresso); color: var(--white);
  }
  .btn-order-now:hover { background: var(--brown-mid); }

  .btn-cancel-popup {
    width: 100%; padding: 11px;
    background: transparent; color: var(--text-muted);
    border: 1.5px solid var(--cream-dark);
    border-radius: var(--radius-md);
    font-family: 'Montserrat', sans-serif;
    font-size: 12px; font-weight: 600;
    cursor: pointer; transition: var(--ease);
  }
  .btn-cancel-popup:hover { border-color: var(--espresso); color: var(--espresso); }

  /* ── CART ── */
  .cart-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.35); z-index: 1999;
  }
  .cart-overlay.active { display: block; }

  .cart-sidebar {
    position: fixed;
    top: 0; right: -420px;
    width: 400px; height: 100%;
    background: var(--white);
    box-shadow: -4px 0 30px rgba(0,0,0,0.15);
    z-index: 2000;
    display: flex; flex-direction: column;
    transition: right var(--ease);
  }
  .cart-sidebar.open { right: 0; }

  .cart-header {
    padding: 20px 24px;
    background: var(--espresso);
    color: var(--white);
    display: flex; justify-content: space-between; align-items: center;
  }
  .cart-header h3 {
    font-family: 'Playfair Display', serif;
    font-size: 1.2rem; font-weight: 400;
    letter-spacing: 0; text-transform: none;
  }
  .cart-close {
    background: none; border: none;
    color: rgba(255,255,255,0.7);
    font-size: 20px; cursor: pointer;
    line-height: 1; padding: 4px;
    transition: color var(--ease);
  }
  .cart-close:hover { color: var(--white); }

  .cart-items { flex: 1; overflow-y: auto; padding: 16px; }

  .cart-item {
    display: flex;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid var(--cream-dark);
    gap: 12px; font-size: 13px;
  }
  .cart-item-info { flex: 1; }
  .cart-item-name { font-weight: 700; color: var(--espresso); margin-bottom: 2px; }
  .cart-item-size { font-size: 11px; color: var(--text-muted); }
  .cart-qty {
    display: flex; align-items: center; gap: 8px;
    margin-top: 6px;
  }
  .cart-qty button {
    width: 26px; height: 26px;
    border-radius: 50%;
    border: 1.5px solid var(--cream-dark);
    background: var(--white); color: var(--espresso);
    font-weight: 700; font-size: 14px;
    cursor: pointer; line-height: 1;
    transition: var(--ease);
  }
  .cart-qty button:hover {
    background: var(--espresso);
    border-color: var(--espresso);
    color: var(--white);
  }
  .cart-qty span { font-weight: 700; min-width: 20px; text-align: center; }
  .cart-item-price { font-weight: 700; font-size: 14px; color: var(--brown-mid); }
  .cart-item-remove {
    background: none; border: none;
    color: #ccc; font-size: 16px;
    cursor: pointer; padding: 4px;
    transition: color var(--ease);
  }
  .cart-item-remove:hover { color: #e74c3c; }

  .cart-empty {
    text-align: center; color: var(--text-muted);
    margin-top: 60px; font-size: 14px; line-height: 1.8;
  }

  .cart-footer {
    padding: 20px 24px;
    border-top: 1px solid var(--cream-dark);
    background: var(--cream);
  }
  .cart-total {
    display: flex; justify-content: space-between;
    font-weight: 700; font-size: 15px;
    margin-bottom: 14px; color: var(--espresso);
  }
  .btn-checkout {
    width: 100%; padding: 14px;
    background: var(--espresso); color: var(--white);
    border: none; border-radius: var(--radius-md);
    font-family: 'Montserrat', sans-serif;
    font-weight: 700; font-size: 13px;
    letter-spacing: 1px; text-transform: uppercase;
    cursor: pointer; transition: var(--ease);
  }
  .btn-checkout:hover { background: var(--brown-mid); }

  .cart-toggle {
    position: fixed;
    bottom: 28px; right: 28px;
    background: var(--espresso); color: var(--white);
    border: none; border-radius: 50px;
    padding: 13px 22px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700; font-size: 13px;
    cursor: pointer;
    box-shadow: 0 6px 24px rgba(30,18,10,0.25);
    z-index: 999;
    display: flex; align-items: center; gap: 10px;
    transition: var(--ease);
  }
  .cart-toggle:hover { background: var(--brown-mid); transform: translateY(-2px); }

  .cart-count {
    background: #e74c3c;
    border-radius: 50%;
    width: 20px; height: 20px;
    font-size: 11px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
  }

  /* ── FOOTER ── */
  .site-footer {
    background: var(--espresso);
    padding: 32px 6%;
    text-align: center;
  }
  .site-footer p {
    font-size: 11px; letter-spacing: 1.5px;
    color: rgba(248,245,239,0.25);
  }

  /* ── RESPONSIVE ── */
  @media (max-width: 768px) {
    :root { --nav-h: 62px; }
    .navbar { padding: 0 20px; }
    .nav-links, .nav-auth { display: none !important; }
    .hamburger { display: flex; }

    .menu-hero { padding: calc(var(--nav-h) + 40px) 20px 60px; }
    .menu-section { padding: 56px 20px; }

    .product-grid {
      grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
      gap: 14px;
    }

    .cart-sidebar { width: 100%; right: -100%; }
    .cart-sidebar.open { right: 0; }

    .cat-nav a { padding: 14px 18px; font-size: 9px; }
  }

  @media (max-width: 400px) {
    .product-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
    .card-body { padding: 12px 12px 10px; }
    .card-footer { padding: 0 12px 12px; }
    .card-name { font-size: 12px; }
  }
</style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar" id="navbar">
  <a href="index.php" class="nav-logo-link">
    <img src="images/logosydnobg.png" alt="SYD Coffee" class="logo-nav">
  </a>

  <ul class="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="menu.php" class="active">Menu</a></li>
    <li><a href="about.php">About</a></li>
    <li><a href="contact.php">Contact</a></li>
  </ul>

  <div class="nav-auth">
    <?php if ($isUser): ?>
      <span class="nav-user">Hi, <?= htmlspecialchars($userName) ?>!</span>
      <a href="logout.php" class="btn-nav btn-nav-logout">Logout</a>
    <?php elseif ($isAdmin): ?>
      <span class="nav-user">Admin: <?= htmlspecialchars($userName) ?></span>
      <a href="admin.php" class="btn-nav btn-nav-dash">Dashboard</a>
      <a href="logout.php" class="btn-nav btn-nav-logout">Logout</a>
    <?php else: ?>
      <a href="login.php" class="btn-nav btn-nav-login">Login</a>
    <?php endif; ?>
  </div>

  <button class="hamburger" id="hamburger" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
  <a href="index.php">Home</a>
  <a href="menu.php">Menu</a>
  <a href="about.php">About</a>
  <a href="contact.php">Contact</a>
  <div class="mobile-divider"></div>
  <?php if ($isUser): ?>
    <span class="nav-user" style="color:var(--brown-mid)">Hi, <?= htmlspecialchars($userName) ?>!</span>
    <a href="logout.php">Logout</a>
  <?php elseif ($isAdmin): ?>
    <a href="admin.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  <?php else: ?>
    <a href="login.php">Login</a>
  <?php endif; ?>
</div>

<!-- Happy Hour Banner -->
<?php if ($isUser && $isHappyHour): ?>
<div class="happy-banner">
  🎉 Happy Hour! 20% off all Cold Drinks until 5:00 PM!
</div>
<?php endif; ?>

<!-- ── HERO ── -->
<div class="menu-hero" <?php if (!$isUser && !$isHappyHour): ?>style="margin-top:var(--nav-h)"<?php endif; ?>>
  <div class="menu-hero-bg"></div>
  <div class="menu-hero-content">
    <span class="menu-hero-eyebrow">Handcrafted Drinks</span>
    <h1>Our <em>Menu</em></h1>
    <p>Every drink made to order — pick your size, pick your flavour, and let us do the rest.</p>
  </div>
</div>

<!-- ── CATEGORY NAV ── -->
<nav class="cat-nav">
  <a href="#coldcoffee">Cold Coffee</a>
  <a href="#noncoffee">Non-Coffee & Soda</a>
  <a href="#hotdrinks">Hot Drinks</a>
</nav>

<!-- SIZE POPUP -->
<div class="popup-overlay" id="sizePopup">
  <div class="popup-box">
    <h3 id="popupItemName"></h3>
    <p>Choose your size</p>
    <div class="size-options" id="sizeOptions"></div>
    <div class="popup-actions">
      <button class="btn-add-cart" onclick="confirmAction('cart')">Add to Cart</button>
      <button class="btn-order-now" onclick="confirmAction('order')">Order Now</button>
    </div>
    <button class="btn-cancel-popup" onclick="closePopup()">Cancel</button>
  </div>
</div>

<!-- Cart -->
<div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>
<div class="cart-sidebar" id="cartSidebar">
  <div class="cart-header">
    <h3>Your Cart</h3>
    <button class="cart-close" onclick="closeCart()">✕</button>
  </div>
  <div class="cart-items" id="cartItems"></div>
  <div class="cart-footer">
    <div class="cart-total">
      <span>Total</span>
      <span id="cartTotal">₱0.00</span>
    </div>
    <button class="btn-checkout" onclick="goToCheckout()">Proceed to Checkout</button>
  </div>
</div>

<button class="cart-toggle" onclick="openCart()">
  🛒 Cart <span class="cart-count" id="cartCount">0</span>
</button>

<!-- ── MENU SECTIONS ── -->
<main>

<!-- COLD COFFEE -->
<section class="menu-section" id="coldcoffee">
  <div class="section-header">
    <span class="section-tag">Iced &amp; Refreshing</span>
    <h2>Cold Coffee</h2>
  </div>
  <div class="product-grid">
  <?php
  $result = $conn->query("SELECT * FROM coffee_items WHERE category='cold_coffee' AND is_available=1 AND deleted_at IS NULL");
  while ($row = $result->fetch_assoc()):
    $small = $row['price_small'];
    $large = $row['price_large'];
    if ($coldDiscount > 0) {
      $small -= $small * $coldDiscount;
      $large -= $large * $coldDiscount;
    }
  ?>
  <div class="product-card">
    <div class="card-img">
      <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" loading="lazy">
    </div>
    <div class="card-body">
      <div class="card-name"><?= htmlspecialchars($row['name']) ?></div>
      <div class="price-row">
        <div class="price-pill">
          <span class="size-label">Small</span>
          ₱<?= number_format($small, 2) ?>
        </div>
        <div class="price-pill">
          <span class="size-label">Large</span>
          ₱<?= number_format($large, 2) ?>
        </div>
      </div>
      <?php if ($isUser && $isHappyHour): ?>
        <span class="discount-tag">🎉 20% OFF — Happy Hour</span>
      <?php elseif ($isUser): ?>
        <span class="discount-tag">✓ 10% Member Discount</span>
      <?php endif; ?>
    </div>
    <div class="card-footer">
      <button class="btn-buy" onclick="openPopup('<?= addslashes($row['name']) ?>', 'two', <?= number_format($small, 2, '.', '') ?>, <?= number_format($large, 2, '.', '') ?>)">Buy Now</button>
    </div>
  </div>
  <?php endwhile; ?>
  </div>
</section>

<!-- NON-COFFEE -->
<section class="menu-section" id="noncoffee">
  <div class="section-header">
    <span class="section-tag">Fruity &amp; Fizzy</span>
    <h2>Non-Coffee &amp; Soda</h2>
  </div>
  <div class="product-grid">
  <?php
  $result = $conn->query("SELECT * FROM coffee_items WHERE category='non_coffee' AND is_available=1 AND deleted_at IS NULL");
  while ($row = $result->fetch_assoc()):
    $small = $row['price_small'];
    $large = $row['price_large'];
    if ($isUser) {
      $small -= $small * $discount;
      $large -= $large * $discount;
    }
  ?>
  <div class="product-card">
    <div class="card-img">
      <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" loading="lazy">
    </div>
    <div class="card-body">
      <div class="card-name"><?= htmlspecialchars($row['name']) ?></div>
      <div class="price-row">
        <div class="price-pill">
          <span class="size-label">Small</span>
          ₱<?= number_format($small, 2) ?>
        </div>
        <div class="price-pill">
          <span class="size-label">Large</span>
          ₱<?= number_format($large, 2) ?>
        </div>
      </div>
      <?php if ($isUser): ?>
        <span class="discount-tag">✓ 10% Member Discount</span>
      <?php endif; ?>
    </div>
    <div class="card-footer">
      <button class="btn-buy" onclick="openPopup('<?= addslashes($row['name']) ?>', 'two', <?= number_format($small, 2, '.', '') ?>, <?= number_format($large, 2, '.', '') ?>)">Buy Now</button>
    </div>
  </div>
  <?php endwhile; ?>
  </div>
</section>

<!-- HOT DRINKS -->
<section class="menu-section" id="hotdrinks">
  <div class="section-header">
    <span class="section-tag">Warm &amp; Cozy</span>
    <h2>Hot Drinks</h2>
  </div>
  <div class="product-grid">
  <?php
  $result = $conn->query("SELECT * FROM coffee_items WHERE category='hot_drinks' AND is_available=1 AND deleted_at IS NULL");
  while ($row = $result->fetch_assoc()):
    $price = $row['price_single'];
    if ($isUser) { $price -= $price * $discount; }
  ?>
  <div class="product-card">
    <div class="card-img">
      <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" loading="lazy">
    </div>
    <div class="card-body">
      <div class="card-name"><?= htmlspecialchars($row['name']) ?></div>
      <div class="price-row">
        <div class="price-pill">
          <span class="size-label">Single</span>
          ₱<?= number_format($price, 2) ?>
        </div>
      </div>
      <?php if ($isUser): ?>
        <span class="discount-tag">✓ 10% Member Discount</span>
      <?php endif; ?>
    </div>
    <div class="card-footer">
      <button class="btn-buy" onclick="openPopup('<?= addslashes($row['name']) ?>', 'single', <?= number_format($price, 2, '.', '') ?>, 0)">Buy Now</button>
    </div>
  </div>
  <?php endwhile; ?>
  </div>
</section>

</main>

<footer class="site-footer">
  <p>&copy; 2025 SYD Coffee</p>
</footer>

<script>
  // Hamburger
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

  // Category nav active state on scroll
  const sections = ['coldcoffee','noncoffee','hotdrinks'];
  const catLinks = document.querySelectorAll('.cat-nav a');
  window.addEventListener('scroll', function() {
    let current = '';
    sections.forEach(function(id) {
      const el = document.getElementById(id);
      if (el && window.scrollY >= el.offsetTop - 160) current = id;
    });
    catLinks.forEach(function(a) {
      a.classList.toggle('active', a.getAttribute('href') === '#' + current);
    });
  }, { passive: true });

  // Cart logic
  function loadCart() {
    try {
      const raw = JSON.parse(localStorage.getItem('sydCart') || '[]');
      return raw.filter(i => i && i.name && i.size && !isNaN(parseFloat(i.price)) && parseFloat(i.price) > 0 && parseInt(i.qty) > 0)
                .map(i => ({ ...i, price: parseFloat(i.price), qty: parseInt(i.qty) }));
    } catch(e) { return []; }
  }

  let cart = loadCart();
  function saveCart() { localStorage.setItem('sydCart', JSON.stringify(cart)); }

  let currentItem = {};

  function openPopup(name, type, price1, price2) {
    const p1 = parseFloat(price1), p2 = parseFloat(price2);
    currentItem = { name, type, price1: p1, price2: p2, selectedSize: null, selectedPrice: null };
    document.getElementById('popupItemName').textContent = name;
    const opts = document.getElementById('sizeOptions');
    opts.innerHTML = '';
    if (type === 'single') {
      currentItem.selectedSize = 'Single';
      currentItem.selectedPrice = p1;
      const btn = document.createElement('button');
      btn.className = 'size-btn selected';
      btn.textContent = 'Single — ₱' + p1.toFixed(2);
      opts.appendChild(btn);
    } else {
      [['Small', p1], ['Large', p2]].forEach(function([label, price]) {
        const btn = document.createElement('button');
        btn.className = 'size-btn';
        btn.textContent = label + ' — ₱' + price.toFixed(2);
        btn.onclick = function() {
          document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('selected'));
          btn.classList.add('selected');
          currentItem.selectedSize = label;
          currentItem.selectedPrice = price;
        };
        opts.appendChild(btn);
      });
    }
    document.getElementById('sizePopup').classList.add('active');
  }

  function closePopup() {
    document.getElementById('sizePopup').classList.remove('active');
    currentItem = {};
  }

  function confirmAction(action) {
    if (!currentItem.selectedSize || currentItem.selectedPrice === null || isNaN(currentItem.selectedPrice)) {
      alert('Please select a size first!'); return;
    }
    const { name, selectedSize: size, selectedPrice: price } = currentItem;
    closePopup();
    addToCart(name, size, parseFloat(price));
    if (action === 'cart') openCart();
    else goToCheckout();
  }

  function addToCart(name, size, price) {
    price = parseFloat(price);
    if (!name || !size || isNaN(price) || price <= 0) return;
    const existing = cart.find(i => i.name === name && i.size === size);
    if (existing) existing.qty++;
    else cart.push({ name, size, price, qty: 1 });
    saveCart(); renderCart();
  }

  function removeFromCart(i) { cart.splice(i,1); saveCart(); renderCart(); }

  function changeQty(i, d) {
    cart[i].qty += d;
    if (cart[i].qty <= 0) cart.splice(i,1);
    saveCart(); renderCart();
  }

  function renderCart() {
    const container = document.getElementById('cartItems');
    const countEl   = document.getElementById('cartCount');
    const totalEl   = document.getElementById('cartTotal');
    let total = 0, count = 0;
    if (!cart.length) {
      container.innerHTML = '<div class="cart-empty">Your cart is empty.<br>Add something delicious!</div>';
    } else {
      container.innerHTML = cart.map((item, i) => {
        const sub = item.price * item.qty;
        total += sub; count += item.qty;
        return `<div class="cart-item">
          <div class="cart-item-info">
            <div class="cart-item-name">${item.name}</div>
            <div class="cart-item-size">${item.size}</div>
            <div class="cart-qty">
              <button onclick="changeQty(${i},-1)">−</button>
              <span>${item.qty}</span>
              <button onclick="changeQty(${i},1)">+</button>
            </div>
          </div>
          <span class="cart-item-price">₱${sub.toFixed(2)}</span>
          <button class="cart-item-remove" onclick="removeFromCart(${i})">✕</button>
        </div>`;
      }).join('');
    }
    countEl.textContent = count;
    totalEl.textContent = '₱' + total.toFixed(2);
  }

  function openCart() {
    document.getElementById('cartSidebar').classList.add('open');
    document.getElementById('cartOverlay').classList.add('active');
  }
  function closeCart() {
    document.getElementById('cartSidebar').classList.remove('open');
    document.getElementById('cartOverlay').classList.remove('active');
  }
  function goToCheckout() {
    if (!cart.length) { alert('Your cart is empty!'); return; }
    saveCart();
    window.location.href = 'checkout.php';
  }

  renderCart();
</script>
</body>
</html>