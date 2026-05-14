<?php
date_default_timezone_set('Asia/Manila');
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('../auth/config.php');

header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: adminlogin.php");
    exit();
}

// Tell MySQL to use PH time for this session
$conn->query("SET time_zone = '+08:00'");

if (isset($_POST['update_status'])) {
    $id = intval($_POST['order_id']);
    $status = $conn->real_escape_string($_POST['status']);
    if (in_array($status, ['pending','preparing','ready','completed','cancelled']))
        $conn->query("UPDATE orders SET status='$status' WHERE id=$id");
    header("Location: adminorders.php"); exit();
}
if (isset($_POST['delete_order'])) {
    $conn->query("UPDATE orders SET deleted_at = NOW() WHERE id=".intval($_POST['order_id']));
    header("Location: adminorders.php"); exit();
}
if (isset($_POST['restore_order'])) {
    $conn->query("UPDATE orders SET deleted_at = NULL WHERE id=".intval($_POST['order_id']));
    header("Location: adminorders.php"); exit();
}
if (isset($_POST['force_delete_order'])) {
    $conn->query("DELETE FROM orders WHERE id=".intval($_POST['order_id']));
    header("Location: adminorders.php"); exit();
}
if (isset($_POST['reset_all'])) {
    $conn->query("DELETE FROM orders WHERE deleted_at IS NULL");
    header("Location: adminorders.php"); exit();
}
if (isset($_POST['empty_bin'])) {
    $conn->query("DELETE FROM orders WHERE deleted_at IS NOT NULL");
    header("Location: adminorders.php"); exit();
}

// Helper: format any datetime string as PH time
function phTime($datetime, $format = 'M j, Y g:i A') {
    if (empty($datetime)) return '';
    $dt = new DateTime($datetime, new DateTimeZone('Asia/Manila'));
    $dt->setTimezone(new DateTimeZone('Asia/Manila'));
    return $dt->format($format);
}

$filter = $_GET['filter'] ?? 'all';
$where  = $filter !== 'all' ? "WHERE deleted_at IS NULL AND status='".$conn->real_escape_string($filter)."'" : "WHERE deleted_at IS NULL";
$orders = $conn->query("SELECT * FROM orders $where ORDER BY created_at DESC");
$trashedOrders  = $conn->query("SELECT * FROM orders WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
$countAll       = $conn->query("SELECT COUNT(*) as c FROM orders WHERE deleted_at IS NULL")->fetch_assoc()['c'];
$countPending   = $conn->query("SELECT COUNT(*) as c FROM orders WHERE deleted_at IS NULL AND status='pending'")->fetch_assoc()['c'];
$countPreparing = $conn->query("SELECT COUNT(*) as c FROM orders WHERE deleted_at IS NULL AND status='preparing'")->fetch_assoc()['c'];
$countReady     = $conn->query("SELECT COUNT(*) as c FROM orders WHERE deleted_at IS NULL AND status='ready'")->fetch_assoc()['c'];
$countCompleted = $conn->query("SELECT COUNT(*) as c FROM orders WHERE deleted_at IS NULL AND status='completed'")->fetch_assoc()['c'];
$countCancelled = $conn->query("SELECT COUNT(*) as c FROM orders WHERE deleted_at IS NULL AND status='cancelled'")->fetch_assoc()['c'];
$countTrashed   = $conn->query("SELECT COUNT(*) as c FROM orders WHERE deleted_at IS NOT NULL")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Orders — SYD Coffee</title>
<link rel="icon" type="image/png" href="../images/logosydnobg.png">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<meta http-equiv="refresh" content="30">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
    --cream:#f8f5ef;--cream-dark:#ede8de;--espresso:#1e120a;--brown-mid:#4a3020;
    --brown-light:#7a5c3e;--gold:#c8973a;--gold-light:#e8b84b;--white:#ffffff;
    --text-dark:#1e120a;--text-mid:#4a3020;--text-muted:#9a7a5a;--text-faint:#c8b09a;
    --green:#2d7a4f;--green-light:#e6f7ee;--orange:#d4680a;--orange-light:#fff0e0;
    --red:#c0392b;--red-light:#fdecea;--blue:#2c7be5;--blue-light:#e8f0fd;
    --purple:#7c3aed;--purple-light:#f3eeff;
    --border:#ede8de;--border-light:#f0ebe2;
    --shadow:0 2px 16px rgba(30,18,10,0.07);--shadow-sm:0 1px 6px rgba(30,18,10,0.05);
    --sidebar-w:220px;--radius:14px;--radius-sm:10px;
}
body{font-family:'Montserrat',sans-serif;background:var(--cream);color:var(--text-dark);min-height:100vh;display:flex;font-size:13px;}

