<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('../auth/config.php');

header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['logged_in']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: adminlogin.php");
    exit();
}

$adminName = isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Admin';

$userCount      = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='user'")->fetch_assoc()['c'];
$adminCount     = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='admin'")->fetch_assoc()['c'];
$itemCount      = $conn->query("SELECT COUNT(*) as c FROM coffee_items WHERE deleted_at IS NULL")->fetch_assoc()['c'];
$availableCount = $conn->query("SELECT COUNT(*) as c FROM coffee_items WHERE is_available=1 AND deleted_at IS NULL")->fetch_assoc()['c'];
$hiddenCount    = $conn->query("SELECT COUNT(*) as c FROM coffee_items WHERE is_available=0 AND deleted_at IS NULL")->fetch_assoc()['c'];
$trashedCount   = $conn->query("SELECT COUNT(*) as c FROM coffee_items WHERE deleted_at IS NOT NULL")->fetch_assoc()['c'];

$recentItems = $conn->query("SELECT * FROM coffee_items WHERE deleted_at IS NULL ORDER BY id DESC LIMIT 6");

$currentHour = (int)date('H');
$greeting = $currentHour < 12 ? 'Good morning' : ($currentHour < 17 ? 'Good afternoon' : 'Good evening');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Dashboard — SYD Coffee</title>
<link rel="icon" type="image/png" href="../images/logosydnobg.png">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<script>window.addEventListener("pageshow",function(e){if(e.persisted)window.location.reload();});</script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
    --cream:#f8f5ef;
    --cream-dark:#ede8de;
    --espresso:#1e120a;
    --brown-mid:#4a3020;
    --brown-light:#7a5c3e;
    --gold:#c8973a;
    --gold-light:#e8b84b;
    --white:#ffffff;
    --text-dark:#1e120a;
    --text-mid:#4a3020;
    --text-muted:#9a7a5a;
    --text-faint:#c8b09a;
    --green:#2d7a4f;
    --green-light:#e6f7ee;
    --orange:#d4680a;
    --orange-light:#fff0e0;
    --red:#c0392b;
    --red-light:#fdecea;
    --blue:#2c7be5;
    --blue-light:#e8f0fd;
    --border:#ede8de;
    --border-light:#f0ebe2;
    --shadow:0 2px 16px rgba(30,18,10,0.07);
    --shadow-sm:0 1px 6px rgba(30,18,10,0.05);
    --sidebar-w:220px;
    --radius:14px;
    --radius-sm:10px;
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
.topbar{display:flex;align-items:center;justify-content:space-between;padding:18px 28px;background:var(--white);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:100;box-shadow:var(--shadow-sm);}
.topbar-left{display:flex;align-items:center;gap:12px;}
.topbar-title{font-size:14px;font-weight:600;color:var(--text-dark);}
.topbar-sub{font-size:11px;color:var(--text-muted);margin-top:2px;}
.topbar-right{display:flex;align-items:center;gap:10px;}
.topbar-clock{font-size:12px;color:var(--text-muted);background:var(--cream);border:1px solid var(--border);padding:6px 14px;border-radius:20px;font-variant-numeric:tabular-nums;}
.topbar-bell{width:34px;height:34px;border-radius:8px;background:var(--cream);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--text-muted);cursor:pointer;font-size:16px;transition:all 0.2s;}
.topbar-bell:hover{border-color:var(--gold);color:var(--gold);}
.menu-toggle{display:none;background:none;border:none;font-size:18px;cursor:pointer;color:var(--text-dark);padding:4px;}

.content{padding:24px 28px;flex:1;}

