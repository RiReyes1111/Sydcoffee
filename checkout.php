<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

$loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $loggedIn ? $_SESSION['name'] : '';
$userRole = $_SESSION['role'] ?? null;
$isUser   = ($loggedIn && $userRole === 'user');

$currentHour = (int)date('H');
$isHappyHour = ($currentHour >= 15 && $currentHour < 17);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>SYD Coffee — Checkout</title>
<link rel="icon" type="image/png" href="images/logosydnobg.png">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/checkoutcss.css">
</head>
<body>

<div class="topbar">
  <a href="index.php"><img src="images/logosydnobg.png" alt="SYD Coffee" class="topbar-logo"></a>
  <div class="topbar-sep"></div>
  <span class="topbar-title">Checkout</span>
  <div class="topbar-steps">
    <div class="step-dot done"></div>
    <div class="step-dot active"></div>
    <div class="step-dot"></div>
  </div>
  <?php if ($isUser): ?>
    <span class="topbar-user"><?= htmlspecialchars($userName) ?></span>
  <?php endif; ?>
  <a href="menu.php" class="btn-back-nav">&#8592; Menu</a>
</div>

<div class="success-screen" id="successScreen">
  <div class="success-icon">&#10003;</div>
  <h2>Order Placed!</h2>
  <p>Thank you<?= $isUser ? ', <strong>' . htmlspecialchars($userName) . '</strong>' : '' ?>!<br>We'll have your order ready soon. You'll hear from us shortly.</p>
  <a href="menu.php" class="btn-back-menu">Back to Menu</a>
</div>

