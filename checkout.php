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
          Col. S. Cruz St. San Rafael corner Greenrose Subd.
        </div>
        <iframe class="map-frame"
          src="https://www.google.com/maps?q=Col.%20S.%20Cruz%20St.%20San%20Rafael%20corner%20Greenrose%20Subd.%20(infront%20of%20ATF%20Builders%20Construction%20Supply)&output=embed"
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

  <!-- Desktop order panel (hidden on mobile via CSS) -->
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
        <?php if ($isUser): ?>
        <div class="total-row discount" id="discountRow">
          <span id="discountLabel">Member Discount (10%)</span>
          <span id="discountDisplay"></span>
        </div>
        <?php endif; ?>
        <div class="total-row delivery" id="deliveryFeeRow" style="display:none;"><span>Delivery Fee</span><span>&#8369;40.00</span></div>
        <div class="total-row grand"><span>Total</span><span id="grandTotalDisplay">&#8369;0.00</span></div>
      </div>
      <button class="btn-place-order" id="placeOrderBtn" onclick="placeOrder()">Place Order &rarr;</button>
      <div class="error-msg" id="errorMsg"></div>
      <p class="order-guarantee">Secure checkout &nbsp;&middot;&nbsp; No hidden fees</p>
    </div>
  </div>

</div>

<!-- Mobile: sticky pill trigger -->
<button class="order-drawer-pill" id="orderPill" aria-expanded="false" aria-controls="orderDrawer">
  <span>&#128722; Order Summary</span>
  <span class="pill-total" id="pillTotal">&#8369;0.00</span>
  <span class="pill-icon">&#8679;</span>
</button>

<!-- Mobile: backdrop -->
<div class="order-drawer-backdrop" id="orderBackdrop"></div>

<!-- Mobile: bottom drawer -->
<div class="order-drawer" id="orderDrawer" role="dialog" aria-label="Order Summary">
  <div class="drawer-handle-bar"></div>
  <div class="drawer-header">
    <span class="drawer-title">Order Summary</span>
    <button class="drawer-close" id="drawerClose" aria-label="Close">&#10005;</button>
  </div>
  <div class="order-summary-items" id="drawerItems"></div>
  <div class="order-totals">
    <div class="total-row"><span>Subtotal</span><span id="drawerSubtotal">&#8369;0.00</span></div>
    <?php if ($isUser): ?>
    <div class="total-row discount" id="drawerDiscountRow">
      <span id="drawerDiscountLabel">Member Discount (10%)</span>
      <span id="drawerDiscountDisplay"></span>
    </div>
    <?php endif; ?>
    <div class="total-row delivery" id="drawerDeliveryRow" style="display:none;"><span>Delivery Fee</span><span>&#8369;40.00</span></div>
    <div class="total-row grand"><span>Total</span><span id="drawerTotal">&#8369;0.00</span></div>
  </div>
  <button class="btn-place-order" onclick="placeOrder(); closeDrawer();">Place Order &rarr;</button>
  <p class="order-guarantee">Secure checkout &nbsp;&middot;&nbsp; No hidden fees</p>
</div>

<script>
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