.sidebar{width:var(--sidebar-w);background:var(--espresso);min-height:100vh;display:flex;flex-direction:column;position:fixed;top:0;left:0;z-index:200;transition:transform 0.3s;}
.sidebar-logo{padding:20px 20px 18px;border-bottom:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;gap:10px;}
.sidebar-logo img{height:38px;width:auto;filter:brightness(0) invert(1);opacity:0.9;}
.sidebar-wordmark{font-family:'Playfair Display',serif;font-size:16px;color:var(--cream);font-style:italic;}
.sidebar-sub{font-size:9px;letter-spacing:2px;color:#a08060;text-transform:uppercase;margin-top:2px;}
.sidebar-nav{padding:18px 12px 8px;flex:1;}
.sidebar-nav-label{font-size:9px;font-weight:700;letter-spacing:2px;color:#7a6040;text-transform:uppercase;padding:0 8px;margin-bottom:8px;}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 10px;border-radius:8px;color:#c8b89a;font-size:12px;font-weight:500;text-decoration:none;transition:all 0.2s;margin-bottom:2px;}
.nav-item i{font-size:15px;}
.nav-item:hover{background:rgba(255,255,255,0.08);color:#ffffff;}
.nav-item.active{background:rgba(200,151,58,0.20);color:#e8b84b;}
.sidebar-spacer{flex:1;}
.sidebar-bottom{padding:14px 12px;border-top:1px solid rgba(255,255,255,0.07);}
.sidebar-user{display:flex;align-items:center;gap:10px;padding:8px;border-radius:8px;cursor:pointer;}
.sidebar-user:hover{background:rgba(255,255,255,0.04);}
.user-avatar{width:30px;height:30px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--espresso);flex-shrink:0;}
.user-name{font-size:12px;font-weight:600;color:#e0d0be;}
.user-role{font-size:10px;color:#7a6040;}
.logout-link{display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:8px;color:#c07060;font-size:12px;text-decoration:none;margin-top:4px;transition:all 0.2s;}
.logout-link i{font-size:15px;}
.logout-link:hover{background:rgba(192,57,43,0.08);color:var(--red);}

.main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh;}
.topbar{display:flex;align-items:center;justify-content:space-between;padding:18px 28px;background:var(--white);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:100;gap:12px;box-shadow:var(--shadow-sm);}
.topbar-left{display:flex;align-items:center;gap:12px;}
.topbar-title{font-size:14px;font-weight:600;color:var(--text-dark);}
.topbar-sub{font-size:11px;color:var(--text-muted);margin-top:2px;}
.topbar-right{display:flex;align-items:center;gap:10px;}
.refresh-badge{font-size:11px;color:var(--text-muted);background:var(--cream);border:1px solid var(--border);padding:6px 14px;border-radius:20px;display:flex;align-items:center;gap:6px;}
.refresh-badge i{font-size:13px;}
.btn-reset{padding:8px 16px;background:var(--red-light);color:var(--red);border:1px solid #f5c6c2;border-radius:20px;font-family:'Montserrat',sans-serif;font-size:11px;font-weight:700;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:6px;}
.btn-reset:hover{background:var(--red);color:var(--white);border-color:var(--red);}
.menu-toggle{display:none;background:none;border:none;font-size:18px;cursor:pointer;color:var(--text-dark);padding:4px;}

.content{padding:24px 28px;flex:1;}

.stats{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
.stat-pill{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:14px 18px;flex:1;min-width:90px;text-decoration:none;color:inherit;display:block;transition:border-color 0.2s,transform 0.2s;border-left:3px solid var(--cream-dark);}
.stat-pill:hover{transform:translateY(-2px);}
.stat-pill.s-all{border-left-color:var(--brown-mid);}
.stat-pill.s-pending{border-left-color:var(--orange);}
.stat-pill.s-preparing{border-left-color:var(--blue);}
.stat-pill.s-ready{border-left-color:var(--purple);}
.stat-pill.s-completed{border-left-color:var(--green);}
.stat-pill.s-cancelled{border-left-color:var(--red);}
.stat-pill.s-trashed{border-left-color:var(--brown-light);}
.stat-num{font-size:24px;font-weight:700;color:var(--text-dark);line-height:1;}
.stat-label{font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.8px;margin-top:4px;}

.filter-tabs{display:flex;gap:6px;margin-bottom:20px;flex-wrap:wrap;}
.filter-tab{padding:7px 16px;border-radius:20px;font-size:11px;font-weight:600;text-decoration:none;color:var(--text-muted);background:var(--white);border:1px solid var(--border);transition:all 0.2s;}
.filter-tab:hover{border-color:var(--gold);color:var(--brown-mid);}
.filter-tab.active{background:var(--espresso);border-color:var(--espresso);color:var(--cream);}

.orders-list{display:flex;flex-direction:column;gap:14px;}
.order-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;transition:border-color 0.2s;}
.order-card:hover{border-color:var(--gold);}
.order-card.is-new{border-color:var(--orange);}
.order-card.is-trashed{opacity:0.7;}

.order-head{display:flex;align-items:center;gap:10px;padding:14px 20px;border-bottom:1px solid var(--border-light);flex-wrap:wrap;}
.order-id{font-size:14px;font-weight:700;color:var(--text-dark);}
.order-time{font-size:11px;color:var(--text-muted);}
.order-customer{font-size:13px;font-weight:600;color:var(--brown-mid);margin-left:auto;display:flex;align-items:center;gap:6px;}
.order-customer i{font-size:14px;color:var(--text-muted);}

.status-badge{padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;}
.badge-pending{background:var(--orange-light);color:var(--orange);}
.badge-preparing{background:var(--blue-light);color:var(--blue);}
.badge-ready{background:var(--purple-light);color:var(--purple);}
.badge-completed{background:var(--green-light);color:var(--green);}
.badge-cancelled{background:var(--red-light);color:var(--red);}
.badge-deleted{background:#f5ede0;color:var(--brown-mid);}

.order-body{display:flex;gap:20px;padding:16px 20px;flex-wrap:wrap;}
.order-items-list{flex:1;min-width:200px;}
.order-item-row{display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--border-light);font-size:12px;}
.order-item-row:last-child{border-bottom:none;}
.order-item-label{color:var(--text-dark);}
.order-item-label span{color:var(--text-muted);font-size:11px;margin-left:4px;}
.order-item-price{font-weight:600;color:var(--brown-mid);}
.order-total{display:flex;justify-content:space-between;font-size:13px;font-weight:700;color:var(--text-dark);padding-top:8px;border-top:2px solid var(--border);margin-top:4px;}

.order-meta{min-width:180px;font-size:12px;display:flex;flex-direction:column;gap:6px;}
.meta-row{display:flex;gap:8px;align-items:flex-start;}
.meta-label{font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.4px;min-width:60px;padding-top:1px;}
.meta-value{color:var(--text-dark);font-weight:500;}
.meta-deleted{font-size:11px;font-weight:600;color:var(--red);margin-top:4px;}

.order-foot{display:flex;align-items:center;gap:10px;padding:12px 20px;background:var(--cream);border-top:1px solid var(--border-light);flex-wrap:wrap;}
.foot-label{font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;}
.status-select{padding:7px 12px;border-radius:20px;border:1px solid var(--border);font-family:'Montserrat',sans-serif;font-size:12px;font-weight:600;color:var(--text-dark);background:var(--white);outline:none;cursor:pointer;transition:border-color 0.2s;}
.status-select:focus{border-color:var(--gold);}
.btn-update-status{padding:7px 18px;background:var(--espresso);color:var(--cream);border:none;border-radius:20px;font-family:'Montserrat',sans-serif;font-size:11px;font-weight:700;cursor:pointer;transition:opacity 0.2s;}
.btn-update-status:hover{opacity:0.85;}
.btn-delete-order{padding:7px 14px;background:transparent;color:var(--red);border:1px solid #f5c6c2;border-radius:20px;font-family:'Montserrat',sans-serif;font-size:11px;font-weight:700;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:5px;}
.btn-delete-order:hover{background:var(--red);color:var(--white);border-color:var(--red);}
.btn-restore{padding:7px 14px;background:transparent;color:var(--green);border:1px solid #b8e8cc;border-radius:20px;font-family:'Montserrat',sans-serif;font-size:11px;font-weight:700;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:5px;}
.btn-restore:hover{background:var(--green);color:var(--white);border-color:var(--green);}
.btn-force{padding:7px 14px;background:transparent;color:var(--brown-mid);border:1px solid var(--border);border-radius:20px;font-family:'Montserrat',sans-serif;font-size:11px;font-weight:700;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:5px;}
.btn-force:hover{background:var(--brown-mid);color:var(--white);border-color:var(--brown-mid);}

.bin-section{margin-top:28px;background:var(--white);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;}
.bin-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#fdf8f8;border-bottom:1px solid #f5e0e0;flex-wrap:wrap;gap:10px;}
.bin-head-title{font-size:13px;font-weight:600;color:var(--red);display:flex;align-items:center;gap:8px;}
.bin-head-title i{font-size:16px;}
.bin-head-right{display:flex;align-items:center;gap:10px;}
.bin-count{font-size:11px;color:var(--text-muted);}
.btn-empty-bin{padding:7px 14px;background:transparent;color:var(--red);border:1px solid #f5c6c2;border-radius:20px;font-family:'Montserrat',sans-serif;font-size:11px;font-weight:700;cursor:pointer;transition:all 0.2s;}
.btn-empty-bin:hover{background:var(--red);color:var(--white);border-color:var(--red);}
.bin-list{padding:12px;display:flex;flex-direction:column;gap:8px;}
.empty-msg{padding:28px;text-align:center;color:var(--text-muted);font-size:12px;}

.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
.sidebar-overlay.show{display:block;}

@media(max-width:768px){
    .sidebar{transform:translateX(-100%);}
    .sidebar.open{transform:translateX(0);}
    .main{margin-left:0;}
    .menu-toggle{display:block;}
    .content{padding:20px 16px;}
    .topbar{padding:14px 16px;}
    .order-customer{margin-left:0;width:100%;}
    .topbar-right{gap:6px;}

    
    .order-foot{
        align-items:center;
    }

    .order-foot form{
        margin:0;
    }

    .order-foot form:last-child{
        margin-left:auto !important;
    }
}
</style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <img src="../images/logosydnobg.png" alt="SYD Coffee">
        <div>
            <div class="sidebar-wordmark">SYD Coffee</div>
            <div class="sidebar-sub">Admin Panel</div>
        </div>
    </div>
    <div class="sidebar-nav">
        <div class="sidebar-nav-label">Navigation</div>
        <a class="nav-item" href="admin.php"><i class="ti ti-layout-dashboard"></i> Dashboard</a>
        <a class="nav-item" href="adminusers.php"><i class="ti ti-users"></i> Users</a>
        <a class="nav-item" href="adminitems.php"><i class="ti ti-coffee"></i> Items</a>
        <a class="nav-item active" href="adminorders.php"><i class="ti ti-clipboard-list"></i> Orders</a>
        <a class="nav-item" href="../menu.php" target="_blank"><i class="ti ti-external-link"></i> View Menu</a>
    </div>
    <div class="sidebar-spacer"></div>
    <div class="sidebar-bottom">
        <div class="sidebar-user">
            <div class="user-avatar"><?= strtoupper(substr($_SESSION['name'],0,1)) ?></div>
            <div>
                <div class="user-name"><?= htmlspecialchars($_SESSION['name']) ?></div>
                <div class="user-role">Super Admin</div>
            </div>
        </div>
        <a class="logout-link" href="../logout.php"><i class="ti ti-logout"></i> Logout</a>
    </div>
</div>

<div class="main">
    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" onclick="openSidebar()"><i class="ti ti-menu-2"></i></button>
            <div>
                <div class="topbar-title">Orders</div>
                <div class="topbar-sub">Review and manage customer orders</div>
            </div>
        </div>
        <div class="topbar-right">
            <div class="refresh-badge"><i class="ti ti-refresh"></i> Auto-refresh: 30s</div>
            <form method="POST" onsubmit="return confirm('Move all active orders to the recycle bin?')">
                <button type="submit" name="reset_all" class="btn-reset"><i class="ti ti-trash"></i> Delete All</button>
            </form>
        </div>
    </div>

    <div class="content">
        <div class="stats">
            <a class="stat-pill s-all"       href="adminorders.php?filter=all">       <div class="stat-num"><?= $countAll ?></div>       <div class="stat-label">All Orders</div></a>
            <a class="stat-pill s-pending"   href="adminorders.php?filter=pending">   <div class="stat-num"><?= $countPending ?></div>   <div class="stat-label">Pending</div></a>
            <a class="stat-pill s-preparing" href="adminorders.php?filter=preparing"> <div class="stat-num"><?= $countPreparing ?></div> <div class="stat-label">Preparing</div></a>
            <a class="stat-pill s-ready"     href="adminorders.php?filter=ready">     <div class="stat-num"><?= $countReady ?></div>     <div class="stat-label">Ready</div></a>
            <a class="stat-pill s-completed" href="adminorders.php?filter=completed"> <div class="stat-num"><?= $countCompleted ?></div> <div class="stat-label">Completed</div></a>
            <a class="stat-pill s-cancelled" href="adminorders.php?filter=cancelled"> <div class="stat-num"><?= $countCancelled ?></div> <div class="stat-label">Cancelled</div></a>
            <a class="stat-pill s-trashed"   href="#recyclebin">                      <div class="stat-num"><?= $countTrashed ?></div>   <div class="stat-label">In Bin</div></a>
        </div>

        <div class="filter-tabs">
            <?php foreach (['all','pending','preparing','ready','completed','cancelled'] as $f): ?>
                <a class="filter-tab <?= $filter===$f?'active':'' ?>" href="?filter=<?= $f ?>"><?= ucfirst($f) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="orders-list">
        <?php
        $statusBadges = ['pending'=>'badge-pending','preparing'=>'badge-preparing','ready'=>'badge-ready','completed'=>'badge-completed','cancelled'=>'badge-cancelled'];
        $phTz = new DateTimeZone('Asia/Manila');
        $hasOrders = false;
        while ($order = $orders->fetch_assoc()):
            $hasOrders = true;
            $items = json_decode($order['items_json'], true) ?: [];
            // Use DateTime with explicit PH timezone for "is new" check and display
            $createdDt = new DateTime($order['created_at'], $phTz);
            $nowDt     = new DateTime('now', $phTz);
            $isNew     = ($nowDt->getTimestamp() - $createdDt->getTimestamp()) < 300 && $order['status'] === 'pending';
            $badge     = $statusBadges[$order['status']] ?? 'badge-pending';
            $createdAt = $createdDt->format('M j, Y g:i A');
        ?>
        <div class="order-card <?= $isNew ? 'is-new' : '' ?>">
            <div class="order-head">
                <div class="order-id">Order #<?= $order['id'] ?></div>
                <div class="order-time"><?= $createdAt ?></div>
                <span class="status-badge <?= $badge ?>"><?= ucfirst($order['status']) ?></span>
                <div class="order-customer"><i class="ti ti-user"></i> <?= htmlspecialchars($order['customer_name']) ?></div>
            </div>
            <div class="order-body">
                <div class="order-items-list">
                    <?php foreach ($items as $item): ?>
                    <div class="order-item-row">
                        <div class="order-item-label"><?= htmlspecialchars($item['name']) ?><span>(<?= htmlspecialchars($item['size']) ?> x<?= intval($item['qty']) ?>)</span></div>
                        <div class="order-item-price">&#8369;<?= number_format($item['price'] * $item['qty'], 2) ?></div>
                    </div>
                    <?php endforeach; ?>
                    <div class="order-total"><span>Total</span><span>&#8369;<?= number_format($order['total'], 2) ?></span></div>
                </div>
                <div class="order-meta">
                    <div class="meta-row">
                        <span class="meta-label">Type</span>
                        <span class="meta-value"><?= $order['fulfillment'] === 'delivery' ? 'Delivery' : 'Pickup' ?></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Payment</span>
                        <span class="meta-value"><?= ucfirst($order['payment_method']) ?></span>
                    </div>
                    <?php if ($order['fulfillment'] === 'delivery'): ?>
                    <div class="meta-row">
                        <span class="meta-label">Address</span>
                        <span class="meta-value"><?= htmlspecialchars($order['delivery_address']) ?></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Contact</span>
                        <span class="meta-value"><?= htmlspecialchars($order['contact_number']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($order['special_notes'])): ?>
                    <div class="meta-row">
                        <span class="meta-label">Notes</span>
                        <span class="meta-value"><?= htmlspecialchars($order['special_notes']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($order['delivery_fee'] > 0): ?>
                    <div class="meta-row">
                        <span class="meta-label">Del. Fee</span>
                        <span class="meta-value">&#8369;<?= number_format($order['delivery_fee'], 2) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="order-foot">
                <span class="foot-label">Status</span>
                <form method="POST" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <select name="status" class="status-select">
                        <?php foreach (['pending','preparing','ready','completed','cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= $order['status']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="update_status" class="btn-update-status">Update</button>
                </form>
                <form method="POST" style="margin-left:auto;" onsubmit="return confirm('Move Order #<?= $order['id'] ?> to recycle bin?')">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <button type="submit" name="delete_order" class="btn-delete-order"><i class="ti ti-trash"></i> Delete</button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
        <?php if (!$hasOrders): ?>
            <div class="empty-msg" style="background:var(--white);border-radius:var(--radius);border:1px solid var(--border);">No orders found<?= $filter!=='all' ? ' for "'.htmlspecialchars($filter).'"' : '' ?>.</div>
        <?php endif; ?>
        </div>

        <div class="bin-section" id="recyclebin">
            <div class="bin-head">
                <div class="bin-head-title"><i class="ti ti-trash"></i> Recycle Bin</div>
                <div class="bin-head-right">
                    <span class="bin-count"><?= $countTrashed ?> order<?= $countTrashed!=1?'s':'' ?></span>
                    <?php if ($countTrashed > 0): ?>
                    <form method="POST" onsubmit="return confirm('Permanently delete ALL orders in the bin?')">
                        <button type="submit" name="empty_bin" class="btn-empty-bin">Empty Bin</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="bin-list">
            <?php
            $hasTrashed = false;
            while ($order = $trashedOrders->fetch_assoc()):
                $hasTrashed = true;
                $items     = json_decode($order['items_json'], true) ?: [];
                $badge     = $statusBadges[$order['status']] ?? 'badge-pending';
                $createdDt = new DateTime($order['created_at'], $phTz);
                $deletedDt = new DateTime($order['deleted_at'], $phTz);
                $createdAt = $createdDt->format('M j, Y g:i A');
                $deletedAt = $deletedDt->format('M j, Y g:i A');
            ?>
            <div class="order-card is-trashed">
                <div class="order-head">
                    <div class="order-id">Order #<?= $order['id'] ?></div>
                    <div class="order-time"><?= $createdAt ?></div>
                    <span class="status-badge <?= $badge ?>"><?= ucfirst($order['status']) ?></span>
                    <span class="status-badge badge-deleted" style="margin-left:4px;">Deleted</span>
                    <div class="order-customer"><i class="ti ti-user"></i> <?= htmlspecialchars($order['customer_name']) ?></div>
                </div>
                <div class="order-body">
                    <div class="order-items-list">
                        <?php foreach ($items as $item): ?>
                        <div class="order-item-row">
                            <div class="order-item-label"><?= htmlspecialchars($item['name']) ?><span>(<?= htmlspecialchars($item['size']) ?> x<?= intval($item['qty']) ?>)</span></div>
                            <div class="order-item-price">&#8369;<?= number_format($item['price'] * $item['qty'], 2) ?></div>
                        </div>
                        <?php endforeach; ?>
                        <div class="order-total"><span>Total</span><span>&#8369;<?= number_format($order['total'], 2) ?></span></div>
                    </div>
                    <div class="order-meta">
                        <div class="meta-row">
                            <span class="meta-label">Type</span>
                            <span class="meta-value"><?= $order['fulfillment']==='delivery'?'Delivery':'Pickup' ?></span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Payment</span>
                            <span class="meta-value"><?= ucfirst($order['payment_method']) ?></span>
                        </div>
                        <?php if ($order['fulfillment']==='delivery' && !empty($order['delivery_address'])): ?>
                        <div class="meta-row">
                            <span class="meta-label">Address</span>
                            <span class="meta-value"><?= htmlspecialchars($order['delivery_address']) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="meta-deleted">Deleted: <?= $deletedAt ?></div>
                    </div>
                </div>
                <div class="order-foot">
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" name="restore_order" class="btn-restore"><i class="ti ti-arrow-back-up"></i> Restore</button>
                    </form>
                    <form method="POST" onsubmit="return confirm('Permanently delete Order #<?= $order['id'] ?>?')">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" name="force_delete_order" class="btn-force"><i class="ti ti-trash-x"></i> Delete Forever</button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
            <?php if (!$hasTrashed): ?>
                <div class="empty-msg">Recycle bin is empty.</div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function openSidebar(){document.getElementById('sidebar').classList.add('open');document.getElementById('sidebarOverlay').classList.add('show');}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebarOverlay').classList.remove('show');}
</script>
</body>
</html>