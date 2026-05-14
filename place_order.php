<?php
session_start();
require_once "auth/config.php";

header('Content-Type: application/json');
header("Cache-Control: no-store");

$loggedIn     = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userRole     = $_SESSION['role'] ?? null;
$isUser       = ($loggedIn && $userRole === 'user');
$userId       = $isUser ? (int)$_SESSION['user_id'] : null;
$customerName = $isUser ? $_SESSION['name'] : 'Guest';

$body = json_decode(file_get_contents('php://input'), true);
if (!$body || empty($body['items'])) {
    echo json_encode(['success' => false, 'message' => 'Empty order.']);
    exit();
}

$items        = $body['items'];
$subtotal     = floatval($body['subtotal']      ?? 0);
$deliveryFee  = floatval($body['delivery_fee']  ?? 0);
$fulfillment  = in_array($body['fulfillment'] ?? '', ['pickup','delivery']) ? $body['fulfillment'] : 'pickup';
$payment      = $body['payment_method']  ?? 'cash';
$deliveryAddr = $body['delivery_address'] ?? '';
$contactNum   = $body['contact_number']  ?? '';
$specialNotes = $body['special_notes']   ?? '';
$itemsJson    = json_encode($items);

if ($fulfillment === 'pickup') {
    $deliveryFee = 0.00;
}

$discountAmount = 0.00;
if ($isUser) {
    $discountAmount = $subtotal * 0.10; 
}

$total = ($subtotal - $discountAmount) + $deliveryFee;

$subtotal    = round($subtotal, 2);
$deliveryFee = round($deliveryFee, 2);
$total       = round($total, 2);

$stmt = $conn->prepare("INSERT INTO orders 
    (customer_name, user_id, items_json, subtotal, delivery_fee, total, fulfillment, payment_method, delivery_address, contact_number, special_notes)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "sissdddsss",
    $customerName,
    $userId,
    $itemsJson,
    $subtotal,
    $deliveryFee,
    $total,
    $fulfillment,
    $payment,
    $deliveryAddr,
    $contactNum,
    $specialNotes
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'order_id' => $stmt->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Order could not be saved.']);
}