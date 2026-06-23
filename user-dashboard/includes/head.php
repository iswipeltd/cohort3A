<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ADEEEEE | Employee Portal</title>
<link rel="icon" href="img/favicon.ico">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>
:root{
  --primary:#2563eb;
  --primary-dark:#1d4ed8;
  --primary-glow:rgba(37,99,235,0.15);
  --accent:#0ea5e9;
  --accent-glow:rgba(14,165,233,0.12);
  --success:#16a34a;
  --success-bg:rgba(22,163,74,0.1);
  --warning:#d97706;
  --warning-bg:rgba(217,119,6,0.1);
  --danger:#dc2626;
  --danger-bg:rgba(220,38,38,0.1);
  --info:#0284c7;
  --info-bg:rgba(2,132,199,0.1);
  --sidebar:#ffffff;
  --sidebar-hover:#f1f5f9;
  --sidebar-active:#eff6ff;
  --bg:#f8fafc;
  --card:#ffffff;
  --card-hover:#f8fafc;
  --border:#e2e8f0;
  --text:#0f172a;
  --text2:#64748b;
  --muted:#94a3b8;
  --radius:12px;
  --shadow:0 1px 3px rgba(0,0,0,0.06);
  --shadow-lg:0 10px 40px rgba(0,0,0,0.1);
}
*{font-family:'Inter',system-ui,-apple-system,sans-serif;box-sizing:border-box;margin:0;}body{background:var(--bg);color:var(--text);font-size:0.88rem;line-height:1.5;-webkit-font-smoothing:antialiased;}
h1,h2,h3,h4,h5,h6{font-family:'Poppins',system-ui,sans-serif;font-weight:700;letter-spacing:-0.01em;}
::-webkit-scrollbar{width:6px;}::-webkit-scrollbar-track{background:transparent;}::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:10px;}

/* Sidebar */
.sidebar{background:var(--sidebar);width:264px;height:100vh;position:fixed;left:0;top:0;z-index:1040;border-right:1px solid var(--border);overflow-y:auto;}
.sidebar-header{padding:24px 20px;border-bottom:1px solid var(--border);}
.sidebar-logo{font-family:'Poppins';font-weight:800;font-size:1.3rem;color:var(--primary);text-decoration:none;display:flex;align-items:center;gap:12px;}
.sidebar-logo i{font-size:1.4rem;}
.sidebar-user{padding:18px 20px;border-bottom:1px solid var(--border);}
.sidebar-menu{list-style:none;padding:10px 0;margin:0;}
.sidebar-menu li{padding:0 12px;margin-bottom:2px;}
.sidebar-menu a{color:var(--text2);text-decoration:none;padding:10px 14px;border-radius:10px;display:flex;align-items:center;gap:12px;transition:all 0.2s;font-size:0.83rem;font-weight:600;}
.sidebar-menu a:hover{background:var(--sidebar-hover);color:var(--text);}
.sidebar-menu a.active{background:var(--primary);color:#fff;box-shadow:0 4px 14px var(--primary-glow);}
.sidebar-section{color:var(--muted);font-size:0.68rem;text-transform:uppercase;letter-spacing:1.5px;padding:18px 20px 6px;font-weight:700;}
.main-content{margin-left:264px;min-height:100vh;}

/* Topbar */
.topbar{height:64px;background:var(--card);border-bottom:1px solid var(--border);padding:0 28px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:1030;}
.topbar-avatar{width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid var(--border);cursor:pointer;}

/* KPI */
.kpi-card{background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius);padding:24px;transition:all 0.2s;}
.kpi-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-lg);}
.kpi-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;margin-bottom:14px;}
.kpi-value{font-family:'Poppins';font-size:1.8rem;font-weight:800;}
.kpi-label{font-size:0.78rem;color:var(--muted);font-weight:500;}

/* Cards */
.card-modern{background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius);overflow:hidden;}
.card-header-modern{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;}
.card-body-modern{padding:24px;}

/* Forms */
.form-control-mod{border:1.5px solid var(--border);border-radius:10px;padding:11px 15px;font-size:0.86rem;background:var(--card);color:var(--text);transition:all 0.2s;}
.form-control-mod:focus{border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-glow);outline:none;background:var(--card);}
.form-select{background-color:var(--card);color:var(--text);border:1.5px solid var(--border);}
.form-select:focus{border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-glow);}
.form-label-mod{font-size:0.76rem;font-weight:700;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.4px;}

/* Buttons */
.btn-primary-mod{background:var(--primary);border:none;color:#fff;font-weight:700;padding:11px 24px;border-radius:10px;text-decoration:none;display:inline-block;transition:all 0.2s;font-size:0.85rem;}
.btn-primary-mod:hover{background:var(--primary-dark);transform:translateY(-1px);box-shadow:0 8px 24px var(--primary-glow);color:#fff;}
.btn-outline-mod{border:1.5px solid var(--border);color:var(--muted);font-weight:700;padding:11px 24px;border-radius:10px;background:var(--card);text-decoration:none;display:inline-block;transition:all 0.2s;font-size:0.85rem;}
.btn-outline-mod:hover{border-color:var(--primary);color:var(--primary);}

/* Status */
.status-badge{font-size:0.74rem;font-weight:700;padding:5px 12px;border-radius:20px;}
.status-active{background:var(--success-bg);color:var(--success);}
.status-pending{background:var(--warning-bg);color:var(--warning);}
.status-rejected{background:var(--danger-bg);color:var(--danger);}
.status-completed{background:var(--info-bg);color:var(--info);}

/* Page Header */
.page-header{padding:24px 28px;}
.page-title{font-size:1.5rem;font-weight:800;margin-bottom:4px;letter-spacing:-0.02em;}
.page-subtitle{color:var(--muted);font-size:0.88rem;font-weight:500;}

/* Alerts */
.alert-danger{background:var(--danger-bg);color:var(--danger);border:1px solid rgba(220,38,38,0.15);}
.alert-success{background:var(--success-bg);color:var(--success);border:1px solid rgba(22,163,74,0.15);}
.alert-warning{background:var(--warning-bg);color:var(--warning);border:1px solid rgba(217,119,6,0.15);}

/* Quick Tiles */
.quick-tile{background:var(--card);border:1.5px solid var(--border);border-radius:var(--radius);padding:20px;text-align:center;text-decoration:none;color:var(--text);transition:all 0.2s;display:block;}
.quick-tile:hover{transform:translateY(-3px);box-shadow:var(--shadow-lg);border-color:var(--primary);color:var(--primary);}
.quick-tile-icon{width:50px;height:50px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.25rem;margin:0 auto 12px;}

/* Responsive */
@media(max-width:992px){.sidebar{transform:translateX(-100%);transition:0.3s;}.sidebar.show{transform:translateX(0);}.main-content{margin-left:0;}}
</style>
</head>