<div class="checkout-layout" id="checkoutForm">

  <div class="form-col">

    <div class="page-title">
      <span class="page-eyebrow">Almost there</span>
      <h1>Complete Your Order</h1>
    </div>

    <?php if ($isUser): ?>
    <div class="member-banner">
      <div>
        <div class="banner-title">Member discount applied, <?= htmlspecialchars($userName) ?>!</div>
        <div class="banner-body">
          <?php if ($isHappyHour): ?>
            You're saving <strong>10% off</strong> all items + <strong>20% off</strong> Cold Drinks (Happy Hour until 5:00 PM). Prices already reflect your discount.
          <?php else: ?>
            You're saving <strong>10% off</strong> all items. Prices already reflect your discount.
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="guest-banner" id="guestBanner">
      <div class="guest-banner-text">
        <div class="banner-title">Unlock 10% off your order</div>
        <div class="banner-body">Create a free account to access member discounts.</div>
      </div>
      <div class="guest-banner-actions">
        <a href="login.php?tab=register&redirect=checkout" class="btn-signup-sm">Sign Up Free</a>
        <button class="btn-dismiss" onclick="document.getElementById('guestBanner').style.display='none'">No thanks</button>
      </div>
    </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-head">
        <div class="card-head-icon">&#9679;</div>
        <div>
          <h3>Fulfillment</h3>
          <div class="card-sub">How would you like to receive your order?</div>
        </div>
      </div>
      <div class="toggle-group">
        <button class="toggle-btn active" id="btnPickup" onclick="setFulfillment('pickup')">Pick Up</button>
        <button class="toggle-btn" id="btnDelivery" onclick="setFulfillment('delivery')">Delivery</button>
      </div>
      <div id="pickupInfo" class="show">
        <div class="address-box">
          <strong>SYD Coffee</strong>
          Greenrose Subdivision, Rodriguez, Rizal 1860, Philippines
        </div>
        <iframe class="map-frame"
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3864.12!2d121.13!3d14.74!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b9b1234abcd%3A0x0!2sGreenrose+Subdivision%2C+Rodriguez%2C+Rizal!5e0!3m2!1sen!2sph!4v1715000000000!5m2!1sen!2sph"
          allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
      <div id="deliveryInfo">
        <div class="field-group">
          <label class="field-label">Delivery Address</label>
          <input type="text" id="deliveryAddress" placeholder="Enter your full delivery address">
        </div>
        <div class="field-group">
          <label class="field-label">Contact Number</label>
          <input type="tel" id="contactNumber" placeholder="e.g. 09XX XXX XXXX">
        </div>
        <div class="field-group">
          <label class="field-label">Special Instructions <span style="font-weight:400;letter-spacing:0;text-transform:none;">(optional)</span></label>
          <textarea id="specialNotes" placeholder="Allergies, landmark, etc."></textarea>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-head">
        <div class="card-head-icon">&#9679;</div>
        <div>
          <h3>Payment Method</h3>
          <div class="card-sub">Choose how you'd like to pay</div>
        </div>
      </div>
      <div class="payment-tabs">
        <button class="pay-tab active" id="btnGcash" onclick="setPayment('gcash')">
          <span class="pay-tab-icon">G</span>GCash
        </button>
        <button class="pay-tab" id="btnVisa" onclick="setPayment('visa')">
          <span class="pay-tab-icon">V</span>Visa / Card
        </button>
        <button class="pay-tab" id="btnCash" onclick="setPayment('cash')">
          <span class="pay-tab-icon">$</span>Cash
        </button>
      </div>

      <div id="gcashInfo" class="show">
        <div class="pay-panel gcash">
          <span class="pay-panel-label">Send payment to</span>
          <div class="gcash-number">0962 071 4739</div>
          <div class="gcash-name">Account Name: <strong>RI***Y T.</strong></div>
          <img src="images/gcash qr.png" alt="GCash QR" class="gcash-qr">
          <p class="pay-note">Please send a screenshot of your payment receipt when placing your order.</p>
        </div>
      </div>

      <div id="visaInfo">
        <div class="pay-panel" style="text-align:left; padding:0; background:transparent; border:none;">
          <div class="card-preview" id="cardPreview">
            <div class="card-chip"></div>
            <div class="card-number-display" id="previewNumber">&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;</div>
            <div class="card-bottom">
              <div>
                <div class="card-label-sm">Card Holder</div>
                <div class="card-value-sm" id="previewName">YOUR NAME</div>
              </div>
              <div>
                <div class="card-label-sm">Expires</div>
                <div class="card-value-sm" id="previewExpiry">MM / YY</div>
              </div>
              <div class="card-brand" id="previewBrand">VISA</div>
            </div>
          </div>
          <div class="card-fields">
            <div class="field-group">
              <label class="field-label">Card Number</label>
              <div class="card-input-wrap">
                <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456"
                  maxlength="19" inputmode="numeric"
                  oninput="formatCardNumber(this)" autocomplete="cc-number">
              </div>
            </div>
            <div class="field-group">
              <label class="field-label">Cardholder Name</label>
              <div class="card-input-wrap">
                <input type="text" id="cardName" placeholder="Name as it appears on card"
                  maxlength="26" oninput="updatePreviewName(this.value)" autocomplete="cc-name">
              </div>
            </div>
            <div class="card-field-row">
              <div class="field-group">
                <label class="field-label">Expiry Date</label>
                <div class="card-input-wrap">
                  <input type="text" id="cardExpiry" placeholder="MM / YY"
                    maxlength="7" inputmode="numeric"
                    oninput="formatExpiry(this)" autocomplete="cc-exp">
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">CVV</label>
                <div class="card-input-wrap">
                  <input type="text" id="cardCvv" placeholder="&bull;&bull;&bull;"
                    maxlength="4" inputmode="numeric"
                    oninput="this.value=this.value.replace(/\D/g,'')"
                    onfocus="flipCard(true)" onblur="flipCard(false)"
                    autocomplete="cc-csc">
                </div>
              </div>
            </div>
            <p class="pay-note" style="margin-top:4px;">
              Card details are used for payment processing only. Your information is secure.
            </p>
          </div>
        </div>
      </div>

      <div id="cashInfo">
        <div class="pay-panel cash">
          <span class="pay-panel-label">Cash on Hand</span>
          <div class="cash-body">
            <p>Pay with cash when you pick up or when we deliver.</p>
            <p>Please prepare the exact amount if possible.</p>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="order-panel">
    <div class="card">
      <div class="card-head">
        <div class="card-head-icon">&#9679;</div>
        <div>
          <h3>Order summary</h3>
          <div class="card-sub" id="summaryItemCount">—</div>
        </div>
      </div>
      <div class="order-summary-items" id="summaryItems"></div>
      <div class="order-totals">
        <div class="total-row"><span>Subtotal</span><span id="subtotalDisplay">&#8369;0.00</span></div>

        <!-- FIXED: always render discount row, JS controls visibility -->
        <div class="total-row discount" id="discountRow" style="display:none;">
          <span id="discountLabel">Member Discount (10%)</span>
          <span id="discountDisplay"></span>
        </div>

        <div class="total-row delivery" id="deliveryFeeRow" style="display:none;"><span>Delivery Fee</span><span>&#8369;40.00</span></div>
        <div class="total-row grand"><span>Total</span><span id="grandTotalDisplay">&#8369;0.00</span></div>
      </div>
      <button class="btn-place-order" id="placeOrderBtn" onclick="placeOrder()">Place Order &rarr;</button>
      <div class="error-msg" id="errorMsg"></div>
      <p class="order-guarantee">Secure checkout &nbsp;&middot;&nbsp; No hidden fees</p>
    </div>
  </div>

