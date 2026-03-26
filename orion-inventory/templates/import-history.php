<?php
if ( ! class_exists( 'Orion_Auth' ) ) { wp_redirect( '/orion/login' ); exit; }
Orion_Auth::require_login();
$current_user = Orion_Auth::get_current_user();
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Import History — Orion Brothers</title>
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
    .orion-input,.orion-select{background:rgba(255,255,255,0.05);border:1px solid rgba(50,237,255,0.18);border-radius:.625rem;color:#fff;padding:.5rem .75rem;font-size:.875rem;outline:none;font-family:'Inter',sans-serif;transition:border-color .2s,background .2s,box-shadow .2s}
    .orion-input:focus,.orion-select:focus{border-color:rgba(50,237,255,0.55);background:rgba(50,237,255,0.06);box-shadow:0 0 0 3px rgba(50,237,255,0.1)}
    .orion-select option{background:#0B1B3C;color:#fff}
    .orion-input::placeholder{color:rgba(255,255,255,0.3)}
    .tbl{width:100%;border-collapse:collapse}
    .tbl th{padding:.625rem .875rem;text-align:left;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:rgba(50,237,255,0.7);border-bottom:1px solid rgba(50,237,255,0.12);white-space:nowrap}
    .tbl td{padding:.625rem .875rem;border-bottom:1px solid rgba(255,255,255,0.04);font-size:.875rem;color:rgba(255,255,255,0.85)}
    .tbl tr:last-child td{border-bottom:none}
    .tbl tbody tr:hover td{background:rgba(50,237,255,0.03)}
    .date-group-header td{background:rgba(50,237,255,0.07);color:#32EDFF;font-weight:700;font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;padding:.5rem .875rem}
    .btn-ghost{background:rgba(255,255,255,0.07);color:rgba(255,255,255,0.75);font-weight:600;border-radius:.75rem;padding:.5rem 1rem;font-size:.8rem;border:1px solid rgba(255,255,255,0.1);cursor:pointer;display:inline-flex;align-items:center;gap:.35rem;transition:background .2s,color .2s}
    .btn-ghost:hover{background:rgba(255,255,255,0.12);color:#fff}
    .btn-primary{background:linear-gradient(135deg,#32EDFF,#1ac8d8);color:#0B1B3C;font-weight:700;border-radius:.75rem;padding:.5rem 1.125rem;font-size:.875rem;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:.4rem;transition:opacity .2s}
    .btn-primary:hover{opacity:.88}
    #emptyState{display:none}
    .skeleton{animation:pulse 1.5s infinite}
    @keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
  </style>
</head>
<body class="min-h-screen">

<header class="glass-header sticky top-0 z-40 px-4 py-3">
  <div class="max-w-5xl mx-auto flex items-center gap-3">
    <a href="/orion/import" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition-colors">
      <iconify-icon icon="solar:arrow-left-linear" width="18" style="color:#32EDFF"></iconify-icon>
    </a>
    <div>
      <h1 class="text-lg font-bold text-white leading-tight">Import History</h1>
      <p class="text-xs text-white/40">All recorded stock imports</p>
    </div>
  </div>
</header>

<main class="max-w-5xl mx-auto px-4 py-6 space-y-5">

  <!-- Filters -->
  <div class="glass-card p-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
      <div>
        <label class="block text-xs text-white/40 font-semibold uppercase tracking-wider mb-1">From</label>
        <input type="date" id="filterFrom" class="orion-input w-full" />
      </div>
      <div>
        <label class="block text-xs text-white/40 font-semibold uppercase tracking-wider mb-1">To</label>
        <input type="date" id="filterTo" class="orion-input w-full" />
      </div>
      <div>
        <label class="block text-xs text-white/40 font-semibold uppercase tracking-wider mb-1">Product</label>
        <select id="filterProduct" class="orion-select w-full">
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
    <!-- Group toggle -->
    <div class="mt-3 flex items-center gap-2">
      <label class="flex items-center gap-2 cursor-pointer select-none text-sm text-white/60">
        <input type="checkbox" id="groupByDate" class="w-4 h-4 accent-cyan-400" checked />
        Group by date
      </label>
    </div>
  </div>

  <!-- Summary Card -->
  <div class="glass-card p-4 flex flex-wrap gap-5">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:rgba(50,237,255,0.12)">
        <iconify-icon icon="solar:box-linear" width="18" style="color:#32EDFF"></iconify-icon>
      </div>
      <div>
        <p class="text-xs text-white/40 uppercase font-semibold tracking-wider">Total Qty</p>
        <p class="text-xl font-bold text-white" id="sumQty">—</p>
      </div>
    </div>
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:rgba(50,237,255,0.12)">
        <iconify-icon icon="solar:layers-linear" width="18" style="color:#32EDFF"></iconify-icon>
      </div>
      <div>
        <p class="text-xs text-white/40 uppercase font-semibold tracking-wider">Import Records</p>
        <p class="text-xl font-bold text-white" id="sumRecords">—</p>
      </div>
    </div>
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:rgba(50,237,255,0.12)">
        <iconify-icon icon="solar:calendar-linear" width="18" style="color:#32EDFF"></iconify-icon>
      </div>
      <div>
        <p class="text-xs text-white/40 uppercase font-semibold tracking-wider">Days in Range</p>
        <p class="text-xl font-bold text-white" id="sumDays">—</p>
      </div>
    </div>
  </div>

  <!-- Table Card -->
  <div class="glass-card overflow-hidden">
    <div class="overflow-x-auto">
      <table class="tbl">
        <thead>
          <tr>
            <th>Date</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Staff</th>
            <th>Created At</th>
          </tr>
        </thead>
        <tbody id="historyBody">
          <tr><td colspan="5" class="text-center py-10 text-white/30 skeleton">Loading…</td></tr>
        </tbody>
      </table>
    </div>
    <div id="emptyState" class="flex flex-col items-center justify-center py-16 gap-3">
      <iconify-icon icon="solar:box-minimalistic-linear" width="48" style="color:rgba(50,237,255,0.3)"></iconify-icon>
      <p class="text-white/40 text-sm">No import records found for this range.</p>
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
function fmtDT(dateStr) {
  if (!dateStr) return '—';
  const d = new Date(dateStr);
  return isNaN(d) ? dateStr : d.toLocaleString('en-GB',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'});
}

async function loadProducts() {
  try {
    const r = await fetch(`${BASE}/products`, {headers: authH()});
    if (!r.ok) return;
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
  let url = `${BASE}/import?`;
  if (from) url += `from=${from}&`;
  if (to)   url += `to=${to}&`;
  if (product) url += `product_id=${product}&`;

  document.getElementById('historyBody').innerHTML = '<tr><td colspan="5" class="text-center py-10 text-white/30 skeleton">Loading…</td></tr>';

  try {
    const r = await fetch(url, {headers: authH()});
    const d = await r.json();
    allData = Array.isArray(d) ? d : (d.data || d.imports || []);
  } catch(_) { allData = []; }

  renderTable();
}

function renderTable() {
  const grouped = document.getElementById('groupByDate').checked;
  const tbody   = document.getElementById('historyBody');
  const empty   = document.getElementById('emptyState');

  if (!allData.length) {
    tbody.innerHTML = '';
    empty.style.display = 'flex';
    document.getElementById('sumQty').textContent     = '0';
    document.getElementById('sumRecords').textContent = '0';
    document.getElementById('sumDays').textContent    = '0';
    return;
  }
  empty.style.display = 'none';

  let totalQty = 0;
  const dateSet = new Set();
  allData.forEach(row => { totalQty += parseInt(row.quantity||0,10); if(row.date) dateSet.add(row.date); });
  document.getElementById('sumQty').textContent     = totalQty;
  document.getElementById('sumRecords').textContent = allData.length;
  document.getElementById('sumDays').textContent    = dateSet.size;

  let html = '';
  if (grouped) {
    const byDate = {};
    allData.forEach(row => {
      const k = row.date || '—';
      if (!byDate[k]) byDate[k] = [];
      byDate[k].push(row);
    });
    Object.keys(byDate).sort((a,b)=>b.localeCompare(a)).forEach(date => {
      const rows = byDate[date];
      const dayQty = rows.reduce((s,r)=>s+parseInt(r.quantity||0,10),0);
      html += `<tr class="date-group-header"><td colspan="5">${fmt(date)} — ${rows.length} record${rows.length!==1?'s':''}, ${dayQty} units total</td></tr>`;
      rows.forEach(row => { html += buildRow(row); });
    });
  } else {
    allData.forEach(row => { html += buildRow(row); });
  }
  tbody.innerHTML = html;
}

function buildRow(row) {
  const staff = row.staff_name || row.created_by_name || row.user || '—';
  return `<tr>
    <td>${fmt(row.date)}</td>
    <td class="font-medium text-white">${row.product_name || '—'}</td>
    <td><span class="px-2 py-0.5 rounded-lg text-xs font-bold" style="background:rgba(50,237,255,0.12);color:#32EDFF">${row.quantity||0}</span></td>
    <td>${staff}</td>
    <td class="text-white/40 text-xs">${fmtDT(row.created_at)}</td>
  </tr>`;
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
document.getElementById('groupByDate').addEventListener('change', renderTable);

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
