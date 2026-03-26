<?php
if ( ! class_exists( 'Orion_Auth' ) ) { wp_redirect( '/orion/login' ); exit; }
Orion_Auth::require_login();
$current_user = Orion_Auth::get_current_user();
$user_name    = $current_user['display_name'] ?? $current_user['username'] ?? 'User';
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Stock Management — Orion Brothers</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script>tailwind.config={theme:{extend:{colors:{navy:'#0B1B3C',cyan:'#32EDFF'},fontFamily:{inter:['Inter','sans-serif']}}}}</script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
  <link rel="stylesheet" href="/wp-content/plugins/orion-inventory/assets/css/main.css">
  <style>
    body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0B1B3C 0%,#060f22 100%);min-height:100vh;color:#fff}
    .glass-header{background:rgba(11,27,60,0.85);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border-bottom:1px solid rgba(50,237,255,0.14)}
    .glass-card{background:rgba(11,27,60,0.65);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(50,237,255,0.15);border-radius:1.25rem}
    .orion-input{background:rgba(255,255,255,0.05);border:1px solid rgba(50,237,255,0.18);border-radius:.5rem;color:#fff;padding:.375rem .625rem;font-size:.875rem;outline:none;font-family:'Inter',sans-serif;transition:border-color .2s,box-shadow .2s;text-align:center}
    .orion-input:focus{border-color:rgba(50,237,255,0.55);box-shadow:0 0 0 3px rgba(50,237,255,0.1)}
    .orion-input:read-only{opacity:.5;cursor:not-allowed}
    .orion-input::placeholder{color:rgba(255,255,255,0.3)}
    .date-input{background:rgba(255,255,255,0.05);border:1px solid rgba(50,237,255,0.18);border-radius:.625rem;color:#fff;padding:.5rem .75rem;font-size:.875rem;outline:none;font-family:'Inter',sans-serif;transition:border-color .2s,box-shadow .2s}
    .date-input:focus{border-color:rgba(50,237,255,0.55);box-shadow:0 0 0 3px rgba(50,237,255,0.1)}
    .tbl{width:100%;border-collapse:collapse}
    .tbl th{padding:.625rem .75rem;text-align:left;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:rgba(50,237,255,0.7);border-bottom:1px solid rgba(50,237,255,0.12);white-space:nowrap}
    .tbl td{padding:.5rem .75rem;border-bottom:1px solid rgba(255,255,255,0.04);vertical-align:middle}
    .tbl tr:last-child td{border-bottom:none}
    .tbl tbody tr:hover td{background:rgba(50,237,255,0.02)}
    .closing-cell{background:rgba(50,237,255,0.07);font-weight:700;color:#32EDFF;border-radius:.5rem;padding:.375rem .75rem;text-align:center;min-width:60px;display:inline-block}
    .low-stock-row td{background:rgba(239,68,68,0.06)!important}
    .low-stock-row td:first-child{border-left:3px solid rgba(239,68,68,0.6)}
    .btn-primary{background:linear-gradient(135deg,#32EDFF,#1ac8d8);color:#0B1B3C;font-weight:700;border-radius:.75rem;padding:.5rem 1.125rem;font-size:.875rem;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:.4rem;transition:opacity .2s}
    .btn-primary:hover{opacity:.88}
    .btn-ghost{background:rgba(255,255,255,0.07);color:rgba(255,255,255,0.75);font-weight:600;border-radius:.75rem;padding:.5rem 1rem;font-size:.8rem;border:1px solid rgba(255,255,255,0.1);cursor:pointer;display:inline-flex;align-items:center;gap:.35rem;transition:background .2s}
    .btn-ghost:hover{background:rgba(255,255,255,0.12);color:#fff}
    .btn-save-row{background:rgba(50,237,255,0.1);color:#32EDFF;border:1px solid rgba(50,237,255,0.25);border-radius:.5rem;padding:.3rem .6rem;font-size:.72rem;font-weight:700;cursor:pointer;transition:background .2s;white-space:nowrap}
    .btn-save-row:hover{background:rgba(50,237,255,0.2)}
    .saved-badge{color:#4ade80;font-size:.68rem;font-weight:600;opacity:0;transition:opacity .3s;display:inline-flex;align-items:center;gap:.2rem}
    .saved-badge.show{opacity:1}
    .formula-tag{font-size:.65rem;color:rgba(255,255,255,0.35);white-space:nowrap}
    .low-warn{color:#f87171;font-size:.65rem;font-weight:700;margin-left:.25rem}
  </style>
</head>
<body class="min-h-screen">

<header class="glass-header sticky top-0 z-40 px-4 py-3">
  <div class="max-w-6xl mx-auto flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <a href="/orion/home" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition-colors">
        <iconify-icon icon="solar:arrow-left-linear" width="18" style="color:#32EDFF"></iconify-icon>
      </a>
      <div>
        <h1 class="text-lg font-bold text-white leading-tight">Stock Management</h1>
        <p class="text-xs text-white/40">Daily opening &amp; closing stock</p>
      </div>
    </div>
    <a href="/orion/stock-history" class="btn-ghost">
      <iconify-icon icon="solar:history-linear" width="15"></iconify-icon>
      History
    </a>
  </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-6 space-y-5">

  <!-- Date + Search + Formula Info -->
  <div class="flex flex-wrap items-center gap-4">
    <div class="glass-card p-4 flex items-center gap-3 flex-shrink-0">
      <iconify-icon icon="solar:calendar-linear" width="20" style="color:#32EDFF"></iconify-icon>
      <div>
        <label class="block text-xs text-white/40 font-semibold uppercase tracking-wider mb-1">Stock Date</label>
        <input type="date" id="stockDate" class="date-input" />
      </div>
    </div>
    <!-- Search -->
    <div class="glass-card p-3 flex items-center gap-2 flex-1 min-w-[200px]">
      <iconify-icon icon="solar:magnifer-linear" width="18" style="color:#32EDFF;flex-shrink:0"></iconify-icon>
      <input type="search" id="stockSearch" placeholder="Search product or category…"
             class="date-input" style="border:none;background:transparent;box-shadow:none;width:100%;padding:0"
             oninput="filterStockRows()" />
    </div>
    <div class="glass-card p-3 px-4 flex items-center gap-2 text-sm text-white/50">
      <iconify-icon icon="solar:calculator-linear" width="16" style="color:#32EDFF"></iconify-icon>
      <span>Closing = <span class="text-white/80">Opening</span> + <span style="color:#32EDFF">Import</span> − <span class="text-orange-400">Sold</span></span>
    </div>
    <div class="ml-auto flex items-center gap-2">
      <button id="saveAllBtn" class="btn-primary">
        <iconify-icon icon="solar:diskette-linear" width="15"></iconify-icon>Save All
      </button>
    </div>
  </div>

  <!-- Legend -->
  <div class="flex flex-wrap gap-3 text-xs text-white/40">
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded" style="background:rgba(50,237,255,0.2);display:inline-block"></span>Closing Stock (calculated)</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-500/30 inline-block"></span>Low stock (&lt;3 units)</span>
  </div>

  <!-- Stock Table -->
  <div class="glass-card overflow-hidden">
    <div class="overflow-x-auto">
      <table class="tbl">
        <thead>
          <tr>
            <th style="min-width:180px">Product Name</th>
            <th style="min-width:110px">Category</th>
            <th style="min-width:100px">Opening</th>
            <th style="min-width:90px">Import</th>
            <th style="min-width:80px">Sold</th>
            <th style="min-width:110px">Closing</th>
            <th style="min-width:90px">Updated</th>
            <th style="min-width:80px">Save</th>
          </tr>
        </thead>
        <tbody id="stockBody">
          <tr><td colspan="8" class="text-center py-10 text-white/30">Loading…</td></tr>
        </tbody>
      </table>
    </div>
  </div>

</main>

<script>
const BASE  = '/wp-json/orion/v1';
const token = () => localStorage.getItem('orion_token') || '';
const authH = () => ({'Content-Type':'application/json','Authorization':'Bearer '+token()});

function fmt(dateStr) {
  if (!dateStr) return '—';
  const d = new Date(dateStr);
  return isNaN(d) ? dateStr : d.toLocaleDateString('en-GB',{day:'2-digit',month:'short'});
}

async function loadStock() {
  const date = document.getElementById('stockDate').value;
  if (!date) return;
  document.getElementById('stockBody').innerHTML = '<tr><td colspan="8" class="text-center py-10 text-white/30">Loading…</td></tr>';
  try {
    const r = await fetch(`${BASE}/stock/products?date=${date}`, {headers: authH()});
    const d = await r.json();
    const rows = Array.isArray(d) ? d : (d.data || d.stock || []);
    renderStockRows(rows);
  } catch(_) {
    document.getElementById('stockBody').innerHTML = '<tr><td colspan="8" class="text-center py-10 text-red-400/60">Failed to load stock data.</td></tr>';
  }
}

function calcClosing(tr) {
  const opening = parseFloat(tr.querySelector('.opening-input').value) || 0;
  const imp     = parseFloat(tr.querySelector('.import-disp').dataset.val) || 0;
  const sold    = parseFloat(tr.querySelector('.sold-disp').dataset.val)   || 0;
  return opening + imp - sold;
}

function renderStockRows(rows) {
  const tbody = document.getElementById('stockBody');
  if (!rows.length) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-10 text-white/30">No products found.</td></tr>';
    return;
  }
  tbody.innerHTML = '';
  rows.forEach(row => {
    const soldQty = parseFloat(row.sold_qty || row.sold_stock || 0);
    const closing = (parseFloat(row.opening_stock||0) + parseFloat(row.import_qty||0) - soldQty);
    const lowStock = closing < 3;
    const tr = document.createElement('tr');
    tr.dataset.productId = row.product_id || row.id;
    tr.dataset.stockId   = row.stock_id   || '';
    if (lowStock) tr.classList.add('low-stock-row');
    tr.innerHTML = `
      <td class="font-medium text-white">${row.product_name||'—'}${lowStock ? '<span class="low-warn">⚠ Low</span>' : ''}</td>
      <td><span class="px-2 py-0.5 rounded text-xs font-semibold" style="background:rgba(50,237,255,0.1);color:rgba(50,237,255,0.8)">${row.category||'—'}</span></td>
      <td><input type="number" class="orion-input opening-input" style="width:72px" min="0" step="1" value="${row.opening_stock||0}" /></td>
      <td><span class="import-disp text-cyan-300/80 font-semibold text-sm" data-val="${row.import_qty||0}">${row.import_qty||0}</span></td>
      <td><span class="sold-disp text-orange-400/80 font-semibold text-sm" data-val="${soldQty}">${soldQty}</span></td>
      <td><span class="closing-cell" id="closing-${row.product_id||row.id}">${Math.max(0,closing)}</span></td>
      <td class="text-white/30 text-xs">${fmt(row.updated_at)}</td>
      <td>
        <div class="flex items-center gap-1">
          <button class="btn-save-row save-row-btn" data-pid="${row.product_id||row.id}">Save</button>
          <span class="saved-badge" id="rsaved-${row.product_id||row.id}"><iconify-icon icon="solar:check-circle-bold" width="12"></iconify-icon></span>
        </div>
      </td>`;
    tbody.appendChild(tr);

    tr.querySelector('.opening-input').addEventListener('input', () => {
      const c = calcClosing(tr);
      const pid = row.product_id || row.id;
      const el  = document.getElementById(`closing-${pid}`);
      if (el) el.textContent = Math.max(0, c);
      if (c < 3) tr.classList.add('low-stock-row'); else tr.classList.remove('low-stock-row');
    });

    tr.querySelector('.save-row-btn').addEventListener('click', () => saveRow(tr));
  });
}

async function saveRow(tr) {
  const pid      = tr.dataset.productId;
  const stockId  = tr.dataset.stockId;
  const date     = document.getElementById('stockDate').value;
  const opening  = parseFloat(tr.querySelector('.opening-input').value) || 0;
  const closing  = calcClosing(tr);
  const importQty = parseFloat(tr.querySelector('.import-disp').dataset.val) || 0;
  const soldQty   = parseFloat(tr.querySelector('.sold-disp').dataset.val) || 0;
  const payload  = { product_id: pid, date, opening_stock: opening, closing_stock: Math.max(0, closing), import_qty: importQty, sold_stock: soldQty };

  try {
    let r;
    if (stockId) {
      r = await fetch(`${BASE}/stock/${stockId}`, {method:'PATCH', headers:authH(), body:JSON.stringify(payload)});
    } else {
      r = await fetch(`${BASE}/stock`, {method:'POST', headers:authH(), body:JSON.stringify(payload)});
    }
    const d = await r.json();
    if (r.ok) {
      const newId = d.id || d.stock_id || stockId;
      if (newId) tr.dataset.stockId = newId;
      const badge = document.getElementById(`rsaved-${pid}`);
      if (badge) { badge.classList.add('show'); setTimeout(()=>badge.classList.remove('show'),2000); }
    }
  } catch(_) {}
}

document.getElementById('saveAllBtn').addEventListener('click', async () => {
  const rows = document.querySelectorAll('#stockBody tr[data-product-id]');
  for (const tr of rows) await saveRow(tr);
});

document.getElementById('stockDate').addEventListener('change', loadStock);
document.getElementById('stockDate').value = new Date().toISOString().split('T')[0];
loadStock();

/* ── Search / filter stock rows ── */
function filterStockRows() {
  const q = (document.getElementById('stockSearch').value || '').toLowerCase().trim();
  document.querySelectorAll('#stockBody tr[data-product-id]').forEach(tr => {
    if (!q) { tr.style.display = ''; return; }
    const name = (tr.querySelector('td:first-child')?.textContent || '').toLowerCase();
    const cat  = (tr.querySelector('td:nth-child(2)')?.textContent || '').toLowerCase();
    tr.style.display = (name.includes(q) || cat.includes(q)) ? '' : 'none';
  });
}
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