</div>

<script>
// These are set from PHP session at page load
const IS_MEMBER     = <?= $isUser ? 'true' : 'false' ?>;
const IS_HAPPY_HOUR = <?= ($isUser && $isHappyHour) ? 'true' : 'false' ?>;
const PLACE_ORDER_URL = '<?= rtrim(dirname($_SERVER['PHP_SELF']), '/') . '/place_order.php' ?>';

function loadCart() {
  try {
    const raw = JSON.parse(localStorage.getItem('sydCart') || '[]');
    return raw.filter(i => i && typeof i.name === 'string' && i.name.trim()
      && typeof i.size === 'string' && i.size.trim()
      && !isNaN(parseFloat(i.price)) && parseFloat(i.price) > 0 && i.qty > 0)
      .map(i => ({ ...i, price: parseFloat(i.price), qty: parseInt(i.qty) }));
  } catch(e) { return []; }
}

const cart = loadCart();
let fulfillment = 'pickup';
let payment = 'gcash';

function showError(msg) {
  const el = document.getElementById('errorMsg');
  el.textContent = msg;
  el.classList.add('show');
}

function clearError() {
  const el = document.getElementById('errorMsg');
  el.textContent = '';
  el.classList.remove('show');
}

function renderSummary() {
  const container = document.getElementById('summaryItems');
  const countEl   = document.getElementById('summaryItemCount');
  if (!cart.length) {
    container.innerHTML = '<p style="color:var(--text-muted);font-size:13px;padding:10px 0;">Your cart is empty. <a href="menu.php" style="color:var(--gold);font-weight:700;">Go back to menu</a></p>';
    updateTotals(0);
    countEl.textContent = 'No items';
    document.getElementById('placeOrderBtn').disabled = true;
    return;
  }
  let subtotal = 0, totalQty = 0;
  container.innerHTML = cart.map(item => {
    const sub = item.price * item.qty;
    subtotal += sub; totalQty += item.qty;
    return `<div class="order-item">
      <div class="order-item-left">
        <div class="order-item-name">${item.name}</div>
        <div class="order-item-meta">
          <span class="order-item-badge">${item.size}</span>
          <span class="order-item-qty">&times; ${item.qty}</span>
        </div>
      </div>
      <div class="order-item-price">\u20B1${sub.toFixed(2)}</div>
    </div>`;
  }).join('');
  countEl.textContent = totalQty + (totalQty === 1 ? ' item' : ' items');
  updateTotals(subtotal);
}

// FIXED: discount row is always in the DOM now,
// JS shows/hides it and applies the correct amount based on IS_MEMBER
function updateTotals(subtotal) {
  const deliveryFee  = fulfillment === 'delivery' ? 40 : 0;
  const discountRow  = document.getElementById('discountRow');

  if (IS_MEMBER) {
    // Cart prices are already discounted, so back-calculate the savings for display
    const rate    = IS_HAPPY_HOUR ? 0.20 : 0.10;
    const savings = subtotal * (rate / (1 - rate));
    const label   = IS_HAPPY_HOUR ? 'Happy Hour (up to 20%)' : 'Member Discount (10%)';
    document.getElementById('discountLabel').textContent   = label;
    document.getElementById('discountDisplay').textContent = '-\u20B1' + savings.toFixed(2) + ' saved';
    discountRow.style.display = 'flex';
  } else {
    discountRow.style.display = 'none';
  }

  const grand = subtotal + deliveryFee;
  document.getElementById('subtotalDisplay').textContent   = '\u20B1' + subtotal.toFixed(2);
  document.getElementById('grandTotalDisplay').textContent = '\u20B1' + grand.toFixed(2);
}

function recalcTotals() {
  const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
  updateTotals(subtotal);
}

