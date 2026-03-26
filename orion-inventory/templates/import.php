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
  <title>Import Stock — Orion Brothers</title>
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
    .glass-header { background: rgba(11,27,60,0.85); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border-bottom: 1px solid rgba(50,237,255,0.14); }
    .glass-card { background: rgba(11,27,60,0.65); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(50,237,255,0.15); border-radius: 1.25rem; }
    .orion-input, .orion-select { width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(50,237,255,0.18); border-radius: 0.625rem; color: #fff; padding: 0.5rem 0.75rem; font-size: 0.875rem; outline: none; font-family: 'Inter', sans-serif; transition: border-color 0.2s, background 0.2s, box-shadow 0.2s; }
    .orion-input::placeholder { color: rgba(255,255,255,0.3); }
    .orion-input:focus, .orion-select:focus { border-color: rgba(50,237,255,0.55); background: rgba(50,237,255,0.06); box-shadow: 0 0 0 3px rgba(50,237,255,0.1); }
    .orion-select option { background: #0B1B3C; color: #fff; }
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table th { padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: rgba(50,237,255,0.7); border-bottom: 1px solid rgba(50,237,255,0.12); white-space: nowrap; }
    .items-table td { padding: 0.5rem 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; }
    .items-table tr:last-child td { border-bottom: none; }
    .items-table tr:hover td { background: rgba(50,237,255,0.03); }
    .btn-primary { background: linear-gradient(135deg, #32EDFF, #1ac8d8); color: #0B1B3C; font-weight: 700; border-radius: 0.75rem; padding: 0.625rem 1.25rem; font-size: 0.875rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; transition: opacity 0.2s, transform 0.15s; }
    .btn-primary:hover { opacity: 0.88; transform: translateY(-1px); }
    .btn-ghost { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.75); font-weight: 600; border-radius: 0.75rem; padding: 0.5rem 1rem; font-size: 0.8rem; border: 1px solid rgba(255,255,255,0.1); cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem; transition: background 0.2s, color 0.2s; }
    .btn-ghost:hover { background: rgba(255,255,255,0.12); color: #fff; }
    .btn-danger { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.25); border-radius: 0.5rem; padding: 0.375rem 0.5rem; cursor: pointer; transition: background 0.2s; display: inline-flex; align-items: center; }
    .btn-danger:hover { background: rgba(239,68,68,0.28); }
    .saved-badge { display: inline-flex; align-items: center; gap: 0.25rem; color: #4ade80; font-size: 0.7rem; font-weight: 600; opacity: 0; transition: opacity 0.3s; }
    .saved-badge.show { opacity: 1; }
    .saving-badge { display: inline-flex; align-items: center; gap: 0.25rem; color: #32EDFF; font-size: 0.7rem; font-weight: 600; opacity: 0; transition: opacity 0.3s; }
    .saving-badge.show { opacity: 1; }
    .error-badge { color: #f87171; font-size: 0.7rem; font-weight: 600; opacity: 0; transition: opacity 0.3s; }
    .error-badge.show { opacity: 1; }
  </style>
</head>
<body class="min-h-screen">

<!-- Header -->
<header class="glass-header sticky top-0 z-40 px-4 py-3">
  <div class="max-w-5xl mx-auto flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <a href="/orion/home" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition-colors">
        <iconify-icon icon="solar:arrow-left-linear" width="18" class="text-cyan-400" style="color:#32EDFF"></iconify-icon>
      </a>
      <div>
        <h1 class="text-lg font-bold text-white leading-tight">Import Stock</h1>
        <p class="text-xs text-white/40">Record incoming inventory</p>
      </div>
    </div>
    <a href="/orion/import-history" class="btn-ghost">
      <iconify-icon icon="solar:history-linear" width="15"></iconify-icon>
      Import History
    </a>
  </div>
</header>

<main class="max-w-5xl mx-auto px-4 py-6 space-y-5">

  <!-- Date & Summary Row -->
  <div class="flex flex-wrap items-start gap-4">
    <!-- Date Picker -->
    <div class="glass-card p-4 flex items-center gap-3 flex-shrink-0">
      <iconify-icon icon="solar:calendar-linear" width="20" style="color:#32EDFF"></iconify-icon>
      <div>
        <label class="block text-xs text-white/40 font-semibold uppercase tracking-wider mb-1">Import Date</label>
        <input type="date" id="importDate" class="orion-input" style="width:auto;padding:0.375rem 0.625rem;" />
      </div>
    </div>
    <!-- Summary -->
    <div class="glass-card p-4 flex items-center gap-4 flex-1 min-w-[200px]">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(50,237,255,0.12)">
        <iconify-icon icon="solar:box-linear" width="22" style="color:#32EDFF"></iconify-icon>
      </div>
      <div>
        <p class="text-xs text-white/40 font-semibold uppercase tracking-wider">Total Items Today</p>
        <p class="text-2xl font-bold text-white" id="totalQty">0</p>
      </div>
    </div>
    <div class="glass-card p-4 flex items-center gap-4 flex-1 min-w-[200px]">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(50,237,255,0.12)">
        <iconify-icon icon="solar:layers-linear" width="22" style="color:#32EDFF"></iconify-icon>
      </div>
      <div>
        <p class="text-xs text-white/40 font-semibold uppercase tracking-wider">Products</p>
        <p class="text-2xl font-bold text-white" id="totalProducts">0</p>
      </div>
    </div>
  </div>

  <!-- Import Table Card -->
  <div class="glass-card overflow-hidden">
    <div class="px-5 py-4 flex items-center justify-between border-b" style="border-color:rgba(50,237,255,0.1)">
      <h2 class="font-semibold text-white text-sm flex items-center gap-2">
        <iconify-icon icon="solar:import-linear" width="18" style="color:#32EDFF"></iconify-icon>
        Import Lines
      </h2>
      <div class="flex items-center gap-2">
        <!-- Search to filter rows -->
        <div class="flex items-center gap-1.5 rounded-lg px-2 py-1.5" style="background:rgba(255,255,255,0.05);border:1px solid rgba(50,237,255,0.18);">
          <iconify-icon icon="solar:magnifer-linear" width="15" style="color:#32EDFF;flex-shrink:0"></iconify-icon>
          <input type="search" id="importSearch" placeholder="Search product…"
                 style="background:transparent;border:none;outline:none;color:#fff;font-size:0.8rem;width:150px;font-family:'Inter',sans-serif;"
                 oninput="filterImportRows()" />
        </div>
        <button id="saveAllBtn" class="btn-primary text-sm px-4 py-2">
          <iconify-icon icon="solar:diskette-linear" width="15"></iconify-icon>
          Save All
        </button>
      </div>
    </div>

    <!-- Table wrapper (scrollable on mobile) -->
    <div class="overflow-x-auto">
      <table class="items-table">
        <thead>
          <tr>
            <th style="min-width:200px">#&nbsp; Product</th>
            <th style="min-width:180px">Product Name</th>
            <th style="min-width:120px">Quantity</th>
            <th style="min-width:100px">Status</th>
            <th style="min-width:60px">Action</th>
          </tr>
        </thead>
        <tbody id="importBody"></tbody>
      </table>
    </div>

    <!-- Add Row Footer -->
    <div class="px-5 py-4 border-t" style="border-color:rgba(50,237,255,0.08)">
      <button id="addRowBtn" class="btn-ghost w-full justify-center py-3">
        <iconify-icon icon="solar:add-circle-linear" width="17"></iconify-icon>
        Add Product
      </button>
    </div>
  </div>

</main>

<script>
const BASE  = '/wp-json/orion/v1';
const token = () => localStorage.getItem('orion_token') || '';
const authH = () => ({ 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token() });

let products   = [];
let rowCount   = 0;
let savedRows  = {}; // rowId -> importId

/* ── Load products dropdown ── */
async function loadProducts() {
  try {
    const r = await fetch(`${BASE}/products`, { headers: authH() });
    if (!r.ok) return;
    const d = await r.json();
    products = Array.isArray(d) ? d : (d.data || d.products || []);
  } catch(_) {}
}

/* ── Build product <option> list ── */
function buildProductOptions(selectedId = '') {
  let opts = '<option value="">— Select Product —</option>';
  products.forEach(p => {
    const sel = String(p.id) === String(selectedId) ? ' selected' : '';
    opts += `<option value="${p.id}" data-name="${(p.name||'').replace(/"/g,'&quot;')}"${sel}>${p.name || p.product_name || ''}</option>`;
  });
  return opts;
}

/* ── Add a new import row ── */
function addRow(data = {}) {
  rowCount++;
  const id = `row-${rowCount}`;
  const tbody = document.getElementById('importBody');
  const tr = document.createElement('tr');
  tr.id = id;
  tr.dataset.importId = data.id || '';
  tr.innerHTML = `
    <td>
      <select class="orion-select product-sel" data-row="${id}" style="min-width:170px">
        ${buildProductOptions(data.product_id || '')}
      </select>
    </td>
    <td>
      <span class="product-name text-white/80 text-sm font-medium">${data.product_name || ''}</span>
    </td>
    <td>
      <input type="number" class="orion-input qty-input" data-row="${id}" min="0" step="1"
        value="${data.quantity || ''}" placeholder="0" style="width:90px" />
    </td>
    <td>
      <span class="saving-badge" id="saving-${id}"><iconify-icon icon="solar:refresh-circle-linear" width="13"></iconify-icon> Saving…</span>
      <span class="saved-badge" id="saved-${id}"><iconify-icon icon="solar:check-circle-bold" width="13"></iconify-icon> Saved</span>
      <span class="error-badge" id="error-${id}">Error</span>
    </td>
    <td>
      <button class="btn-danger remove-btn" data-row="${id}" title="Remove row">
        <iconify-icon icon="solar:trash-bin-trash-linear" width="15"></iconify-icon>
      </button>
    </td>`;
  tbody.appendChild(tr);

  // Events
  tr.querySelector('.product-sel').addEventListener('change', e => {
    const opt = e.target.selectedOptions[0];
    tr.querySelector('.product-name').textContent = opt ? (opt.dataset.name || '') : '';
    autoSaveRow(id);
  });
  tr.querySelector('.qty-input').addEventListener('change', () => autoSaveRow(id));
  tr.querySelector('.qty-input').addEventListener('blur',   () => autoSaveRow(id));
  tr.querySelector('.remove-btn').addEventListener('click', () => removeRow(id));

  updateSummary();
}

/* ── Remove a row ── */
async function removeRow(rowId) {
  const tr = document.getElementById(rowId);
  const importId = tr?.dataset.importId;
  if (importId) {
    try {
      await fetch(`${BASE}/import/${importId}`, { method: 'DELETE', headers: authH() });
    } catch(_) {}
  }
  tr?.remove();
  updateSummary();
}

/* ── Show/hide status badges ── */
function setStatus(rowId, state) {
  ['saving','saved','error'].forEach(s => {
    document.getElementById(`${s}-${rowId}`)?.classList.remove('show');
  });
  if (state) document.getElementById(`${state}-${rowId}`)?.classList.add('show');
  if (state === 'saved') {
    setTimeout(() => document.getElementById(`saved-${rowId}`)?.classList.remove('show'), 2500);
  }
}

/* ── Auto-save a single row ── */
async function autoSaveRow(rowId) {
  const tr        = document.getElementById(rowId);
  if (!tr) return;
  const productId = tr.querySelector('.product-sel').value;
  const qty       = tr.querySelector('.qty-input').value;
  if (!productId || qty === '') return;

  setStatus(rowId, 'saving');
  const date      = document.getElementById('importDate').value;
  const importId  = tr.dataset.importId;
  const payload   = { product_id: productId, quantity: parseInt(qty, 10), date };

  try {
    let r;
    if (importId) {
      r = await fetch(`${BASE}/import/${importId}`, { method: 'PATCH', headers: authH(), body: JSON.stringify(payload) });
    } else {
      r = await fetch(`${BASE}/import`, { method: 'POST', headers: authH(), body: JSON.stringify(payload) });
    }
    const d = await r.json();
    if (r.ok) {
      const newId = d.id || d.import_id || importId;
      if (newId) tr.dataset.importId = newId;
      setStatus(rowId, 'saved');
      updateSummary();
    } else {
      setStatus(rowId, 'error');
      document.getElementById(`error-${rowId}`).textContent = d.message || 'Error';
    }
  } catch(_) {
    setStatus(rowId, 'error');
  }
}

/* ── Save all rows ── */
document.getElementById('saveAllBtn').addEventListener('click', async () => {
  const rows = document.querySelectorAll('#importBody tr');
  for (const tr of rows) {
    await autoSaveRow(tr.id);
  }
});

/* ── Update summary counts ── */
function updateSummary() {
  let totalQty = 0, totalProducts = 0;
  document.querySelectorAll('#importBody tr').forEach(tr => {
    const qty = parseInt(tr.querySelector('.qty-input')?.value || 0, 10);
    const pid = tr.querySelector('.product-sel')?.value;
    if (pid) { totalProducts++; totalQty += qty || 0; }
  });
  document.getElementById('totalQty').textContent      = totalQty;
  document.getElementById('totalProducts').textContent = totalProducts;
}

/* ── Date change: reload existing imports for that date ── */
document.getElementById('importDate').addEventListener('change', loadImportsForDate);

async function loadImportsForDate() {
  const date = document.getElementById('importDate').value;
  if (!date) return;
  try {
    const r = await fetch(`${BASE}/import?date=${date}`, { headers: authH() });
    if (!r.ok) return;
    const d = await r.json();
    const rows = Array.isArray(d) ? d : (d.data || d.imports || []);
    document.getElementById('importBody').innerHTML = '';
    rowCount = 0;
    if (rows.length > 0) {
      rows.forEach(row => addRow(row));
    } else {
      addRow();
    }
    updateSummary();
  } catch(_) {
    addRow();
  }
}

/* ── Add row button ── */
document.getElementById('addRowBtn').addEventListener('click', () => addRow());

/* ── Filter import rows by product name ── */
function filterImportRows() {
  const q = (document.getElementById('importSearch')?.value || '').toLowerCase().trim();
  document.querySelectorAll('#importBody tr').forEach(tr => {
    if (!q) { tr.style.display = ''; return; }
    const name = (tr.querySelector('.product-name')?.textContent || '').toLowerCase();
    const sel  = tr.querySelector('.product-sel');
    const selText = sel ? (sel.options[sel.selectedIndex]?.text || '').toLowerCase() : '';
    tr.style.display = (name.includes(q) || selText.includes(q)) ? '' : 'none';
  });
}

/* ── Init ── */
(async () => {
  document.getElementById('importDate').value = new Date().toISOString().split('T')[0];
  await loadProducts();
  await loadImportsForDate();
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
