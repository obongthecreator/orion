<?php
if ( ! class_exists( 'Orion_Auth' ) ) { wp_redirect( '/orion/login' ); exit; }
Orion_Auth::require_login();
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Stock History — Orion Brothers</title>
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
    .orion-input,.orion-select{background:rgba(255,255,255,0.05);border:1px solid rgba(50,237,255,0.18);border-radius:.625rem;color:#fff;padding:.5rem .75rem;font-size:.875rem;outline:none;font-family:'Inter',sans-serif;transition:border-color .2s,box-shadow .2s;width:100%}
    .orion-input:focus,.orion-select:focus{border-color:rgba(50,237,255,0.55);box-shadow:0 0 0 3px rgba(50,237,255,0.1)}
    .orion-select option{background:#0B1B3C;color:#fff}
    .orion-input::placeholder{color:rgba(255,255,255,0.3)}
    .tbl{width:100%;border-collapse:collapse}
    .tbl th{padding:.625rem .75rem;text-align:left;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:rgba(50,237,255,0.7);border-bottom:1px solid rgba(50,237,255,0.12);white-space:nowrap}
    .tbl td{padding:.625rem .75rem;border-bottom:1px solid rgba(255,255,255,0.04);font-size:.875rem;color:rgba(255,255,255,0.85)}
    .tbl tr:last-child td{border-bottom:none}
    .tbl tbody tr:hover td{background:rgba(50,237,255,0.03)}
    .btn-primary{background:linear-gradient(135deg,#32EDFF,#1ac8d8);color:#0B1B3C;font-weight:700;border-radius:.75rem;padding:.5rem 1.125rem;font-size:.875rem;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:.4rem;transition:opacity .2s}
    .btn-primary:hover{opacity:.88}
    .btn-ghost{background:rgba(255,255,255,0.07);color:rgba(255,255,255,0.75);font-weight:600;border-radius:.75rem;padding:.5rem 1rem;font-size:.8rem;border:1px solid rgba(255,255,255,0.1);cursor:pointer;display:inline-flex;align-items:center;gap:.35rem;transition:background .2s}
    .btn-ghost:hover{background:rgba(255,255,255,0.12);color:#fff}
    .trend-bar{height:6px;border-radius:3px;background:rgba(50,237,255,0.15);overflow:hidden;margin-top:3px}
    .trend-fill{height:100%;border-radius:3px;background:linear-gradient(90deg,#32EDFF,#1ac8d8);transition:width .4s}
    .low-badge{background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3);border-radius:.4rem;padding:.1rem .45rem;font-size:.65rem;font-weight:700}
    .ok-badge{background:rgba(74,222,128,0.12);color:#4ade80;border:1px solid rgba(74,222,128,0.25);border-radius:.4rem;padding:.1rem .45rem;font-size:.65rem;font-weight:700}
  </style>
</head>
<body class="min-h-screen">

<header class="glass-header sticky top-0 z-40 px-4 py-3">
  <div class="max-w-6xl mx-auto flex items-center gap-3">
    <a href="/orion/stock" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition-colors">
      <iconify-icon icon="solar:arrow-left-linear" width="18" style="color:#32EDFF"></iconify-icon>
    </a>
    <div>
      <h1 class="text-lg font-bold text-white leading-tight">Stock History</h1>
      <p class="text-xs text-white/40">Historical stock levels &amp; trends</p>
    </div>
  </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-6 space-y-5">

  <!-- Filters -->
  <div class="glass-card p-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
      <div>
        <label class="block text-xs text-white/40 font-semibold uppercase tracking-wider mb-1">From</label>
        <input type="date" id="filterFrom" class="orion-input" />
      </div>
      <div>
        <label class="block text-xs text-white/40 font-semibold uppercase tracking-wider mb-1">To</label>
        <input type="date" id="filterTo" class="orion-input" />
      </div>
      <div>
        <label class="block text-xs text-white/40 font-semibold uppercase tracking-wider mb-1">Product</label>
        <select id="filterProduct" class="orion-select">
          <option value="">All Products</option>
        </select>
      </div>
      <div class="flex gap-2">
        <button id="filterBtn" class="btn-primary flex-1 justify-center">
          <iconify-icon icon="solar:filter-linear" width="15"></iconify-icon>Filter
        </button>
        <button id="resetBtn" class="btn-ghost">
          <iconify-icon icon="solar:refresh-linear" width="15"></iconify-icon>
        </button>
      </div>
    </div>
  </div>

  <!-- KPI row -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    <div class="glass-card p-4"><p class="text-xs text-white/40 uppercase font-semibold tracking-wider mb-1">Avg Closing</p><p class="text-xl font-bold text-white" id="kpiAvgClose">—</p></div>
    <div class="glass-card p-4"><p class="text-xs text-white/40 uppercase font-semibold tracking-wider mb-1">Min Closing</p><p class="text-xl font-bold text-red-400" id="kpiMin">—</p></div>
    <div class="glass-card p-4"><p class="text-xs text-white/40 uppercase font-semibold tracking-wider mb-1">Max Closing</p><p class="text-xl font-bold" style="color:#32EDFF" id="kpiMax">—</p></div>
    <div class="glass-card p-4"><p class="text-xs text-white/40 uppercase font-semibold tracking-wider mb-1">Low Stock Days</p><p class="text-xl font-bold text-orange-400" id="kpiLow">—</p></div>
  </div>

  <!-- Table -->
  <div class="glass-card overflow-hidden">
    <div class="overflow-x-auto">
      <table class="tbl">
        <thead>
          <tr>
            <th>Date</th>
            <th>Product</th>
            <th>Opening</th>
            <th>Import</th>
            <th>Sold</th>
            <th>Closing</th>
            <th>Trend</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="historyBody">
          <tr><td colspan="8" class="text-center py-10 text-white/30">Loading…</td></tr>
        </tbody>
      </table>
    </div>
  </div>

</main>

<script>
const BASE  = '/wp-json/orion/v1';
const token = () => localStorage.getItem('orion_token') || '';
const authH = () => ({'Authorization':'Bearer '+token()});

let allData = [];

function fmt(dateStr) {
  if (!dateStr) return '—';
  const d = new Date(dateStr);
  return isNaN(d) ? dateStr : d.toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'});
}

async function loadProducts() {
  try {
    const r = await fetch(`${BASE}/products`, {headers: authH()});
    const d = await r.json();
    const prods = Array.isArray(d) ? d : (d.data || d.products || []);
    const sel = document.getElementById('filterProduct');
    prods.forEach(p => {
      const o = document.createElement('option');
      o.value = p.id; o.textContent = p.name || p.product_name || '';
      sel.appendChild(o);
    });
  } catch(_) {}
}

async function loadHistory() {
  const from    = document.getElementById('filterFrom').value;
  const to      = document.getElementById('filterTo').value;
  const product = document.getElementById('filterProduct').value;
  let url = `${BASE}/stock/history?`;
  if (from) url += `from=${from}&`;
  if (to)   url += `to=${to}&`;
  if (product) url += `product_id=${product}&`;

  document.getElementById('historyBody').innerHTML = '<tr><td colspan="8" class="text-center py-10 text-white/30">Loading…</td></tr>';
  try {
    const r = await fetch(url, {headers: authH()});
    const d = await r.json();
    allData = Array.isArray(d) ? d : (d.data || d.stock || []);
  } catch(_) { allData = []; }
  renderTable();
}

function renderTable() {
  const tbody = document.getElementById('historyBody');
  if (!allData.length) {
    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-10 text-white/30">No stock history found.</td></tr>';
    updateKPIs([]);
    return;
  }
  updateKPIs(allData);
  const maxClose = Math.max(...allData.map(r => parseFloat(r.closing_stock||0)));
  tbody.innerHTML = allData.map(row => {
    const closing = parseFloat(row.closing_stock||0);
    const pct     = maxClose > 0 ? Math.round((closing/maxClose)*100) : 0;
    const isLow   = closing < 3;
    return `<tr>
      <td>${fmt(row.date)}</td>
      <td class="font-medium text-white">${row.product_name||'—'}</td>
      <td>${row.opening_stock||0}</td>
      <td style="color:#32EDFF">${row.import_qty||0}</td>
      <td class="text-orange-400">${row.sold_qty||0}</td>
      <td class="font-bold text-white">${closing}</td>
      <td style="min-width:80px"><div class="trend-bar"><div class="trend-fill" style="width:${pct}%"></div></div><span class="text-white/30 text-xs">${pct}%</span></td>
      <td>${isLow ? '<span class="low-badge">⚠ Low</span>' : '<span class="ok-badge">OK</span>'}</td>
    </tr>`;
  }).join('');
}

function updateKPIs(data) {
  if (!data.length) { ['kpiAvgClose','kpiMin','kpiMax','kpiLow'].forEach(id => document.getElementById(id).textContent = '—'); return; }
  const closing = data.map(r => parseFloat(r.closing_stock||0));
  document.getElementById('kpiAvgClose').textContent = (closing.reduce((a,b)=>a+b,0)/closing.length).toFixed(1);
  document.getElementById('kpiMin').textContent      = Math.min(...closing);
  document.getElementById('kpiMax').textContent      = Math.max(...closing);
  document.getElementById('kpiLow').textContent      = closing.filter(v=>v<3).length;
}

document.getElementById('filterBtn').addEventListener('click', loadHistory);
document.getElementById('resetBtn').addEventListener('click', () => {
  const today = new Date().toISOString().split('T')[0];
  const week  = new Date(Date.now()-6*864e5).toISOString().split('T')[0];
  document.getElementById('filterFrom').value = week;
  document.getElementById('filterTo').value   = today;
  document.getElementById('filterProduct').value = '';
  loadHistory();
});

(async () => {
  const today = new Date().toISOString().split('T')[0];
  const week  = new Date(Date.now()-6*864e5).toISOString().split('T')[0];
  document.getElementById('filterFrom').value = week;
  document.getElementById('filterTo').value   = today;
  await loadProducts();
  await loadHistory();
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