function setFulfillment(type) {
  fulfillment = type;
  document.getElementById('pickupInfo').classList.toggle('show', type === 'pickup');
  document.getElementById('deliveryInfo').classList.toggle('show', type === 'delivery');
  document.getElementById('btnPickup').classList.toggle('active', type === 'pickup');
  document.getElementById('btnDelivery').classList.toggle('active', type === 'delivery');
  document.getElementById('deliveryFeeRow').style.display = type === 'delivery' ? 'flex' : 'none';
  recalcTotals();
}

function setPayment(type) {
  payment = type;
  ['gcash','visa','cash'].forEach(t => {
    document.getElementById(t + 'Info').classList.toggle('show', t === type);
    const key = 'btn' + t.charAt(0).toUpperCase() + t.slice(1);
    document.getElementById(key).classList.toggle('active', t === type);
  });
}

function placeOrder() {
  clearError();
  if (!cart.length) { showError('Your cart is empty.'); return; }

  let deliveryAddress = '', contactNumber = '', specialNotes = '';
  if (fulfillment === 'delivery') {
    deliveryAddress = document.getElementById('deliveryAddress').value.trim();
    contactNumber   = document.getElementById('contactNumber').value.trim();
    specialNotes    = document.getElementById('specialNotes').value.trim();
    if (!deliveryAddress) { showError('Please enter your delivery address.'); return; }
    if (!contactNumber)   { showError('Please enter your contact number.');   return; }
  }

  const subtotal    = cart.reduce((s, i) => s + i.price * i.qty, 0);
  const deliveryFee = fulfillment === 'delivery' ? 40 : 0;
  const total       = subtotal + deliveryFee;

  // FIXED: include discount info in the payload so place_order.php
  // also records the correct discounted total server-side
  let discountRate   = 0;
  let discountAmount = 0;
  if (IS_MEMBER) {
    discountRate   = IS_HAPPY_HOUR ? 0.20 : 0.10;
    discountAmount = parseFloat((subtotal * (discountRate / (1 - discountRate))).toFixed(2));
  }

  const payload = {
    items: cart,
    subtotal: parseFloat(subtotal.toFixed(2)),
    discount_rate: discountRate,
    discount_amount: discountAmount,
    delivery_fee: deliveryFee,
    total: parseFloat(total.toFixed(2)),
    fulfillment,
    payment_method: payment,
    delivery_address: deliveryAddress,
    contact_number: contactNumber,
    special_notes: specialNotes
  };

  const btn = document.getElementById('placeOrderBtn');
  btn.disabled = true;
  btn.textContent = 'Placing Order...';

  fetch(PLACE_ORDER_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
  .then(r => {
    if (!r.ok) throw new Error('Server returned ' + r.status);
    return r.json();
  })
  .then(data => {
    if (data.success) {
      localStorage.removeItem('sydCart');
      document.getElementById('checkoutForm').style.display = 'none';
      document.getElementById('successScreen').classList.add('show');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
      showError('Something went wrong: ' + (data.message || 'Unknown error'));
      btn.disabled = false;
      btn.textContent = 'Place Order \u2192';
    }
  })
  .catch(err => {
    showError('Could not reach the server. Check your connection and try again. (' + err.message + ')');
    btn.disabled = false;
    btn.textContent = 'Place Order \u2192';
  });
}

renderSummary();

function formatCardNumber(input) {
  let val = input.value.replace(/\D/g, '').substring(0, 16);
  input.value = val.replace(/(.{4})/g, '$1 ').trim();
  const display = val.padEnd(16, '\u2022').replace(/(.{4})/g, '$1 ').trim();
  document.getElementById('previewNumber').textContent = display;
  const brand = document.getElementById('previewBrand');
  if      (/^4/.test(val))      brand.textContent = 'VISA';
  else if (/^5[1-5]/.test(val)) brand.textContent = 'MASTERCARD';
  else if (/^3[47]/.test(val))  brand.textContent = 'AMEX';
  else                           brand.textContent = 'VISA';
}

function updatePreviewName(val) {
  document.getElementById('previewName').textContent = val.trim().toUpperCase() || 'YOUR NAME';
}

function formatExpiry(input) {
  let val = input.value.replace(/\D/g, '').substring(0, 4);
  if (val.length >= 3) val = val.substring(0,2) + ' / ' + val.substring(2);
  input.value = val;
  document.getElementById('previewExpiry').textContent = val || 'MM / YY';
}

function flipCard(isCvv) {
  const preview = document.getElementById('cardPreview');
  if (preview) {
    preview.style.filter = isCvv ? 'brightness(0.75)' : 'brightness(1)';
    preview.style.transition = 'filter 0.3s ease';
  }
}
</script>
</body>
</html><?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

$loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $loggedIn ? $_SESSION['name'] : '';
$userRole = $_SESSION['role'] ?? null;
$isUser   = ($loggedIn && $userRole === 'user');

$currentHour = (int)date('H');
$isHappyHour = ($currentHour >= 15 && $currentHour < 17);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>SYD Coffee — Checkout</title>
<link rel="icon" type="image/png" href="images/logosydnobg.png">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/checkoutcss.css">
</head>
<body>

<div class="topbar">
  <a href="index.php"><img src="images/logosydnobg.png" alt="SYD Coffee" class="topbar-logo"></a>
  <div class="topbar-sep"></div>
  <span class="topbar-title">Checkout</span>
  <div class="topbar-steps">
    <div class="step-dot done"></div>
    <div class="step-dot active"></div>
    <div class="step-dot"></div>
  </div>
  <?php if ($isUser): ?>
    <span class="topbar-user"><?= htmlspecialchars($userName) ?></span>
  <?php endif; ?>
  <a href="menu.php" class="btn-back-nav">&#8592; Menu</a>
</div>

<div class="success-screen" id="successScreen">
  <div class="success-icon">&#10003;</div>
  <h2>Order Placed!</h2>
  <p>Thank you<?= $isUser ? ', <strong>' . htmlspecialchars($userName) . '</strong>' : '' ?>!<br>We'll have your order ready soon. You'll hear from us shortly.</p>
  <a href="menu.php" class="btn-back-menu">Back to Menu</a>
</div>

<div class="checkout-layout" id="checkoutForm">

  <div class="form-col">

    <div class="page-title">
      <span class="page-eyebrow">Almost there</span>
      <h1>Complete Your Order</h1>
    </div>

    <?php if ($isUser): ?>
    <div class="member-banner">
      <div>
        <div class="banner-title">Member discount applied, <?= htmlspecialchars($userName) ?>!</div>
        <div class="banner-body">
          <?php if ($isHappyHour): ?>
            You're saving <strong>10% off</strong> all items + <strong>20% off</strong> Cold Drinks (Happy Hour until 5:00 PM). Prices already reflect your discount.
          <?php else: ?>
            You're saving <strong>10% off</strong> all items. Prices already reflect your discount.
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div class="guest-banner" id="guestBanner">
      <div class="guest-banner-text">
        <div class="banner-title">Unlock 10% off your order</div>
        <div class="banner-body">Create a free account to access member discounts.</div>
      </div>
      <div class="guest-banner-actions">
        <a href="login.php?tab=register&redirect=checkout" class="btn-signup-sm">Sign Up Free</a>
        <button class="btn-dismiss" onclick="document.getElementById('guestBanner').style.display='none'">No thanks</button>
      </div>
    </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-head">
        <div class="card-head-icon">&#9679;</div>
        <div>
          <h3>Fulfillment</h3>
          <div class="card-sub">How would you like to receive your order?</div>
        </div>
      </div>
      <div class="toggle-group">
        <button class="toggle-btn active" id="btnPickup" onclick="setFulfillment('pickup')">Pick Up</button>
        <button class="toggle-btn" id="btnDelivery" onclick="setFulfillment('delivery')">Delivery</button>
      </div>
      <div id="pickupInfo" class="show">
        <div class="address-box">
          <strong>SYD Coffee</strong>
          Greenrose Subdivision, Rodriguez, Rizal 1860, Philippines
        </div>
        <iframe class="map-frame"
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3864.12!2d121.13!3d14.74!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b9b1234abcd%3A0x0!2sGreenrose+Subdivision%2C+Rodriguez%2C+Rizal!5e0!3m2!1sen!2sph!4v1715000000000!5m2!1sen!2sph"
          allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
      <div id="deliveryInfo">
        <div class="field-group">
          <label class="field-label">Delivery Address</label>
          <input type="text" id="deliveryAddress" placeholder="Enter your full delivery address">
        </div>
        <div class="field-group">
          <label class="field-label">Contact Number</label>
          <input type="tel" id="contactNumber" placeholder="e.g. 09XX XXX XXXX">
        </div>
        <div class="field-group">
          <label class="field-label">Special Instructions <span style="font-weight:400;letter-spacing:0;text-transform:none;">(optional)</span></label>
          <textarea id="specialNotes" placeholder="Allergies, landmark, etc."></textarea>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-head">
        <div class="card-head-icon">&#9679;</div>
        <div>
          <h3>Payment Method</h3>
          <div class="card-sub">Choose how you'd like to pay</div>
        </div>
      </div>
      <div class="payment-tabs">
        <button class="pay-tab active" id="btnGcash" onclick="setPayment('gcash')">
          <span class="pay-tab-icon">G</span>GCash
        </button>
        <button class="pay-tab" id="btnVisa" onclick="setPayment('visa')">
          <span class="pay-tab-icon">V</span>Visa / Card
        </button>
        <button class="pay-tab" id="btnCash" onclick="setPayment('cash')">
          <span class="pay-tab-icon">$</span>Cash
        </button>
      </div>

      <div id="gcashInfo" class="show">
        <div class="pay-panel gcash">
          <span class="pay-panel-label">Send payment to</span>
          <div class="gcash-number">0962 071 4739</div>
          <div class="gcash-name">Account Name: <strong>RI***Y T.</strong></div>
          <img src="images/gcash qr.png" alt="GCash QR" class="gcash-qr">
          <p class="pay-note">Please send a screenshot of your payment receipt when placing your order.</p>
        </div>
      </div>

      <div id="visaInfo">
        <div class="pay-panel" style="text-align:left; padding:0; background:transparent; border:none;">
          <div class="card-preview" id="cardPreview">
            <div class="card-chip"></div>
            <div class="card-number-display" id="previewNumber">&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;</div>
            <div class="card-bottom">
              <div>
                <div class="card-label-sm">Card Holder</div>
                <div class="card-value-sm" id="previewName">YOUR NAME</div>
              </div>
              <div>
                <div class="card-label-sm">Expires</div>
                <div class="card-value-sm" id="previewExpiry">MM / YY</div>
              </div>
              <div class="card-brand" id="previewBrand">VISA</div>
            </div>
          </div>
          <div class="card-fields">
            <div class="field-group">
              <label class="field-label">Card Number</label>
              <div class="card-input-wrap">
                <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456"
                  maxlength="19" inputmode="numeric"
                  oninput="formatCardNumber(this)" autocomplete="cc-number">
              </div>
            </div>
            <div class="field-group">
              <label class="field-label">Cardholder Name</label>
              <div class="card-input-wrap">
                <input type="text" id="cardName" placeholder="Name as it appears on card"
                  maxlength="26" oninput="updatePreviewName(this.value)" autocomplete="cc-name">
              </div>
            </div>
            <div class="card-field-row">
              <div class="field-group">
                <label class="field-label">Expiry Date</label>
                <div class="card-input-wrap">
                  <input type="text" id="cardExpiry" placeholder="MM / YY"
                    maxlength="7" inputmode="numeric"
                    oninput="formatExpiry(this)" autocomplete="cc-exp">
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">CVV</label>
                <div class="card-input-wrap">
                  <input type="text" id="cardCvv" placeholder="&bull;&bull;&bull;"
                    maxlength="4" inputmode="numeric"
                    oninput="this.value=this.value.replace(/\D/g,'')"
                    onfocus="flipCard(true)" onblur="flipCard(false)"
                    autocomplete="cc-csc">
                </div>
              </div>
            </div>
            <p class="pay-note" style="margin-top:4px;">
              Card details are used for payment processing only. Your information is secure.
            </p>
          </div>
        </div>
      </div>

      <div id="cashInfo">
        <div class="pay-panel cash">
          <span class="pay-panel-label">Cash on Hand</span>
          <div class="cash-body">
            <p>Pay with cash when you pick up or when we deliver.</p>
            <p>Please prepare the exact amount if possible.</p>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="order-panel">
    <div class="card">
      <div class="card-head">
        <div class="card-head-icon">&#9679;</div>
        <div>
          <h3>Order summary</h3>
          <div class="card-sub" id="summaryItemCount">—</div>
        </div>
      </div>
      <div class="order-summary-items" id="summaryItems"></div>
      <div class="order-totals">
        <div class="total-row"><span>Subtotal</span><span id="subtotalDisplay">&#8369;0.00</span></div>

        <!-- FIXED: always render discount row, JS controls visibility -->
        <div class="total-row discount" id="discountRow" style="display:none;">
          <span id="discountLabel">Member Discount (10%)</span>
          <span id="discountDisplay"></span>
        </div>

        <div class="total-row delivery" id="deliveryFeeRow" style="display:none;"><span>Delivery Fee</span><span>&#8369;40.00</span></div>
        <div class="total-row grand"><span>Total</span><span id="grandTotalDisplay">&#8369;0.00</span></div>
      </div>
      <button class="btn-place-order" id="placeOrderBtn" onclick="placeOrder()">Place Order &rarr;</button>
      <div class="error-msg" id="errorMsg"></div>
      <p class="order-guarantee">Secure checkout &nbsp;&middot;&nbsp; No hidden fees</p>
    </div>
  </div>

</div>

<script>
// These are set from PHP session at page load
const IS_MEMBER     = <?= $isUser ? 'true' : 'false' ?>;
const IS_HAPPY_HOUR = <?= ($isUser && $isHappyHour) ? 'true' : 'false' ?>;
const PLACE_ORDER_URL = '<?= rtrim(dirname($_SERVER['PHP_SELF']), '/') . '/place_order.php' ?>';

function loadCart() {
  try {
    const raw = JSON.parse(localStorage.getItem('sydCart') || '[]');
    return raw.filter(i => i && typeof i.name === 'string' && i.name.trim()
      && typeof i.size === 'string' && i.size.trim()
      && !isNaN(parseFloat(i.price)) && parseFloat(i.price) > 0 && i.qty > 0)
      .map(i => ({ ...i, price: parseFloat(i.price), qty: parseInt(i.qty) }));
  } catch(e) { return []; }
}

const cart = loadCart();
let fulfillment = 'pickup';
let payment = 'gcash';

function showError(msg) {
  const el = document.getElementById('errorMsg');
  el.textContent = msg;
  el.classList.add('show');
}

function clearError() {
  const el = document.getElementById('errorMsg');
  el.textContent = '';
  el.classList.remove('show');
}

function renderSummary() {
  const container = document.getElementById('summaryItems');
  const countEl   = document.getElementById('summaryItemCount');
  if (!cart.length) {
    container.innerHTML = '<p style="color:var(--text-muted);font-size:13px;padding:10px 0;">Your cart is empty. <a href="menu.php" style="color:var(--gold);font-weight:700;">Go back to menu</a></p>';
    updateTotals(0);
    countEl.textContent = 'No items';
    document.getElementById('placeOrderBtn').disabled = true;
    return;
  }
  let subtotal = 0, totalQty = 0;
  container.innerHTML = cart.map(item => {
    const sub = item.price * item.qty;
    subtotal += sub; totalQty += item.qty;
    return `<div class="order-item">
      <div class="order-item-left">
        <div class="order-item-name">${item.name}</div>
        <div class="order-item-meta">
          <span class="order-item-badge">${item.size}</span>
          <span class="order-item-qty">&times; ${item.qty}</span>
        </div>
      </div>
      <div class="order-item-price">\u20B1${sub.toFixed(2)}</div>
    </div>`;
  }).join('');
  countEl.textContent = totalQty + (totalQty === 1 ? ' item' : ' items');
  updateTotals(subtotal);
}

// FIXED: discount row is always in the DOM now,
// JS shows/hides it and applies the correct amount based on IS_MEMBER
function updateTotals(subtotal) {
  const deliveryFee  = fulfillment === 'delivery' ? 40 : 0;
  const discountRow  = document.getElementById('discountRow');

  if (IS_MEMBER) {
    // Cart prices are already discounted, so back-calculate the savings for display
    const rate    = IS_HAPPY_HOUR ? 0.20 : 0.10;
    const savings = subtotal * (rate / (1 - rate));
    const label   = IS_HAPPY_HOUR ? 'Happy Hour (up to 20%)' : 'Member Discount (10%)';
    document.getElementById('discountLabel').textContent   = label;
    document.getElementById('discountDisplay').textContent = '-\u20B1' + savings.toFixed(2) + ' saved';
    discountRow.style.display = 'flex';
  } else {
    discountRow.style.display = 'none';
  }

  const grand = subtotal + deliveryFee;
  document.getElementById('subtotalDisplay').textContent   = '\u20B1' + subtotal.toFixed(2);
  document.getElementById('grandTotalDisplay').textContent = '\u20B1' + grand.toFixed(2);
}

function recalcTotals() {
  const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
  updateTotals(subtotal);
}

function setFulfillment(type) {
  fulfillment = type;
  document.getElementById('pickupInfo').classList.toggle('show', type === 'pickup');
  document.getElementById('deliveryInfo').classList.toggle('show', type === 'delivery');
  document.getElementById('btnPickup').classList.toggle('active', type === 'pickup');
  document.getElementById('btnDelivery').classList.toggle('active', type === 'delivery');
  document.getElementById('deliveryFeeRow').style.display = type === 'delivery' ? 'flex' : 'none';
  recalcTotals();
}

function setPayment(type) {
  payment = type;
  ['gcash','visa','cash'].forEach(t => {
    document.getElementById(t + 'Info').classList.toggle('show', t === type);
    const key = 'btn' + t.charAt(0).toUpperCase() + t.slice(1);
    document.getElementById(key).classList.toggle('active', t === type);
  });
}

function placeOrder() {
  clearError();
  if (!cart.length) { showError('Your cart is empty.'); return; }

  let deliveryAddress = '', contactNumber = '', specialNotes = '';
  if (fulfillment === 'delivery') {
    deliveryAddress = document.getElementById('deliveryAddress').value.trim();
    contactNumber   = document.getElementById('contactNumber').value.trim();
    specialNotes    = document.getElementById('specialNotes').value.trim();
    if (!deliveryAddress) { showError('Please enter your delivery address.'); return; }
    if (!contactNumber)   { showError('Please enter your contact number.');   return; }
  }

  const subtotal    = cart.reduce((s, i) => s + i.price * i.qty, 0);
  const deliveryFee = fulfillment === 'delivery' ? 40 : 0;
  const total       = subtotal + deliveryFee;

  // FIXED: include discount info in the payload so place_order.php
  // also records the correct discounted total server-side
  let discountRate   = 0;
  let discountAmount = 0;
  if (IS_MEMBER) {
    discountRate   = IS_HAPPY_HOUR ? 0.20 : 0.10;
    discountAmount = parseFloat((subtotal * (discountRate / (1 - discountRate))).toFixed(2));
  }

  const payload = {
    items: cart,
    subtotal: parseFloat(subtotal.toFixed(2)),
    discount_rate: discountRate,
    discount_amount: discountAmount,
    delivery_fee: deliveryFee,
    total: parseFloat(total.toFixed(2)),
    fulfillment,
    payment_method: payment,
    delivery_address: deliveryAddress,
    contact_number: contactNumber,
    special_notes: specialNotes
  };

  const btn = document.getElementById('placeOrderBtn');
  btn.disabled = true;
  btn.textContent = 'Placing Order...';

  fetch(PLACE_ORDER_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  })
  .then(r => {
    if (!r.ok) throw new Error('Server returned ' + r.status);
    return r.json();
  })
  .then(data => {
    if (data.success) {
      localStorage.removeItem('sydCart');
      document.getElementById('checkoutForm').style.display = 'none';
      document.getElementById('successScreen').classList.add('show');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
      showError('Something went wrong: ' + (data.message || 'Unknown error'));
      btn.disabled = false;
      btn.textContent = 'Place Order \u2192';
    }
  })
  .catch(err => {
    showError('Could not reach the server. Check your connection and try again. (' + err.message + ')');
    btn.disabled = false;
    btn.textContent = 'Place Order \u2192';
  });
}

