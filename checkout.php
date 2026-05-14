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
<link href="googleapis.com" rel="stylesheet">
<link rel="stylesheet" href="css/checkoutcss.css">
<style>
.summary-item-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 12px 0;
}
.item-title-box {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.item-main-name {
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  font-size: 0.95rem;
  color: #111;
}
.item-meta-spec {
  display: inline-block;
  background-color: #f5f0eb;
  color: #8c6239;
  font-size: 0.7rem;
  font-weight: 800;
  padding: 2px 6px;
  border-radius: 4px;
  letter-spacing: 0.5px;
}
.item-price-display {
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  font-size: 0.95rem;
  color: #111;
  text-align: right;
}
</style>
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
  <a href="menu.php" class="btn-back-nav">← Menu</a>
</div>

<div class="success-screen" id="successScreen">
  <div class="success-icon">✓</div>
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
        <div class="card-head-icon">●</div>
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
          <strong>SYD Coffee</strong><br>
          Greenrose Subdivision, Rodriguez, Rizal 1860, Philippines
        </div>
        <iframe class="map-frame"
          src="google.com"
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
        <div class="card-head-icon">●</div>
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
                  <input type="text" id="cardExpiry" placeholder="MM / YY" maxlength="5" oninput="formatExpiry(this)">
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">CVV</label>
                <div class="card-input-wrap">
                  <input type="password" id="cardCvv" placeholder="123" maxlength="3">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div id="cashInfo">
        <div class="pay-panel">
          <p class="pay-note">Please prepare exact change upon collection or delivery to expedite fulfillment.</p>
        </div>
      </div>

    </div>
  </div>

  <div class="summary-col">
    <div class="sticky-summary">
      <div class="card">
        <div class="card-head" style="margin-bottom:20px;">
          <div class="card-head-icon" style="color: var(--brown-mid, #6f4e37);">●</div>
          <div>
            <h3 style="margin:0; font-size:1.1rem; font-weight:700;">Order summary</h3>
            <div class="card-sub" id="summaryCount" style="font-size:0.85rem; color:#666;">0 items</div>
          </div>
        </div>

        <div class="summary-items" id="summaryItemsList" style="border-bottom: 1px solid #eee; padding-bottom:15px; margin-bottom:15px;"></div>

        <div class="summary-totals" style="margin-bottom: 20px;">
          <div class="total-row" style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:0.95rem; color:#666;">
            <span>Subtotal</span>
            <span id="txtSubtotal" style="font-weight:600;">₱0.00</span>
          </div>
          
          <?php if ($isUser): ?>
          <div class="total-row discount-row" id="discountDisplayRow" style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:0.95rem; color: #28a745; font-weight:500;">
            <span>Member Discount (10%)</span>
            <span id="txtDiscount">-₱0.00 saved</span>
          </div>
          <?php endif; ?>

          <div class="total-row" id="deliveryFeeRow" style="display:flex; justify-content:space-between; margin-bottom:15px; font-size:0.95rem; color:#666;">
            <span>Fulfillment Fee</span>
            <span id="txtFee" style="font-weight:600;">₱0.00</span>
          </div>
          
          <div class="total-row grand-total" style="display:flex; justify-content:space-between; border-top: 2px solid #222; padding-top:15px; font-size:1.2rem; color:#111;">
            <span style="font-weight:700;">Total</span>
            <span id="txtTotal" style="font-weight:800;">₱0.00</span>
          </div>
        </div>

        <button class="btn-place-order" onclick="submitOrder()" style="width:100%; background:#111; color:#fff; border:none; padding:15px; font-weight:700; font-size:1rem; border-radius:30px; cursor:pointer; letter-spacing:1px; text-transform:uppercase; transition: background 0.2s;">
          PLACE ORDER →
        </button>
        <div class="secure-footer" style="text-align:center; font-size:0.8rem; color:#888; margin-top:12px;">Secure checkout · No hidden fees</div>
      </div>
    </div>
  </div>

</div>

<script>
let currentFulfillment = 'pickup';
let currentPayment = 'gcash';
const isMember = <?= $isUser ? 'true' : 'false' ?>;

let cart = JSON.parse(localStorage.getItem('cart')) || [
    { id: 1, name: 'Cafe Americano', size: 'LARGE', quantity: 1, price: 85.50 }
];

function initOrderSummary() {
    const listContainer = document.getElementById('summaryItemsList');
    if(!listContainer) return;
    
    listContainer.innerHTML = '';
    let itemizedSubtotal = 0;
    let actualDiscountTotal = 0;
    let itemCount = 0;

    cart.forEach(item => {
        let baseLineRowPrice = item.price * item.quantity;
        itemizedSubtotal += baseLineRowPrice;
        itemCount += item.quantity;

        let calculatedRowDiscount = isMember ? (baseLineRowPrice * 0.10) : 0;
        actualDiscountTotal += calculatedRowDiscount;
        let finalRowPriceDisplay = baseLineRowPrice - calculatedRowDiscount;

        const itemDiv = document.createElement('div');
        itemDiv.className = 'summary-item-row';
        itemDiv.innerHTML = `
            <div class="item-title-box">
                <div class="item-main-name">${item.name}</div>
                <div>
                    <span class="item-meta-spec">${item.size}</span>
                    <span style="font-size: 0.8rem; color: #777; margin-left: 4px;">× ${item.quantity}</span>
                </div>
            </div>
            <div class="item-price-display">₱${finalRowPriceDisplay.toFixed(2)}</div>
        `;
        listContainer.appendChild(itemDiv);
    });

    document.getElementById('summaryCount').innerText = `${itemCount} item${itemCount !== 1 ? 's' : ''}`;
    calculateFinalTotals(itemizedSubtotal, actualDiscountTotal);
}

function calculateFinalTotals(subtotal, discount) {
    let fee = (currentFulfillment === 'delivery') ? 50.00 : 0.00;
    
    // MATHEMATICAL MATH CORRECTION: Subtract the discount before adding the delivery fee variable
    let finalTotal = (subtotal - discount) + fee;

    document.getElementById('txtSubtotal').innerText = `₱${subtotal.toFixed(2)}`;
    if (isMember) {
        document.getElementById('txtDiscount').innerText = `-₱${discount.toFixed(2)} saved`;
    }
    document.getElementById('txtFee').innerText = `₱${fee.toFixed(2)}`;
    document.getElementById('txtTotal').innerText = `₱${finalTotal.toFixed(2)}`;
}

function setFulfillment(type) {
    currentFulfillment = type;
    document.getElementById('btnPickup').classList.toggle('active', type === 'pickup');
    document.getElementById('btnDelivery').classList.toggle('active', type === 'delivery');
    
    document.getElementById('pickupInfo').classList.toggle('show', type === 'pickup');
    document.getElementById('deliveryInfo').classList.toggle('show', type === 'delivery');
    
    initOrderSummary();
}

function setPayment(method) {
    currentPayment = method;
    document.getElementById('btnGcash').classList.toggle('active', method === 'gcash');
    document.getElementById('btnVisa').classList.toggle('active', method === 'visa');
    document.getElementById('btnCash').classList.toggle('active', method === 'cash');
    
    document.getElementById('gcashInfo').classList.toggle('show', method === 'gcash');
    document.getElementById('visaInfo').classList.toggle('show', method === 'visa');
    document.getElementById('cashInfo').classList.toggle('show', method === 'cash');
}

function formatCardNumber(input) {
    let v = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let matches = v.match(/\d{4,16}/g);
    let match = matches && matches || '';
    let parts = [];

    for (let i=0, len=match.length; i<len; i+=4) {
        parts.push(match.substring(i, i+4));
    }

    if (parts.length > 0) {
        input.value = parts.join(' ');
    } else {
        input.value = v;
    }
    document.getElementById('previewNumber').innerText = input.value || '•••• •••• •••• ••••';
}

function updatePreviewName(val) {
    document.getElementById('previewName').innerText = val.toUpperCase() || 'YOUR NAME';
}

function formatExpiry(input) {
    let v = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    if (v.length >= 2) {
        input.value = v.substring(0, 2) + ' / ' + v.substring(2, 4);
    } else {
        input.value = v;
    }
    document.getElementById('previewExpiry').innerText = input.value || 'MM / YY';
}

async function submitOrder() {
    let subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    let fee = (currentFulfillment === 'delivery') ? 50.00 : 0.00;

    let payload = {
        items: cart,
        subtotal: subtotal,
        delivery_fee: fee,
        fulfillment: currentFulfillment,
        payment_method: currentPayment,
        delivery_address: document.getElementById('deliveryAddress')?.value || '',
        contact_number: document.getElementById('contactNumber')?.value || '',
        special_notes: document.getElementById('specialNotes')?.value || ''
    };

    if (currentFulfillment === 'delivery' && (!payload.delivery_address || !payload.contact_number)) {
        alert("Please complete the delivery form fields parameters.");
        return;
    }

    try {
        let response = await fetch('place_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        
        let result = await response.json();
        if (result.success) {
            localStorage.removeItem('cart');
            document.getElementById('checkoutForm').style.display = 'none';
            document.getElementById('successScreen').style.display = 'block';
        } else {
            alert("Error placing order: " + result.message);
        }
    } catch(err) {
        alert("Network communication operational error.");
    }
}

window.addEventListener('DOMContentLoaded', initOrderSummary);
</script>
</body>
</html>