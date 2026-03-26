<?php
if ( class_exists( 'Orion_Auth' ) && Orion_Auth::is_logged_in() ) {
    wp_redirect( '/orion/home' );
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — Orion Brothers</title>
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
    body { font-family: 'Inter', sans-serif; }

    /* Animated beam noodles background */
    .beam-canvas {
      position: fixed; inset: 0; overflow: hidden; pointer-events: none; z-index: 0;
    }
    .beam {
      position: absolute;
      height: 2px;
      border-radius: 9999px;
      background: linear-gradient(90deg, transparent, rgba(50,237,255,0.6), transparent);
      animation: beam-slide linear infinite;
      opacity: 0;
    }
    @keyframes beam-slide {
      0%   { transform: translateX(-120%) rotate(var(--angle)); opacity: 0; }
      10%  { opacity: 1; }
      90%  { opacity: 1; }
      100% { transform: translateX(220vw) rotate(var(--angle)); opacity: 0; }
    }

    /* Floating orbs */
    .orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      animation: orb-float ease-in-out infinite alternate;
    }
    @keyframes orb-float {
      from { transform: translateY(0) scale(1); }
      to   { transform: translateY(-30px) scale(1.05); }
    }

    /* Glass card */
    .glass-card {
      background: rgba(11,27,60,0.75);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      border: 1px solid rgba(50,237,255,0.18);
      border-radius: 1.5rem;
      box-shadow: 0 8px 64px rgba(0,0,0,0.45), inset 0 1px 0 rgba(255,255,255,0.06);
    }

    /* Input field */
    .orion-input {
      width: 100%;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(50,237,255,0.20);
      border-radius: 0.75rem;
      color: #fff;
      padding: 0.75rem 1rem 0.75rem 2.75rem;
      font-size: 0.9375rem;
      outline: none;
      transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    }
    .orion-input::placeholder { color: rgba(255,255,255,0.35); }
    .orion-input:focus {
      border-color: rgba(50,237,255,0.6);
      background: rgba(50,237,255,0.06);
      box-shadow: 0 0 0 3px rgba(50,237,255,0.12);
    }

    /* Cyan pill button */
    .btn-cyan {
      width: 100%;
      padding: 0.875rem 1.5rem;
      border-radius: 9999px;
      background: linear-gradient(135deg, #32EDFF 0%, #00b8d4 100%);
      color: #0B1B3C;
      font-weight: 700;
      font-size: 1rem;
      letter-spacing: 0.02em;
      border: none;
      cursor: pointer;
      transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
      box-shadow: 0 4px 24px rgba(50,237,255,0.35);
    }
    .btn-cyan:hover { opacity: 0.92; transform: translateY(-1px); box-shadow: 0 8px 32px rgba(50,237,255,0.45); }
    .btn-cyan:active { transform: translateY(0); }
    .btn-cyan:disabled { opacity: 0.55; cursor: not-allowed; transform: none; }

    /* Loading dots */
    .dot-flashing { display: inline-flex; gap: 6px; align-items: center; }
    .dot-flashing span {
      width: 8px; height: 8px;
      background: #0B1B3C;
      border-radius: 50%;
      animation: dot-blink 1.2s infinite ease-in-out;
    }
    .dot-flashing span:nth-child(2) { animation-delay: 0.2s; }
    .dot-flashing span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes dot-blink {
      0%, 80%, 100% { transform: scale(0.7); opacity: 0.5; }
      40% { transform: scale(1); opacity: 1; }
    }

    /* Error shake */
    @keyframes shake {
      0%,100% { transform: translateX(0); }
      20%,60% { transform: translateX(-6px); }
      40%,80% { transform: translateX(6px); }
    }
    .shake { animation: shake 0.4s ease; }
  </style>
</head>
<body class="bg-navy min-h-screen flex items-center justify-center p-4 relative overflow-hidden" style="background: linear-gradient(135deg, #0B1B3C 0%, #060f22 100%);">

  <!-- Animated background beams -->
  <div class="beam-canvas" id="beamCanvas" aria-hidden="true">
    <div class="orb" style="width:420px;height:420px;background:rgba(50,237,255,0.07);top:-80px;left:-100px;animation-duration:7s;"></div>
    <div class="orb" style="width:300px;height:300px;background:rgba(50,237,255,0.05);bottom:-60px;right:-60px;animation-duration:9s;animation-delay:2s;"></div>
    <div class="orb" style="width:200px;height:200px;background:rgba(11,27,60,0.9);bottom:30%;left:10%;animation-duration:11s;animation-delay:1s;"></div>
    <!-- Mobile → Desktop beam icons -->
    <div style="position:absolute;bottom:24px;left:50%;transform:translateX(-50%);display:flex;align-items:center;gap:32px;opacity:0.12;">
      <iconify-icon icon="solar:smartphone-bold" style="color:#32EDFF;font-size:2.5rem;"></iconify-icon>
      <div style="width:120px;height:2px;background:linear-gradient(90deg,rgba(50,237,255,0),rgba(50,237,255,0.8),rgba(50,237,255,0));"></div>
      <iconify-icon icon="solar:monitor-bold" style="color:#32EDFF;font-size:2.5rem;"></iconify-icon>
    </div>
  </div>

  <!-- Login Card -->
  <div class="glass-card w-full max-w-md px-8 py-10 relative z-10" id="loginCard">

    <!-- Logo -->
    <div class="text-center mb-8">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4" style="background:rgba(50,237,255,0.12);border:1px solid rgba(50,237,255,0.25);">
        <iconify-icon icon="solar:box-bold" style="color:#32EDFF;font-size:2rem;"></iconify-icon>
      </div>
      <h1 class="text-3xl font-black text-white tracking-tight">
        Orion <span style="color:#32EDFF;">Brothers</span>
      </h1>
      <p class="text-sm mt-1" style="color:rgba(255,255,255,0.5);">Inventory Management System</p>
    </div>

    <!-- Error message -->
    <div id="errorBox"
         class="hidden flex items-center gap-3 rounded-xl px-4 py-3 mb-5"
         style="background:rgba(255,80,80,0.12);border:1px solid rgba(255,80,80,0.3);"
         role="alert">
      <iconify-icon icon="solar:danger-triangle-bold" style="color:#ff6b6b;font-size:1.25rem;flex-shrink:0;"></iconify-icon>
      <span id="errorText" class="text-sm" style="color:#ff9898;"></span>
    </div>

    <!-- Form -->
    <form id="loginForm" novalidate autocomplete="on">

      <!-- Username -->
      <div class="mb-4">
        <label for="username" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:rgba(255,255,255,0.5);">Username</label>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none" style="color:rgba(50,237,255,0.6);">
            <iconify-icon icon="solar:user-bold" style="font-size:1.1rem;"></iconify-icon>
          </span>
          <input
            type="text"
            id="username"
            name="username"
            class="orion-input"
            placeholder="Enter your username"
            autocomplete="username"
            required
          >
        </div>
      </div>

      <!-- Password -->
      <div class="mb-6">
        <label for="password" class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color:rgba(255,255,255,0.5);">Password</label>
        <div class="relative">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none" style="color:rgba(50,237,255,0.6);">
            <iconify-icon icon="solar:lock-password-bold" style="font-size:1.1rem;"></iconify-icon>
          </span>
          <input
            type="password"
            id="password"
            name="password"
            class="orion-input"
            placeholder="Enter your password"
            autocomplete="current-password"
            style="padding-right: 3rem;"
            required
          >
          <button
            type="button"
            id="togglePassword"
            class="absolute inset-y-0 right-0 flex items-center pr-3"
            style="color:rgba(255,255,255,0.4);background:none;border:none;cursor:pointer;"
            aria-label="Toggle password visibility"
          >
            <iconify-icon id="eyeIcon" icon="solar:eye-bold" style="font-size:1.1rem;"></iconify-icon>
          </button>
        </div>
      </div>

      <!-- Submit button -->
      <button type="submit" id="submitBtn" class="btn-cyan">
        <span id="btnText">Sign In</span>
        <span id="btnLoading" class="hidden dot-flashing justify-center">
          <span></span><span></span><span></span>
        </span>
      </button>

    </form>

    <p class="text-center mt-6 text-xs" style="color:rgba(255,255,255,0.2);">
      Orion Brothers &copy; <?php echo date('Y'); ?>
    </p>
  </div>

<script>
  /* ── Animated beam noodles ── */
  (function spawnBeams() {
    const canvas = document.getElementById('beamCanvas');
    const angles = [-15, 0, 12, -5, 8];
    function createBeam() {
      const el = document.createElement('div');
      el.className = 'beam';
      const angle = angles[Math.floor(Math.random() * angles.length)];
      const top   = Math.random() * 100;
      const width = 200 + Math.random() * 400;
      const dur   = 6 + Math.random() * 10;
      const delay = Math.random() * 8;
      el.style.cssText = `top:${top}%;width:${width}px;--angle:${angle}deg;animation-duration:${dur}s;animation-delay:${delay}s;`;
      canvas.appendChild(el);
    }
    for (let i = 0; i < 18; i++) createBeam();
  })();

  /* ── Toggle password visibility ── */
  const BASE       = (window.orionConfig && window.orionConfig.baseUrl) ? window.orionConfig.baseUrl : '/wp-json/orion/v1';
  const toggleBtn  = document.getElementById('togglePassword');
  const passInput  = document.getElementById('password');
  const eyeIcon    = document.getElementById('eyeIcon');
  let   passVisible = false;

  toggleBtn.addEventListener('click', () => {
    passVisible = !passVisible;
    passInput.type = passVisible ? 'text' : 'password';
    eyeIcon.setAttribute('icon', passVisible ? 'solar:eye-closed-bold' : 'solar:eye-bold');
    toggleBtn.style.color = passVisible ? 'rgba(50,237,255,0.8)' : 'rgba(255,255,255,0.4)';
  });

  /* ── UI helpers ── */
  function showError(msg) {
    const box  = document.getElementById('errorBox');
    const text = document.getElementById('errorText');
    text.textContent = msg;
    box.classList.remove('hidden');
    box.classList.remove('flex');
    box.classList.add('flex');
    document.getElementById('loginCard').classList.add('shake');
    setTimeout(() => document.getElementById('loginCard').classList.remove('shake'), 500);
  }

  function hideError() {
    document.getElementById('errorBox').classList.add('hidden');
  }

  function showLoading() {
    const btn  = document.getElementById('submitBtn');
    const text = document.getElementById('btnText');
    const load = document.getElementById('btnLoading');
    btn.disabled = true;
    text.classList.add('hidden');
    load.classList.remove('hidden');
    load.classList.add('flex');
  }

  function hideLoading() {
    const btn  = document.getElementById('submitBtn');
    const text = document.getElementById('btnText');
    const load = document.getElementById('btnLoading');
    btn.disabled = false;
    text.classList.remove('hidden');
    load.classList.add('hidden');
    load.classList.remove('flex');
  }

  /* ── Login handler ── */
  async function login() {
    hideError();
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;

    if (!username || !password) {
      showError('Please enter your username and password.');
      return;
    }

    showLoading();

    try {
      const response = await fetch(`${BASE}/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password })
      });
      const data = await response.json();

      if (data.success) {
        localStorage.setItem('orion_token', data.token);
        window.location.href = '/orion/home';
      } else {
        hideLoading();
        showError(data.message || 'Invalid credentials. Please try again.');
      }
    } catch (err) {
      hideLoading();
      showError('Connection error. Please check your network and try again.');
    }
  }

  /* ── Form submit ── */
  document.getElementById('loginForm').addEventListener('submit', (e) => {
    e.preventDefault();
    login();
  });

  /* ── Enter key on fields ── */
  ['username', 'password'].forEach(id => {
    document.getElementById(id).addEventListener('keydown', (e) => {
      if (e.key === 'Enter') login();
    });
  });

  /* ── Clear error on input ── */
  ['username', 'password'].forEach(id => {
    document.getElementById(id).addEventListener('input', hideError);
  });
</script>

<script>
window.orionConfig = {
  baseUrl: '/wp-json/orion/v1',
  currentUser: <?php echo json_encode(class_exists('Orion_Auth') ? Orion_Auth::get_current_user() : null); ?>,
  ajaxUrl: '<?php echo function_exists('admin_url') ? admin_url('admin-ajax.php') : '/wp-admin/admin-ajax.php'; ?>'
};
</script>
<script src="/wp-content/plugins/orion-inventory/assets/js/app.js"></script>
</body>
</html>
