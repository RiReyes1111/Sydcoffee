<?php
session_start();
include "config.php";

header('Content-Type: application/json');
header("Cache-Control: no-store");

$loggedIn    = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userRole    = $_SESSION['role'] ?? null;
$isUser      = ($loggedIn && $userRole === 'user');
$userId      = $isUser ? $_SESSION['user_id'] : null;
$customerName = $isUser ? $_SESSION['name'] : 'Guest';

$body = json_decode(file_get_contents('php://input'), true);
if (!$body || empty($body['items'])) {
    echo json_encode(['success' => false, 'message' => 'Empty order.']);
    exit();
}

$items         = $body['items'];
$subtotal      = floatval($body['subtotal'] ?? 0);
$deliveryFee   = floatval($body['delivery_fee'] ?? 0);
$total         = floatval($body['total'] ?? 0);
$fulfillment   = in_array($body['fulfillment'] ?? '', ['pickup','delivery']) ? $body['fulfillment'] : 'pickup';
$paymentMethod = $conn->real_escape_string($body['payment_method'] ?? 'cash');
$deliveryAddr  = $conn->real_escape_string($body['delivery_address'] ?? '');
$contactNum    = $conn->real_escape_string($body['contact_number'] ?? '');
$specialNotes  = $conn->real_escape_string($body['special_notes'] ?? '');
$itemsJson     = $conn->real_escape_string(json_encode($items));
$customerName  = $conn->real_escape_string($customerName);

$userIdSql = $userId ? intval($userId) : 'NULL';

$sql = "INSERT INTO orders 
    (customer_name, user_id, items_json, subtotal, delivery_fee, total, fulfillment, payment_method, delivery_address, contact_number, special_notes)
    VALUES 
    ('$customerName', $userIdSql, '$itemsJson', $subtotal, $deliveryFee, $total, '$fulfillment', '$paymentMethod', '$deliveryAddr', '$contactNum', '$specialNotes')";

if ($conn->query($sql)) {
    echo json_encode(['success' => true, 'order_id' => $conn->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}