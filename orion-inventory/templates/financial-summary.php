<?php
if ( ! class_exists( 'Orion_Auth' ) ) {
    wp_redirect( '/orion/login' );
    exit;
}
Orion_Auth::require_login();
$current_user = Orion_Auth::get_current_user();
$user_name    = $current_user['display_name'] ?? $current_user['username'] ?? 'User';
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Financial Summary — Orion Brothers</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
  <link rel="stylesheet" href="/wp-content/plugins/orion-inventory/assets/css/main.css">
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

    /* Form fields */
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
    .orion-input[readonly] {
      background: rgba(50,237,255,0.08);
      border-color: rgba(50,237,255,0.35);
      cursor: default;
    }
    .orion-input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1) opacity(.4); cursor: pointer; }
    .orion-select option { background: #0B1B3C; color: #fff; }
    .orion-textarea { resize: vertical; min-height: 80px; }

    label { color: rgba(255,255,255,0.6); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.375rem; }

    /* Currency prefix wrapper */
    .currency-wrap { position: relative; }
    .currency-wrap .prefix {
      position: absolute;
      left: 0.75rem; top: 50%; transform: translateY(-50%);
      color: rgba(50,237,255,0.6);
      font-size: 0.875rem;
      font-weight: 700;
      pointer-events: none;
      z-index: 1;
    }
    .currency-wrap input { padding-left: 1.75rem; }

    /* Cash Left highlight */
    .cash-left-wrap {
      background: rgba(50,237,255,0.1);
      border: 1.5px solid rgba(50,237,255,0.4);
      border-radius: 0.875rem;
      padding: 1.125rem 1.25rem;
    }
    .cash-left-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: rgba(50,237,255,0.7); margin-bottom: 0.375rem; }
    .cash-left-value { font-size: 1.75rem; font-weight: 900; color: #32EDFF; line-height: 1; letter-spacing: -0.02em; }

    /* Auto-calc readonly with suffix note */
    .auto-note { font-size: 0.7rem; color: rgba(50,237,255,0.5); margin-top: 0.25rem; font-style: italic; }

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
    .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

    .btn-ghost {
      display: inline-flex; align-items: center; gap: 0.4rem;
      padding: 0.5rem 1rem;
      border-radius: 0.625rem;
      border: 1px solid rgba(50,237,255,0.2);
      background: rgba(50,237,255,0.06);
      color: #32EDFF;
      font-size: 0.8125rem;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s;
    }
    .btn-ghost:hover { background: rgba(50,237,255,0.14); border-color: rgba(50,237,255,0.4); }

    /* Date loaded indicator */
    .date-loaded-badge {
      display: inline-flex; align-items: center; gap: 0.4rem;
      padding: 0.3rem 0.875rem;
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 600;
    }
    .date-loaded-badge.existing { background: rgba(251,191,36,0.12); color: #fbbf24; border: 1px solid rgba(251,191,36,0.25); }
    .date-loaded-badge.new      { background: rgba(52,211,153,0.12); color: #34d399; border: 1px solid rgba(52,211,153,0.25); }

    /* Toast */
    .toast {
      position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%) translateY(60px);
      background: rgba(11,27,60,0.95);
      border: 1px solid rgba(50,237,255,0.25);
      border-radius: 9999px;
      padding: 0.75rem 1.5rem;
      font-size: 0.875rem;
      font-weight: 600;
      color: #fff;
      z-index: 999;
      transition: transform 0.35s cubic-bezier(.34,1.56,.64,1), opacity 0.3s;
      opacity: 0;
      white-space: nowrap;
    }
    .toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
    .toast.success { border-color: rgba(52,211,153,0.4); color: #34d399; }
    .toast.error   { border-color: rgba(248,113,113,0.4); color: #f87171; }

    /* Divider */
    .field-divider { border: none; border-top: 1px solid rgba(50,237,255,0.08); margin: 0.5rem 0; }

    /* Orb */
    .orb { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; z-index: 0; }
  </style>
</head>
<body>
  <div class="orb" style="width:500px;height:500px;background:rgba(50,237,255,0.04);top:-100px;right:-150px;"></div>
  <div class="orb" style="width:400px;height:400px;background:rgba(50,237,255,0.03);bottom:-80px;left:-100px;"></div>

  <!-- Header -->
  <header class="glass-header sticky top-0 z-50 px-4 sm:px-6">
    <div class="max-w-3xl mx-auto flex items-center gap-3 h-16">
      <a href="/orion/home" class="flex items-center justify-center w-9 h-9 rounded-xl flex-shrink-0"
         style="background:rgba(50,237,255,0.08);border:1px solid rgba(50,237,255,0.18);" aria-label="Back">
        <iconify-icon icon="solar:arrow-left-linear" style="color:#32EDFF;font-size:1.25rem;"></iconify-icon>
      </a>
      <div class="flex items-center gap-2.5 flex-1 min-w-0">
        <div class="flex items-center justify-center w-9 h-9 rounded-xl flex-shrink-0"
             style="background:rgba(50,237,255,0.1);border:1px solid rgba(50,237,255,0.2);">
          <iconify-icon icon="solar:dollar-minimalistic-linear" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
        </div>
        <div>
          <h1 class="text-white font-bold text-lg leading-tight">Financial Summary</h1>
          <p class="text-xs leading-tight" style="color:rgba(255,255,255,0.4);">Daily financial record</p>
        </div>
      </div>
      <a href="/orion/financial-history" class="btn-ghost flex-shrink-0">
        <iconify-icon icon="solar:history-linear" style="font-size:1rem;"></iconify-icon>
        <span class="hidden sm:inline">View History</span>
      </a>
    </div>
  </header>

  <main class="relative z-10 max-w-3xl mx-auto px-4 sm:px-6 py-8">

    <!-- Date Picker Row -->
    <div class="glass-card p-4 mb-5 flex flex-col sm:flex-row sm:items-center gap-3">
      <div class="flex-1">
        <label for="summaryDate">Summary Date</label>
        <input type="date" id="summaryDate" class="orion-input" onchange="onDateChange()">
      </div>
      <div class="flex items-end">
        <div id="dateBadge" class="hidden date-loaded-badge new">
          <iconify-icon icon="solar:info-circle-linear" style="font-size:0.9rem;"></iconify-icon>
          <span id="dateBadgeText">New record</span>
        </div>
      </div>
    </div>

    <!-- Main Form Card -->
    <form id="financialForm" novalidate>
      <div class="glass-card p-5 mb-5">
        <h2 class="font-bold text-white text-base flex items-center gap-2 mb-5">
          <iconify-icon icon="solar:chart-square-linear" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
          Revenue
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
          <!-- Total Sales -->
          <div>
            <label for="total_sales">Total Sales (₦)</label>
            <div class="currency-wrap">
              <span class="prefix">₦</span>
              <input type="text" id="total_sales" name="total_sales" class="orion-input"
                     placeholder="0.00" inputmode="decimal"
                     oninput="formatMoneyInput(this); recalculate();">
            </div>
          </div>

          <!-- Transfer / Card Sales -->
          <div>
            <label for="transfer_card_sales">Transfer / Card Sales (₦)</label>
            <div class="currency-wrap">
              <span class="prefix">₦</span>
              <input type="text" id="transfer_card_sales" name="transfer_card_sales" class="orion-input"
                     placeholder="0.00" inputmode="decimal"
                     oninput="formatMoneyInput(this); recalculate();">
            </div>
          </div>
        </div>

        <!-- Cash Sales (auto) -->
        <div class="mb-4">
          <label for="cash_sales">Cash Sales (₦) <span style="color:rgba(50,237,255,0.5);font-weight:400;text-transform:none;letter-spacing:0;">— auto calculated</span></label>
          <div class="currency-wrap">
            <span class="prefix">₦</span>
            <input type="text" id="cash_sales" name="cash_sales" class="orion-input"
                   placeholder="0.00" readonly tabindex="-1">
          </div>
          <p class="auto-note">= Total Sales − Transfer/Card Sales</p>
        </div>
      </div>

      <!-- Debtors Card -->
      <div class="glass-card p-5 mb-5">
        <h2 class="font-bold text-white text-base flex items-center gap-2 mb-5">
          <iconify-icon icon="solar:hand-money-linear" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
          Debtors &amp; Old Cash
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label for="old_cash">Old Cash (₦)</label>
            <div class="currency-wrap">
              <span class="prefix">₦</span>
              <input type="text" id="old_cash" name="old_cash" class="orion-input"
                     placeholder="0.00" inputmode="decimal"
                     oninput="formatMoneyInput(this); recalculate();">
            </div>
          </div>
          <div>
            <label for="debtors_transfer">Debtors Transfer (₦)</label>
            <div class="currency-wrap">
              <span class="prefix">₦</span>
              <input type="text" id="debtors_transfer" name="debtors_transfer" class="orion-input"
                     placeholder="0.00" inputmode="decimal"
                     oninput="formatMoneyInput(this); recalculate();">
            </div>
          </div>
          <div>
            <label for="debtors_cash">Debtors Cash (₦)</label>
            <div class="currency-wrap">
              <span class="prefix">₦</span>
              <input type="text" id="debtors_cash" name="debtors_cash" class="orion-input"
                     placeholder="0.00" inputmode="decimal"
                     oninput="formatMoneyInput(this); recalculate();">
            </div>
          </div>
        </div>
      </div>

      <!-- Expenses Card -->
      <div class="glass-card p-5 mb-5">
        <h2 class="font-bold text-white text-base flex items-center gap-2 mb-5">
          <iconify-icon icon="solar:minus-circle-linear" style="color:#f87171;font-size:1.2rem;"></iconify-icon>
          Expenses &amp; Discount
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
          <div>
            <label for="expense">Expense (₦)</label>
            <div class="currency-wrap">
              <span class="prefix">₦</span>
              <input type="text" id="expense" name="expense" class="orion-input"
                     placeholder="0.00" inputmode="decimal"
                     oninput="formatMoneyInput(this); recalculate();">
            </div>
          </div>
          <div>
            <label for="discount">Discount (₦)</label>
            <div class="currency-wrap">
              <span class="prefix">₦</span>
              <input type="text" id="discount" name="discount" class="orion-input"
                     placeholder="0.00" inputmode="decimal"
                     oninput="formatMoneyInput(this);">
            </div>
          </div>
        </div>
        <div>
          <label for="expense_remarks">Expense Remarks</label>
          <textarea id="expense_remarks" name="expense_remarks" class="orion-textarea"
                    placeholder="Brief description of expenses…"></textarea>
        </div>
      </div>

      <!-- Cash Left Card (highlighted) -->
      <div class="cash-left-wrap mb-6">
        <div class="cash-left-label flex items-center gap-1.5">
          <iconify-icon icon="solar:wallet-money-linear" style="font-size:1rem;"></iconify-icon>
          Cash Left
        </div>
        <div class="flex items-end justify-between gap-4 mt-2 flex-wrap">
          <div class="cash-left-value" id="cashLeftDisplay">₦0.00</div>
          <input type="hidden" id="cash_left" name="cash_left" value="0">
          <p class="text-xs" style="color:rgba(50,237,255,0.5);font-style:italic;max-width:300px;">
            = Total Sales − Transfer/Card + Debtors Cash − Expense + Old Cash
          </p>
        </div>
      </div>

      <!-- Submit -->
      <div class="flex items-center justify-between flex-wrap gap-3">
        <div id="existingRecordNote" class="hidden text-sm flex items-center gap-1.5" style="color:#fbbf24;">
          <iconify-icon icon="solar:info-circle-linear" style="font-size:1rem;"></iconify-icon>
          Editing existing record for this date
        </div>
        <div class="ml-auto flex gap-3">
          <button type="submit" class="btn-primary" id="submitBtn">
            <iconify-icon icon="solar:diskette-linear" style="font-size:1.1rem;"></iconify-icon>
            <span id="submitLabel">Save Summary</span>
          </button>
        </div>
      </div>
    </form>

  </main>

  <!-- Toast -->
  <div class="toast" id="toast" role="alert" aria-live="assertive"></div>

  <script>
  window.orionConfig = {
    baseUrl: '/wp-json/orion/v1',
    currentUser: <?php echo json_encode(Orion_Auth::get_current_user()); ?>,
  };
  </script>
  <script src="/wp-content/plugins/orion-inventory/assets/js/app.js"></script>
  <script>
  const BASE = window.orionConfig.baseUrl;
  let existingRecordId = null;
  let submitting = false;

  /* ── Utilities ── */
  function parseNum(id_or_val) {
    const v = typeof id_or_val === 'string' && document.getElementById(id_or_val)
              ? document.getElementById(id_or_val).value
              : id_or_val;
    const n = parseFloat(String(v).replace(/,/g,''));
    return isNaN(n) ? 0 : n;
  }

  function formatNum(n) {
    return Number(n).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function formatMoneyInput(el) {
    const raw   = el.value.replace(/[^0-9.]/g, '');
    const parts = raw.split('.');
    let integer = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    el.value = parts.length > 1 ? integer + '.' + parts[1].slice(0, 2) : integer;
  }

  function setField(id, val) {
    const el = document.getElementById(id);
    if (!el) return;
    el.value = val !== null && val !== undefined ? formatNum(parseFloat(val)||0) : '';
  }

  function showToast(msg, type = '') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast' + (type ? ' ' + type : '');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
  }

  /* ── Real-time calculations ── */
  function calculateCashLeft() {
    const totalSales  = parseNum('total_sales');
    const transferCard = parseNum('transfer_card_sales');
    const debtorsCash = parseNum('debtors_cash');
    const expense     = parseNum('expense');
    const oldCash     = parseNum('old_cash');
    const cashLeft    = totalSales - transferCard + debtorsCash - expense + oldCash;
    return cashLeft;
  }

  function recalculate() {
    const totalSales   = parseNum('total_sales');
    const transferCard = parseNum('transfer_card_sales');
    const cashSales    = Math.max(0, totalSales - transferCard);

    document.getElementById('cash_sales').value = formatNum(cashSales);

    const cashLeft = calculateCashLeft();
    document.getElementById('cash_left').value = cashLeft.toFixed(2);
    document.getElementById('cashLeftDisplay').textContent = '₦' + formatNum(cashLeft);
    document.getElementById('cashLeftDisplay').style.color = cashLeft < 0 ? '#f87171' : '#32EDFF';
  }

  /* ── Date input ── */
  function todayISO() {
    const d = new Date();
    return d.toISOString().slice(0, 10);
  }

  function onDateChange() {
    const date = document.getElementById('summaryDate').value;
    if (date) loadExistingRecord(date);
  }

  async function loadExistingRecord(date) {
    const badge     = document.getElementById('dateBadge');
    const badgeText = document.getElementById('dateBadgeText');
    const note      = document.getElementById('existingRecordNote');
    const submitLabel = document.getElementById('submitLabel');

    badge.classList.remove('hidden', 'existing', 'new');
    badge.classList.add('new');
    badgeText.textContent = 'New record';
    existingRecordId = null;
    note.classList.add('hidden');
    submitLabel.textContent = 'Save Summary';

    try {
      const r = await fetch(`${BASE}/financial-summary?date=${encodeURIComponent(date)}`);
      if (r.status === 404) {
        badge.classList.remove('hidden');
        return;
      }
      if (!r.ok) throw new Error('HTTP ' + r.status);
      const d = await r.json();
      const record = Array.isArray(d) ? d[0] : d;
      if (record && record.id) {
        existingRecordId = record.id;
        populateForm(record);
        badge.classList.remove('new');
        badge.classList.add('existing', 'hidden');
        badge.classList.remove('hidden');
        badgeText.textContent = 'Existing record loaded';
        note.classList.remove('hidden');
        submitLabel.textContent = 'Update Summary';
      } else {
        badge.classList.remove('hidden');
      }
    } catch (_) {
      badge.classList.remove('hidden');
    }
  }

  function populateForm(r) {
    const fields = ['total_sales','transfer_card_sales','expense','old_cash','debtors_transfer','debtors_cash','discount'];
    fields.forEach(f => setField(f, r[f] ?? 0));
    const remarksEl = document.getElementById('expense_remarks');
    if (remarksEl) remarksEl.value = r.expense_remarks || '';
    recalculate();
  }

  /* ── Form submit ── */
  document.getElementById('financialForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (submitting) return;

    const date = document.getElementById('summaryDate').value;
    if (!date) { showToast('Please select a date.', 'error'); return; }

    recalculate();

    const payload = {
      summary_date:        date,
      total_sales:         parseNum('total_sales'),
      transfer_card_sales: parseNum('transfer_card_sales'),
      cash_sales:          parseNum('cash_sales'),
      expense:             parseNum('expense'),
      expense_remarks:     document.getElementById('expense_remarks').value.trim(),
      old_cash:            parseNum('old_cash'),
      debtors_transfer:    parseNum('debtors_transfer'),
      debtors_cash:        parseNum('debtors_cash'),
      discount:            parseNum('discount'),
      cash_left:           parseFloat(document.getElementById('cash_left').value) || 0,
    };

    submitting = true;
    const btn   = document.getElementById('submitBtn');
    const label = document.getElementById('submitLabel');
    btn.disabled = true;
    const origLabel = label.textContent;
    label.textContent = 'Saving…';

    try {
      const method = existingRecordId ? 'PUT' : 'POST';
      const url    = existingRecordId
        ? `${BASE}/financial-summary/${existingRecordId}`
        : `${BASE}/financial-summary`;

      const r = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const d = await r.json();
      if (!r.ok) throw new Error(d.message || 'Save failed.');

      if (!existingRecordId && d.id) {
        existingRecordId = d.id;
        label.textContent = 'Update Summary';
        document.getElementById('existingRecordNote').classList.remove('hidden');
        const badge = document.getElementById('dateBadge');
        badge.className = 'date-loaded-badge existing';
        document.getElementById('dateBadgeText').textContent = 'Record saved';
        badge.classList.remove('hidden');
      }

      showToast(existingRecordId ? 'Summary updated!' : 'Summary saved!', 'success');
    } catch (err) {
      showToast(err.message, 'error');
      label.textContent = origLabel;
    } finally {
      submitting = false;
      btn.disabled = false;
    }
  });

  /* ── Init ── */
  (function init() {
    const dateEl = document.getElementById('summaryDate');
    dateEl.value = todayISO();
    loadExistingRecord(dateEl.value);
  })();
  </script>
</body>
</html>
