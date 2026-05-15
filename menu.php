<?php
  date_default_timezone_set('Asia/Manila');
  session_start();
  include "auth/config.php";

  header("Cache-Control: no-store, no-cache, must-revalidate, private");
  header("Pragma: no-cache");
  header("Expires: 0");

  $loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
  $userName = $loggedIn ? $_SESSION['name'] : '';
  $userRole = $_SESSION['role'] ?? null;

  $isUser  = ($loggedIn && $userRole === 'user');
  $isAdmin = ($loggedIn && $userRole === 'admin');

  $currentHour = (int)date('H');
  $isHappyHour = ($currentHour >= 6 && $currentHour < 10);

  $discount     = 0.10;
  $coldDiscount = ($isUser && $isHappyHour) ? 0.20 : ($isUser ? 0.10 : 0);
  ?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>SYD Coffee — Menu</title>
  <link rel="icon" type="image/png" href="images/logosydnobg.png">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/menucss.css">
  <script>
    window.addEventListener("pageshow", function(e) { if (e.persisted) window.location.reload(); });
  </script>
  </head>
  <body>

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
        <a href="admin/admin.php" class="btn-nav btn-nav-dash">Dashboard</a>
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
    <?php if ($isUser): ?>
      <span class="nav-user" style="color:var(--brown-mid)">Hi, <?= htmlspecialchars($userName) ?>!</span>
      <a href="logout.php">Logout</a>
    <?php elseif ($isAdmin): ?>
      <a href="admin/admin.php">Dashboard</a>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
    <?php endif; ?>
  </div>

  <?php if ($isUser && $isHappyHour): ?>
  <div class="happy-banner">Happy Hour! 20% off all Cold Drinks until 5:00 PM!</div>
  <?php endif; ?>

  <?php if ($isAdmin): ?>
  <div class="admin-menu-banner">
    <span class="admin-menu-banner-icon">&#9888;</span>
    You are viewing the menu as an admin — ordering is disabled. Go to the <a href="admin/admin.php">Dashboard</a> to manage items.
  </div>
  <?php endif; ?>

  <div class="menu-hero" <?php if (!$isUser && !$isHappyHour): ?>style="margin-top:var(--nav-h)"<?php endif; ?>>
    <div class="menu-hero-bg"></div>
    <div class="menu-hero-content">
      <span class="menu-hero-eyebrow">Handcrafted Drinks</span>
      <h1>Our <em>Menu</em></h1>
      <p>Every drink made to order — pick your size, pick your flavour, and let us do the rest.</p>
    </div>
  </div>

  <nav class="cat-nav">
    <a href="#coldcoffee">Cold Coffee</a>
    <a href="#noncoffee">Non-Coffee &amp; Soda</a>
    <a href="#hotdrinks">Hot Drinks</a>
  </nav>

  <?php if (!$isAdmin): ?>
  <div class="popup-overlay" id="sizePopup">
    <div class="popup-box">
      <span class="popup-icon">&#9749;</span>
      <h3 id="popupItemName"></h3>
      <p class="popup-sub">Choose your size</p>
      <div class="popup-divider"></div>
      <div class="size-options" id="sizeOptions"></div>
      <div class="qty-wrap">
        <span class="qty-label">Quantity</span>
        <div class="qty-controls">
          <button class="qty-btn" id="qtyMinus" onclick="changePopupQty(-1)">&#8722;</button>
          <div class="qty-display" id="qtyDisplay">1</div>
          <button class="qty-btn" id="qtyPlus" onclick="changePopupQty(1)">+</button>
        </div>
      </div>
      <div style="height:20px;"></div>
      <button class="btn-order-now" onclick="confirmAction('order')">Order Now</button>
      <button class="btn-add-cart" onclick="confirmAction('cart')">Add to Cart</button>
      <button class="btn-cancel-popup" onclick="closePopup()">Cancel</button>
    </div>
  </div>

  <div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>
  <div class="cart-sidebar" id="cartSidebar">
    <div class="cart-header">
      <span class="cart-header-title">Your Cart</span>
      <span class="cart-header-count" id="cartHeaderCount">0 items</span>
      <button class="cart-close" onclick="closeCart()">&#10005;</button>
    </div>
    <div class="cart-items" id="cartItems"></div>
    <div class="cart-footer" id="cartFooter" style="display:none;">
      <div class="cart-summary">
        <div class="cart-summary-row"><span>Subtotal</span><span id="cartSubtotal">&#8369;0.00</span></div>
        <div class="cart-summary-row"><span>Items</span><span id="cartItemCount">0</span></div>
        <div class="cart-summary-row total"><span>Total</span><span id="cartTotal">&#8369;0.00</span></div>
      </div>
      <div class="cart-actions">
        <button class="btn-checkout" onclick="goToCheckout()">Proceed to Checkout &#8594;</button>
        <button class="btn-clear-cart" onclick="clearCart()">Clear Cart</button>
      </div>
    </div>
  </div>

  <button class="cart-toggle" onclick="openCart()" id="cartToggleBtn">
    Cart <span class="cart-count" id="cartCount">0</span>
  </button>
  <?php endif; ?>

  <main>

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
          <div class="price-pill"><span class="size-label">Small</span>&#8369;<?= number_format($small, 2) ?></div>
          <div class="price-pill"><span class="size-label">Large</span>&#8369;<?= number_format($large, 2) ?></div>
        </div>
        <?php if ($isUser && $isHappyHour): ?>
          <span class="discount-tag">20% OFF — Happy Hour</span>
        <?php elseif ($isUser): ?>
          <span class="discount-tag">10% Member Discount</span>
        <?php endif; ?>
      </div>
      <div class="card-footer">
        <?php if ($isAdmin): ?>
          <span class="admin-view-badge">Admin View Only</span>
        <?php else: ?>
          <button class="btn-buy" onclick="openPopup('<?= addslashes($row['name']) ?>', 'two', <?= number_format($small, 2, '.', '') ?>, <?= number_format($large, 2, '.', '') ?>)">Buy Now</button>
        <?php endif; ?>
      </div>
    </div>
    <?php endwhile; ?>
    </div>
  </section>

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
          <div class="price-pill"><span class="size-label">Small</span>&#8369;<?= number_format($small, 2) ?></div>
          <div class="price-pill"><span class="size-label">Large</span>&#8369;<?= number_format($large, 2) ?></div>
        </div>
        <?php if ($isUser): ?>
          <span class="discount-tag">10% Member Discount</span>
        <?php endif; ?>
      </div>
      <div class="card-footer">
        <?php if ($isAdmin): ?>
          <span class="admin-view-badge">Admin View Only</span>
        <?php else: ?>
          <button class="btn-buy" onclick="openPopup('<?= addslashes($row['name']) ?>', 'two', <?= number_format($small, 2, '.', '') ?>, <?= number_format($large, 2, '.', '') ?>)">Buy Now</button>
        <?php endif; ?>
      </div>
    </div>
    <?php endwhile; ?>
    </div>
  </section>

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
          <div class="price-pill"><span class="size-label">Single</span>&#8369;<?= number_format($price, 2) ?></div>
        </div>
        <?php if ($isUser): ?>
          <span class="discount-tag">10% Member Discount</span>
        <?php endif; ?>
      </div>
      <div class="card-footer">
        <?php if ($isAdmin): ?>
          <span class="admin-view-badge">Admin View Only</span>
        <?php else: ?>
          <button class="btn-buy" onclick="openPopup('<?= addslashes($row['name']) ?>', 'single', <?= number_format($price, 2, '.', '') ?>, 0)">Buy Now</button>
        <?php endif; ?>
      </div>
    </div>
    <?php endwhile; ?>
    </div>
  </section>

  </main>

  <footer class="site-footer">
    <p>&copy; 2026 SYD Coffee</p>
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

    <?php if (!$isAdmin): ?>
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
    let popupQty = 1;

    function openPopup(name, type, price1, price2) {
      const p1 = parseFloat(price1), p2 = parseFloat(price2);
      popupQty = 1;
      currentItem = { name, type, price1: p1, price2: p2, selectedSize: null, selectedPrice: null };
      document.getElementById('popupItemName').textContent = name;
      document.getElementById('qtyDisplay').textContent = '1';
      const opts = document.getElementById('sizeOptions');
      opts.innerHTML = '';
      if (type === 'single') {
        currentItem.selectedSize  = 'Single';
        currentItem.selectedPrice = p1;
        const btn = document.createElement('button');
        btn.className = 'size-btn selected';
        btn.textContent = 'Single — \u20B1' + p1.toFixed(2);
        opts.appendChild(btn);
      } else {
        [['Small', p1], ['Large', p2]].forEach(function([label, price]) {
          const btn = document.createElement('button');
          btn.className = 'size-btn';
          btn.textContent = label + ' — \u20B1' + price.toFixed(2);
          btn.onclick = function() {
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('selected'));
            btn.classList.add('selected');
            currentItem.selectedSize  = label;
            currentItem.selectedPrice = price;
          };
          opts.appendChild(btn);
        });
      }
      document.getElementById('sizePopup').classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function changePopupQty(delta) {
      popupQty = Math.max(1, Math.min(20, popupQty + delta));
      document.getElementById('qtyDisplay').textContent = popupQty;
    }

    function closePopup() {
      document.getElementById('sizePopup').classList.remove('active');
      document.body.style.overflow = '';
      currentItem = {};
      popupQty = 1;
    }

    function confirmAction(action) {
      if (!currentItem.selectedSize || currentItem.selectedPrice === null || isNaN(currentItem.selectedPrice)) {
        alert('Please select a size first!'); return;
      }
      const { name, selectedSize: size, selectedPrice: price } = currentItem;
      const qty = popupQty;
      closePopup();
      addToCart(name, size, parseFloat(price), qty);
      if (action === 'cart') openCart();
      else goToCheckout();
    }

    function addToCart(name, size, price, qty) {
      qty   = parseInt(qty) || 1;
      price = parseFloat(price);
      if (!name || !size || isNaN(price) || price <= 0) return;
      const existing = cart.find(i => i.name === name && i.size === size);
      if (existing) existing.qty += qty;
      else cart.push({ name, size, price, qty });
      saveCart();
      renderCart();
      bumpToggle();
    }

    function removeFromCart(idx) {
      cart.splice(idx, 1);
      saveCart(); renderCart();
    }

    function changeQty(idx, delta) {
      cart[idx].qty += delta;
      if (cart[idx].qty <= 0) cart.splice(idx, 1);
      saveCart(); renderCart();
    }

    function clearCart() {
      if (!cart.length) return;
      if (!confirm('Remove all items from your cart?')) return;
      cart = [];
      saveCart(); renderCart();
    }

    function bumpToggle() {
      const btn = document.getElementById('cartToggleBtn');
      btn.classList.remove('bump');
      void btn.offsetWidth;
      btn.classList.add('bump');
    }

    function renderCart() {
      const container   = document.getElementById('cartItems');
      const countEl     = document.getElementById('cartCount');
      const headerCount = document.getElementById('cartHeaderCount');
      const footer      = document.getElementById('cartFooter');
      const subtotalEl  = document.getElementById('cartSubtotal');
      const totalEl     = document.getElementById('cartTotal');
      const itemCntEl   = document.getElementById('cartItemCount');

      let total = 0, count = 0;
      cart.forEach(i => { total += i.price * i.qty; count += i.qty; });

      countEl.textContent     = count;
      headerCount.textContent = count === 1 ? '1 item' : count + ' items';

      if (!cart.length) {
        footer.style.display = 'none';
        container.innerHTML = `
          <div class="cart-empty">
            <h4>Your cart is empty</h4>
            <p>Looks like you haven't added anything yet. Browse the menu and find something you love!</p>
            <button class="btn-browse" onclick="closeCart()">Browse Menu</button>
          </div>`;
      } else {
        footer.style.display = 'block';
        subtotalEl.textContent = '\u20B1' + total.toFixed(2);
        totalEl.textContent    = '\u20B1' + total.toFixed(2);
        itemCntEl.textContent  = count + (count === 1 ? ' item' : ' items');
        container.innerHTML = cart.map((item, i) => `
          <div class="cart-item">
            <div class="cart-item-details">
              <div class="cart-item-name">${item.name}</div>
              <div class="cart-item-meta">
                <span class="cart-item-size">${item.size}</span>
                <span class="cart-item-unit">\u20B1${item.price.toFixed(2)} each</span>
              </div>
              <div class="cart-item-qty">
                <button class="cqty-btn" onclick="changeQty(${i},-1)">&#8722;</button>
                <div class="cqty-display">${item.qty}</div>
                <button class="cqty-btn" onclick="changeQty(${i},1)">+</button>
              </div>
            </div>
            <div class="cart-item-right">
              <span class="cart-item-price">\u20B1${(item.price * item.qty).toFixed(2)}</span>
              <button class="cart-item-remove" onclick="removeFromCart(${i})" title="Remove">&#10005;</button>
            </div>
          </div>`).join('');
      }
    }

    function openCart() {
      document.getElementById('cartSidebar').classList.add('open');
      document.getElementById('cartOverlay').classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closeCart() {
      document.getElementById('cartSidebar').classList.remove('open');
      document.getElementById('cartOverlay').classList.remove('active');
      document.body.style.overflow = '';
    }

    function goToCheckout() {
      if (!cart.length) { alert('Your cart is empty!'); return; }
      saveCart();
      window.location.href = 'checkout.php';
    }

    renderCart();
    <?php endif; ?>
  </script>
  <?php include "includes/backtotop.php"; ?>
  </body>
  </html>