.banner{background:#3d2a1a;border-radius:var(--radius);padding:24px 28px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:16px;}
.banner-left{display:flex;align-items:center;gap:18px;}
.banner-accent{width:2px;height:42px;background:var(--gold);border-radius:0;flex-shrink:0;}
.banner-title{font-family:'Playfair Display',serif;font-size:18px;color:var(--cream);font-style:italic;margin-bottom:4px;}
.banner-sub{font-size:11px;color:#a08060;}
.banner-actions{display:flex;gap:8px;flex-shrink:0;}
.btn-gold{background:var(--gold);color:var(--espresso);padding:9px 22px;border-radius:30px;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;text-decoration:none;border:none;cursor:pointer;font-family:'Montserrat',sans-serif;transition:opacity 0.2s;}
.btn-gold:hover{opacity:0.88;}
.btn-outline{background:transparent;color:var(--text-faint);padding:9px 22px;border-radius:30px;font-size:11px;font-weight:600;text-decoration:none;border:1px solid rgba(255,255,255,0.18);font-family:'Montserrat',sans-serif;transition:opacity 0.2s;}
.btn-outline:hover{opacity:0.8;}

.stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px;}
.stat-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:18px 20px;text-decoration:none;color:inherit;display:block;transition:border-color 0.2s,transform 0.2s;}
.stat-card:hover{border-color:var(--gold);transform:translateY(-2px);}
.stat-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;}
.stat-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;}
.stat-icon.blue{background:var(--blue-light);color:var(--blue);}
.stat-icon.brown{background:#f5ede0;color:var(--brown-light);}
.stat-icon.gold{background:#fdf3e0;color:var(--gold);}
.stat-icon.green{background:var(--green-light);color:var(--green);}
.stat-icon.orange{background:var(--orange-light);color:var(--orange);}
.stat-icon.red{background:var(--red-light);color:var(--red);}
.stat-badge{font-size:10px;font-weight:600;}
.stat-badge.blue{color:var(--blue);}
.stat-badge.brown{color:var(--brown-light);}
.stat-badge.gold{color:var(--gold);}
.stat-badge.green{color:var(--green);}
.stat-badge.orange{color:var(--orange);}
.stat-badge.red{color:var(--red);}
.stat-num{font-size:28px;font-weight:700;color:var(--text-dark);line-height:1;}
.stat-label{font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;margin-top:4px;}
.stat-divider{width:100%;height:1px;background:var(--border-light);margin-top:12px;}

.bottom-grid{display:grid;grid-template-columns:1fr 210px;gap:16px;}
.section-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;}
.section-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border-light);}
.section-title{font-size:12px;font-weight:600;color:var(--brown-mid);}
.section-link{font-size:11px;color:var(--gold);text-decoration:none;}
.section-link:hover{text-decoration:underline;}

