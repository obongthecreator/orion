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
  <title>Sales History — Orion Brothers</title>
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

    /* Search / filter inputs */
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
    .orion-select option { background: #0B1B3C; color: #fff; }

    /* Table */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th {
      padding: 0.75rem 1rem;
      text-align: left;
      font-size: 0.7rem;
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
    .data-table td {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      font-size: 0.875rem;
      vertical-align: middle;
    }
    .data-table tbody tr { cursor: pointer; transition: background 0.15s; }
    .data-table tbody tr:hover td { background: rgba(50,237,255,0.04); }
    .data-table tbody tr.expanded td { background: rgba(50,237,255,0.06); }

    /* Expanded detail row */
    .detail-row { display: none; }
    .detail-row.open { display: table-row; }
    .detail-cell {
      background: rgba(11,27,60,0.8) !important;
      border-bottom: 1px solid rgba(50,237,255,0.1) !important;
      padding: 0 !important;
    }
    .detail-inner {
      padding: 1rem 1.5rem;
      border-top: 1px solid rgba(50,237,255,0.08);
    }

    /* Badge */
    .badge {
      display: inline-flex; align-items: center;
      padding: 0.2rem 0.6rem;
      border-radius: 9999px;
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }
    .badge-transfer { background: rgba(50,150,255,0.15); color: #60a5fa; border: 1px solid rgba(50,150,255,0.25); }
    .badge-cash     { background: rgba(50,237,100,0.12); color: #4ade80; border: 1px solid rgba(50,237,100,0.2); }
    .badge-both     { background: rgba(200,100,255,0.12); color: #c084fc; border: 1px solid rgba(200,100,255,0.2); }

    /* Buttons */
    .btn-ghost {
      display: inline-flex; align-items: center; gap: 0.4rem;
      padding: 0.5rem 1rem;
      border-radius: 0.625rem;
      border: 1px solid rgba(50,237,255,0.2);
      background: rgba(50,237,255,0.07);
      color: rgba(50,237,255,0.85);
      font-size: 0.8125rem; font-weight: 600;
      cursor: pointer; transition: all 0.15s;
      text-decoration: none;
    }
    .btn-ghost:hover { background: rgba(50,237,255,0.15); border-color: rgba(50,237,255,0.4); }

    .btn-icon {
      display: inline-flex; align-items: center; justify-content: center;
      width: 2rem; height: 2rem;
      border-radius: 0.5rem;
      border: 1px solid rgba(50,237,255,0.2);
      background: rgba(50,237,255,0.07);
      color: rgba(50,237,255,0.8);
      cursor: pointer; transition: all 0.15s;
    }
    .btn-icon:hover { background: rgba(50,237,255,0.15); color: #32EDFF; }

    .btn-icon-danger {
      display: inline-flex; align-items: center; justify-content: center;
      width: 2rem; height: 2rem;
      border-radius: 0.5rem;
      border: 1px solid rgba(255,80,80,0.2);
      background: rgba(255,80,80,0.07);
      color: rgba(255,100,100,0.8);
      cursor: pointer; transition: all 0.15s;
    }
    .btn-icon-danger:hover { background: rgba(255,80,80,0.18); color: #ff8080; border-color: rgba(255,80,80,0.4); }

    /* Pagination */
    .page-btn {
      min-width: 2.25rem; height: 2.25rem;
      border-radius: 0.5rem;
      border: 1px solid rgba(50,237,255,0.18);
      background: rgba(50,237,255,0.05);
      color: rgba(255,255,255,0.6);
      font-size: 0.875rem; font-weight: 600;
      cursor: pointer; transition: all 0.15s;
      display: inline-flex; align-items: center; justify-content: center; padding: 0 0.5rem;
    }
    .page-btn:hover  { background: rgba(50,237,255,0.12); color: #32EDFF; border-color: rgba(50,237,255,0.35); }
    .page-btn.active { background: rgba(50,237,255,0.2); color: #32EDFF; border-color: #32EDFF; }
    .page-btn:disabled { opacity: 0.3; cursor: not-allowed; }

    /* Loading skeleton */
    .skeleton { background: linear-gradient(90deg, rgba(255,255,255,0.04) 25%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0.04) 75%); background-size: 200%; animation: shimmer 1.4s infinite; border-radius: 0.375rem; }
    @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

    /* Toast */
    .toast {
      position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%) translateY(100px);
      padding: 0.875rem 1.5rem; border-radius: 0.875rem;
      font-size: 0.9375rem; font-weight: 600; z-index: 9999;
      transition: transform 0.35s cubic-bezier(.34,1.56,.64,1), opacity 0.3s;
      opacity: 0; pointer-events: none; white-space: nowrap;
    }
    .toast.success { background: rgba(50,237,100,0.92); color: #0B1B3C; }
    .toast.error   { background: rgba(255,80,80,0.92);  color: #fff; }
    .toast.show    { transform: translateX(-50%) translateY(0); opacity: 1; }

    /* Modal */
    .modal-backdrop {
      position: fixed; inset: 0;
      background: rgba(0,0,0,0.7); backdrop-filter: blur(6px);
      z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 1rem;
      opacity: 0; pointer-events: none; transition: opacity 0.25s;
    }
    .modal-backdrop.open { opacity: 1; pointer-events: auto; }
    .modal-box {
      background: #0d1f45; border: 1px solid rgba(50,237,255,0.2); border-radius: 1.25rem;
      max-width: 480px; width: 100%; max-height: 85vh; overflow-y: auto;
      transform: translateY(20px) scale(0.97); transition: transform 0.25s cubic-bezier(.34,1.56,.64,1);
    }
    .modal-backdrop.open .modal-box { transform: translateY(0) scale(1); }
    .receipt-area { font-family: 'Courier New', monospace; font-size: 0.8125rem; line-height: 1.6; color: #fff; }

    /* Confirm delete modal */
    .confirm-modal { max-width: 380px; }

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
    <div class="max-w-7xl mx-auto flex items-center justify-between h-14 gap-3">
      <div class="flex items-center gap-2">
        <a href="/orion/home" class="btn-ghost px-2.5 py-2" aria-label="Back to home">
          <iconify-icon icon="solar:arrow-left-bold" style="font-size:1.1rem;"></iconify-icon>
        </a>
        <h1 class="text-white font-bold text-lg">Sales History</h1>
      </div>
      <a href="/orion/sales" class="btn-ghost">
        <iconify-icon icon="solar:add-circle-bold" style="font-size:0.95rem;"></iconify-icon>
        <span class="hidden sm:inline">New Sale</span>
      </a>
    </div>
  </header>

  <!-- Main -->
  <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-5">

    <!-- Filter bar -->
    <section class="glass-card p-4">
      <div class="flex flex-col sm:flex-row gap-3">
        <!-- Search -->
        <div class="relative flex-1">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none" style="color:rgba(50,237,255,0.5);">
            <iconify-icon icon="solar:magnifer-bold" style="font-size:1rem;"></iconify-icon>
          </span>
          <input type="search" id="searchInput" class="orion-input w-full" placeholder="Search by customer name…" style="padding-left:2.25rem;">
        </div>
        <!-- Date from -->
        <div class="flex items-center gap-2">
          <label for="dateFrom" class="text-xs font-semibold whitespace-nowrap" style="color:rgba(255,255,255,0.5);">From</label>
          <input type="date" id="dateFrom" class="orion-input">
        </div>
        <!-- Date to -->
        <div class="flex items-center gap-2">
          <label for="dateTo" class="text-xs font-semibold whitespace-nowrap" style="color:rgba(255,255,255,0.5);">To</label>
          <input type="date" id="dateTo" class="orion-input">
        </div>
        <button type="button" id="filterBtn" class="btn-ghost flex-shrink-0">
          <iconify-icon icon="solar:filter-bold" style="font-size:0.9rem;"></iconify-icon>
          Filter
        </button>
        <button type="button" id="clearFilterBtn" class="btn-ghost flex-shrink-0" style="border-color:rgba(255,255,255,0.12);color:rgba(255,255,255,0.4);">
          <iconify-icon icon="solar:close-circle-bold" style="font-size:0.9rem;"></iconify-icon>
          Clear
        </button>
      </div>
    </section>

    <!-- Results summary -->
    <div class="flex items-center justify-between">
      <p class="text-sm" style="color:rgba(255,255,255,0.45);">
        Showing <span id="resultCount" class="text-white font-semibold">—</span> sale(s)
      </p>
      <p class="text-sm font-semibold" style="color:rgba(50,237,255,0.8);">
        Total: <span id="totalRevenue">—</span>
      </p>
    </div>

    <!-- Table card -->
    <section class="glass-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="data-table min-w-[700px]" id="salesTable">
          <thead>
            <tr>
              <th data-sort="date">Date <iconify-icon icon="solar:sort-vertical-bold" style="font-size:0.75rem;vertical-align:-1px;opacity:0.5;"></iconify-icon></th>
              <th data-sort="customer">Customer</th>
              <th>Items</th>
              <th data-sort="total">Total <iconify-icon icon="solar:sort-vertical-bold" style="font-size:0.75rem;vertical-align:-1px;opacity:0.5;"></iconify-icon></th>
              <th>Payment</th>
              <th>Staff</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody id="salesBody">
            <!-- Rows injected by JS -->
          </tbody>
        </table>
      </div>

      <!-- Empty / loading state -->
      <div id="tableState" class="py-16 text-center hidden">
        <iconify-icon id="stateIcon" icon="solar:cart-large-bold" style="font-size:3rem;color:rgba(50,237,255,0.2);"></iconify-icon>
        <p id="stateText" class="mt-3 text-sm" style="color:rgba(255,255,255,0.35);">Loading sales…</p>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between px-4 py-3 border-t" style="border-color:rgba(50,237,255,0.1);">
        <p class="text-xs" style="color:rgba(255,255,255,0.35);">Page <span id="pageInfo">1 of 1</span></p>
        <div class="flex gap-1.5" id="paginationBtns"></div>
      </div>
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
    </div>
  </div>

  <!-- Confirm delete modal -->
  <div class="modal-backdrop" id="confirmModal" role="dialog" aria-modal="true">
    <div class="modal-box confirm-modal p-6">
      <div class="flex items-center gap-3 mb-4">
        <div class="flex items-center justify-center w-11 h-11 rounded-xl" style="background:rgba(255,80,80,0.15);border:1px solid rgba(255,80,80,0.3);">
          <iconify-icon icon="solar:trash-bin-trash-bold" style="color:#ff8080;font-size:1.4rem;"></iconify-icon>
        </div>
        <div>
          <h3 class="text-white font-bold text-base">Delete Sale?</h3>
          <p class="text-xs mt-0.5" style="color:rgba(255,255,255,0.45);">This action cannot be undone.</p>
        </div>
      </div>
      <div class="flex gap-2.5 mt-2">
        <button type="button" id="cancelDeleteBtn" class="btn-ghost flex-1 justify-center">Cancel</button>
        <button type="button" id="confirmDeleteBtn"
                class="flex-1 flex items-center justify-center gap-1.5 py-2 px-4 rounded-xl font-semibold text-sm"
                style="background:rgba(255,80,80,0.2);border:1px solid rgba(255,80,80,0.4);color:#ff8080;cursor:pointer;">
          <iconify-icon icon="solar:trash-bin-trash-bold" style="font-size:1rem;"></iconify-icon>
          Delete
        </button>
      </div>
    </div>
  </div>

<script>
/* ══════════════════════════════════════════════
   SALES HISTORY PAGE — VANILLA JS
══════════════════════════════════════════════ */

const IS_ADMIN = <?php echo $is_admin ? 'true' : 'false'; ?>;
const fmt      = n => '₦' + Number(n || 0).toLocaleString('en-NG', { minimumFractionDigits: 0, maximumFractionDigits: 2 });

let allSales      = [];
let filteredSales = [];
let currentPage   = 1;
const PER_PAGE    = 15;
let sortKey       = 'date';
let sortDir       = 'desc';
let pendingDeleteId = null;

/* ── Toast ── */
function showToast(msg, type = 'success') {
  const el = document.getElementById('toast');
  el.textContent = msg;
  el.className = `toast ${type}`;
  void el.offsetWidth;
  el.classList.add('show');
  setTimeout(() => el.classList.remove('show'), 3500);
}

/* ── Fetch sales ── */
async function loadSales() {
  setTableState('loading');
  try {
    const params = new URLSearchParams();
    const search = document.getElementById('searchInput').value.trim();
    const from   = document.getElementById('dateFrom').value;
    const to     = document.getElementById('dateTo').value;
    if (search) params.set('search', search);
    if (from)   params.set('date_from', from);
    if (to)     params.set('date_to', to);

    const res  = await fetch(`/wp-json/orion/v1/sales?${params}`, {
      headers: { 'Authorization': 'Bearer ' + (localStorage.getItem('orion_token') || '') }
    });
    const data = await res.json();
    allSales = Array.isArray(data) ? data : (data.data || []);
    applySort();
  } catch (_) {
    setTableState('error');
  }
}

/* ── Sort ── */
function applySort() {
  filteredSales = [...allSales].sort((a, b) => {
    let va = a[sortKey] ?? '', vb = b[sortKey] ?? '';
    if (sortKey === 'total') { va = parseFloat(va) || 0; vb = parseFloat(vb) || 0; }
    if (va < vb) return sortDir === 'asc' ?  -1 : 1;
    if (va > vb) return sortDir === 'asc' ?   1 : -1;
    return 0;
  });
  currentPage = 1;
  renderTable();
}

/* ── Render ── */
function renderTable() {
  const tbody  = document.getElementById('salesBody');
  const start  = (currentPage - 1) * PER_PAGE;
  const page   = filteredSales.slice(start, start + PER_PAGE);

  if (!filteredSales.length) { setTableState('empty'); return; }
  setTableState('hidden');

  const totalRev = filteredSales.reduce((s, r) => s + (parseFloat(r.grand_total) || 0), 0);
  document.getElementById('resultCount').textContent  = filteredSales.length;
  document.getElementById('totalRevenue').textContent = fmt(totalRev);

  tbody.innerHTML = '';
  page.forEach((sale, i) => {
    const globalIdx = start + i;
    const date      = sale.order_date ? new Date(sale.order_date).toLocaleDateString('en-NG', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
    const items     = Array.isArray(sale.items) ? sale.items : [];
    const summary   = items.length
      ? items.slice(0, 2).map(it => it.product_name || it.name || 'Item').join(', ') + (items.length > 2 ? ` +${items.length - 2} more` : '')
      : '—';
    const payMethod = (sale.payment_method || '').toLowerCase();
    const badgeCls  = payMethod === 'cash' ? 'badge-cash' : payMethod === 'both' ? 'badge-both' : 'badge-transfer';

    /* Main row */
    const tr = document.createElement('tr');
    tr.dataset.idx    = globalIdx;
    tr.dataset.saleId = sale.id;
    tr.innerHTML = `
      <td>
        <div class="font-medium text-sm text-white">${date}</div>
        <div class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35);">${sale.id ? '#' + sale.id : ''}</div>
      </td>
      <td>
        <div class="font-medium text-sm text-white">${escHtml(sale.customer_name || '—')}</div>
        ${sale.customer_whatsapp ? `<div class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35);">${escHtml(sale.customer_whatsapp)}</div>` : ''}
      </td>
      <td class="text-xs" style="color:rgba(255,255,255,0.6);max-width:200px;">
        <span class="line-clamp-2">${escHtml(summary)}</span>
      </td>
      <td>
        <span class="font-bold" style="color:#32EDFF;">${fmt(sale.grand_total)}</span>
      </td>
      <td><span class="badge ${badgeCls}">${escHtml(sale.payment_method || '—')}</span></td>
      <td class="text-sm" style="color:rgba(255,255,255,0.6);">${escHtml(sale.staff_name || '—')}</td>
      <td>
        <div class="flex items-center justify-end gap-1.5">
          <button type="button" class="btn-icon receipt-btn" data-idx="${globalIdx}" aria-label="View receipt">
            <iconify-icon icon="solar:printer-bold" style="font-size:0.95rem;"></iconify-icon>
          </button>
          ${IS_ADMIN ? `<button type="button" class="btn-icon-danger delete-btn" data-sale-id="${sale.id}" aria-label="Delete sale"><iconify-icon icon="solar:trash-bin-trash-bold" style="font-size:0.95rem;"></iconify-icon></button>` : ''}
          <button type="button" class="btn-icon expand-btn" data-idx="${globalIdx}" aria-label="Expand details" aria-expanded="false">
            <iconify-icon icon="solar:alt-arrow-down-bold" style="font-size:0.85rem;"></iconify-icon>
          </button>
        </div>
      </td>
    `;

    /* Detail row */
    const detailTr = document.createElement('tr');
    detailTr.className = 'detail-row';
    detailTr.dataset.detailFor = globalIdx;
    const itemsHtml = items.length
      ? items.map(it => `
          <div class="flex items-center justify-between py-1 border-b" style="border-color:rgba(255,255,255,0.05);">
            <span class="text-sm text-white">${escHtml(it.product_name || it.name || 'Item')}</span>
            <div class="text-right text-xs" style="color:rgba(255,255,255,0.5);">
              ${fmt(it.price)} × ${it.qty || 1}
              ${it.discount > 0 ? ` − disc ${fmt(it.discount)}` : ''}
              = <span class="font-semibold" style="color:#32EDFF;">${fmt(Math.max(0, (it.price * (it.qty || 1)) - (it.discount || 0)))}</span>
            </div>
          </div>`).join('')
      : '<p class="text-xs" style="color:rgba(255,255,255,0.35);">No item details available.</p>';

    detailTr.innerHTML = `
      <td class="detail-cell" colspan="7">
        <div class="detail-inner">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:rgba(50,237,255,0.6);">Items Purchased</p>
              ${itemsHtml}
            </div>
            <div class="text-sm space-y-2">
              <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:rgba(50,237,255,0.6);">Payment Details</p>
              <div class="flex justify-between"><span style="color:rgba(255,255,255,0.5);">Method</span><span class="font-medium">${escHtml(sale.payment_method || '—')}</span></div>
              ${sale.transfer_amount > 0 ? `<div class="flex justify-between"><span style="color:rgba(255,255,255,0.5);">Transfer</span><span class="font-medium">${fmt(sale.transfer_amount)}</span></div>` : ''}
              ${sale.cash_amount > 0 ? `<div class="flex justify-between"><span style="color:rgba(255,255,255,0.5);">Cash</span><span class="font-medium">${fmt(sale.cash_amount)}</span></div>` : ''}
              <div class="flex justify-between pt-1 border-t" style="border-color:rgba(255,255,255,0.08);"><span style="color:rgba(255,255,255,0.5);">Grand Total</span><span class="font-bold" style="color:#32EDFF;">${fmt(sale.grand_total)}</span></div>
            </div>
          </div>
        </div>
      </td>
    `;

    tbody.appendChild(tr);
    tbody.appendChild(detailTr);

    /* Row expand on click */
    tr.addEventListener('click', (e) => {
      if (e.target.closest('.receipt-btn, .delete-btn')) return;
      toggleDetailRow(globalIdx, tr, detailTr);
    });

    /* Receipt button */
    tr.querySelector('.receipt-btn').addEventListener('click', (e) => {
      e.stopPropagation();
      showReceipt(globalIdx);
    });

    /* Delete button */
    if (IS_ADMIN) {
      tr.querySelector('.delete-btn').addEventListener('click', (e) => {
        e.stopPropagation();
        pendingDeleteId = sale.id;
        document.getElementById('confirmModal').classList.add('open');
      });
    }
  });

  renderPagination();
}

function toggleDetailRow(idx, mainTr, detailTr) {
  const isOpen = detailTr.classList.contains('open');
  detailTr.classList.toggle('open', !isOpen);
  mainTr.classList.toggle('expanded', !isOpen);
  const expandBtn = mainTr.querySelector('.expand-btn iconify-icon');
  if (expandBtn) {
    expandBtn.setAttribute('icon', isOpen ? 'solar:alt-arrow-down-bold' : 'solar:alt-arrow-up-bold');
  }
  mainTr.querySelector('.expand-btn').setAttribute('aria-expanded', String(!isOpen));
}

/* ── Pagination ── */
function renderPagination() {
  const total     = Math.ceil(filteredSales.length / PER_PAGE);
  const container = document.getElementById('paginationBtns');
  document.getElementById('pageInfo').textContent = `${currentPage} of ${total || 1}`;
  container.innerHTML = '';

  const prevBtn = document.createElement('button');
  prevBtn.type = 'button';
  prevBtn.className = 'page-btn';
  prevBtn.disabled = currentPage <= 1;
  prevBtn.innerHTML = `<iconify-icon icon="solar:arrow-left-bold" style="font-size:0.85rem;"></iconify-icon>`;
  prevBtn.addEventListener('click', () => { currentPage--; renderTable(); });
  container.appendChild(prevBtn);

  /* Page numbers — show max 5 around current */
  let start = Math.max(1, currentPage - 2);
  let end   = Math.min(total, start + 4);
  start     = Math.max(1, end - 4);
  for (let p = start; p <= end; p++) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'page-btn' + (p === currentPage ? ' active' : '');
    btn.textContent = p;
    btn.addEventListener('click', (pp => () => { currentPage = pp; renderTable(); })(p));
    container.appendChild(btn);
  }

  const nextBtn = document.createElement('button');
  nextBtn.type = 'button';
  nextBtn.className = 'page-btn';
  nextBtn.disabled = currentPage >= total;
  nextBtn.innerHTML = `<iconify-icon icon="solar:arrow-right-bold" style="font-size:0.85rem;"></iconify-icon>`;
  nextBtn.addEventListener('click', () => { currentPage++; renderTable(); });
  container.appendChild(nextBtn);
}

/* ── State display ── */
function setTableState(state) {
  const el = document.getElementById('tableState');
  const icon = document.getElementById('stateIcon');
  const text = document.getElementById('stateText');
  if (state === 'hidden') { el.classList.add('hidden'); return; }
  el.classList.remove('hidden');
  if (state === 'loading') {
    icon.setAttribute('icon', 'solar:refresh-bold');
    icon.style.animation = 'spin 1s linear infinite';
    text.textContent = 'Loading sales…';
  } else if (state === 'empty') {
    icon.setAttribute('icon', 'solar:cart-large-bold');
    icon.style.animation = '';
    text.textContent = 'No sales found.';
    document.getElementById('resultCount').textContent = '0';
    document.getElementById('totalRevenue').textContent = fmt(0);
  } else {
    icon.setAttribute('icon', 'solar:danger-circle-bold');
    icon.style.animation = '';
    text.textContent = 'Failed to load sales.';
  }
}

/* ── Receipt ── */
function showReceipt(idx) {
  const sale = filteredSales[idx];
  if (!sale) return;

  const items   = Array.isArray(sale.items) ? sale.items : [];
  const dateStr = sale.order_date ? new Date(sale.order_date).toLocaleString('en-NG') : new Date().toLocaleString('en-NG');
  const method  = (sale.payment_method || '').toLowerCase();

  let lines = '';
  lines += `================================\n`;
  lines += `     ORION BROTHERS\n`;
  lines += `     Inventory System\n`;
  lines += `================================\n`;
  lines += `Receipt # : ${sale.id || 'N/A'}\n`;
  lines += `Date      : ${dateStr}\n`;
  lines += `Customer  : ${sale.customer_name || '—'}\n`;
  if (sale.customer_whatsapp) lines += `WhatsApp  : ${sale.customer_whatsapp}\n`;
  lines += `Staff     : ${sale.staff_name || '—'}\n`;
  lines += `--------------------------------\n`;
  items.forEach((it, i) => {
    const total = Math.max(0, (it.price * (it.qty || 1)) - (it.discount || 0));
    lines += `${i+1}. ${it.product_name || it.name || 'Item'}\n`;
    lines += `   ${fmt(it.price)} x ${it.qty || 1}`;
    if (it.discount > 0) lines += ` - disc ${fmt(it.discount)}`;
    lines += ` = ${fmt(total)}\n`;
  });
  lines += `--------------------------------\n`;
  lines += `TOTAL     : ${fmt(sale.grand_total)}\n`;
  lines += `Payment   : ${(sale.payment_method || '').toUpperCase()}\n`;
  if (sale.transfer_amount > 0) lines += `Transfer  : ${fmt(sale.transfer_amount)}\n`;
  if (sale.cash_amount > 0)     lines += `Cash      : ${fmt(sale.cash_amount)}\n`;
  lines += `================================\n`;
  lines += `    Thank you for your patronage!\n`;
  lines += `================================\n`;

  document.getElementById('receiptContent').innerHTML = `<pre style="white-space:pre-wrap;word-break:break-all;">${lines}</pre>`;
  document.getElementById('receiptModal').classList.add('open');
}

/* ── Delete ── */
document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
  if (!pendingDeleteId) return;
  document.getElementById('confirmModal').classList.remove('open');
  try {
    const res  = await fetch(`/wp-json/orion/v1/sales/${pendingDeleteId}`, {
      method: 'DELETE',
      headers: { 'Authorization': 'Bearer ' + (localStorage.getItem('orion_token') || '') }
    });
    const data = await res.json();
    if (data.success) {
      showToast('Sale deleted.');
      await loadSales();
    } else {
      showToast(data.message || 'Failed to delete sale.', 'error');
    }
  } catch (_) {
    showToast('Network error.', 'error');
  }
  pendingDeleteId = null;
});
document.getElementById('cancelDeleteBtn').addEventListener('click', () => {
  document.getElementById('confirmModal').classList.remove('open');
  pendingDeleteId = null;
});

/* ── Column sort ── */
document.querySelectorAll('.data-table th[data-sort]').forEach(th => {
  th.addEventListener('click', () => {
    const key = th.dataset.sort;
    if (sortKey === key) sortDir = sortDir === 'asc' ? 'desc' : 'asc';
    else { sortKey = key; sortDir = 'desc'; }
    applySort();
  });
});

/* ── Filter / search ── */
document.getElementById('filterBtn').addEventListener('click', loadSales);
document.getElementById('clearFilterBtn').addEventListener('click', () => {
  document.getElementById('searchInput').value = '';
  document.getElementById('dateFrom').value    = '';
  document.getElementById('dateTo').value      = '';
  loadSales();
});
document.getElementById('searchInput').addEventListener('keydown', e => { if (e.key === 'Enter') loadSales(); });

/* ── Modal close ── */
document.getElementById('closeReceiptBtn').addEventListener('click', () => {
  document.getElementById('receiptModal').classList.remove('open');
});
document.getElementById('receiptModal').addEventListener('click', e => {
  if (e.target === document.getElementById('receiptModal'))
    document.getElementById('receiptModal').classList.remove('open');
});
document.getElementById('confirmModal').addEventListener('click', e => {
  if (e.target === document.getElementById('confirmModal')) {
    document.getElementById('confirmModal').classList.remove('open');
    pendingDeleteId = null;
  }
});

/* ── Helpers ── */
function escHtml(str) {
  return String(str).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

/* ── Init ── */
loadSales();

/* CSS spin */
const styleEl = document.createElement('style');
styleEl.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
document.head.appendChild(styleEl);
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
