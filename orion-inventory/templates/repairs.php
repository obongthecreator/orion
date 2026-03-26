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
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Repair — Orion Brothers</title>
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
    body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0B1B3C 0%, #060f22 100%); min-height: 100vh; color: #fff; }

    .glass-header {
      background: rgba(11,27,60,0.85);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      border-bottom: 1px solid rgba(50,237,255,0.14);
    }
    .glass-card {
      background: rgba(11,27,60,0.65);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(50,237,255,0.15);
      border-radius: 1.25rem;
    }

    .orion-input, .orion-select, .orion-textarea {
      width: 100%;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(50,237,255,0.18);
      border-radius: 0.625rem;
      color: #fff;
      padding: 0.625rem 0.875rem;
      font-size: 0.875rem;
      outline: none;
      font-family: 'Inter', sans-serif;
      transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    }
    .orion-textarea { resize: vertical; min-height: 90px; }
    .orion-input::placeholder, .orion-textarea::placeholder { color: rgba(255,255,255,0.3); }
    .orion-input:focus, .orion-select:focus, .orion-textarea:focus {
      border-color: rgba(50,237,255,0.55);
      background: rgba(50,237,255,0.06);
      box-shadow: 0 0 0 3px rgba(50,237,255,0.1);
    }
    .orion-select option { background: #0B1B3C; color: #fff; }
    label.field-label {
      color: rgba(255,255,255,0.6);
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      display: block;
      margin-bottom: 0.375rem;
    }

    /* Photo upload */
    .photo-upload-box {
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      border: 2px dashed rgba(50,237,255,0.25);
      border-radius: 0.875rem;
      background: rgba(50,237,255,0.04);
      padding: 1.5rem 1rem;
      cursor: pointer;
      transition: border-color 0.2s, background 0.2s;
      overflow: hidden;
      min-height: 140px;
    }
    .photo-upload-box:hover { border-color: rgba(50,237,255,0.5); background: rgba(50,237,255,0.08); }
    .photo-upload-box input[type="file"] {
      position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
    .photo-preview {
      width: 100%; height: 100%;
      object-fit: cover;
      border-radius: 0.625rem;
      display: none;
      position: absolute; inset: 0;
    }
    .photo-preview.visible { display: block; }
    .photo-placeholder { pointer-events: none; display: flex; flex-direction: column; align-items: center; gap: 0.4rem; }

    /* Radio pills */
    .radio-pill {
      display: inline-flex; align-items: center; gap: 0.5rem;
      padding: 0.5rem 1rem;
      border-radius: 9999px;
      border: 1px solid rgba(50,237,255,0.2);
      background: rgba(50,237,255,0.05);
      cursor: pointer;
      font-size: 0.875rem;
      font-weight: 500;
      color: rgba(255,255,255,0.6);
      transition: all 0.2s;
      user-select: none;
    }
    .radio-pill.selected {
      border-color: #32EDFF;
      background: rgba(50,237,255,0.15);
      color: #32EDFF;
    }

    /* Buttons */
    .btn-primary {
      display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
      padding: 0.75rem 2rem;
      border-radius: 9999px;
      background: linear-gradient(135deg, #32EDFF 0%, #00b8d4 100%);
      color: #0B1B3C;
      font-weight: 700;
      font-size: 0.9375rem;
      border: none;
      cursor: pointer;
      transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
      box-shadow: 0 4px 20px rgba(50,237,255,0.3);
    }
    .btn-primary:hover  { opacity: 0.9; transform: translateY(-1px); }
    .btn-primary:active { transform: translateY(0); }
    .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

    .btn-ghost {
      display: inline-flex; align-items: center; gap: 0.4rem;
      padding: 0.5rem 1rem;
      border-radius: 0.625rem;
      border: 1px solid rgba(50,237,255,0.2);
      background: rgba(50,237,255,0.07);
      color: rgba(50,237,255,0.85);
      font-size: 0.8125rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.15s;
      text-decoration: none;
    }
    .btn-ghost:hover { background: rgba(50,237,255,0.15); border-color: rgba(50,237,255,0.4); }

    /* Toast */
    .toast {
      position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%) translateY(100px);
      padding: 0.875rem 1.5rem;
      border-radius: 0.875rem;
      font-size: 0.9375rem; font-weight: 600;
      z-index: 9999;
      transition: transform 0.35s cubic-bezier(.34,1.56,.64,1), opacity 0.3s;
      opacity: 0; pointer-events: none; white-space: nowrap;
    }
    .toast.success { background: rgba(50,237,100,0.92); color: #0B1B3C; box-shadow: 0 8px 32px rgba(50,237,100,0.35); }
    .toast.error   { background: rgba(255,80,80,0.92);  color: #fff;    box-shadow: 0 8px 32px rgba(255,80,80,0.35); }
    .toast.show    { transform: translateX(-50%) translateY(0); opacity: 1; }

    /* Modal */
    .modal-backdrop {
      position: fixed; inset: 0;
      background: rgba(0,0,0,0.7); backdrop-filter: blur(6px);
      z-index: 1000;
      display: flex; align-items: center; justify-content: center; padding: 1rem;
      opacity: 0; pointer-events: none; transition: opacity 0.25s;
    }
    .modal-backdrop.open { opacity: 1; pointer-events: auto; }
    .modal-box {
      background: #0d1f45;
      border: 1px solid rgba(50,237,255,0.2);
      border-radius: 1.25rem;
      max-width: 480px; width: 100%; max-height: 85vh; overflow-y: auto;
      transform: translateY(20px) scale(0.97);
      transition: transform 0.25s cubic-bezier(.34,1.56,.64,1);
    }
    .modal-backdrop.open .modal-box { transform: translateY(0) scale(1); }
    .receipt-area { font-family: 'Courier New', monospace; font-size: 0.8125rem; line-height: 1.6; color: #fff; }

    @media print {
      body > *:not(#receiptModal) { display: none !important; }
      .modal-backdrop { position: static !important; background: none !important; backdrop-filter: none !important; display: block !important; padding: 0 !important; }
      .modal-box { border: none !important; max-height: none !important; background: #fff !important; }
      .receipt-area { color: #000 !important; }
      .no-print { display: none !important; }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header class="glass-header sticky top-0 z-50 px-4 sm:px-6">
    <div class="max-w-3xl mx-auto flex items-center justify-between h-14 gap-3">
      <div class="flex items-center gap-2">
        <a href="/orion/home" class="btn-ghost px-2.5 py-2" aria-label="Back to home">
          <iconify-icon icon="solar:arrow-left-bold" style="font-size:1.1rem;"></iconify-icon>
        </a>
        <h1 class="text-white font-bold text-lg">New Repair</h1>
      </div>
      <a href="/orion/repairs-history" class="btn-ghost">
        <iconify-icon icon="solar:history-bold" style="font-size:0.95rem;"></iconify-icon>
        <span class="hidden sm:inline">Repairs History</span>
      </a>
    </div>
  </header>

  <!-- Main -->
  <main class="max-w-3xl mx-auto px-4 sm:px-6 py-8 space-y-6">

    <!-- Repair details card -->
    <section class="glass-card p-5 sm:p-6 space-y-5">
      <h2 class="text-white font-bold text-base flex items-center gap-2">
        <iconify-icon icon="solar:settings-bold" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
        Repair Details
      </h2>

      <!-- Category -->
      <div>
        <label class="field-label" for="repairCategory">Category <span style="color:#ff8080;">*</span></label>
        <select id="repairCategory" class="orion-select">
          <option value="">— Loading categories… —</option>
        </select>
      </div>

      <!-- Complaint -->
      <div>
        <label class="field-label" for="complaint">Complaint <span style="color:#ff8080;">*</span></label>
        <textarea id="complaint" class="orion-textarea" placeholder="Describe the customer's complaint…"></textarea>
      </div>

      <!-- Diagnosis -->
      <div>
        <label class="field-label" for="diagnosis">Diagnosis</label>
        <textarea id="diagnosis" class="orion-textarea" placeholder="Your diagnosis of the issue…"></textarea>
      </div>

      <!-- Solution -->
      <div>
        <label class="field-label" for="solution">Solution</label>
        <textarea id="solution" class="orion-textarea" placeholder="Solution / work done…"></textarea>
      </div>

      <!-- Price & Total -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="field-label" for="repairPrice">Price (₦) <span style="color:#ff8080;">*</span></label>
          <input type="text" inputmode="decimal" id="repairPrice" class="orion-input" placeholder="0.00">
        </div>
        <div>
          <label class="field-label" for="repairTotal">Total (₦)</label>
          <input type="text" inputmode="decimal" id="repairTotal" class="orion-input" placeholder="0.00">
        </div>
      </div>
    </section>

    <!-- Customer info -->
    <section class="glass-card p-5 sm:p-6 space-y-5">
      <h2 class="text-white font-bold text-base flex items-center gap-2">
        <iconify-icon icon="solar:user-bold" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
        Customer Information
      </h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="field-label" for="customerName">Customer Name <span style="color:#ff8080;">*</span></label>
          <input type="text" id="customerName" class="orion-input" placeholder="e.g. Amaka Okafor">
        </div>
        <div>
          <label class="field-label" for="customerWhatsapp">WhatsApp Number</label>
          <input type="tel" id="customerWhatsapp" class="orion-input" placeholder="e.g. 08012345678">
        </div>
      </div>

      <!-- Photo uploads -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Customer photo -->
        <div>
          <label class="field-label">Customer Photo</label>
          <div class="photo-upload-box" id="customerPhotoBox" role="button" aria-label="Upload customer photo">
            <input type="file" id="customerPhotoInput" accept="image/*" aria-label="Customer photo file input">
            <img id="customerPhotoPreview" class="photo-preview" alt="Customer photo preview">
            <div class="photo-placeholder" id="customerPhotoPlaceholder">
              <iconify-icon icon="solar:camera-bold" style="color:#32EDFF;font-size:2rem;"></iconify-icon>
              <span class="text-xs text-center" style="color:rgba(255,255,255,0.45);">Tap to upload<br>customer photo</span>
            </div>
          </div>
        </div>
        <!-- Device photo -->
        <div>
          <label class="field-label">Device Photo</label>
          <div class="photo-upload-box" id="devicePhotoBox" role="button" aria-label="Upload device photo">
            <input type="file" id="devicePhotoInput" accept="image/*" aria-label="Device photo file input">
            <img id="devicePhotoPreview" class="photo-preview" alt="Device photo preview">
            <div class="photo-placeholder" id="devicePhotoPlaceholder">
              <iconify-icon icon="solar:smartphone-bold" style="color:#32EDFF;font-size:2rem;"></iconify-icon>
              <span class="text-xs text-center" style="color:rgba(255,255,255,0.45);">Tap to upload<br>device photo</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Payment -->
    <section class="glass-card p-5 sm:p-6 space-y-5">
      <h2 class="text-white font-bold text-base flex items-center gap-2">
        <iconify-icon icon="solar:wallet-money-bold" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
        Payment
      </h2>

      <!-- Method selection -->
      <div>
        <label class="field-label mb-3">Payment Method <span style="color:#ff8080;">*</span></label>
        <div class="flex flex-wrap gap-2.5">
          <label class="radio-pill" id="pill-transfer">
            <input type="radio" name="paymentMethod" value="transfer" style="display:none;">
            <iconify-icon icon="solar:card-transfer-bold" style="font-size:1rem;"></iconify-icon>
            Transfer / Card
          </label>
          <label class="radio-pill" id="pill-cash">
            <input type="radio" name="paymentMethod" value="cash" style="display:none;">
            <iconify-icon icon="solar:banknote-bold" style="font-size:1rem;"></iconify-icon>
            Cash
          </label>
          <label class="radio-pill" id="pill-both">
            <input type="radio" name="paymentMethod" value="both" style="display:none;">
            <iconify-icon icon="solar:wallet-money-bold" style="font-size:1rem;"></iconify-icon>
            Both
          </label>
        </div>
      </div>

      <!-- Amount inputs -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div id="transferField" class="hidden">
          <label class="field-label" for="transferAmount">Transfer / Card Amount (₦)</label>
          <input type="text" inputmode="decimal" id="transferAmount" class="orion-input" placeholder="0.00">
        </div>
        <div id="cashField" class="hidden">
          <label class="field-label" for="cashAmount">Cash Amount (₦)</label>
          <input type="text" inputmode="decimal" id="cashAmount" class="orion-input" placeholder="0.00">
        </div>
        <div id="remainingField" class="hidden">
          <label class="field-label">Balance / Remaining</label>
          <div class="orion-input text-sm font-semibold" id="remainingAmount" style="color:#32EDFF;cursor:default;">₦0.00</div>
        </div>
      </div>
    </section>

    <!-- Submit -->
    <section class="glass-card p-5 space-y-4">
      <!-- Live date + clock -->
      <div>
        <label class="field-label">Date &amp; Time</label>
        <div class="orion-input flex items-center gap-3" style="cursor:default;user-select:none;">
          <iconify-icon icon="solar:calendar-bold" style="color:#32EDFF;font-size:1.1rem;flex-shrink:0;"></iconify-icon>
          <span id="repairDateDisplay" class="font-medium text-sm text-white"></span>
          <span style="color:rgba(255,255,255,0.3);">|</span>
          <iconify-icon icon="solar:clock-circle-bold" style="color:#32EDFF;font-size:1.1rem;flex-shrink:0;"></iconify-icon>
          <span id="repairClockDisplay" class="font-mono font-semibold" style="color:#32EDFF;letter-spacing:0.05em;"></span>
        </div>
      </div>
      <button type="button" id="submitBtn" class="btn-primary">
        <iconify-icon icon="solar:check-circle-bold" style="font-size:1.1rem;"></iconify-icon>
        <span id="submitText">Save Repair</span>
      </button>
    </section>

  </main>

  <!-- Toast -->
  <div id="toast" class="toast" role="status" aria-live="polite"></div>

  <!-- Receipt Modal -->
  <div class="modal-backdrop" id="receiptModal" role="dialog" aria-modal="true" aria-labelledby="receiptTitle">
    <div class="modal-box p-6">
      <div class="flex items-center justify-between mb-4 no-print">
        <h3 id="receiptTitle" class="text-white font-bold text-lg">Repair Receipt</h3>
        <div class="flex gap-2">
          <button type="button" onclick="window.print()" class="btn-ghost">
            <iconify-icon icon="solar:printer-bold" style="font-size:0.95rem;"></iconify-icon>
            Print
          </button>
          <button type="button" id="closeReceiptBtn" class="btn-ghost" aria-label="Close">
            <iconify-icon icon="solar:close-circle-bold" style="font-size:0.95rem;"></iconify-icon>
          </button>
        </div>
      </div>
      <div class="receipt-area" id="receiptContent"></div>
      <div class="mt-5 no-print">
        <button type="button" id="newRepairBtn" class="btn-primary w-full">
          <iconify-icon icon="solar:add-circle-bold" style="font-size:1rem;"></iconify-icon>
          New Repair
        </button>
      </div>
    </div>
  </div>

<script>
/* ══════════════════════════════════════════════
   REPAIRS PAGE — VANILLA JS
══════════════════════════════════════════════ */

const BASE         = (window.orionConfig && window.orionConfig.baseUrl) ? window.orionConfig.baseUrl : '/wp-json/orion/v1';
let submitCooldown = false;
const fmt      = n => '₦' + Number(n || 0).toLocaleString('en-NG', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
const parseNum = v => parseFloat((v || '').toString().replace(/[^\d.]/g, '')) || 0;

function formatMoneyInput(el) {
  const raw   = el.value.replace(/[^0-9.]/g, '');
  const parts = raw.split('.');
  const int   = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  el.value    = parts.length > 1 ? int + '.' + parts[1].slice(0, 2) : int;
}

function getRepairTotal() {
  const totalVal = parseNum(document.getElementById('repairTotal').value);
  const priceVal = parseNum(document.getElementById('repairPrice').value);
  return totalVal || priceVal;
}

/* ── Toast ── */
function showToast(msg, type = 'success') {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.className = `toast ${type}`;
  void el.offsetWidth;
  el.classList.add('show');
  setTimeout(() => el.classList.remove('show'), 3500);
}

/* ── Load categories ── */
async function loadCategories() {
  try {
    const res  = await fetch(`${BASE}/categories?type=repairs`, {
      headers: { 'Authorization': 'Bearer ' + (localStorage.getItem('orion_token') || '') }
    });
    const data = await res.json();
    const cats = Array.isArray(data) ? data : (data.data || []);
    const sel  = document.getElementById('repairCategory');
    sel.innerHTML = '<option value="">— Select category —</option>';
    cats.forEach(c => {
      const opt = document.createElement('option');
      opt.value       = c.id || c.slug || c.name;
      opt.textContent = c.name;
      sel.appendChild(opt);
    });
  } catch (_) {
    document.getElementById('repairCategory').innerHTML = '<option value="">— Could not load categories —</option>';
  }
}

/* ── Price → Total auto-copy & formatting ── */
document.getElementById('repairPrice').addEventListener('input', function() {
  formatMoneyInput(this);
  const totalField = document.getElementById('repairTotal');
  if (!totalField.dataset.edited) totalField.value = this.value;
  syncPaymentAmounts();
});
document.getElementById('repairTotal').addEventListener('input', function() {
  formatMoneyInput(this);
  this.dataset.edited = '1';
  syncPaymentAmounts();
});

/* ── Payment method ── */
function syncPaymentAmounts() {
  const method = document.querySelector('input[name="paymentMethod"]:checked')?.value;
  if (!method) return;
  const total = getRepairTotal();
  if (method === 'transfer') {
    document.getElementById('transferAmount').value    = total.toLocaleString('en-NG', {minimumFractionDigits:2,maximumFractionDigits:2});
    document.getElementById('transferAmount').readOnly = true;
  } else if (method === 'cash') {
    document.getElementById('cashAmount').value    = total.toLocaleString('en-NG', {minimumFractionDigits:2,maximumFractionDigits:2});
    document.getElementById('cashAmount').readOnly = true;
  } else if (method === 'both') {
    const cash     = parseNum(document.getElementById('cashAmount').value);
    const transfer = Math.max(0, total - cash);
    document.getElementById('transferAmount').value    = transfer.toLocaleString('en-NG', {minimumFractionDigits:2,maximumFractionDigits:2});
    document.getElementById('transferAmount').readOnly = true;
    document.getElementById('cashAmount').readOnly     = false;
  }
  updateRemaining();
}

document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
  radio.addEventListener('change', function() {
    const val = this.value;
    document.querySelectorAll('.radio-pill').forEach(p => p.classList.remove('selected'));
    document.getElementById(`pill-${val}`).classList.add('selected');
    document.getElementById('transferField').classList.toggle('hidden', val === 'cash');
    document.getElementById('cashField').classList.toggle('hidden',     val === 'transfer');
    document.getElementById('remainingField').classList.toggle('hidden', val !== 'both');
    document.getElementById('transferAmount').value    = '';
    document.getElementById('transferAmount').readOnly = false;
    document.getElementById('cashAmount').value        = '';
    document.getElementById('cashAmount').readOnly     = false;
    syncPaymentAmounts();
  });
});

// When cash changes (Both mode): auto-compute transfer = total - cash
document.getElementById('cashAmount').addEventListener('input', function() {
  formatMoneyInput(this);
  const method = document.querySelector('input[name="paymentMethod"]:checked')?.value;
  if (method !== 'both') return;
  const total    = getRepairTotal();
  const cash     = parseNum(this.value);
  const transfer = Math.max(0, total - cash);
  document.getElementById('transferAmount').value = transfer.toLocaleString('en-NG', {minimumFractionDigits:2,maximumFractionDigits:2});
  updateRemaining();
});

function updateRemaining() {
  const method = document.querySelector('input[name="paymentMethod"]:checked')?.value;
  if (method !== 'both') return;
  const total    = getRepairTotal();
  const transfer = parseNum(document.getElementById('transferAmount').value);
  const cash     = parseNum(document.getElementById('cashAmount').value);
  const rem      = total - transfer - cash;
  const el       = document.getElementById('remainingAmount');
  el.textContent = fmt(rem);
  el.style.color = Math.abs(rem) < 0.02 ? '#32ed80' : '#ff8080';
}

/* ── Photo upload preview ── */
function setupPhotoPreview(inputId, previewId, placeholderId) {
  document.getElementById(inputId).addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.getElementById(previewId);
      img.src = e.target.result;
      img.classList.add('visible');
      document.getElementById(placeholderId).style.display = 'none';
    };
    reader.readAsDataURL(file);
  });
}
setupPhotoPreview('customerPhotoInput', 'customerPhotoPreview', 'customerPhotoPlaceholder');
setupPhotoPreview('devicePhotoInput',   'devicePhotoPreview',   'devicePhotoPlaceholder');

/* ── Submit ── */
document.getElementById('submitBtn').addEventListener('click', async () => {
  if (submitCooldown) return;

  const category     = document.getElementById('repairCategory').value;
  const complaint    = document.getElementById('complaint').value.trim();
  const customerName = document.getElementById('customerName').value.trim();
  const payMethod    = document.querySelector('input[name="paymentMethod"]:checked')?.value;
  const price        = getRepairTotal();

  if (!category)     { showToast('Please select a category.', 'error');         return; }
  if (!complaint)    { showToast('Complaint is required.', 'error');             return; }
  if (!customerName) { showToast('Customer name is required.', 'error');         return; }
  if (!payMethod)    { showToast('Please select a payment method.', 'error');    return; }
  if (!price)        { showToast('Please enter a repair price.', 'error');       return; }

  const transferAmount = parseNum(document.getElementById('transferAmount').value);
  const cashAmount     = parseNum(document.getElementById('cashAmount').value);

  // Validate split for 'both' mode
  if (payMethod === 'both') {
    const diff = Math.abs(transferAmount + cashAmount - price);
    if (diff > 0.02) {
      showToast(`Transfer + Cash must equal Total (${fmt(price)}).`, 'error');
      return;
    }
  }

  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  document.getElementById('submitText').textContent = 'Saving…';
  submitCooldown = true;

  /* Build FormData to support file uploads */
  const formData = new FormData();
  formData.append('category',          category);
  formData.append('complaint',         complaint);
  formData.append('diagnosis',         document.getElementById('diagnosis').value.trim());
  formData.append('solution',          document.getElementById('solution').value.trim());
  formData.append('price',             parseNum(document.getElementById('repairPrice').value));
  formData.append('total',             price);
  formData.append('customer_name',     customerName);
  formData.append('customer_whatsapp', document.getElementById('customerWhatsapp').value.trim());
  formData.append('payment_method',    payMethod);
  formData.append('transfer_amount',   payMethod === 'cash'     ? 0 : transferAmount);
  formData.append('cash_amount',       payMethod === 'transfer' ? 0 : cashAmount);

  const custFile   = document.getElementById('customerPhotoInput').files[0];
  const deviceFile = document.getElementById('devicePhotoInput').files[0];
  if (custFile)   formData.append('customer_image', custFile);
  if (deviceFile) formData.append('device_image',   deviceFile);

  try {
    const res  = await fetch(`${BASE}/repairs`, {
      method: 'POST',
      headers: { 'Authorization': 'Bearer ' + (localStorage.getItem('orion_token') || '') },
      body: formData
    });
    const data = await res.json();

    if (res.ok && (data.success || data.id)) {
      showToast('Repair saved successfully!');
      buildReceipt(data.id);
      document.getElementById('receiptModal').classList.add('open');
    } else {
      showToast(data.message || 'Failed to save repair.', 'error');
      btn.disabled = false;
      document.getElementById('submitText').textContent = 'Save Repair';
    }
  } catch (_) {
    showToast('Network error. Please try again.', 'error');
    btn.disabled = false;
    document.getElementById('submitText').textContent = 'Save Repair';
  }

  setTimeout(() => { submitCooldown = false; }, 3000);
});

/* ── Build receipt ── */
function buildReceipt(repairId) {
  const dateStr = new Date().toLocaleString('en-NG');
  const catSel  = document.getElementById('repairCategory');
  const catName = catSel.options[catSel.selectedIndex]?.text || '';
  const price   = parseNum(document.getElementById('repairPrice').value);
  const total   = getRepairTotal();
  const method  = document.querySelector('input[name="paymentMethod"]:checked')?.value || '';

  let lines = '';
  lines += `================================\n`;
  lines += `     ORION BROTHERS\n`;
  lines += `     Inventory System\n`;
  lines += `================================\n`;
  lines += `Repair #  : ${repairId || 'N/A'}\n`;
  lines += `Date      : ${dateStr}\n`;
  lines += `Customer  : ${document.getElementById('customerName').value}\n`;
  const wa = document.getElementById('customerWhatsapp').value;
  if (wa) lines += `WhatsApp  : ${wa}\n`;
  lines += `Staff     : <?php echo htmlspecialchars($user_name); ?>\n`;
  lines += `--------------------------------\n`;
  lines += `Category  : ${catName}\n`;
  lines += `Complaint : ${document.getElementById('complaint').value}\n`;
  const diag = document.getElementById('diagnosis').value;
  if (diag) lines += `Diagnosis : ${diag}\n`;
  const sol  = document.getElementById('solution').value;
  if (sol)  lines += `Solution  : ${sol}\n`;
  lines += `--------------------------------\n`;
  lines += `Price     : ${fmt(price)}\n`;
  lines += `TOTAL     : ${fmt(total)}\n`;
  lines += `Payment   : ${method.toUpperCase()}\n`;
  if (method === 'transfer' || method === 'both') lines += `Transfer  : ${fmt(parseNum(document.getElementById('transferAmount').value))}\n`;
  if (method === 'cash'     || method === 'both') lines += `Cash      : ${fmt(parseNum(document.getElementById('cashAmount').value))}\n`;
  lines += `================================\n`;
  lines += `  Thank you for choosing\n`;
  lines += `    Orion Brothers!\n`;
  lines += `================================\n`;

  document.getElementById('receiptContent').innerHTML = `<pre style="white-space:pre-wrap;word-break:break-all;">${lines}</pre>`;
}

/* ── Modal & reset ── */
document.getElementById('closeReceiptBtn').addEventListener('click', () => {
  document.getElementById('receiptModal').classList.remove('open');
});
document.getElementById('receiptModal').addEventListener('click', e => {
  if (e.target === document.getElementById('receiptModal'))
    document.getElementById('receiptModal').classList.remove('open');
});
document.getElementById('newRepairBtn').addEventListener('click', () => {
  document.getElementById('receiptModal').classList.remove('open');
  document.getElementById('repairCategory').value  = '';
  document.getElementById('complaint').value       = '';
  document.getElementById('diagnosis').value       = '';
  document.getElementById('solution').value        = '';
  document.getElementById('repairPrice').value     = '';
  document.getElementById('repairTotal').value     = '';
  delete document.getElementById('repairTotal').dataset.edited;
  document.getElementById('customerName').value    = '';
  document.getElementById('customerWhatsapp').value = '';
  document.querySelectorAll('input[name="paymentMethod"]').forEach(r => r.checked = false);
  document.querySelectorAll('.radio-pill').forEach(p => p.classList.remove('selected'));
  ['transferField','cashField','remainingField'].forEach(id => document.getElementById(id).classList.add('hidden'));
  document.getElementById('transferAmount').value    = '';
  document.getElementById('transferAmount').readOnly = false;
  document.getElementById('cashAmount').value        = '';
  document.getElementById('cashAmount').readOnly     = false;
  ['customerPhotoPreview','devicePhotoPreview'].forEach(id => {
    const img = document.getElementById(id);
    img.src = ''; img.classList.remove('visible');
  });
  document.getElementById('customerPhotoPlaceholder').style.display = '';
  document.getElementById('devicePhotoPlaceholder').style.display   = '';
  document.getElementById('customerPhotoInput').value = '';
  document.getElementById('devicePhotoInput').value   = '';
  document.getElementById('submitBtn').disabled = false;
  document.getElementById('submitText').textContent = 'Save Repair';
});

/* ── Init ── */
loadCategories();

/* ── Live date + clock ── */
(function startRepairClock() {
  function tick() {
    const now = new Date();
    document.getElementById('repairDateDisplay').textContent =
      now.toLocaleDateString('en-NG', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' });
    document.getElementById('repairClockDisplay').textContent =
      now.toLocaleTimeString('en-NG', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
  }
  tick();
  setInterval(tick, 1000);
})();
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
