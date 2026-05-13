<?php
session_start();
include "config.php";

header("Cache-Control: no-store, no-cache, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: adminlogin.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: adminusers.php"); exit();
}
if (isset($_GET['make_admin'])) {
    $id = intval($_GET['make_admin']);
    if ($id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: adminusers.php"); exit();
}
if (isset($_GET['make_user'])) {
    $id = intval($_GET['make_user']);
    if ($id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("UPDATE users SET role = 'user' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    header("Location: adminusers.php"); exit();
}

$users       = $conn->query("SELECT * FROM users ORDER BY id DESC");
$totalUsers  = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='user'")->fetch_assoc()['c'];
$totalAdmins = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='admin'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Users Management — SYD Coffee</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
    --cream:#f8f5ef;--cream-dark:#ede8de;--espresso:#1e120a;--brown-mid:#4a3020;
    --brown-light:#7a5c3e;--gold:#c8973a;--white:#ffffff;
    --text-dark:#1e120a;--text-muted:#9a7a5a;--text-faint:#c8b09a;
    --green:#2d7a4f;--green-light:#e6f7ee;
    --red:#c0392b;--red-light:#fdecea;--blue:#2c7be5;--blue-light:#e8f0fd;
    --border:#ede8de;--border-light:#f0ebe2;
    --shadow:0 2px 16px rgba(30,18,10,0.07);--shadow-sm:0 1px 6px rgba(30,18,10,0.05);
    --sidebar-w:220px;--radius:14px;--radius-sm:10px;
}
body{font-family:'Montserrat',sans-serif;background:var(--cream);color:var(--text-dark);min-height:100vh;display:flex;font-size:13px;}

.sidebar{width:var(--sidebar-w);background:var(--espresso);min-height:100vh;display:flex;flex-direction:column;position:fixed;top:0;left:0;z-index:200;transition:transform 0.3s;}
.sidebar-logo{padding:26px 20px 22px;border-bottom:1px solid rgba(255,255,255,0.08);}
.sidebar-wordmark{font-family:'Playfair Display',serif;font-size:18px;color:var(--cream);font-style:italic;}
.sidebar-sub{font-size:9px;letter-spacing:2px;color:#7a6040;text-transform:uppercase;margin-top:3px;}
.sidebar-nav{padding:18px 12px 8px;}
.sidebar-nav-label{font-size:9px;font-weight:700;letter-spacing:2px;color:#4a3020;text-transform:uppercase;padding:0 8px;margin-bottom:8px;}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 10px;border-radius:8px;color:#7a6040;font-size:12px;font-weight:500;text-decoration:none;transition:all 0.2s;margin-bottom:2px;}
.nav-item i{font-size:15px;}
.nav-item:hover{background:rgba(255,255,255,0.05);color:var(--gold);}
.nav-item.active{background:rgba(200,151,58,0.14);color:#e8b84b;}
.sidebar-spacer{flex:1;}
.sidebar-bottom{padding:14px 12px;border-top:1px solid rgba(255,255,255,0.07);}
.sidebar-user{display:flex;align-items:center;gap:10px;padding:8px;border-radius:8px;cursor:pointer;}
.sidebar-user:hover{background:rgba(255,255,255,0.04);}
.user-avatar{width:30px;height:30px;border-radius:50%;background:var(--gold);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--espresso);flex-shrink:0;}
.user-name{font-size:12px;font-weight:600;color:var(--text-faint);}
.user-role{font-size:10px;color:#4a3020;}
.logout-link{display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:8px;color:#7a3a2a;font-size:12px;text-decoration:none;margin-top:4px;transition:all 0.2s;}
.logout-link i{font-size:15px;}
.logout-link:hover{background:rgba(192,57,43,0.08);color:var(--red);}

.main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh;}
.topbar{display:flex;align-items:center;padding:18px 28px;background:var(--white);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:100;gap:12px;box-shadow:var(--shadow-sm);}
.topbar-title{font-size:14px;font-weight:600;color:var(--text-dark);}
.topbar-sub{font-size:11px;color:var(--text-muted);margin-top:2px;}
.menu-toggle{display:none;background:none;border:none;font-size:18px;cursor:pointer;color:var(--text-dark);padding:4px;}

.content{padding:24px 28px;flex:1;max-width:960px;width:100%;}

.stats{display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap;}
.stat-pill{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:16px 22px;flex:1;min-width:110px;border-left:3px solid var(--gold);}
.stat-pill.admin-stat{border-left-color:var(--brown-light);}
.stat-pill.total-stat{border-left-color:var(--espresso);}
.stat-num{font-size:26px;font-weight:700;color:var(--text-dark);line-height:1;}
.stat-label{font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:1px;margin-top:4px;}

.section-card{background:var(--white);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;}
.section-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border-light);}
.section-title{font-size:13px;font-weight:600;color:var(--brown-mid);}
.section-count{font-size:11px;color:var(--text-muted);}

.search-wrap{padding:14px 16px;border-bottom:1px solid var(--border-light);position:relative;}
.search-icon{position:absolute;left:28px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:15px;pointer-events:none;}
.search-input{width:100%;padding:10px 12px 10px 36px;border:1px solid var(--border);border-radius:8px;font-family:'Montserrat',sans-serif;font-size:12px;background:var(--cream);color:var(--text-dark);outline:none;transition:border-color 0.2s;}
.search-input:focus{border-color:var(--gold);}

.user-list{padding:16px;display:flex;flex-direction:column;gap:10px;}
.user-card{background:var(--cream);border:1px solid var(--border);border-radius:var(--radius-sm);padding:14px 18px;display:flex;align-items:center;gap:14px;transition:border-color 0.2s;}
.user-card:hover{border-color:var(--gold);}
.user-avatar-lg{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:var(--white);flex-shrink:0;text-transform:uppercase;}
.avatar-user{background:var(--blue);}
.avatar-admin{background:var(--espresso);}
.user-info{flex:1;min-width:0;}
.user-name-txt{font-size:13px;font-weight:600;color:var(--text-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.user-meta{font-size:11px;color:var(--text-muted);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.user-badge{padding:4px 12px;border-radius:20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;flex-shrink:0;}
.badge-member{background:var(--blue-light);color:var(--blue);}
.badge-admin{background:#f5ede0;color:var(--brown-mid);}
.badge-you{background:var(--green-light);color:var(--green);}
.user-actions{display:flex;gap:6px;flex-shrink:0;}
.action-btn{padding:6px 14px;border-radius:20px;font-size:11px;font-weight:600;text-decoration:none;transition:opacity 0.2s;}
.action-btn:hover{opacity:0.85;}
.btn-promote{background:var(--espresso);color:var(--cream);}
.btn-demote{background:var(--cream-dark);color:var(--brown-mid);border:1px solid var(--border);}
.btn-delete{background:var(--red-light);color:var(--red);border:1px solid #f5c6c2;}
.current-label{font-size:11px;color:var(--green);font-weight:700;background:var(--green-light);padding:6px 12px;border-radius:20px;}

.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:199;}
.sidebar-overlay.show{display:block;}

@media(max-width:768px){
    .sidebar{transform:translateX(-100%);}
    .sidebar.open{transform:translateX(0);}
    .main{margin-left:0;}
    .menu-toggle{display:block;}
    .content{padding:20px 16px;}
    .topbar{padding:14px 16px;}
    .user-card{flex-wrap:wrap;}
    .user-actions{width:100%;justify-content:flex-end;}
}
</style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-wordmark">SYD Coffee</div>
        <div class="sidebar-sub">Admin Panel</div>
    </div>
    <div class="sidebar-nav">
        <div class="sidebar-nav-label">Navigation</div>
        <a class="nav-item" href="admin.php"><i class="ti ti-layout-dashboard"></i> Dashboard</a>
        <a class="nav-item active" href="adminusers.php"><i class="ti ti-users"></i> Users</a>
        <a class="nav-item" href="adminitems.php"><i class="ti ti-coffee"></i> Items</a>
        <a class="nav-item" href="adminorders.php"><i class="ti ti-clipboard-list"></i> Orders</a>
        <a class="nav-item" href="menu.php" target="_blank"><i class="ti ti-external-link"></i> View Menu</a>
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
        <a class="logout-link" href="logout.php"><i class="ti ti-logout"></i> Logout</a>
    </div>
</div>

<div class="main">
    <div class="topbar">
        <button class="menu-toggle" onclick="openSidebar()"><i class="ti ti-menu-2"></i></button>
        <div>
            <div class="topbar-title">Users Management</div>
            <div class="topbar-sub">Manage accounts and roles</div>
        </div>
    </div>

    <div class="content">
        <div class="stats">
            <div class="stat-pill total-stat">
                <div class="stat-num"><?= $totalUsers + $totalAdmins ?></div>
                <div class="stat-label">Total Accounts</div>
            </div>
            <div class="stat-pill">
                <div class="stat-num"><?= $totalUsers ?></div>
                <div class="stat-label">Members</div>
            </div>
            <div class="stat-pill admin-stat">
                <div class="stat-num"><?= $totalAdmins ?></div>
                <div class="stat-label">Admins</div>
            </div>
        </div>

        <div class="section-card">
            <div class="section-head">
                <span class="section-title">All Accounts</span>
                <span class="section-count"><?= $totalUsers + $totalAdmins ?> registered</span>
            </div>
            <div class="search-wrap">
                <i class="ti ti-search search-icon"></i>
                <input class="search-input" type="text" id="searchInput" placeholder="Search by name, email or username..." oninput="filterUsers()">
            </div>
            <div class="user-list" id="userList">
            <?php while ($u = $users->fetch_assoc()):
                $initial = strtoupper(substr($u['name'], 0, 1));
                $isSelf  = ($u['id'] == $_SESSION['user_id']);
                $isAdmin = ($u['role'] === 'admin');
            ?>
                <div class="user-card"
                     data-name="<?= htmlspecialchars(strtolower($u['name'])) ?>"
                     data-email="<?= htmlspecialchars(strtolower($u['email'])) ?>"
                     data-username="<?= htmlspecialchars(strtolower($u['username'])) ?>">
                    <div class="user-avatar-lg <?= $isAdmin ? 'avatar-admin' : 'avatar-user' ?>"><?= $initial ?></div>
                    <div class="user-info">
                        <div class="user-name-txt"><?= htmlspecialchars($u['name']) ?></div>
                        <div class="user-meta"><?= htmlspecialchars($u['email']) ?> &middot; @<?= htmlspecialchars($u['username']) ?></div>
                    </div>
                    <?php if ($isSelf): ?>
                        <span class="user-badge badge-you">You</span>
                    <?php elseif ($isAdmin): ?>
                        <span class="user-badge badge-admin">Admin</span>
                    <?php else: ?>
                        <span class="user-badge badge-member">Member</span>
                    <?php endif; ?>
                    <div class="user-actions">
                        <?php if ($isSelf): ?>
                            <span class="current-label">Current Admin</span>
                        <?php else: ?>
                            <?php if ($isAdmin): ?>
                                <a class="action-btn btn-demote" href="adminusers.php?make_user=<?= $u['id'] ?>">Make Member</a>
                            <?php else: ?>
                                <a class="action-btn btn-promote" href="adminusers.php?make_admin=<?= $u['id'] ?>">Make Admin</a>
                            <?php endif; ?>
                            <a class="action-btn btn-delete" href="adminusers.php?delete=<?= $u['id'] ?>" onclick="return confirm('Delete <?= htmlspecialchars($u['name']) ?>? This cannot be undone.')">Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<script>
function filterUsers(){
    const q=document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.user-card').forEach(card=>{
        const match=card.dataset.name.includes(q)||card.dataset.email.includes(q)||card.dataset.username.includes(q);
        card.style.display=match?'':'none';
    });
}
function openSidebar(){document.getElementById('sidebar').classList.add('open');document.getElementById('sidebarOverlay').classList.add('show');}
function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebarOverlay').classList.remove('show');}
</script>
</body>
</html>