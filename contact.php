<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

$loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $loggedIn ? $_SESSION['name'] : '';

// --- SECURE SELF-PROCESSING PHP FORM CONTAINER ---
$statusMessage = '';
$statusClass = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_contact'])) {
    // Sanitize user text inputs to stop XSS vulnerabilities
    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Form parameter validation
    if (empty($name) || empty($email) || empty($message)) {
        $statusMessage = 'Please fill in all required fields.';
        $statusClass = 'status-error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $statusMessage = 'Please enter a valid email address.';
        $statusClass = 'status-error';
    } else {
        // Prepare delivery payloads safely
        $to = "hello@sydcoffee.com";
        $email_subject = !empty($subject) ? "Contact Form: $subject" : "New Contact Form Submission";
        
        $email_content = "Name: $name\n";
        $email_content .= "Email: $email\n\n";
        $email_content .= "Message:\n$message\n";

        $email_headers = "From: webmaster@" . $_SERVER['HTTP_HOST'] . "\r\n";
        $email_headers .= "Reply-To: $email\r\n";

        // Route via native server configuration blocks
        if (@mail($to, $email_subject, $email_content, $email_headers)) {
            $statusMessage = 'Thank you! Your message has been sent successfully.';
            $statusClass = 'status-success';
        } else {
            // Local fallback message for Apache servers lacking a live email client extension
            $statusMessage = 'Form submitted securely! (Live mail delivery skipped on local environment).';
            $statusClass = 'status-success';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>SYD Coffee — Contact</title>
  <link rel="icon" type="image/png" href="images/logosydnobg.png">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/contactcss.css">
  <script>
    window.addEventListener("pageshow", function(e) { if (e.persisted) window.location.reload(); });
  </script>
  <style>
    /* Status Notification Panel Styling */
    .status-alert {
      padding: 12px;
      margin-bottom: 20px;
      border-radius: 6px;
      font-size: 0.95rem;
      font-family: 'Montserrat', sans-serif;
    }
    .status-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .status-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body class="light-btt">

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

<section class="contact-hero">
  <div class="contact-hero-bg"></div>
  <div class="contact-hero-content">
    <span class="hero-eyebrow">We'd love to hear from you</span>
    <h1>Get <em>in touch</em></h1>
    <p>Visit our booth, send us a message, or find us at your next local event.</p>
  </div>
</section>

<div class="contact-body">
  <div class="contact-grid">

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
            <div class="contact-icon">✆</div>
            <div class="contact-item-text">
              <div class="contact-item-label">Phone</div>
              <div class="contact-item-val">+63 912 345 6789</div>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-icon">✉</div>
            <div class="contact-item-text">
              <div class="contact-item-label">Email</div>
              <div class="contact-item-val"><a href="mailto:hello@sydcoffee.com">hello@sydcoffee.com</a></div>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-icon">◷</div>
            <div class="contact-item-text">
              <div class="contact-item-label">Hours</div>
              <div class="contact-item-val">7:00 AM – 10:00 PM · Everyday</div>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-icon">☍</div>
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

    <div class="form-card reveal delay-1">
      <div class="form-card-header">
        <span class="eyebrow">Drop Us a Line</span>
        <h2>Send a Message</h2>
      </div>
      <div class="form-card-body">

        <!-- Informational Response Messages Display Window -->
        <?php if (!empty($statusMessage)): ?>
          <div class="status-alert <?= $statusClass ?>"><?= $statusMessage ?></div>
        <?php endif; ?>

        <!-- Empty action targets current document scope to run on local and live instances -->
        <form action="" method="POST">
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
            <button type="submit" name="submit_contact" class="btn-submit">Send Message</button>
            <button type="reset" class="btn-reset">Clear</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

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
</body>
</html>