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
  <title>Repairs History — Orion Brothers</title>
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

    /* Detail row */
    .detail-row { display: none; }
    .detail-row.open { display: table-row; }
    .detail-cell {
      background: rgba(11,27,60,0.8) !important;
      border-bottom: 1px solid rgba(50,237,255,0.1) !important;
      padding: 0 !important;
    }
    .detail-inner { padding: 1.25rem 1.5rem; border-top: 1px solid rgba(50,237,255,0.08); }

    /* Badges */
    .badge {
      display: inline-flex; align-items: center;
      padding: 0.2rem 0.6rem; border-radius: 9999px;
      font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
    }
    .badge-transfer { background: rgba(50,150,255,0.15); color: #60a5fa; border: 1px solid rgba(50,150,255,0.25); }
    .badge-cash     { background: rgba(50,237,100,0.12); color: #4ade80; border: 1px solid rgba(50,237,100,0.2); }
    .badge-both     { background: rgba(200,100,255,0.12); color: #c084fc; border: 1px solid rgba(200,100,255,0.2); }

    /* Category badge */
    .cat-badge {
      display: inline-flex; align-items: center;
      padding: 0.2rem 0.65rem; border-radius: 9999px;
      font-size: 0.7rem; font-weight: 600;
      background: rgba(50,237,255,0.1); color: rgba(50,237,255,0.9);
      border: 1px solid rgba(50,237,255,0.2);
    }

    /* Buttons */
    .btn-ghost {
      display: inline-flex; align-items: center; gap: 0.4rem;
      padding: 0.5rem 1rem; border-radius: 0.625rem;
      border: 1px solid rgba(50,237,255,0.2); background: rgba(50,237,255,0.07);
      color: rgba(50,237,255,0.85); font-size: 0.8125rem; font-weight: 600;
      cursor: pointer; transition: all 0.15s; text-decoration: none;
    }
    .btn-ghost:hover { background: rgba(50,237,255,0.15); border-color: rgba(50,237,255,0.4); }

    .btn-icon {
      display: inline-flex; align-items: center; justify-content: center;
      width: 2rem; height: 2rem; border-radius: 0.5rem;
      border: 1px solid rgba(50,237,255,0.2); background: rgba(50,237,255,0.07);
      color: rgba(50,237,255,0.8); cursor: pointer; transition: all 0.15s;
    }
    .btn-icon:hover { background: rgba(50,237,255,0.15); color: #32EDFF; }

    .btn-icon-danger {
      display: inline-flex; align-items: center; justify-content: center;
      width: 2rem; height: 2rem; border-radius: 0.5rem;
      border: 1px solid rgba(255,80,80,0.2); background: rgba(255,80,80,0.07);
      color: rgba(255,100,100,0.8); cursor: pointer; transition: all 0.15s;
    }
    .btn-icon-danger:hover { background: rgba(255,80,80,0.18); color: #ff8080; border-color: rgba(255,80,80,0.4); }

    /* Pagination */
    .page-btn {
      min-width: 2.25rem; height: 2.25rem; border-radius: 0.5rem;
      border: 1px solid rgba(50,237,255,0.18); background: rgba(50,237,255,0.05);
      color: rgba(255,255,255,0.6); font-size: 0.875rem; font-weight: 600;
      cursor: pointer; transition: all 0.15s;
      display: inline-flex; align-items: center; justify-content: center; padding: 0 0.5rem;
    }
    .page-btn:hover  { background: rgba(50,237,255,0.12); color: #32EDFF; border-color: rgba(50,237,255,0.35); }
    .page-btn.active { background: rgba(50,237,255,0.2); color: #32EDFF; border-color: #32EDFF; }
    .page-btn:disabled { opacity: 0.3; cursor: not-allowed; }

    /* Detail view modal */
    .modal-backdrop {
      position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(6px);
      z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 1rem;
      opacity: 0; pointer-events: none; transition: opacity 0.25s;
    }
    .modal-backdrop.open { opacity: 1; pointer-events: auto; }
    .modal-box {
      background: #0d1f45; border: 1px solid rgba(50,237,255,0.2); border-radius: 1.25rem;
      max-width: 540px; width: 100%; max-height: 88vh; overflow-y: auto;
      transform: translateY(20px) scale(0.97); transition: transform 0.25s cubic-bezier(.34,1.56,.64,1);
    }
    .modal-backdrop.open .modal-box { transform: translateY(0) scale(1); }

    .receipt-area { font-family: 'Courier New', monospace; font-size: 0.8125rem; line-height: 1.6; color: #fff; }

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
        <h1 class="text-white font-bold text-lg">Repairs History</h1>
      </div>
      <a href="/orion/repairs" class="btn-ghost">
        <iconify-icon icon="solar:add-circle-bold" style="font-size:0.95rem;"></iconify-icon>
        <span class="hidden sm:inline">New Repair</span>
      </a>
    </div>
  </header>

  <!-- Main -->
  <main class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-5">

    <!-- Filter bar -->
    <section class="glass-card p-4">
      <div class="flex flex-col sm:flex-row gap-3 flex-wrap">
        <!-- Search -->
        <div class="relative flex-1 min-w-[180px]">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none" style="color:rgba(50,237,255,0.5);">
            <iconify-icon icon="solar:magnifer-bold" style="font-size:1rem;"></iconify-icon>
          </span>
          <input type="search" id="searchInput" class="orion-input w-full" placeholder="Search by customer name…" style="padding-left:2.25rem;">
        </div>
        <!-- Category filter -->
        <div class="min-w-[160px]">
          <select id="categoryFilter" class="orion-select w-full">
            <option value="">All Categories</option>
          </select>
        </div>
        <!-- Date from/to -->
        <div class="flex items-center gap-2">
          <label for="dateFrom" class="text-xs font-semibold whitespace-nowrap" style="color:rgba(255,255,255,0.5);">From</label>
          <input type="date" id="dateFrom" class="orion-input">
        </div>
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
        Showing <span id="resultCount" class="text-white font-semibold">—</span> repair(s)
      </p>
      <p class="text-sm font-semibold" style="color:rgba(50,237,255,0.8);">
        Total: <span id="totalRevenue">—</span>
      </p>
    </div>

    <!-- Table card -->
    <section class="glass-card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="data-table min-w-[860px]" id="repairsTable">
          <thead>
            <tr>
              <th data-sort="date">Date <iconify-icon icon="solar:sort-vertical-bold" style="font-size:0.75rem;vertical-align:-1px;opacity:0.5;"></iconify-icon></th>
              <th data-sort="customer_name">Customer</th>
              <th>Category</th>
              <th>Complaint</th>
              <th data-sort="price">Price <iconify-icon icon="solar:sort-vertical-bold" style="font-size:0.75rem;vertical-align:-1px;opacity:0.5;"></iconify-icon></th>
              <th data-sort="total">Total <iconify-icon icon="solar:sort-vertical-bold" style="font-size:0.75rem;vertical-align:-1px;opacity:0.5;"></iconify-icon></th>
              <th>Payment</th>
              <th>Staff</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody id="repairsBody"></tbody>
        </table>
      </div>

      <!-- Empty/loading state -->
      <div id="tableState" class="py-16 text-center hidden">
        <iconify-icon id="stateIcon" icon="solar:settings-bold" style="font-size:3rem;color:rgba(50,237,255,0.2);"></iconify-icon>
        <p id="stateText" class="mt-3 text-sm" style="color:rgba(255,255,255,0.35);">Loading repairs…</p>
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

  <!-- Detail View Modal -->
  <div class="modal-backdrop" id="detailModal" role="dialog" aria-modal="true" aria-labelledby="detailTitle">
    <div class="modal-box p-6">
      <div class="flex items-center justify-between mb-5 no-print">
        <h3 id="detailTitle" class="text-white font-bold text-lg">Repair Details</h3>
        <button type="button" id="closeDetailBtn" class="btn-ghost" aria-label="Close">
          <iconify-icon icon="solar:close-circle-bold" style="font-size:0.95rem;"></iconify-icon>
        </button>
      </div>
      <div id="detailContent"></div>
    </div>
  </div>

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
          <h3 class="text-white font-bold text-base">Delete Repair?</h3>
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
   REPAIRS HISTORY PAGE — VANILLA JS
══════════════════════════════════════════════ */

const IS_ADMIN = <?php echo $is_admin ? 'true' : 'false'; ?>;
const BASE     = (window.orionConfig && window.orionConfig.baseUrl) ? window.orionConfig.baseUrl : '/wp-json/orion/v1';
const fmt      = n => '₦' + Number(n || 0).toLocaleString('en-NG', { minimumFractionDigits: 0, maximumFractionDigits: 2 });

let allRepairs      = [];
let filteredRepairs = [];
let currentPage     = 1;
const PER_PAGE      = 15;
let sortKey         = 'date';
let sortDir         = 'desc';
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

/* ── Load categories for filter ── */
async function loadCategories() {
  try {
    const res  = await fetch(`${BASE}/categories?type=repairs`, {
      headers: { 'Authorization': 'Bearer ' + (localStorage.getItem('orion_token') || '') }
    });
    const data = await res.json();
    const cats = Array.isArray(data) ? data : (data.data || []);
    const sel  = document.getElementById('categoryFilter');
    cats.forEach(c => {
      const opt = document.createElement('option');
      opt.value       = c.slug || c.name || c.id;
      opt.textContent = c.name;
      sel.appendChild(opt);
    });
  } catch (_) { /* silent */ }
}

/* ── Fetch repairs ── */
async function loadRepairs() {
  setTableState('loading');
  try {
    const params = new URLSearchParams();
    const search   = document.getElementById('searchInput').value.trim();
    const category = document.getElementById('categoryFilter').value;
    const from     = document.getElementById('dateFrom').value;
    const to       = document.getElementById('dateTo').value;
    if (search)   params.set('search', search);
    if (category) params.set('category', category);
    if (from)     params.set('date_from', from);
    if (to)       params.set('date_to', to);

    const res  = await fetch(`${BASE}/repairs?${params}`, {
      headers: { 'Authorization': 'Bearer ' + (localStorage.getItem('orion_token') || '') }
    });
    const data = await res.json();
    allRepairs = Array.isArray(data) ? data : (data.data || []);
    applySort();
  } catch (_) {
    setTableState('error');
  }
}

/* ── Sort ── */
function applySort() {
  filteredRepairs = [...allRepairs].sort((a, b) => {
    let va = a[sortKey] ?? '', vb = b[sortKey] ?? '';
    if (['price','total'].includes(sortKey)) { va = parseFloat(va) || 0; vb = parseFloat(vb) || 0; }
    if (va < vb) return sortDir === 'asc' ? -1 : 1;
    if (va > vb) return sortDir === 'asc' ?  1 : -1;
    return 0;
  });
  currentPage = 1;
  renderTable();
}

/* ── Render ── */
function renderTable() {
  const tbody = document.getElementById('repairsBody');
  const start = (currentPage - 1) * PER_PAGE;
  const page  = filteredRepairs.slice(start, start + PER_PAGE);

  if (!filteredRepairs.length) { setTableState('empty'); return; }
  setTableState('hidden');

  const totalRev = filteredRepairs.reduce((s, r) => s + (parseFloat(r.total) || parseFloat(r.price) || 0), 0);
  document.getElementById('resultCount').textContent  = filteredRepairs.length;
  document.getElementById('totalRevenue').textContent = fmt(totalRev);

  tbody.innerHTML = '';

  page.forEach((repair, i) => {
    const globalIdx = start + i;
    const dateObj   = repair.created_at ? new Date(repair.created_at) : (repair.date ? new Date(repair.date) : null);
    const date      = dateObj ? dateObj.toLocaleDateString('en-NG', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
    const timeStr   = dateObj ? dateObj.toLocaleTimeString('en-NG', { hour: '2-digit', minute: '2-digit', hour12: true }) : '';
    const payMethod = (repair.payment_method || '').toLowerCase();
    const badgeCls  = payMethod === 'cash' ? 'badge-cash' : payMethod === 'both' ? 'badge-both' : 'badge-transfer';
    const complaint = (repair.complaint || '').substring(0, 60) + ((repair.complaint || '').length > 60 ? '…' : '');
    const total     = parseFloat(repair.total) || parseFloat(repair.price) || 0;

    /* Main row */
    const tr = document.createElement('tr');
    tr.dataset.idx      = globalIdx;
    tr.dataset.repairId = repair.id;
    tr.innerHTML = `
      <td>
        <div class="font-medium text-sm text-white">${date}</div>
        <div class="text-xs mt-0.5" style="color:rgba(255,255,255,0.45);">${timeStr}</div>
        <div class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35);">${repair.id ? '#' + repair.id : ''}</div>
      </td>
      <td>
        <div class="font-medium text-sm text-white">${escHtml(repair.customer_name || '—')}</div>
        ${repair.customer_whatsapp ? `<div class="text-xs mt-0.5" style="color:rgba(255,255,255,0.35);">${escHtml(repair.customer_whatsapp)}</div>` : ''}
      </td>
      <td><span class="cat-badge">${escHtml(repair.category || '—')}</span></td>
      <td class="text-xs max-w-[160px]" style="color:rgba(255,255,255,0.65);">
        <span class="line-clamp-2">${escHtml(complaint || '—')}</span>
      </td>
      <td class="font-medium" style="color:rgba(255,255,255,0.85);">${fmt(repair.price)}</td>
      <td><span class="font-bold" style="color:#32EDFF;">${fmt(total)}</span></td>
      <td><span class="badge ${badgeCls}">${escHtml(repair.payment_method || '—')}</span></td>
      <td class="text-sm" style="color:rgba(255,255,255,0.6);">${escHtml(repair.staff_name || '—')}</td>
      <td>
        <div class="flex items-center justify-end gap-1.5">
          <button type="button" class="btn-icon view-btn" data-idx="${globalIdx}" aria-label="View repair details" title="View Details">
            <iconify-icon icon="solar:eye-bold" style="font-size:0.95rem;"></iconify-icon>
          </button>
          <button type="button" class="btn-icon receipt-btn" data-idx="${globalIdx}" aria-label="Print receipt" title="Print Receipt">
            <iconify-icon icon="solar:printer-bold" style="font-size:0.95rem;"></iconify-icon>
          </button>
          ${IS_ADMIN ? `<button type="button" class="btn-icon-danger delete-btn" data-repair-id="${repair.id}" aria-label="Delete repair" title="Delete"><iconify-icon icon="solar:trash-bin-trash-bold" style="font-size:0.95rem;"></iconify-icon></button>` : ''}
          <button type="button" class="btn-icon expand-btn" data-idx="${globalIdx}" aria-label="Expand row" aria-expanded="false">
            <iconify-icon icon="solar:alt-arrow-down-bold" style="font-size:0.85rem;"></iconify-icon>
          </button>
        </div>
      </td>
    `;

    /* Detail row (inline expanded) */
    const detailTr = document.createElement('tr');
    detailTr.className        = 'detail-row';
    detailTr.dataset.detailFor = globalIdx;
    detailTr.innerHTML = `
      <td class="detail-cell" colspan="9">
        <div class="detail-inner">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
            <div class="space-y-2">
              <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:rgba(50,237,255,0.6);">Complaint &amp; Diagnosis</p>
              ${repair.complaint  ? `<div><span class="font-semibold" style="color:rgba(255,255,255,0.5);">Complaint:</span><p class="mt-0.5">${escHtml(repair.complaint)}</p></div>` : ''}
              ${repair.diagnosis  ? `<div><span class="font-semibold" style="color:rgba(255,255,255,0.5);">Diagnosis:</span><p class="mt-0.5">${escHtml(repair.diagnosis)}</p></div>` : ''}
              ${repair.solution   ? `<div><span class="font-semibold" style="color:rgba(255,255,255,0.5);">Solution:</span><p class="mt-0.5">${escHtml(repair.solution)}</p></div>` : ''}
            </div>
            <div class="space-y-2">
              <p class="text-xs font-semibold uppercase tracking-wider mb-2" style="color:rgba(50,237,255,0.6);">Payment Details</p>
              <div class="flex justify-between"><span style="color:rgba(255,255,255,0.5);">Price</span><span class="font-medium">${fmt(repair.price)}</span></div>
              <div class="flex justify-between"><span style="color:rgba(255,255,255,0.5);">Total</span><span class="font-bold" style="color:#32EDFF;">${fmt(total)}</span></div>
              <div class="flex justify-between"><span style="color:rgba(255,255,255,0.5);">Method</span><span class="font-medium">${escHtml(repair.payment_method || '—')}</span></div>
              ${repair.transfer_amount > 0 ? `<div class="flex justify-between"><span style="color:rgba(255,255,255,0.5);">Transfer</span><span>${fmt(repair.transfer_amount)}</span></div>` : ''}
              ${repair.cash_amount > 0 ? `<div class="flex justify-between"><span style="color:rgba(255,255,255,0.5);">Cash</span><span>${fmt(repair.cash_amount)}</span></div>` : ''}
            </div>
          </div>
          ${(repair.customer_photo_url || repair.device_photo_url) ? `
          <div class="flex gap-3 mt-4">
            ${repair.customer_photo_url ? `<img src="${escHtml(repair.customer_photo_url)}" alt="Customer photo" class="w-20 h-20 object-cover rounded-xl border" style="border-color:rgba(50,237,255,0.2);">` : ''}
            ${repair.device_photo_url   ? `<img src="${escHtml(repair.device_photo_url)}"   alt="Device photo"   class="w-20 h-20 object-cover rounded-xl border" style="border-color:rgba(50,237,255,0.2);">` : ''}
          </div>` : ''}
        </div>
      </td>
    `;

    tbody.appendChild(tr);
    tbody.appendChild(detailTr);

    /* Row expand on click */
    tr.addEventListener('click', e => {
      if (e.target.closest('.view-btn, .receipt-btn, .delete-btn')) return;
      toggleDetailRow(globalIdx, tr, detailTr);
    });

    /* View details modal */
    tr.querySelector('.view-btn').addEventListener('click', e => {
      e.stopPropagation();
      showDetailModal(globalIdx);
    });

    /* Receipt */
    tr.querySelector('.receipt-btn').addEventListener('click', e => {
      e.stopPropagation();
      showReceipt(globalIdx);
    });

    /* Delete */
    if (IS_ADMIN) {
      tr.querySelector('.delete-btn').addEventListener('click', e => {
        e.stopPropagation();
        pendingDeleteId = repair.id;
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
  const icon = mainTr.querySelector('.expand-btn iconify-icon');
  if (icon) icon.setAttribute('icon', isOpen ? 'solar:alt-arrow-down-bold' : 'solar:alt-arrow-up-bold');
  mainTr.querySelector('.expand-btn').setAttribute('aria-expanded', String(!isOpen));
}

/* ── Pagination ── */
function renderPagination() {
  const total     = Math.ceil(filteredRepairs.length / PER_PAGE);
  const container = document.getElementById('paginationBtns');
  document.getElementById('pageInfo').textContent = `${currentPage} of ${total || 1}`;
  container.innerHTML = '';

  const prevBtn = document.createElement('button');
  prevBtn.type = 'button'; prevBtn.className = 'page-btn'; prevBtn.disabled = currentPage <= 1;
  prevBtn.innerHTML = `<iconify-icon icon="solar:arrow-left-bold" style="font-size:0.85rem;"></iconify-icon>`;
  prevBtn.addEventListener('click', () => { currentPage--; renderTable(); });
  container.appendChild(prevBtn);

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
  nextBtn.type = 'button'; nextBtn.className = 'page-btn'; nextBtn.disabled = currentPage >= total;
  nextBtn.innerHTML = `<iconify-icon icon="solar:arrow-right-bold" style="font-size:0.85rem;"></iconify-icon>`;
  nextBtn.addEventListener('click', () => { currentPage++; renderTable(); });
  container.appendChild(nextBtn);
}

/* ── State ── */
function setTableState(state) {
  const el   = document.getElementById('tableState');
  const icon = document.getElementById('stateIcon');
  const text = document.getElementById('stateText');
  if (state === 'hidden') { el.classList.add('hidden'); return; }
  el.classList.remove('hidden');
  if (state === 'loading') {
    icon.setAttribute('icon', 'solar:refresh-bold');
    icon.style.animation = 'spin 1s linear infinite';
    text.textContent = 'Loading repairs…';
  } else if (state === 'empty') {
    icon.setAttribute('icon', 'solar:settings-bold');
    icon.style.animation = '';
    text.textContent = 'No repairs found.';
    document.getElementById('resultCount').textContent  = '0';
    document.getElementById('totalRevenue').textContent = fmt(0);
  } else {
    icon.setAttribute('icon', 'solar:danger-circle-bold');
    icon.style.animation = '';
    text.textContent = 'Failed to load repairs.';
  }
}

/* ── Detail modal ── */
function showDetailModal(idx) {
  const repair = filteredRepairs[idx];
  if (!repair) return;

  const total = parseFloat(repair.total) || parseFloat(repair.price) || 0;
  const payMethod = (repair.payment_method || '').toLowerCase();
  const badgeCls  = payMethod === 'cash' ? 'badge-cash' : payMethod === 'both' ? 'badge-both' : 'badge-transfer';
  const dateObj   = repair.created_at ? new Date(repair.created_at) : (repair.date ? new Date(repair.date) : null);
  const date      = dateObj
    ? dateObj.toLocaleDateString('en-NG', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) +
      ' ' + dateObj.toLocaleTimeString('en-NG', { hour: '2-digit', minute: '2-digit', hour12: true })
    : '—';

  document.getElementById('detailContent').innerHTML = `
    <div class="space-y-4 text-sm">
      <div class="grid grid-cols-2 gap-3">
        <div><p class="text-xs uppercase tracking-wider mb-1" style="color:rgba(50,237,255,0.6);">Date</p><p class="text-white font-medium">${date}</p></div>
        <div><p class="text-xs uppercase tracking-wider mb-1" style="color:rgba(50,237,255,0.6);">Reference</p><p class="text-white font-medium">${repair.id ? '#' + repair.id : '—'}</p></div>
        <div><p class="text-xs uppercase tracking-wider mb-1" style="color:rgba(50,237,255,0.6);">Customer</p><p class="text-white font-medium">${escHtml(repair.customer_name || '—')}</p></div>
        <div><p class="text-xs uppercase tracking-wider mb-1" style="color:rgba(50,237,255,0.6);">WhatsApp</p><p class="text-white font-medium">${escHtml(repair.customer_whatsapp || '—')}</p></div>
        <div><p class="text-xs uppercase tracking-wider mb-1" style="color:rgba(50,237,255,0.6);">Category</p><span class="cat-badge">${escHtml(repair.category || '—')}</span></div>
        <div><p class="text-xs uppercase tracking-wider mb-1" style="color:rgba(50,237,255,0.6);">Staff</p><p class="text-white font-medium">${escHtml(repair.staff_name || '—')}</p></div>
      </div>
      <div class="border-t pt-4" style="border-color:rgba(50,237,255,0.1);">
        ${repair.complaint  ? `<div class="mb-3"><p class="text-xs uppercase tracking-wider mb-1" style="color:rgba(50,237,255,0.6);">Complaint</p><p style="color:rgba(255,255,255,0.8);">${escHtml(repair.complaint)}</p></div>` : ''}
        ${repair.diagnosis  ? `<div class="mb-3"><p class="text-xs uppercase tracking-wider mb-1" style="color:rgba(50,237,255,0.6);">Diagnosis</p><p style="color:rgba(255,255,255,0.8);">${escHtml(repair.diagnosis)}</p></div>` : ''}
        ${repair.solution   ? `<div class="mb-3"><p class="text-xs uppercase tracking-wider mb-1" style="color:rgba(50,237,255,0.6);">Solution</p><p style="color:rgba(255,255,255,0.8);">${escHtml(repair.solution)}</p></div>` : ''}
      </div>
      <div class="border-t pt-4" style="border-color:rgba(50,237,255,0.1);">
        <p class="text-xs uppercase tracking-wider mb-3" style="color:rgba(50,237,255,0.6);">Payment</p>
        <div class="flex justify-between mb-1.5"><span style="color:rgba(255,255,255,0.5);">Price</span><span>${fmt(repair.price)}</span></div>
        <div class="flex justify-between mb-1.5"><span style="color:rgba(255,255,255,0.5);">Total</span><span class="font-bold" style="color:#32EDFF;">${fmt(total)}</span></div>
        <div class="flex justify-between mb-1.5"><span style="color:rgba(255,255,255,0.5);">Method</span><span class="badge ${badgeCls}">${escHtml(repair.payment_method || '—')}</span></div>
        ${repair.transfer_amount > 0 ? `<div class="flex justify-between mb-1.5"><span style="color:rgba(255,255,255,0.5);">Transfer</span><span>${fmt(repair.transfer_amount)}</span></div>` : ''}
        ${repair.cash_amount > 0 ? `<div class="flex justify-between"><span style="color:rgba(255,255,255,0.5);">Cash</span><span>${fmt(repair.cash_amount)}</span></div>` : ''}
      </div>
      ${(repair.customer_photo_url || repair.device_photo_url) ? `
      <div class="border-t pt-4 flex gap-4" style="border-color:rgba(50,237,255,0.1);">
        ${repair.customer_photo_url ? `<div><p class="text-xs uppercase tracking-wider mb-1.5" style="color:rgba(50,237,255,0.6);">Customer Photo</p><img src="${escHtml(repair.customer_photo_url)}" alt="Customer" class="w-28 h-28 object-cover rounded-xl border" style="border-color:rgba(50,237,255,0.2);"></div>` : ''}
        ${repair.device_photo_url   ? `<div><p class="text-xs uppercase tracking-wider mb-1.5" style="color:rgba(50,237,255,0.6);">Device Photo</p><img src="${escHtml(repair.device_photo_url)}" alt="Device" class="w-28 h-28 object-cover rounded-xl border" style="border-color:rgba(50,237,255,0.2);"></div>` : ''}
      </div>` : ''}
      <div class="border-t pt-4 flex gap-2 no-print" style="border-color:rgba(50,237,255,0.1);">
        <button type="button" class="btn-ghost flex-1 justify-center" onclick="showReceipt(${idx});document.getElementById('detailModal').classList.remove('open');">
          <iconify-icon icon="solar:printer-bold" style="font-size:0.9rem;"></iconify-icon>
          Print Receipt
        </button>
      </div>
    </div>
  `;
  document.getElementById('detailModal').classList.add('open');
}

/* ── Receipt ── */
function showReceipt(idx) {
  const repair  = filteredRepairs[idx];
  if (!repair) return;
  const total   = parseFloat(repair.total) || parseFloat(repair.price) || 0;
  const dateStr = repair.created_at ? new Date(repair.created_at).toLocaleString('en-NG')
    : (repair.date ? new Date(repair.date).toLocaleString('en-NG') : new Date().toLocaleString('en-NG'));
  const method  = (repair.payment_method || '').toLowerCase();

  let lines = '';
  lines += `================================\n`;
  lines += `     ORION BROTHERS\n`;
  lines += `     Inventory System\n`;
  lines += `================================\n`;
  lines += `Repair #  : ${repair.id || 'N/A'}\n`;
  lines += `Date      : ${dateStr}\n`;
  lines += `Customer  : ${repair.customer_name || '—'}\n`;
  if (repair.customer_whatsapp) lines += `WhatsApp  : ${repair.customer_whatsapp}\n`;
  lines += `Staff     : ${repair.staff_name || '—'}\n`;
  lines += `--------------------------------\n`;
  lines += `Category  : ${repair.category || '—'}\n`;
  if (repair.complaint) lines += `Complaint : ${repair.complaint}\n`;
  if (repair.diagnosis) lines += `Diagnosis : ${repair.diagnosis}\n`;
  if (repair.solution)  lines += `Solution  : ${repair.solution}\n`;
  lines += `--------------------------------\n`;
  lines += `Price     : ${fmt(repair.price)}\n`;
  lines += `TOTAL     : ${fmt(total)}\n`;
  lines += `Payment   : ${(repair.payment_method || '').toUpperCase()}\n`;
  if (repair.transfer_amount > 0) lines += `Transfer  : ${fmt(repair.transfer_amount)}\n`;
  if (repair.cash_amount > 0)     lines += `Cash      : ${fmt(repair.cash_amount)}\n`;
  lines += `================================\n`;
  lines += `  Thank you for choosing\n`;
  lines += `    Orion Brothers!\n`;
  lines += `================================\n`;

  document.getElementById('receiptContent').innerHTML = `<pre style="white-space:pre-wrap;word-break:break-all;">${lines}</pre>`;
  document.getElementById('receiptModal').classList.add('open');
}

/* ── Delete ── */
document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
  if (!pendingDeleteId) return;
  document.getElementById('confirmModal').classList.remove('open');
  try {
    const res  = await fetch(`${BASE}/repairs/${pendingDeleteId}`, {
      method: 'DELETE',
      headers: { 'Authorization': 'Bearer ' + (localStorage.getItem('orion_token') || '') }
    });
    const data = await res.json();
    if (data.success) {
      showToast('Repair deleted.');
      await loadRepairs();
    } else {
      showToast(data.message || 'Failed to delete.', 'error');
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

/* ── Sort ── */
document.querySelectorAll('.data-table th[data-sort]').forEach(th => {
  th.addEventListener('click', () => {
    const key = th.dataset.sort;
    if (sortKey === key) sortDir = sortDir === 'asc' ? 'desc' : 'asc';
    else { sortKey = key; sortDir = 'desc'; }
    applySort();
  });
});

/* ── Filters ── */
document.getElementById('filterBtn').addEventListener('click', loadRepairs);
document.getElementById('clearFilterBtn').addEventListener('click', () => {
  document.getElementById('searchInput').value     = '';
  document.getElementById('categoryFilter').value  = '';
  document.getElementById('dateFrom').value        = '';
  document.getElementById('dateTo').value          = '';
  loadRepairs();
});
document.getElementById('searchInput').addEventListener('keydown', e => { if (e.key === 'Enter') loadRepairs(); });

/* ── Modal close ── */
document.getElementById('closeDetailBtn').addEventListener('click', () => {
  document.getElementById('detailModal').classList.remove('open');
});
document.getElementById('detailModal').addEventListener('click', e => {
  if (e.target === document.getElementById('detailModal'))
    document.getElementById('detailModal').classList.remove('open');
});
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
(async () => {
  await Promise.all([loadCategories(), loadRepairs()]);
})();

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
