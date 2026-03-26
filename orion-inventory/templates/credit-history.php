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
  <title>Credit History — Orion Brothers</title>
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

    /* Data table */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th {
      padding: 0.75rem 1rem;
      text-align: left;
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: rgba(50,237,255,0.7);
      border-bottom: 1px solid rgba(50,237,255,0.12);
      white-space: nowrap;
    }
    .data-table td {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      font-size: 0.875rem;
      vertical-align: middle;
    }
    .data-table tbody tr { transition: background 0.15s; cursor: pointer; }
    .data-table tbody tr:hover td { background: rgba(50,237,255,0.04); }
    .data-table tbody tr.expanded-parent td { background: rgba(50,237,255,0.06); }

    .detail-row { display: none; }
    .detail-row.open { display: table-row; }
    .detail-cell {
      background: rgba(11,27,60,0.8) !important;
      border-bottom: 1px solid rgba(50,237,255,0.1) !important;
      padding: 0 !important;
    }
    .detail-inner { padding: 1rem 1.25rem; border-top: 1px solid rgba(50,237,255,0.08); }

    /* Stats cards */
    .stat-card {
      background: rgba(11,27,60,0.65);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(50,237,255,0.15);
      border-radius: 1rem;
      padding: 1.125rem 1.25rem;
    }

    /* Buttons */
    .btn-primary {
      display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
      padding: 0.625rem 1.5rem;
      border-radius: 9999px;
      background: linear-gradient(135deg, #32EDFF 0%, #00b8d4 100%);
      color: #0B1B3C;
      font-weight: 700;
      font-size: 0.875rem;
      border: none;
      cursor: pointer;
      transition: opacity 0.2s, transform 0.15s;
      box-shadow: 0 4px 16px rgba(50,237,255,0.25);
    }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }

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

    /* Running balance indicator */
    .running-balance {
      font-weight: 700;
      font-size: 0.8rem;
    }
    .running-balance.positive { color: #f87171; }
    .running-balance.zero     { color: #34d399; }

    /* Timeline dot */
    .timeline-dot {
      width: 10px; height: 10px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    /* Table scroll */
    .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-scroll::-webkit-scrollbar { height: 4px; }
    .table-scroll::-webkit-scrollbar-track { background: transparent; }
    .table-scroll::-webkit-scrollbar-thumb { background: rgba(50,237,255,0.2); border-radius: 2px; }

    /* Badge */
    .badge {
      display: inline-flex; align-items: center; gap: 0.3rem;
      padding: 0.25rem 0.75rem;
      border-radius: 9999px;
      font-size: 0.7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .badge-green  { background: rgba(52,211,153,0.15); color: #34d399; border: 1px solid rgba(52,211,153,0.25); }
    .badge-yellow { background: rgba(251,191,36,0.15); color: #fbbf24; border: 1px solid rgba(251,191,36,0.25); }
    .badge-red    { background: rgba(248,113,113,0.15); color: #f87171; border: 1px solid rgba(248,113,113,0.25); }

    /* Orb */
    .orb { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; z-index: 0; }

    /* Print */
    @media print {
      .glass-header, .no-print { display: none !important; }
      body { background: #fff !important; color: #000 !important; }
      .glass-card { background: #fff !important; border: 1px solid #ddd !important; }
      .data-table th { color: #333 !important; }
      .data-table td { color: #000 !important; border-color: #ddd !important; }
      .orb { display: none !important; }
    }
  </style>
</head>
<body>
  <div class="orb" style="width:500px;height:500px;background:rgba(50,237,255,0.04);top:-100px;right:-150px;"></div>
  <div class="orb" style="width:400px;height:400px;background:rgba(50,237,255,0.03);bottom:-80px;left:-100px;"></div>

  <!-- Header -->
  <header class="glass-header sticky top-0 z-50 px-4 sm:px-6">
    <div class="max-w-5xl mx-auto flex items-center gap-3 h-16">
      <a href="/orion/credit-sales" class="flex items-center justify-center w-9 h-9 rounded-xl flex-shrink-0"
         style="background:rgba(50,237,255,0.08);border:1px solid rgba(50,237,255,0.18);" aria-label="Back">
        <iconify-icon icon="solar:arrow-left-linear" style="color:#32EDFF;font-size:1.25rem;"></iconify-icon>
      </a>
      <div class="flex items-center gap-2.5 flex-1 min-w-0">
        <div class="flex items-center justify-center w-9 h-9 rounded-xl flex-shrink-0"
             style="background:rgba(50,237,255,0.1);border:1px solid rgba(50,237,255,0.2);">
          <iconify-icon icon="solar:history-linear" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
        </div>
        <div class="min-w-0">
          <h1 class="text-white font-bold text-lg leading-tight truncate">Credit History</h1>
          <p class="text-xs leading-tight truncate" style="color:rgba(255,255,255,0.4);" id="headerSubtitle">Loading customer…</p>
        </div>
      </div>
      <button class="btn-ghost flex-shrink-0 no-print" onclick="window.print()">
        <iconify-icon icon="solar:printer-linear" style="font-size:1rem;"></iconify-icon>
        <span class="hidden sm:inline">Print</span>
      </button>
    </div>
  </header>

  <main class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 py-8">

    <!-- Loading state -->
    <div id="pageLoading" class="flex flex-col items-center gap-4 py-20">
      <iconify-icon icon="solar:refresh-linear" class="animate-spin" style="color:#32EDFF;font-size:2.5rem;"></iconify-icon>
      <p style="color:rgba(255,255,255,0.4);">Loading credit history…</p>
    </div>

    <!-- Error state -->
    <div id="pageError" class="hidden flex flex-col items-center gap-4 py-20 text-center">
      <iconify-icon icon="solar:close-circle-linear" style="color:#f87171;font-size:2.5rem;"></iconify-icon>
      <p style="color:#f87171;" id="errorMsg">Failed to load history.</p>
      <button class="btn-ghost" onclick="loadHistory()">
        <iconify-icon icon="solar:refresh-linear" style="font-size:1rem;"></iconify-icon>
        Retry
      </button>
    </div>

    <!-- Content (shown after load) -->
    <div id="pageContent" class="hidden">

      <!-- Customer Info Card -->
      <div class="glass-card p-5 mb-6" id="customerInfoCard">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
          <div class="flex items-center justify-center w-14 h-14 rounded-2xl flex-shrink-0"
               style="background:rgba(50,237,255,0.1);border:1px solid rgba(50,237,255,0.2);">
            <iconify-icon icon="solar:user-bold" style="color:#32EDFF;font-size:1.75rem;"></iconify-icon>
          </div>
          <div class="flex-1 min-w-0">
            <h2 class="text-xl font-black text-white leading-tight" id="custName">—</h2>
            <p class="text-sm mt-0.5 flex items-center gap-1.5" style="color:rgba(255,255,255,0.5);">
              <iconify-icon icon="solar:phone-linear" style="color:#32EDFF;font-size:0.9rem;"></iconify-icon>
              <span id="custWhatsapp">—</span>
            </p>
          </div>
          <div class="flex-shrink-0 text-right sm:text-left">
            <p class="text-xs mb-1" style="color:rgba(255,255,255,0.4);">Total Outstanding</p>
            <p class="text-2xl font-black" id="custTotalOwed" style="color:#f87171;">₦0.00</p>
          </div>
        </div>
      </div>

      <!-- Summary Stats -->
      <div class="grid grid-cols-3 gap-4 mb-6" id="statsGrid">
        <div class="stat-card text-center">
          <p class="text-xs mb-1.5" style="color:rgba(255,255,255,0.45);">Total Owed</p>
          <p class="text-lg font-black" id="statTotalOwed" style="color:#32EDFF;">₦0.00</p>
        </div>
        <div class="stat-card text-center">
          <p class="text-xs mb-1.5" style="color:rgba(255,255,255,0.45);">Total Paid</p>
          <p class="text-lg font-black" id="statTotalPaid" style="color:#34d399;">₦0.00</p>
        </div>
        <div class="stat-card text-center">
          <p class="text-xs mb-1.5" style="color:rgba(255,255,255,0.45);">Balance</p>
          <p class="text-lg font-black" id="statBalance" style="color:#f87171;">₦0.00</p>
        </div>
      </div>

      <!-- Transaction Table -->
      <div class="glass-card overflow-hidden">
        <div class="flex items-center justify-between p-4 border-b" style="border-color:rgba(50,237,255,0.1);">
          <h3 class="font-bold text-white text-base flex items-center gap-2">
            <iconify-icon icon="solar:clipboard-list-linear" style="color:#32EDFF;font-size:1.1rem;"></iconify-icon>
            Transaction Timeline
            <span id="txCount" class="ml-2 text-xs px-2 py-0.5 rounded-full"
                  style="background:rgba(50,237,255,0.12);color:#32EDFF;font-weight:700;">0</span>
          </h3>
        </div>

        <div class="table-scroll">
          <table class="data-table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Items</th>
                <th>Total (₦)</th>
                <th>Paid (₦)</th>
                <th>Sale Balance</th>
                <th>Running Total</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="txTableBody"></tbody>
          </table>
        </div>

        <!-- Empty state -->
        <div id="txEmpty" class="hidden flex flex-col items-center gap-3 py-14 text-center px-4">
          <iconify-icon icon="solar:history-linear" style="color:rgba(50,237,255,0.25);font-size:3rem;"></iconify-icon>
          <p class="font-semibold" style="color:rgba(255,255,255,0.4);">No transactions found for this customer</p>
        </div>
      </div>

    </div><!-- /pageContent -->

  </main>

  <script>
  window.orionConfig = {
    baseUrl: '/wp-json/orion/v1',
    currentUser: <?php echo json_encode(Orion_Auth::get_current_user()); ?>,
  };
  </script>
  <script src="/wp-content/plugins/orion-inventory/assets/js/app.js"></script>
  <script>
  const BASE = window.orionConfig.baseUrl;

  function parseNum(v) { const n = parseFloat(String(v).replace(/,/g,'')); return isNaN(n)?0:n; }
  function formatNum(n) { return Number(n).toLocaleString('en-NG',{minimumFractionDigits:2,maximumFractionDigits:2}); }
  function escHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  /* Read query params */
  function getParam(key) {
    return new URLSearchParams(window.location.search).get(key) || '';
  }

  async function loadHistory() {
    const whatsapp = getParam('whatsapp');
    const name     = getParam('name');

    document.getElementById('pageLoading').classList.remove('hidden');
    document.getElementById('pageError').classList.add('hidden');
    document.getElementById('pageContent').classList.add('hidden');

    if (!whatsapp) {
      document.getElementById('pageLoading').classList.add('hidden');
      document.getElementById('pageError').classList.remove('hidden');
      document.getElementById('errorMsg').textContent = 'No customer WhatsApp provided in URL.';
      return;
    }

    try {
      const r = await fetch(`${BASE}/credit-history?whatsapp=${encodeURIComponent(whatsapp)}`);
      if (!r.ok) throw new Error('Server error ' + r.status);
      const d = await r.json();
      const transactions = Array.isArray(d) ? d : (d.transactions || []);
      const customerName = name || (transactions[0] && transactions[0].customer_name) || whatsapp;

      renderPage(transactions, customerName, whatsapp);

      document.getElementById('pageLoading').classList.add('hidden');
      document.getElementById('pageContent').classList.remove('hidden');
    } catch (err) {
      document.getElementById('pageLoading').classList.add('hidden');
      document.getElementById('pageError').classList.remove('hidden');
      document.getElementById('errorMsg').textContent = err.message || 'Failed to load history.';
    }
  }

  function renderPage(transactions, customerName, whatsapp) {
    /* Header subtitle */
    document.getElementById('headerSubtitle').textContent = whatsapp;

    /* Customer card */
    document.getElementById('custName').textContent = customerName;
    document.getElementById('custWhatsapp').textContent = whatsapp;

    /* Totals */
    let totalOwed = 0; let totalPaid = 0;
    transactions.forEach(t => {
      totalOwed += parseNum(t.total_amount);
      totalPaid += parseNum(t.amount_paid);
    });
    const totalBalance = Math.max(0, totalOwed - totalPaid);

    document.getElementById('custTotalOwed').textContent = '₦' + formatNum(totalBalance);
    document.getElementById('statTotalOwed').textContent = '₦' + formatNum(totalOwed);
    document.getElementById('statTotalPaid').textContent = '₦' + formatNum(totalPaid);
    document.getElementById('statBalance').textContent   = '₦' + formatNum(totalBalance);
    document.getElementById('txCount').textContent = transactions.length;

    const tbody = document.getElementById('txTableBody');
    tbody.innerHTML = '';

    if (!transactions.length) {
      document.getElementById('txEmpty').classList.remove('hidden');
      return;
    }
    document.getElementById('txEmpty').classList.add('hidden');

    let runningBalance = 0;

    transactions.forEach((t, idx) => {
      const saleBal = parseNum(t.balance);
      const paid    = parseNum(t.amount_paid);
      const total   = parseNum(t.total_amount);
      runningBalance += saleBal;

      const date  = t.created_at ? new Date(t.created_at).toLocaleDateString('en-NG', {day:'2-digit',month:'short',year:'numeric'}) : '—';
      const items = Array.isArray(t.items) ? t.items : [];
      const itemSummary = items.length
        ? items.slice(0,2).map(i=>escHtml(i.product_name||i.name||'Item')).join(', ') + (items.length>2?` +${items.length-2} more`:'')
        : (t.item_summary || '—');

      let badge;
      if (saleBal <= 0)  badge = '<span class="badge badge-green">Paid</span>';
      else if (paid > 0) badge = '<span class="badge badge-yellow">Partial</span>';
      else               badge = '<span class="badge badge-red">Unpaid</span>';

      const rowId = `tx-${idx}`;
      const detailId = `td-${idx}`;

      const tr = document.createElement('tr');
      tr.className = 'cursor-pointer';
      tr.onclick = () => toggleDetail(idx);
      tr.innerHTML = `
        <td style="color:rgba(255,255,255,0.6);font-size:.8rem;white-space:nowrap;">${escHtml(date)}</td>
        <td class="font-medium" style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${itemSummary}</td>
        <td class="font-bold" style="color:#32EDFF;">₦${formatNum(total)}</td>
        <td style="color:#34d399;">₦${formatNum(paid)}</td>
        <td style="color:${saleBal>0?'#f87171':'#34d399'};">₦${formatNum(saleBal)}</td>
        <td>
          <span class="running-balance ${runningBalance > 0 ? 'positive' : 'zero'}">
            ₦${formatNum(runningBalance)}
          </span>
        </td>
        <td>${badge}</td>`;

      tbody.appendChild(tr);

      /* Expandable detail row */
      if (items.length > 0) {
        const detailTr = document.createElement('tr');
        detailTr.className = 'detail-row';
        detailTr.id = detailId;
        let itemsHtml = '<table style="width:100%;border-collapse:collapse;"><thead><tr>'
          + '<th style="padding:.5rem .75rem;text-align:left;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:rgba(50,237,255,.6);border-bottom:1px solid rgba(50,237,255,.1);">Item</th>'
          + '<th style="padding:.5rem .75rem;text-align:left;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:rgba(50,237,255,.6);border-bottom:1px solid rgba(50,237,255,.1);">Price</th>'
          + '<th style="padding:.5rem .75rem;text-align:left;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:rgba(50,237,255,.6);border-bottom:1px solid rgba(50,237,255,.1);">Qty</th>'
          + '<th style="padding:.5rem .75rem;text-align:left;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:rgba(50,237,255,.6);border-bottom:1px solid rgba(50,237,255,.1);">Total</th>'
          + '</tr></thead><tbody>';
        items.forEach(i => {
          itemsHtml += `<tr>
            <td style="padding:.45rem .75rem;font-size:.8rem;">${escHtml(i.product_name||i.name||'—')}</td>
            <td style="padding:.45rem .75rem;font-size:.8rem;">₦${formatNum(i.price)}</td>
            <td style="padding:.45rem .75rem;font-size:.8rem;">${i.qty||1}</td>
            <td style="padding:.45rem .75rem;font-size:.8rem;font-weight:700;color:#32EDFF;">₦${formatNum(i.total)}</td>
          </tr>`;
        });
        itemsHtml += '</tbody></table>';
        detailTr.innerHTML = `<td colspan="7" class="detail-cell"><div class="detail-inner">${itemsHtml}</div></td>`;
        tbody.appendChild(detailTr);
      }
    });
  }

  function toggleDetail(idx) {
    const detailRow = document.getElementById(`td-${idx}`);
    if (detailRow) detailRow.classList.toggle('open');
  }

  loadHistory();
  </script>
</body>
</html>
