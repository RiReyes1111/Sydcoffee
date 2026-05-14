<?php
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

if (isset($_POST['add_item'])) {
    $name         = $_POST['name'];
    $category     = $_POST['category'];
    $price_small  = $_POST['price_small']  ?: null;
    $price_large  = $_POST['price_large']  ?: null;
    $price_single = $_POST['price_single'] ?: null;
    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
            $filename = uniqid('item_', true) . '.' . $ext;
            $dest = "../images/" . $filename;
            move_uploaded_file($_FILES['image']['tmp_name'], $dest);
            $image = "images/" . $filename;
        }
    }
    $stmt = $conn->prepare("INSERT INTO coffee_items (name, category, price_small, price_large, price_single, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddds", $name, $category, $price_small, $price_large, $price_single, $image);
    $stmt->execute();
    header("Location: adminitems.php"); exit();
}

if (isset($_GET['delete'])) {
    $conn->query("UPDATE coffee_items SET deleted_at = NOW() WHERE id=".intval($_GET['delete']));
    header("Location: adminitems.php"); exit();
}
if (isset($_GET['restore'])) {
    $conn->query("UPDATE coffee_items SET deleted_at = NULL WHERE id=".intval($_GET['restore']));
    header("Location: adminitems.php"); exit();
}
if (isset($_GET['force_delete'])) {
    $conn->query("DELETE FROM coffee_items WHERE id=".intval($_GET['force_delete']));
    header("Location: adminitems.php"); exit();
}
if (isset($_GET['toggle'])) {
    $conn->query("UPDATE coffee_items SET is_available = NOT is_available WHERE id=".intval($_GET['toggle']));
    header("Location: adminitems.php"); exit();
}

if (isset($_POST['update_item'])) {
    $id           = intval($_POST['id']);
    $name         = $conn->real_escape_string($_POST['name']);
    $category     = $conn->real_escape_string($_POST['category']);
    $price_small  = $_POST['price_small']  ?: null;
    $price_large  = $_POST['price_large']  ?: null;
    $price_single = $_POST['price_single'] ?: null;
    $image_sql = "";
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
            $filename = uniqid('item_', true) . '.' . $ext;
            $dest = "../images/" . $filename;
            move_uploaded_file($_FILES['image']['tmp_name'], $dest);
            $image_sql = ", image='" . $conn->real_escape_string("images/" . $filename) . "'";
        }
    }
    $conn->query("UPDATE coffee_items SET name='$name', category='$category',
        price_small=".($price_small ?: "NULL").",
        price_large=".($price_large ?: "NULL").",
        price_single=".($price_single ?: "NULL")."
        $image_sql WHERE id=$id");
    header("Location: adminitems.php"); exit();
}

$editItem = null;
if (isset($_GET['edit'])) {
    $result = $conn->query("SELECT * FROM coffee_items WHERE id=".intval($_GET['edit']));
    $editItem = $result->fetch_assoc();
}

