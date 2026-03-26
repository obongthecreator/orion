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
  <title>New Sale — Orion Brothers</title>
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
    .orion-input::placeholder, .orion-textarea::placeholder { color: rgba(255,255,255,0.3); }
    .orion-input:focus, .orion-select:focus, .orion-textarea:focus {
      border-color: rgba(50,237,255,0.55);
      background: rgba(50,237,255,0.06);
      box-shadow: 0 0 0 3px rgba(50,237,255,0.1);
    }
    .orion-select option { background: #0B1B3C; color: #fff; }
    label { color: rgba(255,255,255,0.6); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.375rem; }

    /* Table */
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table th {
      padding: 0.625rem 0.75rem;
      text-align: left;
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: rgba(50,237,255,0.7);
      border-bottom: 1px solid rgba(50,237,255,0.12);
      white-space: nowrap;
    }
    .items-table td {
      padding: 0.5rem 0.5rem;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      vertical-align: middle;
    }
    .items-table tr:last-child td { border-bottom: none; }
    .items-table tr:hover td { background: rgba(50,237,255,0.03); }

    /* Qty stepper */
    .qty-btn {
      width: 1.75rem; height: 1.75rem;
      background: rgba(50,237,255,0.1);
      border: 1px solid rgba(50,237,255,0.2);
      border-radius: 0.375rem;
      color: #32EDFF;
      font-size: 1rem;
      line-height: 1;
      cursor: pointer;
      display: inline-flex; align-items: center; justify-content: center;
      transition: background 0.15s;
    }
    .qty-btn:hover { background: rgba(50,237,255,0.2); }
    .qty-input { width: 3rem; text-align: center; background: transparent; border: none; color: #fff; font-size: 0.875rem; font-weight: 600; outline: none; }

    /* Payment method radio */
    .pay-option { display: none; }
    .pay-option.active { display: block; }

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
    .radio-pill:has(input:checked),
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
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
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
    }
    .btn-ghost:hover { background: rgba(50,237,255,0.15); border-color: rgba(50,237,255,0.4); }

    .btn-danger-ghost {
      display: inline-flex; align-items: center; justify-content: center;
      width: 1.875rem; height: 1.875rem;
      border-radius: 0.5rem;
      border: 1px solid rgba(255,80,80,0.2);
      background: rgba(255,80,80,0.08);
      color: rgba(255,120,120,0.7);
      cursor: pointer;
      transition: all 0.15s;
    }
    .btn-danger-ghost:hover { background: rgba(255,80,80,0.18); color: #ff8080; border-color: rgba(255,80,80,0.4); }

    /* Toast */
    .toast {
      position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%) translateY(100px);
      padding: 0.875rem 1.5rem;
      border-radius: 0.875rem;
      font-size: 0.9375rem;
      font-weight: 600;
      z-index: 9999;
      transition: transform 0.35s cubic-bezier(.34,1.56,.64,1), opacity 0.3s;
      opacity: 0;
      pointer-events: none;
      white-space: nowrap;
    }
    .toast.success { background: rgba(50,237,100,0.92); color: #0B1B3C; box-shadow: 0 8px 32px rgba(50,237,100,0.35); }
    .toast.error   { background: rgba(255,80,80,0.92);  color: #fff;    box-shadow: 0 8px 32px rgba(255,80,80,0.35); }
    .toast.show    { transform: translateX(-50%) translateY(0); opacity: 1; }

    /* Modal */
    .modal-backdrop {
      position: fixed; inset: 0;
      background: rgba(0,0,0,0.7);
      backdrop-filter: blur(6px);
      z-index: 1000;
      display: flex; align-items: center; justify-content: center;
      padding: 1rem;
      opacity: 0; pointer-events: none;
      transition: opacity 0.25s;
    }
    .modal-backdrop.open { opacity: 1; pointer-events: auto; }
    .modal-box {
      background: #0d1f45;
      border: 1px solid rgba(50,237,255,0.2);
      border-radius: 1.25rem;
      max-width: 480px;
      width: 100%;
      max-height: 85vh;
      overflow-y: auto;
      transform: translateY(20px) scale(0.97);
      transition: transform 0.25s cubic-bezier(.34,1.56,.64,1);
    }
    .modal-backdrop.open .modal-box { transform: translateY(0) scale(1); }

    /* Receipt */
    .receipt-area { font-family: 'Courier New', monospace; font-size: 0.8125rem; line-height: 1.6; color: #fff; }

    @media print {
      body > *:not(#receiptModal) { display: none !important; }
      .modal-backdrop { position: static !important; background: none !important; backdrop-filter: none !important; display: block !important; padding: 0 !important; }
      .modal-box { border: none !important; max-height: none !important; background: #fff !important; color: #000 !important; }
      .receipt-area { color: #000 !important; }
      .no-print { display: none !important; }
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header class="glass-header sticky top-0 z-50 px-4 sm:px-6">
    <div class="max-w-5xl mx-auto flex items-center justify-between h-14 gap-3">
      <div class="flex items-center gap-2">
        <a href="/orion/home" class="btn-ghost px-2.5 py-2" aria-label="Back to home">
          <iconify-icon icon="solar:arrow-left-bold" style="font-size:1.1rem;"></iconify-icon>
        </a>
        <h1 class="text-white font-bold text-lg">New Sale</h1>
      </div>
      <a href="/orion/sales-history" class="btn-ghost">
        <iconify-icon icon="solar:history-bold" style="font-size:0.95rem;"></iconify-icon>
        <span class="hidden sm:inline">Sales History</span>
      </a>
    </div>
  </header>

  <!-- Main -->
  <main class="max-w-5xl mx-auto px-4 sm:px-6 py-8 space-y-6">

    <!-- Items section -->
    <section class="glass-card p-5">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-white font-bold text-base flex items-center gap-2">
          <iconify-icon icon="solar:cart-large-bold" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
          Sale Items
        </h2>
        <button type="button" id="addItemBtn" class="btn-ghost">
          <iconify-icon icon="solar:add-circle-bold" style="font-size:0.95rem;"></iconify-icon>
          Add Item
        </button>
      </div>

      <!-- Table wrapper — horizontally scrollable on mobile -->
      <div class="overflow-x-auto -mx-2 px-2">
        <table class="items-table min-w-[640px]" id="itemsTable">
          <thead>
            <tr>
              <th class="w-8">#</th>
              <th class="min-w-[180px]">Item</th>
              <th class="min-w-[120px]">Category</th>
              <th class="w-28">Price (₦)</th>
              <th class="w-28">Qty</th>
              <th class="w-28">Discount (₦)</th>
              <th class="w-28">Total (₦)</th>
              <th class="w-10"></th>
            </tr>
          </thead>
          <tbody id="itemsBody">
            <!-- rows injected by JS -->
          </tbody>
        </table>
      </div>

      <!-- Totals -->
      <div class="mt-5 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div class="text-sm" style="color:rgba(255,255,255,0.45);">
          Discount Total: <span id="discountTotal" class="font-semibold text-white">₦0</span>
        </div>
        <div class="text-right">
          <div class="text-xs uppercase tracking-wider mb-0.5" style="color:rgba(255,255,255,0.4);">Grand Total</div>
          <div id="grandTotal" class="text-3xl font-black" style="color:#32EDFF;">₦0</div>
        </div>
      </div>
    </section>

    <!-- Customer & Payment -->
    <section class="glass-card p-5">
      <h2 class="text-white font-bold text-base flex items-center gap-2 mb-5">
        <iconify-icon icon="solar:user-bold" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
        Customer &amp; Payment
      </h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
        <div>
          <label for="customerName">Customer Name <span style="color:#ff8080;">*</span></label>
          <input type="text" id="customerName" class="orion-input" placeholder="e.g. John Adeyemi" required>
        </div>
        <div>
          <label for="customerWhatsapp">WhatsApp Number</label>
          <input type="tel" id="customerWhatsapp" class="orion-input" placeholder="e.g. 08012345678">
        </div>
      </div>

      <!-- Payment method -->
      <div class="mb-5">
        <label class="mb-3">Payment Method <span style="color:#ff8080;">*</span></label>
        <div class="flex flex-wrap gap-2.5" id="paymentMethods">
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

      <!-- Payment inputs -->
      <div id="paymentInputs" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
        <div id="transferField" class="hidden">
          <label for="transferAmount">Transfer / Card Amount (₦)</label>
          <input type="text" inputmode="decimal" id="transferAmount" class="orion-input" placeholder="0.00">
        </div>
        <div id="cashField" class="hidden">
          <label for="cashAmount">Cash Amount (₦)</label>
          <input type="text" inputmode="decimal" id="cashAmount" class="orion-input" placeholder="0.00">
        </div>
        <div id="remainingField" class="hidden">
          <label>Remaining</label>
          <div class="orion-input text-sm font-semibold" id="remainingAmount" style="color:#32EDFF;cursor:default;">₦0.00</div>
        </div>
      </div>

      <!-- Order date -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="orderDate">Order Date</label>
          <input type="date" id="orderDate" class="orion-input">
        </div>
      </div>
    </section>

    <!-- Confirm & Submit -->
    <section class="glass-card p-5">
      <label class="flex items-start gap-3 cursor-pointer mb-5 select-none" id="confirmLabel">
        <input type="checkbox" id="confirmCheck" class="mt-0.5 flex-shrink-0" style="accent-color:#32EDFF;width:1.125rem;height:1.125rem;">
        <span class="text-sm" style="color:rgba(255,255,255,0.7);">I confirm this sale is correct and ready to be recorded.</span>
      </label>
      <button type="button" id="submitBtn" class="btn-primary" disabled>
        <iconify-icon icon="solar:check-circle-bold" style="font-size:1.1rem;"></iconify-icon>
        <span id="submitText">Record Sale</span>
      </button>
    </section>

  </main>

  <!-- Toast -->
  <div id="toast" class="toast" role="status" aria-live="polite"></div>

  <!-- Receipt Modal -->
  <div class="modal-backdrop" id="receiptModal" role="dialog" aria-modal="true" aria-labelledby="receiptTitle">
    <div class="modal-box p-6">
      <div class="flex items-center justify-between mb-4 no-print">
        <h3 id="receiptTitle" class="text-white font-bold text-lg">Sale Receipt</h3>
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
        <button type="button" id="newSaleBtn" class="btn-primary w-full">
          <iconify-icon icon="solar:add-circle-bold" style="font-size:1rem;"></iconify-icon>
          New Sale
        </button>
      </div>
    </div>
  </div>

<script>
/* ══════════════════════════════════════════════
   SALES PAGE — VANILLA JS
══════════════════════════════════════════════ */

const BASE         = (window.orionConfig && window.orionConfig.baseUrl) ? window.orionConfig.baseUrl : '/wp-json/orion/v1';
let products       = [];
let rowCount       = 0;
let submitCooldown = false;

/* ── Formatters ── */
const fmt     = n => '₦' + Number(n || 0).toLocaleString('en-NG', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
const parseNum = v => parseFloat((v || '').toString().replace(/[^\d.]/g, '')) || 0;

function formatMoneyInput(el) {
  const raw   = el.value.replace(/[^0-9.]/g, '');
  const parts = raw.split('.');
  const int   = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  el.value    = parts.length > 1 ? int + '.' + parts[1].slice(0, 2) : int;
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

/* ── Fetch products ── */
async function loadProducts() {
  try {
    const res  = await fetch(`${BASE}/products?type=sales`, {
      headers: { 'Authorization': 'Bearer ' + (localStorage.getItem('orion_token') || '') }
    });
    const data = await res.json();
    products   = Array.isArray(data) ? data : (data.data || []);
  } catch (_) { products = []; }
}

/* ── Build product <select> options ── */
function buildOptions(selectedId = '') {
  let opts = '<option value="">— Select product —</option>';
  products.forEach(p => {
    const sel = String(p.id) === String(selectedId) ? ' selected' : '';
    opts += `<option value="${p.id}" data-category="${p.category_name || p.category || ''}" data-price="${p.price || 0}"${sel}>${p.name}</option>`;
  });
  return opts;
}

/* ── Add row ── */
function addRow() {
  rowCount++;
  const idx   = rowCount;
  const tbody = document.getElementById('itemsBody');
  const tr    = document.createElement('tr');
  tr.dataset.idx = idx;
  tr.innerHTML = `
    <td class="text-center text-xs font-semibold" style="color:rgba(255,255,255,0.4);">${tbody.rows.length + 1}</td>
    <td>
      <select class="orion-select item-product" data-idx="${idx}" style="min-width:160px;">
        ${buildOptions()}
      </select>
    </td>
    <td>
      <input type="text" class="orion-input item-category" data-idx="${idx}" placeholder="Auto-filled" readonly
             style="background:rgba(255,255,255,0.02);cursor:default;color:rgba(255,255,255,0.5);">
    </td>
    <td>
      <input type="text" inputmode="decimal" class="orion-input item-price" data-idx="${idx}" placeholder="0" value="">
    </td>
    <td>
      <div class="flex items-center gap-1">
        <button type="button" class="qty-btn qty-dec" data-idx="${idx}" aria-label="Decrease quantity">−</button>
        <input type="number" class="qty-input item-qty" data-idx="${idx}" value="1" min="1">
        <button type="button" class="qty-btn qty-inc" data-idx="${idx}" aria-label="Increase quantity">+</button>
      </div>
    </td>
    <td>
      <input type="text" inputmode="decimal" class="orion-input item-discount" data-idx="${idx}" placeholder="0" value="0">
    </td>
    <td>
      <span class="item-total font-semibold text-sm" data-idx="${idx}" style="color:#32EDFF;">₦0</span>
    </td>
    <td>
      <button type="button" class="btn-danger-ghost remove-row" data-idx="${idx}" aria-label="Remove item">
        <iconify-icon icon="solar:trash-bin-trash-bold" style="font-size:0.9rem;"></iconify-icon>
      </button>
    </td>
  `;
  tbody.appendChild(tr);
  attachRowEvents(tr);
  updateTotals();
}

/* ── Attach row events ── */
function attachRowEvents(tr) {
  const idx = tr.dataset.idx;

  tr.querySelector('.item-product').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    tr.querySelector('.item-category').value = opt.dataset.category || '';
    const price = parseFloat(opt.dataset.price) || 0;
    tr.querySelector('.item-price').value = price ? price.toLocaleString('en-NG') : '';
    updateRowTotal(idx);
  });

  tr.querySelector('.item-price').addEventListener('input', function() {
    formatMoneyInput(this);
    updateRowTotal(idx);
  });

  tr.querySelector('.item-discount').addEventListener('input', function() {
    formatMoneyInput(this);
    updateRowTotal(idx);
  });

  tr.querySelector('.item-qty').addEventListener('input', () => updateRowTotal(idx));

  tr.querySelector('.qty-dec').addEventListener('click', () => {
    const q = tr.querySelector('.item-qty');
    q.value = Math.max(1, parseInt(q.value) - 1);
    updateRowTotal(idx);
  });
  tr.querySelector('.qty-inc').addEventListener('click', () => {
    const q = tr.querySelector('.item-qty');
    q.value = parseInt(q.value) + 1;
    updateRowTotal(idx);
  });

  tr.querySelector('.remove-row').addEventListener('click', () => {
    tr.remove();
    renumberRows();
    updateTotals();
  });
}

function updateRowTotal(idx) {
  const tr    = document.querySelector(`tr[data-idx="${idx}"]`);
  const price = parseNum(tr.querySelector('.item-price').value);
  const qty   = parseInt(tr.querySelector('.item-qty').value) || 1;
  const disc  = parseNum(tr.querySelector('.item-discount').value);
  const total = Math.max(0, price * qty - disc);
  tr.querySelector('.item-total').textContent = fmt(total);
  updateTotals();
}

function getGrandTotal() {
  let grand = 0;
  document.querySelectorAll('#itemsBody tr').forEach(tr => {
    grand += parseNum(tr.querySelector('.item-total')?.textContent);
  });
  return grand;
}

function updateTotals() {
  let grand = 0, discSum = 0;
  document.querySelectorAll('#itemsBody tr').forEach(tr => {
    const price = parseNum(tr.querySelector('.item-price')?.value);
    const qty   = parseInt(tr.querySelector('.item-qty')?.value) || 1;
    const disc  = parseNum(tr.querySelector('.item-discount')?.value);
    grand   += Math.max(0, price * qty - disc);
    discSum += disc;
  });
  document.getElementById('grandTotal').textContent    = fmt(grand);
  document.getElementById('discountTotal').textContent = fmt(discSum);
  syncPaymentAmounts();
}

function renumberRows() {
  document.querySelectorAll('#itemsBody tr').forEach((tr, i) => {
    tr.querySelector('td:first-child').textContent = i + 1;
  });
}

/* ── Payment method ── */
function syncPaymentAmounts() {
  const method = document.querySelector('input[name="paymentMethod"]:checked')?.value;
  if (!method) return;
  const grand = getGrandTotal();
  if (method === 'transfer') {
    document.getElementById('transferAmount').value    = grand.toLocaleString('en-NG', {minimumFractionDigits:2,maximumFractionDigits:2});
    document.getElementById('transferAmount').readOnly = true;
  } else if (method === 'cash') {
    document.getElementById('cashAmount').value    = grand.toLocaleString('en-NG', {minimumFractionDigits:2,maximumFractionDigits:2});
    document.getElementById('cashAmount').readOnly = true;
  } else if (method === 'both') {
    const cash     = parseNum(document.getElementById('cashAmount').value);
    const transfer = Math.max(0, grand - cash);
    document.getElementById('transferAmount').value    = transfer.toLocaleString('en-NG', {minimumFractionDigits:2,maximumFractionDigits:2});
    document.getElementById('transferAmount').readOnly = true;
    document.getElementById('cashAmount').readOnly     = false;
  }
}

document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
  radio.addEventListener('change', function() {
    const val = this.value;
    document.querySelectorAll('.radio-pill').forEach(p => p.classList.remove('selected'));
    document.getElementById(`pill-${val}`).classList.add('selected');

    document.getElementById('transferField').classList.toggle('hidden', val === 'cash');
    document.getElementById('cashField').classList.toggle('hidden',     val === 'transfer');
    document.getElementById('remainingField').classList.toggle('hidden', val !== 'both');

    // Reset amounts and apply logic
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
  const grand    = getGrandTotal();
  const cash     = parseNum(this.value);
  const transfer = Math.max(0, grand - cash);
  document.getElementById('transferAmount').value = transfer.toLocaleString('en-NG', {minimumFractionDigits:2,maximumFractionDigits:2});
  updateRemaining();
});

function updateRemaining() {
  const method = document.querySelector('input[name="paymentMethod"]:checked')?.value;
  if (method !== 'both') return;
  const grand     = getGrandTotal();
  const transfer  = parseNum(document.getElementById('transferAmount').value);
  const cash      = parseNum(document.getElementById('cashAmount').value);
  const remaining = grand - transfer - cash;
  const el        = document.getElementById('remainingAmount');
  el.textContent  = fmt(remaining);
  el.style.color  = Math.abs(remaining) < 0.01 ? '#32ed80' : '#ff8080';
}

/* ── Confirm checkbox enables submit ── */
document.getElementById('confirmCheck').addEventListener('change', function() {
  document.getElementById('submitBtn').disabled = !this.checked;
});

/* ── Set today's date ── */
document.getElementById('orderDate').value = new Date().toISOString().split('T')[0];

/* ── Submit ── */
document.getElementById('submitBtn').addEventListener('click', async () => {
  if (submitCooldown) return;

  const rows  = [...document.querySelectorAll('#itemsBody tr')];
  const items = rows.map(tr => {
    const sel      = tr.querySelector('.item-product');
    const price    = parseNum(tr.querySelector('.item-price').value);
    const qty      = parseInt(tr.querySelector('.item-qty').value) || 1;
    const discount = parseNum(tr.querySelector('.item-discount').value);
    const total    = Math.max(0, price * qty - discount);
    return {
      product_id:   sel.value,
      product_name: sel.options[sel.selectedIndex]?.text || '',
      category_name: tr.querySelector('.item-category').value,
      price,
      quantity: qty,
      discount,
      total,
    };
  }).filter(i => i.product_id);

  if (!items.length) { showToast('Please add at least one item.', 'error'); return; }

  const customerName = document.getElementById('customerName').value.trim();
  if (!customerName) { showToast('Customer name is required.', 'error'); return; }

  const payMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value;
  if (!payMethod) { showToast('Please select a payment method.', 'error'); return; }

  const grandTotal     = getGrandTotal();
  const transferAmount = parseNum(document.getElementById('transferAmount').value);
  const cashAmount     = parseNum(document.getElementById('cashAmount').value);

  // For 'both' mode, validate split sums to total
  if (payMethod === 'both') {
    const diff = Math.abs(transferAmount + cashAmount - grandTotal);
    if (diff > 0.02) {
      showToast(`Transfer (${fmt(transferAmount)}) + Cash (${fmt(cashAmount)}) must equal Total (${fmt(grandTotal)}).`, 'error');
      return;
    }
  }

  const payload = {
    items,
    customer_name:      customerName,
    customer_whatsapp:  document.getElementById('customerWhatsapp').value.trim(),
    payment_method:     payMethod,
    transfer_amount:    payMethod === 'cash'     ? 0 : transferAmount,
    cash_amount:        payMethod === 'transfer' ? 0 : cashAmount,
    order_date:         document.getElementById('orderDate').value,
    total_amount:       grandTotal,
    discount_total:     parseNum(document.getElementById('discountTotal').textContent),
  };

  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  document.getElementById('submitText').textContent = 'Recording…';
  submitCooldown = true;

  try {
    const res  = await fetch(`${BASE}/sales`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + (localStorage.getItem('orion_token') || '')
      },
      body: JSON.stringify(payload)
    });
    const data = await res.json();

    if (res.ok && (data.success || data.id)) {
      showToast('Sale recorded successfully!');
      buildReceipt(payload, data.id);
      document.getElementById('receiptModal').classList.add('open');
    } else {
      showToast(data.message || 'Failed to record sale.', 'error');
      btn.disabled = false;
      document.getElementById('submitText').textContent = 'Record Sale';
    }
  } catch (_) {
    showToast('Network error. Please try again.', 'error');
    btn.disabled = false;
    document.getElementById('submitText').textContent = 'Record Sale';
  }

  setTimeout(() => { submitCooldown = false; }, 3000);
});

/* ── Build receipt ── */
function buildReceipt(payload, saleId) {
  const dateStr = new Date().toLocaleString('en-NG');
  let lines = ``;
  lines += `================================\n`;
  lines += `     ORION BROTHERS\n`;
  lines += `     Inventory System\n`;
  lines += `================================\n`;
  lines += `Receipt #: ${saleId || 'N/A'}\n`;
  lines += `Date     : ${dateStr}\n`;
  lines += `Customer : ${payload.customer_name}\n`;
  if (payload.customer_whatsapp) lines += `WhatsApp : ${payload.customer_whatsapp}\n`;
  lines += `Staff    : <?php echo htmlspecialchars($user_name); ?>\n`;
  lines += `--------------------------------\n`;
  payload.items.forEach((item, i) => {
    const total = Math.max(0, item.price * item.quantity - item.discount);
    lines += `${i+1}. ${item.product_name}\n`;
    lines += `   ${fmt(item.price)} x ${item.quantity}`;
    if (item.discount > 0) lines += ` - disc ${fmt(item.discount)}`;
    lines += ` = ${fmt(total)}\n`;
  });
  lines += `--------------------------------\n`;
  lines += `TOTAL    : ${fmt(payload.total_amount)}\n`;
  lines += `Payment  : ${payload.payment_method.toUpperCase()}\n`;
  if (payload.payment_method === 'transfer' || payload.payment_method === 'both') lines += `Transfer : ${fmt(payload.transfer_amount)}\n`;
  if (payload.payment_method === 'cash'     || payload.payment_method === 'both') lines += `Cash     : ${fmt(payload.cash_amount)}\n`;
  lines += `================================\n`;
  lines += `    Thank you for your patronage!\n`;
  lines += `================================\n`;

  document.getElementById('receiptContent').innerHTML = `<pre style="white-space:pre-wrap;word-break:break-all;">${lines}</pre>`;
}

/* ── Modal close ── */
document.getElementById('closeReceiptBtn').addEventListener('click', () => {
  document.getElementById('receiptModal').classList.remove('open');
});
document.getElementById('receiptModal').addEventListener('click', (e) => {
  if (e.target === document.getElementById('receiptModal'))
    document.getElementById('receiptModal').classList.remove('open');
});
document.getElementById('newSaleBtn').addEventListener('click', () => {
  document.getElementById('receiptModal').classList.remove('open');
  document.getElementById('itemsBody').innerHTML = '';
  rowCount = 0;
  document.getElementById('customerName').value     = '';
  document.getElementById('customerWhatsapp').value = '';
  document.getElementById('confirmCheck').checked   = false;
  document.getElementById('submitBtn').disabled     = true;
  document.getElementById('submitText').textContent = 'Record Sale';
  document.querySelectorAll('input[name="paymentMethod"]').forEach(r => r.checked = false);
  document.querySelectorAll('.radio-pill').forEach(p => p.classList.remove('selected'));
  ['transferField','cashField','remainingField'].forEach(id => document.getElementById(id).classList.add('hidden'));
  document.getElementById('transferAmount').value = '';
  document.getElementById('cashAmount').value     = '';
  document.getElementById('orderDate').value      = new Date().toISOString().split('T')[0];
  updateTotals();
  addRow();
});

/* ── Add item button ── */
document.getElementById('addItemBtn').addEventListener('click', addRow);

/* ── Init ── */
(async () => {
  await loadProducts();
  addRow();
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
