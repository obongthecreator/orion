<?php
if ( ! class_exists( 'Orion_Auth' ) ) {
    wp_redirect( '/orion/login' );
    exit;
}
Orion_Auth::require_login();
$current_user = Orion_Auth::get_current_user();
$user_name    = $current_user['display_name'] ?? $current_user['username'] ?? 'User';
$user_role    = $current_user['role'] ?? 'staff';
$is_admin     = in_array( $user_role, [ 'admin', 'super_admin' ], true );
$initials     = strtoupper( implode( '', array_map( fn($w) => $w[0], array_slice( explode( ' ', trim($user_name) ), 0, 2 ) ) ) );
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home — Orion Brothers</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
  <link rel="stylesheet" href="/wp-content/plugins/orion-inventory/assets/css/main.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { navy: '#0B1B3C', cyan: '#32EDFF' },
          fontFamily: { inter: ['Inter', 'sans-serif'] }
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0B1B3C 0%, #060f22 100%); min-height: 100vh; }

    /* Glassmorphism header */
    .glass-header {
      background: rgba(11,27,60,0.82);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      border-bottom: 1px solid rgba(50,237,255,0.14);
      box-shadow: 0 2px 32px rgba(0,0,0,0.3);
    }

    /* Glassmorphism nav card */
    .nav-card {
      background: rgba(11,27,60,0.65);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(50,237,255,0.15);
      border-radius: 1.25rem;
      padding: 1.75rem 1.5rem;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 0.75rem;
      text-decoration: none;
      transition: transform 0.22s cubic-bezier(.34,1.56,.64,1), border-color 0.2s, box-shadow 0.2s, background 0.2s;
      position: relative;
      overflow: hidden;
    }
    .nav-card::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(50,237,255,0.04) 0%, transparent 60%);
      opacity: 0;
      transition: opacity 0.2s;
    }
    .nav-card:hover {
      transform: translateY(-4px) scale(1.02);
      border-color: rgba(50,237,255,0.45);
      box-shadow: 0 12px 40px rgba(0,0,0,0.35), 0 0 0 1px rgba(50,237,255,0.12), 0 0 24px rgba(50,237,255,0.1);
      background: rgba(11,27,60,0.82);
    }
    .nav-card:hover::before { opacity: 1; }
    .nav-card:active { transform: translateY(-1px) scale(1.005); }

    .nav-card .icon-wrap {
      width: 3rem; height: 3rem;
      border-radius: 0.875rem;
      display: flex; align-items: center; justify-content: center;
      background: rgba(50,237,255,0.12);
      border: 1px solid rgba(50,237,255,0.2);
      margin-bottom: 0.25rem;
    }

    /* Letter animation */
    .letter-animate span {
      display: inline-block;
      opacity: 0;
      transform: translateY(20px);
      animation: letter-in 0.5s cubic-bezier(.34,1.56,.64,1) forwards;
    }
    @keyframes letter-in {
      to { opacity: 1; transform: translateY(0); }
    }

    /* Avatar */
    .user-avatar {
      width: 2.5rem; height: 2.5rem;
      border-radius: 50%;
      background: linear-gradient(135deg, #32EDFF, #0099b8);
      color: #0B1B3C;
      font-weight: 800;
      font-size: 0.875rem;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
      border: 2px solid rgba(50,237,255,0.35);
    }

    /* Logout button */
    .btn-logout {
      display: inline-flex; align-items: center; gap: 0.5rem;
      padding: 0.5rem 1.25rem;
      border-radius: 9999px;
      border: 1px solid rgba(255,80,80,0.35);
      background: rgba(255,80,80,0.1);
      color: #ff9898;
      font-size: 0.875rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, border-color 0.2s, transform 0.15s;
    }
    .btn-logout:hover {
      background: rgba(255,80,80,0.2);
      border-color: rgba(255,80,80,0.6);
      transform: translateY(-1px);
    }

    /* Floating orbs */
    .orb { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; z-index: 0; }

    /* Scroll area */
    .main-content { position: relative; z-index: 1; }
  </style>
</head>
<body>
  <!-- Background orbs -->
  <div class="orb" style="width:500px;height:500px;background:rgba(50,237,255,0.04);top:-100px;right:-150px;"></div>
  <div class="orb" style="width:400px;height:400px;background:rgba(50,237,255,0.03);bottom:-80px;left:-100px;"></div>

  <!-- Sticky Header -->
  <header class="glass-header sticky top-0 z-50 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto flex items-center justify-between h-16 gap-4">
      <!-- Logo -->
      <div class="flex items-center gap-2.5 flex-shrink-0">
        <div class="flex items-center justify-center w-9 h-9 rounded-xl" style="background:rgba(50,237,255,0.12);border:1px solid rgba(50,237,255,0.25);">
          <iconify-icon icon="solar:box-bold" style="color:#32EDFF;font-size:1.25rem;"></iconify-icon>
        </div>
        <span class="font-black text-white text-lg hidden sm:block">Orion <span style="color:#32EDFF;">Brothers</span></span>
      </div>

      <!-- Clock & Date — center -->
      <div class="flex flex-col items-center flex-1 min-w-0">
        <div id="orion-clock" class="text-white font-bold text-xl sm:text-2xl tabular-nums leading-none" aria-live="polite"></div>
        <div id="orion-date" class="text-xs mt-0.5" style="color:rgba(255,255,255,0.45);"></div>
      </div>

      <!-- User info -->
      <div class="flex items-center gap-2.5 flex-shrink-0">
        <div class="user-avatar" aria-label="User avatar"><?php echo htmlspecialchars($initials); ?></div>
        <div class="hidden sm:block text-right">
          <div class="text-white text-sm font-semibold leading-tight"><?php echo htmlspecialchars($user_name); ?></div>
          <div class="text-xs capitalize leading-tight" style="color:rgba(50,237,255,0.7);"><?php echo htmlspecialchars(str_replace('_', ' ', $user_role)); ?></div>
        </div>
      </div>
    </div>
  </header>

  <!-- Main content -->
  <main class="main-content max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Welcome heading (letter-by-letter animation) -->
    <div class="mb-10">
      <h2 class="text-3xl sm:text-4xl font-black text-white leading-tight letter-animate" id="welcomeHeading" aria-label="Welcome back, <?php echo htmlspecialchars($user_name); ?>"></h2>
      <p class="mt-2 text-base" style="color:rgba(255,255,255,0.45);" id="todayDate"></p>
    </div>

    <!-- Navigation cards grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="navGrid">

      <!-- Sales -->
      <a href="/orion/sales" class="nav-card">
        <div class="icon-wrap"><iconify-icon icon="solar:cart-large-bold" style="color:#32EDFF;font-size:1.6rem;"></iconify-icon></div>
        <div>
          <div class="text-white font-bold text-lg leading-tight">Sales</div>
          <div class="text-sm mt-0.5" style="color:rgba(255,255,255,0.45);">Record new sales transactions</div>
        </div>
        <iconify-icon icon="solar:arrow-right-bold" style="color:rgba(50,237,255,0.4);font-size:1.1rem;position:absolute;right:1.25rem;bottom:1.25rem;"></iconify-icon>
      </a>

      <!-- Repairs -->
      <a href="/orion/repairs" class="nav-card">
        <div class="icon-wrap"><iconify-icon icon="solar:settings-bold" style="color:#32EDFF;font-size:1.6rem;"></iconify-icon></div>
        <div>
          <div class="text-white font-bold text-lg leading-tight">Repairs</div>
          <div class="text-sm mt-0.5" style="color:rgba(255,255,255,0.45);">Log device repairs and services</div>
        </div>
        <iconify-icon icon="solar:arrow-right-bold" style="color:rgba(50,237,255,0.4);font-size:1.1rem;position:absolute;right:1.25rem;bottom:1.25rem;"></iconify-icon>
      </a>

      <!-- Credit Sales -->
      <a href="/orion/credit-sales" class="nav-card">
        <div class="icon-wrap"><iconify-icon icon="solar:hand-money-bold" style="color:#32EDFF;font-size:1.6rem;"></iconify-icon></div>
        <div>
          <div class="text-white font-bold text-lg leading-tight">Credit Sales</div>
          <div class="text-sm mt-0.5" style="color:rgba(255,255,255,0.45);">Manage credit transactions</div>
        </div>
        <iconify-icon icon="solar:arrow-right-bold" style="color:rgba(50,237,255,0.4);font-size:1.1rem;position:absolute;right:1.25rem;bottom:1.25rem;"></iconify-icon>
      </a>

      <!-- Product Summary -->
      <a href="/orion/product-summary" class="nav-card">
        <div class="icon-wrap"><iconify-icon icon="solar:chart-square-bold" style="color:#32EDFF;font-size:1.6rem;"></iconify-icon></div>
        <div>
          <div class="text-white font-bold text-lg leading-tight">Product Summary</div>
          <div class="text-sm mt-0.5" style="color:rgba(255,255,255,0.45);">View product inventory</div>
        </div>
        <iconify-icon icon="solar:arrow-right-bold" style="color:rgba(50,237,255,0.4);font-size:1.1rem;position:absolute;right:1.25rem;bottom:1.25rem;"></iconify-icon>
      </a>

      <!-- Financial Summary -->
      <a href="/orion/financial-summary" class="nav-card">
        <div class="icon-wrap"><iconify-icon icon="solar:dollar-minimalistic-bold" style="color:#32EDFF;font-size:1.6rem;"></iconify-icon></div>
        <div>
          <div class="text-white font-bold text-lg leading-tight">Financial Summary</div>
          <div class="text-sm mt-0.5" style="color:rgba(255,255,255,0.45);">Daily financial records</div>
        </div>
        <iconify-icon icon="solar:arrow-right-bold" style="color:rgba(50,237,255,0.4);font-size:1.1rem;position:absolute;right:1.25rem;bottom:1.25rem;"></iconify-icon>
      </a>

      <!-- Import -->
      <a href="/orion/import" class="nav-card">
        <div class="icon-wrap"><iconify-icon icon="solar:import-bold" style="color:#32EDFF;font-size:1.6rem;"></iconify-icon></div>
        <div>
          <div class="text-white font-bold text-lg leading-tight">Import</div>
          <div class="text-sm mt-0.5" style="color:rgba(255,255,255,0.45);">Import new stock items</div>
        </div>
        <iconify-icon icon="solar:arrow-right-bold" style="color:rgba(50,237,255,0.4);font-size:1.1rem;position:absolute;right:1.25rem;bottom:1.25rem;"></iconify-icon>
      </a>

      <!-- Stock -->
      <a href="/orion/stock" class="nav-card">
        <div class="icon-wrap"><iconify-icon icon="solar:box-bold" style="color:#32EDFF;font-size:1.6rem;"></iconify-icon></div>
        <div>
          <div class="text-white font-bold text-lg leading-tight">Stock</div>
          <div class="text-sm mt-0.5" style="color:rgba(255,255,255,0.45);">Manage stock levels</div>
        </div>
        <iconify-icon icon="solar:arrow-right-bold" style="color:rgba(50,237,255,0.4);font-size:1.1rem;position:absolute;right:1.25rem;bottom:1.25rem;"></iconify-icon>
      </a>

      <?php if ( $is_admin ) : ?>
      <!-- Admin Panel -->
      <a href="/orion/admin-panel" class="nav-card">
        <div class="icon-wrap"><iconify-icon icon="solar:shield-user-bold" style="color:#32EDFF;font-size:1.6rem;"></iconify-icon></div>
        <div>
          <div class="text-white font-bold text-lg leading-tight">Admin Panel</div>
          <div class="text-sm mt-0.5" style="color:rgba(255,255,255,0.45);">Manage system settings</div>
        </div>
        <iconify-icon icon="solar:arrow-right-bold" style="color:rgba(50,237,255,0.4);font-size:1.1rem;position:absolute;right:1.25rem;bottom:1.25rem;"></iconify-icon>
      </a>

      <!-- Analytics -->
      <a href="/orion/analytics" class="nav-card">
        <div class="icon-wrap"><iconify-icon icon="solar:graph-up-bold" style="color:#32EDFF;font-size:1.6rem;"></iconify-icon></div>
        <div>
          <div class="text-white font-bold text-lg leading-tight">Analytics</div>
          <div class="text-sm mt-0.5" style="color:rgba(255,255,255,0.45);">Business insights &amp; reports</div>
        </div>
        <iconify-icon icon="solar:arrow-right-bold" style="color:rgba(50,237,255,0.4);font-size:1.1rem;position:absolute;right:1.25rem;bottom:1.25rem;"></iconify-icon>
      </a>
      <?php endif; ?>

    </div><!-- /grid -->

    <!-- Footer -->
    <footer class="mt-16 flex flex-col sm:flex-row items-center justify-between gap-4 pb-6">
      <p class="text-sm" style="color:rgba(255,255,255,0.3);">
        <iconify-icon icon="solar:map-point-bold" style="color:rgba(50,237,255,0.4);vertical-align:-3px;"></iconify-icon>
        Orion Brothers &copy; <?php echo date('Y'); ?> — Lagos, Nigeria
      </p>
      <button id="logoutBtn" class="btn-logout" aria-label="Sign out">
        <iconify-icon icon="solar:logout-bold" style="font-size:1rem;"></iconify-icon>
        Sign Out
      </button>
    </footer>

  </main>

  <script>
    /* ── Real-time clock ── */
    function updateClock() {
      const now  = new Date();
      const time = now.toLocaleTimeString('en-NG', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
      const date = now.toLocaleDateString('en-NG', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
      document.getElementById('orion-clock').textContent = time;
      document.getElementById('orion-date').textContent  = date;
    }
    updateClock();
    setInterval(updateClock, 1000);

    /* ── Today date for subtitle ── */
    document.getElementById('todayDate').textContent =
      new Date().toLocaleDateString('en-NG', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

    /* ── Letter-by-letter welcome animation ── */
    (function animateWelcome() {
      const name    = <?php echo json_encode('Welcome back, ' . $user_name); ?>;
      const el      = document.getElementById('welcomeHeading');
      const chars   = Array.from(name);
      let   markup  = '';
      chars.forEach((ch, i) => {
        const ch_escaped = ch === ' ' ? '&nbsp;' : ch.replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        markup += `<span style="animation-delay:${i * 0.04}s">${ch_escaped}</span>`;
      });
      el.innerHTML = markup;
      // Highlight the name portion
      const nameStart = 'Welcome back, '.length;
      const spans     = el.querySelectorAll('span');
      spans.forEach((s, i) => {
        if (i >= nameStart) s.style.color = '#32EDFF';
      });
    })();

    /* ── Logout ── */
    document.getElementById('logoutBtn').addEventListener('click', async () => {
      const base = (window.orionConfig && window.orionConfig.baseUrl) ? window.orionConfig.baseUrl : '/wp-json/orion/v1';
      try {
        await fetch(`${base}/logout`, { method: 'POST' });
      } catch (_) { /* ignore */ }
      localStorage.removeItem('orion_token');
      window.location.href = '/orion/login';
    });
  </script>

<script>
window.orionConfig = {
  baseUrl: '/wp-json/orion/v1',
  currentUser: <?php echo json_encode(Orion_Auth::get_current_user()); ?>,
  ajaxUrl: '<?php echo admin_url('admin-ajax.php'); ?>'
};
</script>
<script src="/wp-content/plugins/orion-inventory/assets/js/app.js"></script>
</body>
</html>