.items-list{padding:12px;display:flex;flex-direction:column;gap:8px;}
.item-row{display:flex;align-items:center;gap:12px;background:var(--cream);border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px 14px;}
.item-thumb{width:38px;height:38px;border-radius:7px;object-fit:cover;flex-shrink:0;background:var(--cream-dark);}
.item-thumb-ph{width:38px;height:38px;border-radius:7px;flex-shrink:0;background:var(--cream-dark);display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:15px;}
.item-info{flex:1;min-width:0;}
.item-name{font-size:12px;font-weight:600;color:var(--text-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.item-cat{font-size:10px;color:var(--text-muted);margin-top:2px;text-transform:uppercase;letter-spacing:0.5px;}
.status-pill{padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;flex-shrink:0;}
.pill-live{background:var(--green-light);color:var(--green);}
.pill-hidden{background:var(--orange-light);color:var(--orange);}
.empty-msg{padding:28px;text-align:center;color:var(--text-muted);font-size:12px;}

.quick-links{padding:12px;display:flex;flex-direction:column;gap:6px;}
.quick-link{display:flex;align-items:center;justify-content:space-between;padding:11px 14px;background:var(--cream);border:1px solid var(--border);border-radius:var(--radius-sm);text-decoration:none;transition:all 0.2s;}
.quick-link:hover{background:var(--espresso);border-color:var(--espresso);}
.quick-link span{font-size:12px;font-weight:500;color:var(--brown-mid);}
.quick-link i{color:var(--text-muted);font-size:14px;}
.quick-link:hover span{color:var(--cream);}
.quick-link:hover i{color:var(--gold);}
.quick-link.danger span{color:var(--red);}
.quick-link.danger{border-color:#fdecea;}
.quick-link.danger:hover{background:var(--red);border-color:var(--red);}
.quick-link.danger:hover span{color:var(--white);}
.quick-link.danger:hover i{color:var(--white);}

.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
.sidebar-overlay.show{display:block;}

@media(max-width:900px){.stats-grid{grid-template-columns:repeat(2,1fr);}.bottom-grid{grid-template-columns:1fr;}}
@media(max-width:768px){
    .sidebar{transform:translateX(-100%);}
    .sidebar.open{transform:translateX(0);}
    .main{margin-left:0;}
    .menu-toggle{display:block;}
    .content{padding:20px 16px;}
    .topbar{padding:14px 16px;}
    .banner-actions{display:none;}
}
@media(max-width:540px){.stats-grid{grid-template-columns:1fr;}}
    
    
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
        <a class="nav-item active" href="admin.php"><i class="ti ti-layout-dashboard"></i> Dashboard</a>
        <a class="nav-item" href="adminusers.php"><i class="ti ti-users"></i> Users</a>
        <a class="nav-item" href="adminitems.php"><i class="ti ti-coffee"></i> Items</a>
        <a class="nav-item" href="adminorders.php"><i class="ti ti-clipboard-list"></i> Orders</a>
        <a class="nav-item" href="../menu.php" target="_blank"><i class="ti ti-external-link"></i> View Menu</a>
    </div>
    <div class="sidebar-spacer"></div>
    <div class="sidebar-bottom">
        <div class="sidebar-user">
            <div class="user-avatar"><?= strtoupper(substr($adminName,0,1)) ?></div>
            <div>
                <div class="user-name"><?= $adminName ?></div>
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
                <div class="topbar-title"><?= $greeting ?>, <?= $adminName ?></div>
                <div class="topbar-sub"><?= date('l, F j') ?> — SYD Coffee Admin</div>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-clock" id="liveClock"></div>
            <div class="topbar-bell"><i class="ti ti-bell"></i></div>
        </div>
    </div>

    <div class="content">
        <div class="banner">
            <div class="banner-left">
                <div class="banner-accent"></div>
                <div>
                    <div class="banner-title">Welcome back, <?= $adminName ?>!</div>
                    <div class="banner-sub">Here's what's happening with SYD Coffee today.</div>
                </div>
            </div>
            <div class="banner-actions">
                <a href="adminitems.php" class="btn-gold">+ Add Item</a>
                <a href="adminusers.php" class="btn-outline">Manage Users</a>
            </div>
        </div>

        <div class="stats-grid">
            <a class="stat-card" href="adminusers.php">
                <div class="stat-top">
                    <div class="stat-icon blue"><i class="ti ti-users"></i></div>
                    <span class="stat-badge blue">Members</span>
                </div>
                <div class="stat-num"><?= $userCount ?></div>
                <div class="stat-label">Registered Users</div>
                <div class="stat-divider"></div>
            </a>
            <a class="stat-card" href="adminusers.php">
                <div class="stat-top">
                    <div class="stat-icon brown"><i class="ti ti-shield"></i></div>
                    <span class="stat-badge brown">Admins</span>
                </div>
                <div class="stat-num"><?= $adminCount ?></div>
                <div class="stat-label">Admin Accounts</div>
                <div class="stat-divider"></div>
            </a>
            <a class="stat-card" href="adminitems.php">
                <div class="stat-top">
                    <div class="stat-icon gold"><i class="ti ti-coffee"></i></div>
                    <span class="stat-badge gold">Menu</span>
                </div>
                <div class="stat-num"><?= $itemCount ?></div>
                <div class="stat-label">Total Items</div>
                <div class="stat-divider"></div>
            </a>
            <a class="stat-card" href="adminitems.php">
                <div class="stat-top">
                    <div class="stat-icon green"><i class="ti ti-eye"></i></div>
                    <span class="stat-badge green">Live</span>
                </div>
                <div class="stat-num"><?= $availableCount ?></div>
                <div class="stat-label">Live on Menu</div>
                <div class="stat-divider"></div>
            </a>
            <a class="stat-card" href="adminitems.php">
                <div class="stat-top">
                    <div class="stat-icon orange"><i class="ti ti-eye-off"></i></div>
                    <span class="stat-badge orange">Hidden</span>
                </div>
                <div class="stat-num"><?= $hiddenCount ?></div>
                <div class="stat-label">Hidden Items</div>
                <div class="stat-divider"></div>
            </a>
            <a class="stat-card" href="adminitems.php">
                <div class="stat-top">
                    <div class="stat-icon red"><i class="ti ti-trash"></i></div>
                    <span class="stat-badge red">Bin</span>
                </div>
                <div class="stat-num"><?= $trashedCount ?></div>
                <div class="stat-label">In Recycle Bin</div>
                <div class="stat-divider"></div>
            </a>
        </div>

        <div class="bottom-grid">
            <div class="section-card">
                <div class="section-head">
                    <span class="section-title">Recent Menu Items</span>
                    <a class="section-link" href="adminitems.php">View all &rsaquo;</a>
                </div>
                <div class="items-list">
                <?php if ($recentItems && $recentItems->num_rows > 0):
                    while ($row = $recentItems->fetch_assoc()):
                        $catLabels = ['cold_coffee'=>'Cold Coffee','non_coffee'=>'Non-Coffee & Soda','hot_drinks'=>'Hot Drinks'];
                        $catLabel  = $catLabels[$row['category']] ?? $row['category'];
                ?>
                    <div class="item-row">
                        <?php if (!empty($row['image'])): ?>
                            <img class="item-thumb" src="../<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                        <?php else: ?>
                            <div class="item-thumb-ph"><i class="ti ti-coffee"></i></div>
                        <?php endif; ?>
                        <div class="item-info">
                            <div class="item-name"><?= htmlspecialchars($row['name']) ?></div>
                            <div class="item-cat"><?= $catLabel ?></div>
                        </div>
                        <span class="status-pill <?= $row['is_available'] ? 'pill-live' : 'pill-hidden' ?>">
                            <?= $row['is_available'] ? 'Live' : 'Hidden' ?>
                        </span>
                    </div>
                <?php endwhile; else: ?>
                    <div class="empty-msg">No items yet.</div>
                <?php endif; ?>
                </div>
            </div>

            <div class="section-card">
                <div class="section-head">
                    <span class="section-title">Quick Actions</span>
                </div>
                <div class="quick-links">
                    <a class="quick-link" href="adminitems.php"><span>Add New Item</span><i class="ti ti-chevron-right"></i></a>
                    <a class="quick-link" href="adminusers.php"><span>Manage Users</span><i class="ti ti-chevron-right"></i></a>
                    <a class="quick-link" href="adminitems.php"><span>Manage Items</span><i class="ti ti-chevron-right"></i></a>
                    <a class="quick-link" href="../menu.php" target="_blank"><span>View Live Menu</span><i class="ti ti-chevron-right"></i></a>
                    <a class="quick-link danger" href="../logout.php"><span>Logout</span><i class="ti ti-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
    
<script>
function updateClock(){
    const now=new Date(),pad=v=>String(v).padStart(2,'0'),el=document.getElementById('liveClock');
    if(el)el.textContent=pad(now.getHours())+':'+pad(now.getMinutes())+':'+pad(now.getSeconds());
}
updateClock();setInterval(updateClock,1000);
function openSidebar(){document.getElementById('sidebar').classList.add('open');document.getElementById('sidebarOverlay').classList.add('show');}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebarOverlay').classList.remove('show');}
</script>
</body>
</html>