$items        = $conn->query("SELECT * FROM coffee_items WHERE deleted_at IS NULL ORDER BY id DESC");
$trashedItems = $conn->query("SELECT * FROM coffee_items WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
$totalActive  = $conn->query("SELECT COUNT(*) as c FROM coffee_items WHERE deleted_at IS NULL AND is_available=1")->fetch_assoc()['c'];
$totalHidden  = $conn->query("SELECT COUNT(*) as c FROM coffee_items WHERE deleted_at IS NULL AND is_available=0")->fetch_assoc()['c'];
$totalTrashed = $conn->query("SELECT COUNT(*) as c FROM coffee_items WHERE deleted_at IS NOT NULL")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manage Items — SYD Coffee</title>
<link rel="icon" type="image/png" href="../images/logosydnobg.png">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<script>window.addEventListener("pageshow",function(e){if(e.persisted)window.location.reload();});</script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
    --cream:#f8f5ef;--cream-dark:#ede8de;--espresso:#1e120a;--brown-mid:#4a3020;
    --brown-light:#7a5c3e;--gold:#c8973a;--gold-light:#e8b84b;--white:#ffffff;
    --text-dark:#1e120a;--text-mid:#4a3020;--text-muted:#9a7a5a;--text-faint:#c8b09a;
    --green:#2d7a4f;--green-light:#e6f7ee;--orange:#d4680a;--orange-light:#fff0e0;
    --red:#c0392b;--red-light:#fdecea;--blue:#2c7be5;--blue-light:#e8f0fd;
    --border:#ede8de;--border-light:#f0ebe2;--shadow:0 2px 16px rgba(30,18,10,0.07);
    --shadow-sm:0 1px 6px rgba(30,18,10,0.05);--sidebar-w:220px;--radius:14px;--radius-sm:10px;
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
.sidebar-user{display:flex;align-items:center;gap:10px;padding:8px;border-radius:8px;}
.user-avatar{width:30px;height:30px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--espresso);flex-shrink:0;}
.user-name{font-size:12px;font-weight:600;color:#e0d0be;}
.user-role{font-size:10px;color:#7a6040;}
.logout-link{display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:8px;color:#c07060;font-size:12px;text-decoration:none;margin-top:4px;transition:all 0.2s;}
.logout-link i{font-size:15px;}
.logout-link:hover{background:rgba(192,57,43,0.08);color:var(--red);}

.main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh;}
.topbar{display:flex;align-items:center;padding:18px 28px;background:var(--white);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:100;gap:12px;box-shadow:var(--shadow-sm);}
.topbar-title{font-size:14px;font-weight:600;color:var(--text-dark);}
.topbar-sub{font-size:11px;color:var(--text-muted);margin-top:2px;}
.menu-toggle{display:none;background:none;border:none;font-size:18px;cursor:pointer;color:var(--text-dark);padding:4px;}
.content{padding:24px 28px;flex:1;}

.stats{display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap;}
.stat-pill{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:16px 22px;flex:1;min-width:110px;border-left:3px solid var(--gold);}
.stat-pill.hidden-stat{border-left-color:var(--orange);}
.stat-pill.trashed-stat{border-left-color:var(--red);}
.stat-num{font-size:26px;font-weight:700;color:var(--text-dark);line-height:1;}
.stat-label{font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;margin-top:4px;}

.layout{display:grid;grid-template-columns:300px 1fr;gap:20px;align-items:start;}

.form-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;position:sticky;top:80px;}
.form-head{display:flex;align-items:center;gap:12px;padding:16px 20px;border-bottom:1px solid var(--border-light);}
.form-head-icon{width:30px;height:30px;border-radius:8px;background:#fdf3e0;display:flex;align-items:center;justify-content:center;color:var(--gold);font-size:15px;flex-shrink:0;}
.form-head-title{font-size:13px;font-weight:600;color:var(--brown-mid);}
.form-body{padding:18px 20px;display:flex;flex-direction:column;gap:14px;}
.form-group label{display:block;font-size:10px;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;}
.form-body input[type="text"],
.form-body input[type="number"],
.form-body select{width:100%;padding:10px 12px;background:var(--cream);border:1px solid var(--border);border-radius:8px;font-family:'Montserrat',sans-serif;font-size:12px;color:var(--text-dark);outline:none;transition:border-color 0.2s;}
.form-body input:focus,.form-body select:focus{border-color:var(--gold);}
.form-body input::placeholder{color:var(--text-muted);}
.price-row{display:grid;grid-template-columns:1fr 1fr;gap:8px;}
.file-zone{border:1px dashed var(--border);border-radius:8px;padding:16px;text-align:center;cursor:pointer;position:relative;background:var(--cream);transition:border-color 0.2s;}
.file-zone:hover{border-color:var(--gold);}
.file-zone input[type="file"]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;}
.file-zone-icon{font-size:22px;color:var(--text-muted);margin-bottom:6px;}
.file-zone-label{font-size:11px;color:var(--text-muted);font-weight:600;}
.file-zone-sub{font-size:10px;color:var(--text-faint);margin-top:3px;}
.current-img{width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:8px;margin-bottom:10px;border:1px solid var(--border);}
.btn-submit{width:100%;padding:11px;border:none;border-radius:30px;font-family:'Montserrat',sans-serif;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:opacity 0.2s;}
.btn-submit:hover{opacity:0.88;}
.btn-add{background:var(--espresso);color:var(--cream);}
.btn-update{background:var(--blue);color:var(--white);}
.btn-cancel{display:block;text-align:center;margin-top:8px;color:var(--red);font-size:12px;text-decoration:none;}
.btn-cancel:hover{text-decoration:underline;}

.right-col{display:flex;flex-direction:column;gap:20px;}
.section-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;}
.section-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border-light);}
.section-title{font-size:12px;font-weight:600;color:var(--brown-mid);}
.section-count{font-size:11px;color:var(--text-muted);}
.section-head.trash-head{background:#fdf8f8;border-bottom-color:#f5e0e0;}
.section-head.trash-head .section-title{color:var(--red);}

.filter-bar{padding:12px 16px;display:flex;gap:6px;flex-wrap:wrap;align-items:center;border-bottom:1px solid var(--border-light);}
.filter-btn{padding:6px 12px;border-radius:20px;font-size:11px;font-weight:600;border:1px solid var(--border);background:transparent;color:var(--text-muted);cursor:pointer;transition:all 0.2s;font-family:'Montserrat',sans-serif;}
.filter-btn:hover{border-color:var(--gold);color:var(--brown-mid);}
.filter-btn.active{background:var(--espresso);border-color:var(--espresso);color:var(--cream);}
.search-wrap{flex:1;min-width:140px;position:relative;}
.search-wrap input{width:100%;padding:7px 12px 7px 32px;border-radius:20px;border:1px solid var(--border);font-family:'Montserrat',sans-serif;font-size:12px;color:var(--text-dark);background:var(--cream);outline:none;transition:border-color 0.2s;}
.search-wrap input:focus{border-color:var(--gold);}
.search-icon{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:14px;pointer-events:none;}

.items-list{padding:12px;display:flex;flex-direction:column;gap:8px;}
.item-card{background:var(--cream);border:1px solid var(--border);border-radius:var(--radius-sm);display:flex;align-items:center;gap:14px;padding:12px 16px;transition:border-color 0.2s;}
.item-card:hover{border-color:var(--gold);}
.item-card.is-hidden{opacity:0.6;}
.item-thumb{width:52px;height:52px;border-radius:8px;object-fit:cover;flex-shrink:0;background:var(--cream-dark);border:1px solid var(--border);}
.item-thumb-ph{width:52px;height:52px;border-radius:8px;flex-shrink:0;background:var(--cream-dark);display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--text-muted);}
.item-info{flex:1;min-width:0;}
.item-name{font-size:13px;font-weight:600;color:var(--text-dark);}
.item-cat{font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;margin-top:2px;}
.item-price{font-size:12px;color:var(--text-muted);margin-top:3px;}
.item-status{padding:4px 10px;border-radius:20px;font-size:10px;font-weight:700;flex-shrink:0;}
.status-live{background:var(--green-light);color:var(--green);}
.status-hidden{background:var(--orange-light);color:var(--orange);}
.status-deleted{background:var(--red-light);color:var(--red);}
.item-actions{display:flex;gap:6px;flex-shrink:0;flex-wrap:wrap;justify-content:flex-end;}
.act-btn{padding:6px 12px;border-radius:20px;font-size:11px;font-weight:600;text-decoration:none;color:var(--white);transition:opacity 0.2s;}
.act-btn:hover{opacity:0.85;}
.act-edit{background:var(--blue);}
.act-toggle{background:var(--orange);}
.act-delete{background:var(--red);}
.act-restore{background:var(--green);}
.act-force{background:var(--brown-mid);}
.empty-msg{padding:28px;text-align:center;color:var(--text-muted);font-size:12px;}
.no-results{padding:24px;text-align:center;color:var(--text-muted);font-size:12px;display:none;}

