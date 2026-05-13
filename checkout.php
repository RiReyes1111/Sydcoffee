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
<title>SYD Coffee - Checkout</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css"/>
<style>
body { font-family: 'Montserrat', sans-serif; background: #f5f5f5; margin: 0; }

.checkout-topbar {
    background: white;
    border-bottom: 1px solid #eee;
    padding: 14px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.checkout-topbar img { height: 36px; }
.checkout-topbar > span {
    font-weight: bold;
    font-size: 16px;
    color: #4a3728;
}
.topbar-user {
    font-size: 13px;
    color: #7a5000;
    background: #fff8f0;
    border: 1px solid #f0c080;
    border-radius: 20px;
    padding: 5px 12px;
    font-weight: 600;
    white-space: nowrap;
}
.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #f0ebe6;
    color: #4a3728;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 13px;
    cursor: pointer;
    text-decoration: none;
    margin-left: auto;
    transition: background 0.2s;
}
.btn-back:hover { background: #e0d5cc; }

.checkout-wrap {
    max-width: 700px;
    margin: 32px auto;
    padding: 0 20px 60px;
}

h2 { color: #4a3728; margin-bottom: 6px; }
.section-label { font-size: 13px; color: #999; margin-bottom: 20px; }

.card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.06);
}
.card h3 { margin: 0 0 16px; color: #4a3728; font-size: 16px; }

.member-banner {
    background: #f0fff4;
    border: 1px solid #a8e6c0;
    border-radius: 12px;
    padding: 14px 20px;
    margin-bottom: 20px;
    font-size: 14px;
    color: #1a6e3a;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
.member-banner .icon { font-size: 20px; line-height: 1.4; }
.member-banner strong { display: block; font-size: 15px; margin-bottom: 2px; }

.order-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 14px;
}
.order-item:last-child { border-bottom: none; }
.order-item-name { color: #333; }
.order-item-size { color: #999; font-size: 12px; }
.order-item-price { font-weight: bold; color: #4a3728; }

.order-totals { margin-top: 12px; }
.total-row {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    padding: 4px 0;
    color: #666;
}
.total-row.discount-row { color: #1a6e3a; font-weight: 600; }
.total-row.grand {
    font-size: 16px;
    font-weight: bold;
    color: #4a3728;
    border-top: 2px solid #4a3728;
    margin-top: 8px;
    padding-top: 10px;
}

.guest-banner {
    background: #fff8f0;
    border: 1px solid #f0c080;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}
.guest-banner p { margin: 0; font-size: 14px; color: #7a5000; }
.guest-banner strong { display: block; font-size: 15px; margin-bottom: 4px; }
.guest-banner-btns { display: flex; gap: 8px; flex-shrink: 0; }
.guest-banner-btns a {
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: bold;
    text-decoration: none;
    white-space: nowrap;
}
.btn-signup { background: #4a3728; color: white; }
.btn-guest  { background: #eee; color: #555; }

.fulfillment-options { display: flex; gap: 12px; margin-bottom: 16px; }
.fulfillment-btn {
    flex: 1; padding: 14px;
    border: 2px solid #ddd; border-radius: 12px;
    background: white; font-weight: bold;
    cursor: pointer; font-size: 14px;
    transition: all 0.2s; color: #555;
}
.fulfillment-btn.active { border-color: #4a3728; background: #4a3728; color: white; }

#pickupInfo, #deliveryInfo { display: none; }
#pickupInfo.show, #deliveryInfo.show { display: block; }

.address-box {
    background: #f9f5f0; border-radius: 10px;
    padding: 14px; font-size: 14px;
    color: #4a3728; margin-bottom: 12px;
}
.address-box strong { display: block; margin-bottom: 4px; }
.map-placeholder { width: 100%; height: 200px; border-radius: 10px; overflow: hidden; border: none; }

input[type="text"], input[type="tel"], textarea {
    width: 100%; padding: 12px;
    border: 1px solid #ddd; border-radius: 10px;
    font-size: 14px; font-family: 'Montserrat', sans-serif;
    box-sizing: border-box; margin-bottom: 10px;
}

.payment-options { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; }
.payment-btn {
    flex: 1; min-width: 100px; padding: 12px;
    border: 2px solid #ddd; border-radius: 10px;
    background: white; font-weight: bold;
    cursor: pointer; font-size: 13px;
    transition: all 0.2s; color: #555;
}
.payment-btn.active { border-color: #4a3728; background: #4a3728; color: white; }

#gcashInfo, #visaInfo, #cashInfo { display: none; }
#gcashInfo.show, #visaInfo.show, #cashInfo.show { display: block; }

.gcash-box { background: #f0f7ff; border-radius: 10px; padding: 16px; text-align: center; }
.gcash-box p { margin: 4px 0; font-size: 14px; color: #333; }
.gcash-number { font-size: 20px; font-weight: bold; color: #0070f3; letter-spacing: 2px; }
.qr-placeholder {
    width: 150px; height: 150px; background: #e0eeff;
    border-radius: 10px; margin: 12px auto;
    display: flex; align-items: center; justify-content: center;
    color: #0070f3; font-size: 12px; font-weight: bold;
}
.visa-box { background: #f5f5ff; border-radius: 10px; padding: 16px; font-size: 14px; }
.visa-box p { margin: 4px 0; color: #444; }
.cash-box { background: #f0fff4; border-radius: 10px; padding: 16px; font-size: 14px; color: #2d6a4f; }

.btn-place-order {
    width: 100%; padding: 16px;
    background: #4a3728; color: white;
    border: none; border-radius: 12px;
    font-size: 16px; font-weight: bold;
    cursor: pointer; margin-top: 10px;
}
.btn-place-order:hover { background: #3a2a1e; }

.success-screen { display: none; text-align: center; padding: 60px 20px; }
.success-screen.show { display: block; }
.success-icon { font-size: 60px; margin-bottom: 16px; }
.success-screen h2 { color: #4a3728; }
.success-screen p { color: #888; }
.btn-back-menu {
    display: inline-block; margin-top: 20px;
    padding: 12px 28px; background: #4a3728;
    color: white; border-radius: 10px;
    text-decoration: none; font-weight: bold;
}
</style>
</head>
<body>

<!-- TOP BAR -->
<div class="checkout-topbar">
  <a href="index.php"><img src="images/logosydnobg.png" alt="SYD Coffee"></a>
  <span>Checkout</span>
  <?php if ($isUser): ?>
    <span class="topbar-user">👋 <?= htmlspecialchars($userName) ?></span>
  <?php endif; ?>
  <a href="menu.php" class="btn-back">← Back to Menu</a>
</div>

<div class="checkout-wrap">

  <!-- SUCCESS SCREEN -->
  <div class="success-screen" id="successScreen">
    <div class="success-icon">✅</div>
    <h2>Order Placed!</h2>
    <p>Thank you<?= $isUser ? ', ' . htmlspecialchars($userName) : '' ?>! We'll have it ready for you soon.</p>
    <a href="menu.php" class="btn-back-menu">Back to Menu</a>
  </div>

  <!-- CHECKOUT FORM -->
  <div id="checkoutForm">
    <h2>Checkout</h2>
    <p class="section-label">Review your order and complete your purchase.</p>

    <?php if ($isUser): ?>
    <div class="member-banner">
      <span class="icon">🎉</span>
      <div>
        <strong>Member discount applied, <?= htmlspecialchars($userName) ?>!</strong>
        <?php if ($isHappyHour): ?>
          You're saving <strong>10% off</strong> all items + <strong>20% off</strong> Cold Drinks (Happy Hour until 5:00 PM). Prices already reflect your discount.
        <?php else: ?>
          You're saving <strong>10% off</strong> all items. Prices already reflect your discount.
        <?php endif; ?>
      </div>
    </div>
    <?php else: ?>
    <div class="guest-banner" id="guestBanner">
      <p><strong>🎉 Get 10% off your order!</strong> Create a free account to unlock member discounts.</p>
      <div class="guest-banner-btns">
        <a href="login.php?tab=register&redirect=checkout" class="btn-signup">Sign Up</a>
        <a href="#" class="btn-guest" onclick="dismissBanner(); return false;">No thanks</a>
      </div>
    </div>
    <?php endif; ?>

    <!-- ORDER SUMMARY -->
    <div class="card">
      <h3>🧾 Order Summary</h3>
      <div id="summaryItems"></div>
      <div class="order-totals">
        <div class="total-row"><span>Subtotal</span><span id="subtotalDisplay">₱0.00</span></div>
        <?php if ($isUser): ?>
        <div class="total-row discount-row">
          <span id="discountLabel">✓ Member Discount (10%)</span>
          <span id="discountDisplay"></span>
        </div>
        <?php endif; ?>
        <div class="total-row" id="deliveryFeeRow" style="display:none;"><span>Delivery Fee</span><span>₱40.00</span></div>
        <div class="total-row grand"><span>Total</span><span id="grandTotalDisplay">₱0.00</span></div>
      </div>
    </div>

    <!-- FULFILLMENT -->
    <div class="card">
      <h3>📦 Fulfillment</h3>
      <div class="fulfillment-options">
        <button class="fulfillment-btn active" id="btnPickup"   onclick="setFulfillment('pickup')">🏪 Pick Up</button>
        <button class="fulfillment-btn"        id="btnDelivery" onclick="setFulfillment('delivery')">🛵 Delivery</button>
      </div>
      <div id="pickupInfo" class="show">
        <div class="address-box">
          <strong>SYD Coffee</strong>
          Greenrose Subdivision, Rodriguez, Rizal 1860, Philippines
        </div>
        <iframe class="map-placeholder"
         src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3864.12!2d121.13!3d14.74!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b9b1234abcd%3A0x0!2sGreenrose+Subdivision%2C+Rodriguez%2C+Rizal!5e0!3m2!1sen!2sph!4v1715000000000!5m2!1sen!2sph"
         allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
       </iframe>
      </div>
      <div id="deliveryInfo">
        <input type="text" id="deliveryAddress" placeholder="Enter your full delivery address" />
        <input type="tel"  id="contactNumber"   placeholder="Contact number" />
        <textarea placeholder="Special instructions (optional)" rows="3" id="specialNotes"></textarea>
      </div>
    </div>

    <!-- PAYMENT -->
    <div class="card">
      <h3>💳 Payment Method</h3>
      <div class="payment-options">
        <button class="payment-btn active" id="btnGcash" onclick="setPayment('gcash')">GCash</button>
        <button class="payment-btn"        id="btnVisa"  onclick="setPayment('visa')">Visa / Card</button>
        <button class="payment-btn"        id="btnCash"  onclick="setPayment('cash')">Cash</button>
      </div>
      <div id="gcashInfo" class="show">
        <div class="gcash-box">
          <p>Send payment to:</p>
          <div class="gcash-number">0962 071 4739</div>
          <p>Account Name: <strong>RI***Y T.</strong></p>
          <img src="images/gcash qr.png" alt="GCash QR Code" style="width:150px;height:150px;border-radius:10px;margin:12px auto;display:block;">
          <p style="font-size:12px;color:#888;">Please send screenshot of payment upon ordering.</p>
        </div>
      </div>
      <div id="visaInfo">
        <div class="visa-box">
          <p>💳 Card payments are processed upon pickup/delivery.</p>
          <p>Accepted: Visa, Mastercard</p>
        </div>
      </div>
      <div id="cashInfo">
        <div class="cash-box">
          <p>💵 Pay with cash upon pickup or delivery.</p>
          <p>Please prepare exact amount if possible.</p>
        </div>
      </div>
    </div>

    <button class="btn-place-order" onclick="placeOrder()">Place Order</button>
  </div>
</div>

<script>
const IS_MEMBER     = <?= $isUser ? 'true' : 'false' ?>;
const IS_HAPPY_HOUR = <?= ($isUser && $isHappyHour) ? 'true' : 'false' ?>;

function loadCart() {
    try {
        const raw = JSON.parse(localStorage.getItem('sydCart') || '[]');
        return raw
            .filter(item => item
                && typeof item.name === 'string' && item.name.trim() !== ''
                && typeof item.size === 'string' && item.size.trim() !== ''
                && !isNaN(parseFloat(item.price)) && parseFloat(item.price) > 0
                && item.qty > 0)
            .map(item => ({ ...item, price: parseFloat(item.price), qty: parseInt(item.qty) }));
    } catch (e) { return []; }
}

const cart = loadCart();
let fulfillment = 'pickup';
let payment = 'gcash';

function renderSummary() {
    const container = document.getElementById('summaryItems');
    if (cart.length === 0) {
        container.innerHTML = '<p style="color:#aaa;font-size:14px;">Your cart is empty. <a href="menu.php">Go back to menu</a></p>';
        updateTotals(0);
        return;
    }
    let subtotal = 0;
    container.innerHTML = cart.map(item => {
        const sub = parseFloat(item.price) * parseInt(item.qty);
        subtotal += sub;
        return `<div class="order-item">
            <div>
                <div class="order-item-name">${item.name} x${item.qty}</div>
                <div class="order-item-size">${item.size}</div>
            </div>
            <div class="order-item-price">₱${sub.toFixed(2)}</div>
        </div>`;
    }).join('');
    updateTotals(subtotal);
}

function updateTotals(subtotal) {
    const deliveryFee = fulfillment === 'delivery' ? 40 : 0;
    const grand = subtotal + deliveryFee;

    document.getElementById('subtotalDisplay').textContent   = `₱${subtotal.toFixed(2)}`;
    document.getElementById('grandTotalDisplay').textContent = `₱${grand.toFixed(2)}`;

    if (IS_MEMBER) {
        // Prices are already discounted. Work backwards to find savings:
        // discounted = original * (1 - rate), so savings = discounted * rate / (1 - rate)
        const rate    = IS_HAPPY_HOUR ? 0.20 : 0.10;
        const savings = subtotal * (rate / (1 - rate));
        const label   = IS_HAPPY_HOUR ? '✓ Happy Hour Discount (up to 20%)' : '✓ Member Discount (10%)';
        document.getElementById('discountLabel').textContent   = label;
        document.getElementById('discountDisplay').textContent = `-₱${savings.toFixed(2)} saved`;
    }
}

function recalcTotals() {
    const subtotal = cart.reduce((sum, i) => sum + parseFloat(i.price) * parseInt(i.qty), 0);
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
        document.getElementById('btn' + t.charAt(0).toUpperCase() + t.slice(1)).classList.toggle('active', t === type);
    });
}

function dismissBanner() {
    document.getElementById('guestBanner').style.display = 'none';
}

function placeOrder() {
    if (cart.length === 0) { alert('Your cart is empty!'); return; }

    let deliveryAddress = '';
    let contactNumber   = '';
    let specialNotes    = '';

    if (fulfillment === 'delivery') {
        deliveryAddress = document.getElementById('deliveryAddress').value.trim();
        contactNumber   = document.getElementById('contactNumber').value.trim();
        specialNotes    = document.getElementById('specialNotes').value.trim();
        if (!deliveryAddress) { alert('Please enter your delivery address.'); return; }
        if (!contactNumber)   { alert('Please enter your contact number.');   return; }
    }

    const subtotal    = cart.reduce((sum, i) => sum + parseFloat(i.price) * parseInt(i.qty), 0);
    const deliveryFee = fulfillment === 'delivery' ? 40 : 0;
    const total       = subtotal + deliveryFee;

    const payload = {
        items:           cart,
        subtotal:        parseFloat(subtotal.toFixed(2)),
        delivery_fee:    deliveryFee,
        total:           parseFloat(total.toFixed(2)),
        fulfillment:     fulfillment,
        payment_method:  payment,
        delivery_address: deliveryAddress,
        contact_number:  contactNumber,
        special_notes:   specialNotes
    };

    fetch('place_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            localStorage.removeItem('sydCart');
            document.getElementById('checkoutForm').style.display = 'none';
            document.getElementById('successScreen').classList.add('show');
            window.scrollTo(0, 0);
        } else {
            alert('Something went wrong: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(() => alert('Could not connect to server. Please try again.'));
}
renderSummary();
</script>
</body>
</html>