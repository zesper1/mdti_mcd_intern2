<style>
    :root { --sidebar-width-collapsed: 70px; --sidebar-width-expanded: 260px; --top-navbar-height: 60px; }
    .sidebar { 
        min-width: var(--sidebar-width-collapsed); max-width: var(--sidebar-width-collapsed); 
        min-height: 100vh; background-color: #212529; transition: all 0.3s ease; 
        position: sticky; top: 0; z-index: 1000; display: flex; flex-direction: column; 
    }
    .sidebar:hover { min-width: var(--sidebar-width-expanded); max-width: var(--sidebar-width-expanded); }
    .sidebar .nav-link { color: rgba(255, 255, 255, 0.75); padding: 1rem; display: flex; align-items: center; white-space: nowrap; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background-color: rgba(255, 255, 255, 0.1); }
    .sidebar .nav-link i { font-size: 1.25rem; min-width: 30px; text-align: center; margin-right: 10px; }
    .sidebar-text { opacity: 0; visibility: hidden; transition: opacity 0.2s ease; margin-left: 10px; }
    .sidebar:hover .sidebar-text { opacity: 1; visibility: visible; }
</style>

<nav class="sidebar">
    <div class="sidebar-brand" style="height: var(--top-navbar-height); display: flex; align-items: center; justify-content: center; color: white; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <i class="fas fa-shield-alt fa-lg"></i>
        <span class="sidebar-text fw-bold fs-5"><?php echo APP_NAME; ?></span>
    </div>

    <ul class="nav nav-pills flex-column mb-auto mt-3">
        <li class="nav-item">
            <a href="/pages/Superadmin/dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="/pages/Superadmin/forms/catalog.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'catalog.php' ? 'active' : ''; ?>">
                <i class="fas fa-book"></i>
                <span class="sidebar-text">Catalogs</span>
            </a>
        </li>
        </ul>
</nav>