.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
.sidebar-overlay.show{display:block;}

@media(max-width:900px){.layout{grid-template-columns:1fr;}.form-card{position:static;}}
@media(max-width:768px){
    .sidebar{transform:translateX(-100%);}
    .sidebar.open{transform:translateX(0);}
    .main{margin-left:0;}
    .menu-toggle{display:block;}
    .content{padding:20px 16px;}
    .topbar{padding:14px 16px;}
    .item-card{flex-wrap:wrap;}
    .item-actions{width:100%;justify-content:flex-start;}
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
        <a class="nav-item active" href="adminitems.php"><i class="ti ti-coffee"></i> Items</a>
        <a class="nav-item" href="adminorders.php"><i class="ti ti-clipboard-list"></i> Orders</a>
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
        <button class="menu-toggle" onclick="openSidebar()"><i class="ti ti-menu-2"></i></button>
        <div>
            <div class="topbar-title">Manage Items</div>
            <div class="topbar-sub">Add, edit and manage your menu items</div>
        </div>
    </div>

    <div class="content">
        <div class="stats">
            <div class="stat-pill">
                <div class="stat-num"><?= $totalActive ?></div>
                <div class="stat-label">Available</div>
            </div>
            <div class="stat-pill hidden-stat">
                <div class="stat-num"><?= $totalHidden ?></div>
                <div class="stat-label">Hidden</div>
            </div>
            <div class="stat-pill trashed-stat">
                <div class="stat-num"><?= $totalTrashed ?></div>
                <div class="stat-label">In Bin</div>
            </div>
        </div>

        <div class="layout">
            <div class="form-card">
                <div class="form-head">
                    <div class="form-head-icon"><i class="ti ti-<?= $editItem ? 'edit' : 'plus' ?>"></i></div>
                    <div class="form-head-title"><?= $editItem ? 'Edit Item' : 'Add New Item' ?></div>
                </div>
                <div class="form-body">
                    <form method="POST" enctype="multipart/form-data">
                        <?php if ($editItem): ?>
                            <input type="hidden" name="id" value="<?= $editItem['id'] ?>">
                        <?php endif; ?>
                        <div class="form-group">
                            <label>Item Name</label>
                            <input type="text" name="name" placeholder="e.g. Caramel Latte" value="<?= htmlspecialchars($editItem['name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" id="category" onchange="togglePriceFields()">
                                <option value="cold_coffee" <?= ($editItem['category'] ?? '') === 'cold_coffee' ? 'selected' : '' ?>>Cold Coffee</option>
                                <option value="non_coffee"  <?= ($editItem['category'] ?? '') === 'non_coffee'  ? 'selected' : '' ?>>Non-Coffee & Soda</option>
                                <option value="hot_drinks"  <?= ($editItem['category'] ?? '') === 'hot_drinks'  ? 'selected' : '' ?>>Hot Drinks</option>
                            </select>
                        </div>
                        <div class="form-group" id="price-small-large">
                            <label>Prices</label>
                            <div class="price-row">
                                <input type="number" name="price_small" placeholder="Small (PHP)" value="<?= $editItem['price_small'] ?? '' ?>" step="0.01">
                                <input type="number" name="price_large" placeholder="Large (PHP)" value="<?= $editItem['price_large'] ?? '' ?>" step="0.01">
                            </div>
                        </div>
                        <div class="form-group" id="price-single" style="display:none;">
                            <label>Price</label>
                            <input type="number" name="price_single" placeholder="Price (PHP)" value="<?= $editItem['price_single'] ?? '' ?>" step="0.01">
                        </div>
                        <div class="form-group">
                            <label>Image</label>
                            <?php if ($editItem && !empty($editItem['image'])): ?>
                                <img src="../<?= htmlspecialchars($editItem['image']) ?>" class="current-img" alt="Current image">
                            <?php endif; ?>
                            <div class="file-zone">
                                <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
                                <div class="file-zone-icon"><i class="ti ti-photo-up"></i></div>
                                <div class="file-zone-label"><?= $editItem ? 'Upload new to replace' : 'Choose an image' ?></div>
                                <div class="file-zone-sub">JPG, PNG, WEBP supported</div>
                            </div>
                            <img id="imgPreview" class="current-img" style="display:none;margin-top:10px;" alt="Preview">
                        </div>
                        <?php if ($editItem): ?>
                            <button class="btn-submit btn-update" name="update_item">Update Item</button>
                            <a href="adminitems.php" class="btn-cancel">Cancel Edit</a>
                        <?php else: ?>
                            <button class="btn-submit btn-add" name="add_item">Add Item</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="right-col">
                <div class="section-card">
                    <div class="section-head">
                        <span class="section-title">Menu Items</span>
                        <span class="section-count"><?= $totalActive + $totalHidden ?> items</span>
                    </div>
                    <div class="filter-bar">
                        <button class="filter-btn active" data-filter="all" onclick="filterItems(this)">All</button>
                        <button class="filter-btn" data-filter="cold_coffee" onclick="filterItems(this)">Cold Coffee</button>
                        <button class="filter-btn" data-filter="non_coffee" onclick="filterItems(this)">Non-Coffee</button>
                        <button class="filter-btn" data-filter="hot_drinks" onclick="filterItems(this)">Hot Drinks</button>
                        <div class="search-wrap">
                            <i class="ti ti-search search-icon"></i>
                            <input type="text" id="itemSearch" placeholder="Search items..." oninput="applyFilters()">
                        </div>
                    </div>
                    <div class="items-list" id="itemsList">
                    <?php
                    $hasItems = false;
                    while ($row = $items->fetch_assoc()):
                        $hasItems = true;
                        $catLabels = ['cold_coffee'=>'Cold Coffee','non_coffee'=>'Non-Coffee & Soda','hot_drinks'=>'Hot Drinks'];
                        $catLabel  = $catLabels[$row['category']] ?? $row['category'];
                        $priceStr  = '';
                        if ($row['price_small'])  $priceStr .= 'S: PHP '.number_format($row['price_small'],2);
                        if ($row['price_large'])  $priceStr .= '  L: PHP '.number_format($row['price_large'],2);
                        if ($row['price_single']) $priceStr  = 'PHP '.number_format($row['price_single'],2);
                    ?>
                        <div class="item-card <?= $row['is_available'] ? '' : 'is-hidden' ?>" data-category="<?= $row['category'] ?>" data-name="<?= strtolower(htmlspecialchars($row['name'])) ?>">
                            <?php if (!empty($row['image'])): ?>
                                <img class="item-thumb" src="../<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                            <?php else: ?>
                                <div class="item-thumb-ph"><i class="ti ti-coffee"></i></div>
                            <?php endif; ?>
                            <div class="item-info">
                                <div class="item-name"><?= htmlspecialchars($row['name']) ?></div>
                                <div class="item-cat"><?= $catLabel ?></div>
                                <div class="item-price"><?= $priceStr ?></div>
                            </div>
                            <span class="item-status <?= $row['is_available'] ? 'status-live' : 'status-hidden' ?>">
                                <?= $row['is_available'] ? 'Live' : 'Hidden' ?>
                            </span>
                            <div class="item-actions">
                                <a class="act-btn act-edit"   href="?edit=<?= $row['id'] ?>">Edit</a>
                                <a class="act-btn act-toggle" href="?toggle=<?= $row['id'] ?>"><?= $row['is_available'] ? 'Hide' : 'Show' ?></a>
                                <a class="act-btn act-delete" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Move to recycle bin?')">Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <?php if (!$hasItems): ?>
                        <div class="empty-msg">No items yet. Add your first menu item.</div>
                    <?php endif; ?>
                    </div>
                    <div class="no-results" id="noResults">No items match your search.</div>
                </div>

                <div class="section-card">
                    <div class="section-head trash-head">
                        <span class="section-title"><i class="ti ti-trash" style="margin-right:6px;"></i>Recycle Bin</span>
                        <span class="section-count"><?= $totalTrashed ?> items</span>
                    </div>
                    <div class="items-list">
                    <?php
                    $hasTrashed = false;
                    while ($row = $trashedItems->fetch_assoc()):
                        $hasTrashed = true;
                        $catLabels  = ['cold_coffee'=>'Cold Coffee','non_coffee'=>'Non-Coffee & Soda','hot_drinks'=>'Hot Drinks'];
                        $catLabel   = $catLabels[$row['category']] ?? $row['category'];
                        $priceStr   = '';
                        if ($row['price_small'])  $priceStr .= 'S: PHP '.number_format($row['price_small'],2);
                        if ($row['price_large'])  $priceStr .= '  L: PHP '.number_format($row['price_large'],2);
                        if ($row['price_single']) $priceStr  = 'PHP '.number_format($row['price_single'],2);
                    ?>
                        <div class="item-card is-hidden">
                            <?php if (!empty($row['image'])): ?>
                                <img class="item-thumb" src="../<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                            <?php else: ?>
                                <div class="item-thumb-ph"><i class="ti ti-coffee"></i></div>
                            <?php endif; ?>
                            <div class="item-info">
                                <div class="item-name"><?= htmlspecialchars($row['name']) ?></div>
                                <div class="item-cat"><?= $catLabel ?></div>
                                <div class="item-price"><?= $priceStr ?></div>
                                <div class="item-cat" style="color:var(--red);margin-top:4px;">Deleted: <?= $row['deleted_at'] ?></div>
                            </div>
                            <span class="item-status status-deleted">Deleted</span>
                            <div class="item-actions">
                                <a class="act-btn act-restore" href="?restore=<?= $row['id'] ?>">Restore</a>
                                <a class="act-btn act-force"   href="?force_delete=<?= $row['id'] ?>" onclick="return confirm('Permanently delete? This cannot be undone.')">Delete Forever</a>
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
    </div>
</div>

<script>
let activeFilter='all';
function filterItems(btn){
    if(btn){document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('active'));btn.classList.add('active');activeFilter=btn.dataset.filter;}
    applyFilters();
}
function applyFilters(){
    const search=document.getElementById('itemSearch').value.toLowerCase().trim();
    const cards=document.querySelectorAll('#itemsList .item-card');
    let visible=0;
    cards.forEach(card=>{
        const show=(activeFilter==='all'||card.dataset.category===activeFilter)&&(!search||card.dataset.name.includes(search));
        card.style.display=show?'flex':'none';
        if(show)visible++;
    });
    document.getElementById('noResults').style.display=visible===0?'block':'none';
}
function togglePriceFields(){
    const cat=document.getElementById('category').value;
    document.getElementById('price-small-large').style.display=cat==='hot_drinks'?'none':'block';
    document.getElementById('price-single').style.display=cat==='hot_drinks'?'block':'none';
}
togglePriceFields();
function previewImage(input){
    const preview=document.getElementById('imgPreview');
    if(input.files&&input.files[0]){
        const reader=new FileReader();
        reader.onload=e=>{preview.src=e.target.result;preview.style.display='block';};
        reader.readAsDataURL(input.files[0]);
    }
}
function openSidebar(){document.getElementById('sidebar').classList.add('open');document.getElementById('sidebarOverlay').classList.add('show');}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebarOverlay').classList.remove('show');}
</script>
</body>
</html>