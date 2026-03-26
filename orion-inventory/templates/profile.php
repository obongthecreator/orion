<?php
if ( ! class_exists( 'Orion_Auth' ) ) { wp_redirect( '/orion/login' ); exit; }
Orion_Auth::require_login();
$current_user = Orion_Auth::get_current_user();
$user_name    = $current_user['display_name'] ?? $current_user['username'] ?? 'User';
$user_role    = $current_user['role'] ?? 'staff';
$initials     = strtoupper( implode( '', array_map( fn($w) => $w[0], array_slice( explode( ' ', trim($user_name) ), 0, 2 ) ) ) );
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile — Orion Brothers</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
  <link rel="stylesheet" href="/wp-content/plugins/orion-inventory/assets/css/main.css">
  <script>tailwind.config={theme:{extend:{colors:{navy:'#0B1B3C',cyan:'#32EDFF'},fontFamily:{inter:['Inter','sans-serif']}}}}</script>
  <style>
    body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0B1B3C 0%,#060f22 100%);min-height:100vh;}
    .glass-header{background:rgba(11,27,60,0.82);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border-bottom:1px solid rgba(50,237,255,0.14);box-shadow:0 2px 32px rgba(0,0,0,0.3);}
    .glass-card{background:rgba(11,27,60,0.65);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(50,237,255,0.15);border-radius:1.25rem;box-shadow:0 8px 32px rgba(0,0,0,0.4);}
    .glass-footer{background:rgba(11,27,60,0.82);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border-top:1px solid rgba(50,237,255,0.14);}
    .form-input{background:rgba(255,255,255,0.07);border:1px solid rgba(50,237,255,0.2);border-radius:0.75rem;color:#fff;padding:0.75rem 1rem;width:100%;font-family:'Inter',sans-serif;transition:border-color 0.2s;}
    .form-input:focus{outline:none;border-color:#32EDFF;background:rgba(50,237,255,0.07);}
    .form-input::placeholder{color:rgba(255,255,255,0.35);}
    .btn-cyan{background:linear-gradient(135deg,#32EDFF,#1bc8d9);color:#0B1B3C;font-weight:700;border-radius:9999px;padding:0.65rem 1.75rem;border:none;cursor:pointer;transition:opacity 0.2s,transform 0.15s;}
    .btn-cyan:hover{opacity:0.9;transform:scale(1.03);}
    .avatar-ring{width:96px;height:96px;border-radius:50%;border:3px solid #32EDFF;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:800;color:#32EDFF;background:rgba(50,237,255,0.12);overflow:hidden;position:relative;cursor:pointer;}
    .avatar-ring img{width:100%;height:100%;object-fit:cover;}
    .toggle-pw{background:none;border:none;color:rgba(255,255,255,0.5);cursor:pointer;position:absolute;right:1rem;top:50%;transform:translateY(-50%);}
    .toast{position:fixed;bottom:1.5rem;right:1.5rem;background:rgba(11,27,60,0.95);border:1px solid #32EDFF;color:#fff;padding:0.85rem 1.5rem;border-radius:0.85rem;display:none;z-index:9999;font-size:0.9rem;}
    .toast.show{display:block;animation:fadeInUp 0.3s ease;}
    @keyframes fadeInUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
  </style>
</head>
<body class="min-h-screen flex flex-col">

<!-- Header -->
<header class="glass-header px-6 py-3 flex items-center justify-between sticky top-0 z-50">
  <div class="flex items-center gap-3">
    <a href="/orion/home" class="text-cyan-300 hover:text-white transition-colors">
      <iconify-icon icon="solar:arrow-left-linear" style="font-size:1.3rem"></iconify-icon>
    </a>
    <span class="text-[#32EDFF] font-bold text-lg">Profile</span>
  </div>
  <div class="flex items-center gap-4">
    <span class="text-white/70 text-xs hidden sm:block" id="realTimeClock"></span>
    <span class="text-white/50 text-xs hidden md:block" id="userLocation">Detecting location...</span>
    <span class="text-[#32EDFF] font-semibold text-sm"><?php echo esc_html($user_name); ?></span>
  </div>
</header>

<main class="flex-1 px-4 py-8 max-w-2xl mx-auto w-full">
  <div class="glass-card p-8">
    <!-- Avatar -->
    <div class="flex flex-col items-center gap-3 mb-8">
      <div class="avatar-ring" id="avatarPreview" onclick="document.getElementById('profilePicInput').click()">
        <?php if (!empty($current_user['profile_pic'])): ?>
          <img src="<?php echo esc_url($current_user['profile_pic']); ?>" alt="Profile" id="avatarImg">
        <?php else: ?>
          <span id="avatarInitials"><?php echo esc_html($initials); ?></span>
        <?php endif; ?>
        <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.5);text-align:center;padding:4px;font-size:0.6rem;color:#32EDFF;">Change</div>
      </div>
      <input type="file" id="profilePicInput" accept="image/*" style="display:none" onchange="previewAvatar(event)">
      <div class="text-center">
        <p class="text-white font-bold text-xl"><?php echo esc_html($user_name); ?></p>
        <span class="text-xs px-3 py-1 rounded-full text-[#32EDFF] border border-[#32EDFF]/30 bg-[#32EDFF]/10"><?php echo esc_html(ucfirst(str_replace('_',' ',$user_role))); ?></span>
      </div>
    </div>

    <!-- Profile Form -->
    <form id="profileForm" class="grid gap-5">
      <div>
        <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Full Name</label>
        <input type="text" name="full_name" class="form-input" placeholder="Full Name" value="<?php echo esc_attr($current_user['full_name'] ?? $user_name); ?>">
      </div>
      <div>
        <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Username</label>
        <input type="text" class="form-input opacity-50" value="<?php echo esc_attr($current_user['username'] ?? ''); ?>" readonly>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Email</label>
          <input type="email" name="email" class="form-input" placeholder="Email address" value="<?php echo esc_attr($current_user['email'] ?? ''); ?>">
        </div>
        <div>
          <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Phone</label>
          <input type="tel" name="phone" class="form-input" placeholder="WhatsApp / Phone" value="<?php echo esc_attr($current_user['phone'] ?? ''); ?>">
        </div>
      </div>
      <div>
        <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Home Address</label>
        <input type="text" name="address" class="form-input" placeholder="Home Address" value="<?php echo esc_attr($current_user['address'] ?? ''); ?>">
      </div>

      <div class="border-t border-white/10 pt-5 mt-1">
        <p class="text-[#32EDFF] text-sm font-semibold mb-4">Change Password</p>
        <div class="grid gap-4">
          <div class="relative">
            <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Current Password</label>
            <input type="password" name="current_password" id="curPw" class="form-input pr-12" placeholder="Current password">
            <button type="button" class="toggle-pw" onclick="togglePw('curPw',this)"><iconify-icon icon="solar:eye-linear" style="font-size:1.1rem"></iconify-icon></button>
          </div>
          <div class="relative">
            <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">New Password</label>
            <input type="password" name="new_password" id="newPw" class="form-input pr-12" placeholder="New password">
            <button type="button" class="toggle-pw" onclick="togglePw('newPw',this)"><iconify-icon icon="solar:eye-linear" style="font-size:1.1rem"></iconify-icon></button>
          </div>
          <div class="relative">
            <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Confirm New Password</label>
            <input type="password" name="confirm_password" id="confPw" class="form-input pr-12" placeholder="Confirm new password">
            <button type="button" class="toggle-pw" onclick="togglePw('confPw',this)"><iconify-icon icon="solar:eye-linear" style="font-size:1.1rem"></iconify-icon></button>
          </div>
        </div>
      </div>

      <button type="submit" class="btn-cyan w-full mt-2">
        <iconify-icon icon="solar:diskette-linear" class="mr-2"></iconify-icon>Save Profile
      </button>
    </form>
  </div>
</main>

<footer class="glass-footer mt-auto px-6 py-4 flex justify-between items-center">
  <div>
    <p class="text-white font-semibold">Orion Brothers</p>
    <p class="text-white/50 text-xs">Technology &amp; Electronics Store</p>
  </div>
  <button onclick="logoutUser()" class="btn-cyan px-5 py-2 text-sm flex items-center gap-2">
    <iconify-icon icon="solar:logout-2-linear"></iconify-icon>Logout
  </button>
</footer>

<div class="toast" id="toast"></div>

<script>
const token = localStorage.getItem('orion_token') || '';

function showToast(msg, ok=true) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.style.borderColor = ok ? '#32EDFF' : '#ff4d4d';
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

function togglePw(id, btn) {
  const el = document.getElementById(id);
  const show = el.type === 'password';
  el.type = show ? 'text' : 'password';
  btn.innerHTML = show
    ? '<iconify-icon icon="solar:eye-closed-linear" style="font-size:1.1rem"></iconify-icon>'
    : '<iconify-icon icon="solar:eye-linear" style="font-size:1.1rem"></iconify-icon>';
}

function previewAvatar(e) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = ev => {
    const p = document.getElementById('avatarPreview');
    p.innerHTML = `<img src="${ev.target.result}" alt="Profile" style="width:100%;height:100%;object-fit:cover;border-radius:50%"><div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.5);text-align:center;padding:4px;font-size:0.6rem;color:#32EDFF;">Change</div>`;
  };
  reader.readAsDataURL(file);
  uploadPicture(file);
}

async function uploadPicture(file) {
  const fd = new FormData();
  fd.append('picture', file);
  const r = await fetch('/wp-json/orion/v1/profile/picture', {
    method: 'POST',
    headers: { 'Authorization': 'Bearer ' + token },
    body: fd
  });
  const d = await r.json();
  if (d.success) showToast('Profile picture updated!');
  else showToast(d.message || 'Upload failed', false);
}

let saving = false;
document.getElementById('profileForm').addEventListener('submit', async e => {
  e.preventDefault();
  if (saving) return;
  saving = true;
  const fd = new FormData(e.target);
  const np = fd.get('new_password'), cp = fd.get('confirm_password');
  if (np && np !== cp) { showToast('Passwords do not match', false); saving = false; return; }
  const body = { full_name: fd.get('full_name'), email: fd.get('email'), phone: fd.get('phone'), address: fd.get('address') };
  if (fd.get('current_password')) { body.current_password = fd.get('current_password'); body.new_password = np; }
  const r = await fetch('/wp-json/orion/v1/profile', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
    body: JSON.stringify(body)
  });
  const d = await r.json();
  saving = false;
  if (d.success) showToast('Profile saved!');
  else showToast(d.message || 'Error saving profile', false);
});

async function logoutUser() {
  await fetch('/wp-json/orion/v1/logout', { method: 'POST', headers: {'Authorization': 'Bearer ' + token} });
  localStorage.removeItem('orion_token');
  window.location.href = '/orion/login';
}

function updateClock() {
  const el = document.getElementById('realTimeClock');
  if (el) el.textContent = new Date().toLocaleString('en-NG', {dateStyle:'medium',timeStyle:'short'});
}
setInterval(updateClock, 1000); updateClock();

if (navigator.geolocation) {
  function updateProfileLocation(pos) {
    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${pos.coords.latitude}&lon=${pos.coords.longitude}&format=json`)
      .then(r=>r.json())
      .then(d=>{
        const a = d.address||{};
        const place = a.suburb||a.neighbourhood||a.village||a.town||a.city||a.county||'';
        const state = a.state||'';
        document.getElementById('userLocation').textContent = place ? (state ? place+', '+state : place) : (state||'Nsukka, Enugu');
      })
      .catch(()=>{ document.getElementById('userLocation').textContent='Nsukka, Enugu'; });
  }
  navigator.geolocation.watchPosition(updateProfileLocation, ()=>{ document.getElementById('userLocation').textContent='Nsukka, Enugu'; }, {enableHighAccuracy:true,maximumAge:30000,timeout:10000});
} else {
  document.getElementById('userLocation').textContent='Nsukka, Enugu';
}
</script>
</body>
</html>