renderSummary();

function formatCardNumber(input) {
  let val = input.value.replace(/\D/g, '').substring(0, 16);
  input.value = val.replace(/(.{4})/g, '$1 ').trim();
  const display = val.padEnd(16, '\u2022').replace(/(.{4})/g, '$1 ').trim();
  document.getElementById('previewNumber').textContent = display;
  const brand = document.getElementById('previewBrand');
  if      (/^4/.test(val))      brand.textContent = 'VISA';
  else if (/^5[1-5]/.test(val)) brand.textContent = 'MASTERCARD';
  else if (/^3[47]/.test(val))  brand.textContent = 'AMEX';
  else                           brand.textContent = 'VISA';
}

function updatePreviewName(val) {
  document.getElementById('previewName').textContent = val.trim().toUpperCase() || 'YOUR NAME';
}

function formatExpiry(input) {
  let val = input.value.replace(/\D/g, '').substring(0, 4);
  if (val.length >= 3) val = val.substring(0,2) + ' / ' + val.substring(2);
  input.value = val;
  document.getElementById('previewExpiry').textContent = val || 'MM / YY';
}

function flipCard(isCvv) {
  const preview = document.getElementById('cardPreview');
  if (preview) {
    preview.style.filter = isCvv ? 'brightness(0.75)' : 'brightness(1)';
    preview.style.transition = 'filter 0.3s ease';
  }
}
</script>
</body>
</html>