function buildItemsHTML(cartData) {
  if (!cartData.length) {
    return '<p style="color:var(--text-muted);font-size:13px;padding:10px 0;">Your cart is empty. <a href="menu.php" style="color:var(--gold);font-weight:700;">Go back to menu</a></p>';
  }
  return cartData.map(item => {
    const sub = item.price * item.qty;
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
}

function renderSummary() {
  const itemsHTML  = buildItemsHTML(cart);
  const countEl    = document.getElementById('summaryItemCount');

  document.getElementById('summaryItems').innerHTML = itemsHTML;
  document.getElementById('drawerItems').innerHTML  = itemsHTML;

  if (!cart.length) {
    updateTotals(0);
    countEl.textContent = 'No items';
    document.getElementById('placeOrderBtn').disabled = true;
    return;
  }

  let subtotal = 0, totalQty = 0;
  cart.forEach(item => { subtotal += item.price * item.qty; totalQty += item.qty; });
  countEl.textContent = totalQty + (totalQty === 1 ? ' item' : ' items');
  updateTotals(subtotal);
}

function updateTotals(subtotal) {
  const deliveryFee    = fulfillment === 'delivery' ? 40 : 0;
  const discountRow    = document.getElementById('discountRow');
  const drawerDiscRow  = document.getElementById('drawerDiscountRow');
  let discountAmount   = 0;

  if (IS_MEMBER) {
    const rate         = IS_HAPPY_HOUR ? 0.20 : 0.10;
    discountAmount     = subtotal * rate;
    const label        = IS_HAPPY_HOUR ? 'Happy Hour (20%)' : 'Member Discount (10%)';
    const savings      = '-\u20B1' + discountAmount.toFixed(2) + ' saved';

    document.getElementById('discountLabel').textContent    = label;
    document.getElementById('discountDisplay').textContent  = savings;
    if (discountRow)   discountRow.style.display   = 'flex';

    if (document.getElementById('drawerDiscountLabel')) {
      document.getElementById('drawerDiscountLabel').textContent   = label;
      document.getElementById('drawerDiscountDisplay').textContent = savings;
    }
    if (drawerDiscRow) drawerDiscRow.style.display = 'flex';
  } else {
    if (discountRow)   discountRow.style.display   = 'none';
    if (drawerDiscRow) drawerDiscRow.style.display = 'none';
  }

  const grand = subtotal - discountAmount + deliveryFee;

  document.getElementById('subtotalDisplay').textContent    = '\u20B1' + subtotal.toFixed(2);
  document.getElementById('grandTotalDisplay').textContent  = '\u20B1' + grand.toFixed(2);
  document.getElementById('drawerSubtotal').textContent     = '\u20B1' + subtotal.toFixed(2);
  document.getElementById('drawerTotal').textContent        = '\u20B1' + grand.toFixed(2);

  // Update pill total
  const pillEl = document.getElementById('pillTotal');
  if (pillEl) pillEl.textContent = '\u20B1' + grand.toFixed(2);
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
  document.getElementById('deliveryFeeRow').style.display  = type === 'delivery' ? 'flex' : 'none';
  document.getElementById('drawerDeliveryRow').style.display = type === 'delivery' ? 'flex' : 'none';
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

  let discountRate   = 0;
  let discountAmount = 0;
  if (IS_MEMBER) {
    discountRate   = IS_HAPPY_HOUR ? 0.20 : 0.10;
    discountAmount = parseFloat((subtotal * discountRate).toFixed(2));
  }

  const total = parseFloat((subtotal - discountAmount + deliveryFee).toFixed(2));

  const payload = {
    items: cart,
    subtotal:         parseFloat(subtotal.toFixed(2)),
    discount_rate:    discountRate,
    discount_amount:  discountAmount,
    delivery_fee:     deliveryFee,
    total:            total,
    fulfillment,
    payment_method:   payment,
    delivery_address: deliveryAddress,
    contact_number:   contactNumber,
    special_notes:    specialNotes
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
      // Hide mobile drawer elements too
      document.getElementById('orderPill').style.display     = 'none';
      document.getElementById('orderDrawer').style.display   = 'none';
      document.getElementById('orderBackdrop').style.display = 'none';
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

// ── Mobile drawer logic ──
const pill     = document.getElementById('orderPill');
const drawer   = document.getElementById('orderDrawer');
const backdrop = document.getElementById('orderBackdrop');
const closeBtn = document.getElementById('drawerClose');

function openDrawer() {
  drawer.classList.add('open');
  backdrop.classList.add('show');
  pill.classList.add('open');
  pill.setAttribute('aria-expanded', 'true');
  document.body.style.overflow = 'hidden';
}

function closeDrawer() {
  drawer.classList.remove('open');
  backdrop.classList.remove('show');
  pill.classList.remove('open');
  pill.setAttribute('aria-expanded', 'false');
  document.body.style.overflow = '';
}

pill.addEventListener('click', () =>
  drawer.classList.contains('open') ? closeDrawer() : openDrawer()
);
backdrop.addEventListener('click', closeDrawer);
closeBtn.addEventListener('click', closeDrawer);

// ── Card preview logic ──
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