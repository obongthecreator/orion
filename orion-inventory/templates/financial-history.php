<?php
if ( ! class_exists( 'Orion_Auth' ) ) {
    wp_redirect( '/orion/login' );
    exit;
}
Orion_Auth::require_login();
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Financial History — Orion Brothers</title>
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

    /* Filter inputs */
    .orion-input, .orion-select {
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(50,237,255,0.18);
      border-radius: 0.625rem;
      color: #fff;
      padding: 0.625rem 0.875rem;
      font-size: 0.875rem;
      outline: none;
      font-family: 'Inter', sans-serif;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .orion-input::placeholder { color: rgba(255,255,255,0.3); }
    .orion-input:focus, .orion-select:focus {
      border-color: rgba(50,237,255,0.5);
      box-shadow: 0 0 0 3px rgba(50,237,255,0.1);
    }
    .orion-input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1) opacity(.4); cursor: pointer; }
    .orion-select option { background: #0B1B3C; color: #fff; }

    /* Stat cards */
    .stat-card {
      background: rgba(11,27,60,0.65);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(50,237,255,0.15);
      border-radius: 1rem;
      padding: 1.125rem 1.375rem;
      transition: border-color 0.2s, transform 0.2s;
    }
    .stat-card:hover { border-color: rgba(50,237,255,0.35); transform: translateY(-2px); }

    /* Data table */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th {
      padding: 0.75rem 0.875rem;
      text-align: left;
      font-size: 0.65rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: rgba(50,237,255,0.7);
      border-bottom: 1px solid rgba(50,237,255,0.12);
      white-space: nowrap;
      cursor: pointer;
      user-select: none;
    }
    .data-table th:hover { color: #32EDFF; }
    .data-table th .sort-icon { opacity: 0.4; font-size: 0.75rem; vertical-align: middle; }
    .data-table th.sort-asc .sort-icon,
    .data-table th.sort-desc .sort-icon { opacity: 1; color: #32EDFF; }
    .data-table td {
      padding: 0.75rem 0.875rem;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      font-size: 0.8125rem;
      vertical-align: middle;
      white-space: nowrap;
    }
    .data-table tbody tr { transition: background 0.15s; cursor: pointer; }
    .data-table tbody tr:hover td { background: rgba(50,237,255,0.04); }
    .data-table tbody tr.expanded-parent td { background: rgba(50,237,255,0.07); }

    /* Expandable detail row */
    .detail-row { display: none; }
    .detail-row.open { display: table-row; }
    .detail-cell {
      background: rgba(11,27,60,0.85) !important;
      border-bottom: 1px solid rgba(50,237,255,0.1) !important;
      padding: 0 !important;
    }
    .detail-inner {
      padding: 1rem 1.25rem;
      border-top: 1px solid rgba(50,237,255,0.08);
    }

    /* Buttons */
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

    /* Amount colors */
    .amt-cyan   { color: #32EDFF; font-weight: 700; }
    .amt-green  { color: #34d399; font-weight: 600; }
    .amt-red    { color: #f87171; font-weight: 600; }
    .amt-yellow { color: #fbbf24; font-weight: 600; }
    .amt-muted  { color: rgba(255,255,255,0.55); }

    /* Table scroll */
    .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-scroll::-webkit-scrollbar { height: 4px; }
    .table-scroll::-webkit-scrollbar-track { background: transparent; }
    .table-scroll::-webkit-scrollbar-thumb { background: rgba(50,237,255,0.2); border-radius: 2px; }

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
    }
    .toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
    .toast.error { border-color: rgba(248,113,113,0.4); color: #f87171; }

    /* Orb */
    .orb { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; z-index: 0; }

    /* Print */
    @media print {
      .glass-header, .no-print { display: none !important; }
      body { background: #fff !important; color: #000 !important; }
      .glass-card, .stat-card { background: #fff !important; border: 1px solid #ddd !important; box-shadow: none !important; }
      .data-table th { color: #555 !important; border-color: #ddd !important; }
      .data-table td { color: #000 !important; border-color: #eee !important; }
      .orb { display: none !important; }
      .amt-cyan { color: #0099b8 !important; }
      .amt-green { color: #059669 !important; }
      .amt-red { color: #dc2626 !important; }
    }
  </style>
</head>
<body>
  <div class="orb" style="width:500px;height:500px;background:rgba(50,237,255,0.04);top:-100px;right:-150px;"></div>
  <div class="orb" style="width:400px;height:400px;background:rgba(50,237,255,0.03);bottom:-80px;left:-100px;"></div>

  <!-- Header -->
  <header class="glass-header sticky top-0 z-50 px-4 sm:px-6">
    <div class="max-w-7xl mx-auto flex items-center gap-3 h-16">
      <a href="/orion/financial-summary" class="flex items-center justify-center w-9 h-9 rounded-xl flex-shrink-0"
         style="background:rgba(50,237,255,0.08);border:1px solid rgba(50,237,255,0.18);" aria-label="Back">
        <iconify-icon icon="solar:arrow-left-linear" style="color:#32EDFF;font-size:1.25rem;"></iconify-icon>
      </a>
      <div class="flex items-center gap-2.5 flex-1 min-w-0">
        <div class="flex items-center justify-center w-9 h-9 rounded-xl flex-shrink-0"
             style="background:rgba(50,237,255,0.1);border:1px solid rgba(50,237,255,0.2);">
          <iconify-icon icon="solar:history-linear" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
        </div>
        <div>
          <h1 class="text-white font-bold text-lg leading-tight">Financial History</h1>
          <p class="text-xs leading-tight" style="color:rgba(255,255,255,0.4);">Past daily financial records</p>
        </div>
      </div>
      <div class="flex items-center gap-2 flex-shrink-0 no-print">
        <button class="btn-ghost" onclick="window.print()">
          <iconify-icon icon="solar:printer-linear" style="font-size:1rem;"></iconify-icon>
          <span class="hidden sm:inline">Print</span>
        </button>
        <button class="btn-ghost" onclick="loadHistory()" id="refreshBtn">
          <iconify-icon icon="solar:refresh-linear" style="font-size:1rem;"></iconify-icon>
          <span class="hidden sm:inline">Refresh</span>
        </button>
      </div>
    </div>
  </header>

  <main class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 py-8">

    <!-- Summary Totals -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-7">
      <div class="stat-card flex items-center gap-4">
        <div class="flex items-center justify-center w-11 h-11 rounded-xl flex-shrink-0"
             style="background:rgba(50,237,255,0.1);border:1px solid rgba(50,237,255,0.2);">
          <iconify-icon icon="solar:chart-2-linear" style="color:#32EDFF;font-size:1.4rem;"></iconify-icon>
        </div>
        <div>
          <p class="text-xs mb-0.5" style="color:rgba(255,255,255,0.45);">Total Revenue</p>
          <p class="text-xl font-black amt-cyan" id="statRevenue">₦0.00</p>
        </div>
      </div>
      <div class="stat-card flex items-center gap-4">
        <div class="flex items-center justify-center w-11 h-11 rounded-xl flex-shrink-0"
             style="background:rgba(248,113,113,0.08);border:1px solid rgba(248,113,113,0.2);">
          <iconify-icon icon="solar:minus-circle-linear" style="color:#f87171;font-size:1.4rem;"></iconify-icon>
        </div>
        <div>
          <p class="text-xs mb-0.5" style="color:rgba(255,255,255,0.45);">Total Expenses</p>
          <p class="text-xl font-black amt-red" id="statExpenses">₦0.00</p>
        </div>
      </div>
      <div class="stat-card flex items-center gap-4">
        <div class="flex items-center justify-center w-11 h-11 rounded-xl flex-shrink-0"
             style="background:rgba(52,211,153,0.08);border:1px solid rgba(52,211,153,0.2);">
          <iconify-icon icon="solar:wallet-money-linear" style="color:#34d399;font-size:1.4rem;"></iconify-icon>
        </div>
        <div>
          <p class="text-xs mb-0.5" style="color:rgba(255,255,255,0.45);">Net Cash Left</p>
          <p class="text-xl font-black" id="statNetCash" style="color:#34d399;">₦0.00</p>
        </div>
      </div>
    </div>

    <!-- Date Range Filter -->
    <div class="glass-card p-4 mb-5 no-print">
      <div class="flex flex-col sm:flex-row items-end gap-3">
        <div class="flex-1">
          <label class="text-xs font-600 uppercase tracking-wide mb-1 block" style="color:rgba(255,255,255,0.5);">From Date</label>
          <input type="date" id="filterFrom" class="orion-input" style="width:100%;" onchange="applyDateFilter()">
        </div>
        <div class="flex-1">
          <label class="text-xs font-600 uppercase tracking-wide mb-1 block" style="color:rgba(255,255,255,0.5);">To Date</label>
          <input type="date" id="filterTo" class="orion-input" style="width:100%;" onchange="applyDateFilter()">
        </div>
        <button class="btn-ghost flex-shrink-0" onclick="clearDateFilter()">
          <iconify-icon icon="solar:close-circle-linear" style="font-size:1rem;"></iconify-icon>
          Clear
        </button>
      </div>
    </div>

    <!-- History Table -->
    <div class="glass-card overflow-hidden">
      <div class="flex items-center justify-between p-4 border-b" style="border-color:rgba(50,237,255,0.1);">
        <h2 class="font-bold text-white text-base flex items-center gap-2">
          <iconify-icon icon="solar:calendar-linear" style="color:#32EDFF;font-size:1.1rem;"></iconify-icon>
          Records
          <span id="recordCount" class="ml-1 text-xs px-2 py-0.5 rounded-full"
                style="background:rgba(50,237,255,0.12);color:#32EDFF;font-weight:700;">0</span>
        </h2>
        <p class="text-xs hidden sm:block" style="color:rgba(255,255,255,0.35);">Click row to expand details</p>
      </div>

      <!-- Loading -->
      <div id="tableLoading" class="flex items-center justify-center gap-3 py-14 text-sm" style="color:rgba(255,255,255,0.4);">
        <iconify-icon icon="solar:refresh-linear" class="animate-spin" style="font-size:1.25rem;"></iconify-icon>
        Loading records…
      </div>

      <!-- Empty -->
      <div id="tableEmpty" class="hidden flex flex-col items-center gap-3 py-14 text-center px-4">
        <iconify-icon icon="solar:calendar-linear" style="color:rgba(50,237,255,0.25);font-size:3rem;"></iconify-icon>
        <p class="font-semibold" style="color:rgba(255,255,255,0.4);">No records found</p>
        <p class="text-sm" style="color:rgba(255,255,255,0.3);">Try adjusting the date range filter</p>
      </div>

      <div class="table-scroll" id="tableWrapper" style="display:none;">
        <table class="data-table" id="historyTable">
          <thead>
            <tr>
              <th onclick="sortBy('summary_date')" data-col="summary_date">
                Date <iconify-icon icon="solar:sort-vertical-linear" class="sort-icon"></iconify-icon>
              </th>
              <th onclick="sortBy('total_sales')" data-col="total_sales">
                Total Sales <iconify-icon icon="solar:sort-vertical-linear" class="sort-icon"></iconify-icon>
              </th>
              <th onclick="sortBy('transfer_card_sales')" data-col="transfer_card_sales">
                Transfer/Card <iconify-icon icon="solar:sort-vertical-linear" class="sort-icon"></iconify-icon>
              </th>
              <th onclick="sortBy('cash_sales')" data-col="cash_sales">
                Cash Sales <iconify-icon icon="solar:sort-vertical-linear" class="sort-icon"></iconify-icon>
              </th>
              <th onclick="sortBy('expense')" data-col="expense">
                Expense <iconify-icon icon="solar:sort-vertical-linear" class="sort-icon"></iconify-icon>
              </th>
              <th onclick="sortBy('old_cash')" data-col="old_cash">
                Old Cash <iconify-icon icon="solar:sort-vertical-linear" class="sort-icon"></iconify-icon>
              </th>
              <th onclick="sortBy('cash_left')" data-col="cash_left">
                Cash Left <iconify-icon icon="solar:sort-vertical-linear" class="sort-icon"></iconify-icon>
              </th>
              <th>Deb. Transfer</th>
              <th>Deb. Cash</th>
              <th onclick="sortBy('discount')" data-col="discount">
                Discount <iconify-icon icon="solar:sort-vertical-linear" class="sort-icon"></iconify-icon>
              </th>
              <th>Staff</th>
            </tr>
          </thead>
          <tbody id="historyTableBody"></tbody>
        </table>
      </div>
    </div>

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
  let allRecords     = [];
  let filteredRecords = [];
  let sortCol        = 'summary_date';
  let sortDir        = 'desc';

  function parseNum(v) { const n = parseFloat(String(v).replace(/,/g,'')); return isNaN(n)?0:n; }
  function formatNum(n) { return Number(n).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
  function escHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  function showToast(msg, type='') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast' + (type ? ' ' + type : '');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
  }

  /* ── Load records from API ── */
  async function loadHistory() {
    const btn = document.getElementById('refreshBtn');
    btn.querySelector('iconify-icon').classList.add('animate-spin');
    document.getElementById('tableLoading').classList.remove('hidden');
    document.getElementById('tableWrapper').style.display = 'none';
    document.getElementById('tableEmpty').classList.add('hidden');

    try {
      const r = await fetch(`${BASE}/financial-history`);
      if (!r.ok) throw new Error('HTTP ' + r.status);
      const d = await r.json();
      allRecords = Array.isArray(d) ? d : (d.records || []);
      applyDateFilter();
    } catch (err) {
      document.getElementById('tableLoading').innerHTML =
        `<iconify-icon icon="solar:close-circle-linear" style="color:#f87171;font-size:1.5rem;"></iconify-icon>
         <span style="color:#f87171;">Failed to load: ${err.message}</span>`;
      showToast('Failed to load history.', 'error');
    } finally {
      btn.querySelector('iconify-icon').classList.remove('animate-spin');
    }
  }

  /* ── Date range filter ── */
  function applyDateFilter() {
    const from = document.getElementById('filterFrom').value;
    const to   = document.getElementById('filterTo').value;

    filteredRecords = allRecords.filter(r => {
      const d = r.summary_date || r.date || '';
      if (from && d < from) return false;
      if (to   && d > to  ) return false;
      return true;
    });

    updateSummaryStats(filteredRecords);
    sortAndRender();
  }

  function clearDateFilter() {
    document.getElementById('filterFrom').value = '';
    document.getElementById('filterTo').value   = '';
    applyDateFilter();
  }

  /* ── Summary stats ── */
  function updateSummaryStats(records) {
    let revenue = 0, expenses = 0, netCash = 0;
    records.forEach(r => {
      revenue  += parseNum(r.total_sales);
      expenses += parseNum(r.expense);
      netCash  += parseNum(r.cash_left);
    });
    document.getElementById('statRevenue').textContent  = '₦' + formatNum(revenue);
    document.getElementById('statExpenses').textContent = '₦' + formatNum(expenses);
    const netEl = document.getElementById('statNetCash');
    netEl.textContent = '₦' + formatNum(netCash);
    netEl.style.color = netCash < 0 ? '#f87171' : '#34d399';
  }

  /* ── Sorting ── */
  function sortBy(col) {
    if (sortCol === col) sortDir = sortDir === 'asc' ? 'desc' : 'asc';
    else { sortCol = col; sortDir = col === 'summary_date' ? 'desc' : 'asc'; }
    document.querySelectorAll('.data-table th[data-col]').forEach(th => {
      th.classList.remove('sort-asc', 'sort-desc');
      if (th.dataset.col === col) th.classList.add('sort-' + sortDir);
    });
    sortAndRender();
  }

  function sortAndRender() {
    const numCols = ['total_sales','transfer_card_sales','cash_sales','expense','old_cash','cash_left','discount','debtors_transfer','debtors_cash'];
    const sorted  = [...filteredRecords].sort((a, b) => {
      if (numCols.includes(sortCol)) {
        const diff = parseNum(a[sortCol]||0) - parseNum(b[sortCol]||0);
        return sortDir === 'asc' ? diff : -diff;
      }
      const av = String(a[sortCol]||''); const bv = String(b[sortCol]||'');
      return sortDir === 'asc' ? av.localeCompare(bv) : bv.localeCompare(av);
    });
    renderTable(sorted);
  }

  /* ── Render ── */
  function renderTable(records) {
    const tbody   = document.getElementById('historyTableBody');
    const loading = document.getElementById('tableLoading');
    const empty   = document.getElementById('tableEmpty');
    const wrapper = document.getElementById('tableWrapper');

    loading.classList.add('hidden');
    document.getElementById('recordCount').textContent = records.length;

    if (!records.length) {
      wrapper.style.display = 'none';
      empty.classList.remove('hidden');
      return;
    }
    empty.classList.add('hidden');
    wrapper.style.display = '';

    tbody.innerHTML = '';

    records.forEach((r, idx) => {
      const date       = r.summary_date || r.date || '—';
      const dateDisp   = date !== '—' ? new Date(date + 'T00:00:00').toLocaleDateString('en-NG', {day:'2-digit',month:'short',year:'numeric'}) : '—';
      const cashLeft   = parseNum(r.cash_left);
      const cashColor  = cashLeft < 0 ? 'amt-red' : 'amt-cyan';
      const detailId   = `detail-${idx}`;

      const tr = document.createElement('tr');
      tr.onclick = () => toggleDetail(idx, tr);
      tr.innerHTML = `
        <td class="font-semibold" style="color:#fff;">${escHtml(dateDisp)}</td>
        <td class="amt-cyan">₦${formatNum(r.total_sales)}</td>
        <td class="amt-green">₦${formatNum(r.transfer_card_sales)}</td>
        <td class="amt-muted">₦${formatNum(r.cash_sales)}</td>
        <td class="amt-red">₦${formatNum(r.expense)}</td>
        <td class="amt-muted">₦${formatNum(r.old_cash)}</td>
        <td class="${cashColor}">₦${formatNum(r.cash_left)}</td>
        <td class="amt-yellow">₦${formatNum(r.debtors_transfer)}</td>
        <td class="amt-yellow">₦${formatNum(r.debtors_cash)}</td>
        <td class="amt-muted">₦${formatNum(r.discount)}</td>
        <td style="color:rgba(255,255,255,0.55);">${escHtml(r.staff_name || r.created_by || '—')}</td>`;
      tbody.appendChild(tr);

      /* Detail row */
      const detailTr = document.createElement('tr');
      detailTr.className = 'detail-row';
      detailTr.id = detailId;
      detailTr.innerHTML = buildDetailCell(r);
      tbody.appendChild(detailTr);
    });

    /* Restore sort indicators */
    document.querySelectorAll('.data-table th[data-col]').forEach(th => {
      th.classList.remove('sort-asc', 'sort-desc');
      if (th.dataset.col === sortCol) th.classList.add('sort-' + sortDir);
    });
  }

  function buildDetailCell(r) {
    const date = r.summary_date || r.date || '';
    const dateISO = date.slice(0,10);
    return `<td colspan="11" class="detail-cell"><div class="detail-inner">
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-4">
        <div>
          <p class="text-xs mb-1" style="color:rgba(255,255,255,0.4);">Expense Remarks</p>
          <p class="text-sm" style="color:rgba(255,255,255,0.75);">${escHtml(r.expense_remarks || '—')}</p>
        </div>
        <div>
          <p class="text-xs mb-1" style="color:rgba(255,255,255,0.4);">Debtors Transfer</p>
          <p class="text-sm amt-yellow">₦${formatNum(r.debtors_transfer)}</p>
        </div>
        <div>
          <p class="text-xs mb-1" style="color:rgba(255,255,255,0.4);">Debtors Cash</p>
          <p class="text-sm amt-yellow">₦${formatNum(r.debtors_cash)}</p>
        </div>
        <div>
          <p class="text-xs mb-1" style="color:rgba(255,255,255,0.4);">Discount</p>
          <p class="text-sm amt-muted">₦${formatNum(r.discount)}</p>
        </div>
      </div>
      <div class="flex items-center justify-between flex-wrap gap-2">
        <p class="text-xs" style="color:rgba(255,255,255,0.3);">
          Recorded by: <span style="color:rgba(255,255,255,0.55);">${escHtml(r.staff_name || r.created_by || 'Unknown')}</span>
          &nbsp;·&nbsp;
          ID: <span style="color:rgba(255,255,255,0.4);">${r.id || '—'}</span>
        </p>
        <a href="/orion/financial-summary?date=${encodeURIComponent(dateISO)}"
           class="btn-ghost" style="font-size:.75rem;padding:.3rem .75rem;" onclick="event.stopPropagation();">
          <iconify-icon icon="solar:pen-linear" style="font-size:.85rem;"></iconify-icon>
          Edit Record
        </a>
      </div>
    </div></td>`;
  }

  function toggleDetail(idx, parentTr) {
    const detailRow = document.getElementById(`detail-${idx}`);
    if (!detailRow) return;
    const isOpen = detailRow.classList.contains('open');
    detailRow.classList.toggle('open', !isOpen);
    parentTr.classList.toggle('expanded-parent', !isOpen);
  }

  /* ── Init ── */
  loadHistory();
  </script>
</body>
</html>
