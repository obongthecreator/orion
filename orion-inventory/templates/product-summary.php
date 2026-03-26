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
  <title>Product Summary — Orion Brothers</title>
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

    /* Stat cards */
    .stat-card {
      background: rgba(11,27,60,0.65);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(50,237,255,0.15);
      border-radius: 1rem;
      padding: 1.25rem 1.375rem;
      transition: border-color 0.2s, transform 0.2s;
    }
    .stat-card:hover { border-color: rgba(50,237,255,0.35); transform: translateY(-2px); }

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
      width: 100%;
    }
    .orion-input::placeholder { color: rgba(255,255,255,0.3); }
    .orion-input:focus, .orion-select:focus {
      border-color: rgba(50,237,255,0.5);
      box-shadow: 0 0 0 3px rgba(50,237,255,0.1);
    }
    .orion-select option { background: #0B1B3C; color: #fff; }

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
      cursor: pointer;
      user-select: none;
    }
    .data-table th:hover { color: #32EDFF; }
    .data-table th .sort-icon { opacity: 0.4; font-size: 0.75rem; vertical-align: middle; }
    .data-table th.sort-asc .sort-icon,
    .data-table th.sort-desc .sort-icon { opacity: 1; color: #32EDFF; }
    .data-table td {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      font-size: 0.875rem;
      vertical-align: middle;
    }
    .data-table tbody tr { transition: background 0.15s; }
    .data-table tbody tr:hover td { background: rgba(50,237,255,0.04); }

    /* Status badges */
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
    .badge-blue   { background: rgba(96,165,250,0.15); color: #60a5fa; border: 1px solid rgba(96,165,250,0.25); }

    /* Type pill */
    .type-pill {
      display: inline-block;
      padding: 0.2rem 0.65rem;
      border-radius: 9999px;
      font-size: 0.7rem;
      font-weight: 600;
    }
    .type-sales   { background: rgba(50,237,255,0.1); color: #32EDFF; border: 1px solid rgba(50,237,255,0.2); }
    .type-repairs { background: rgba(167,139,250,0.1); color: #a78bfa; border: 1px solid rgba(167,139,250,0.2); }

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
      transition: all 0.2s;
    }
    .btn-ghost:hover { background: rgba(50,237,255,0.14); border-color: rgba(50,237,255,0.4); }

    /* Table scroll */
    .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-scroll::-webkit-scrollbar { height: 4px; }
    .table-scroll::-webkit-scrollbar-track { background: transparent; }
    .table-scroll::-webkit-scrollbar-thumb { background: rgba(50,237,255,0.2); border-radius: 2px; }

    /* Low stock pulse */
    @keyframes pulse-dot { 0%,100%{opacity:1;} 50%{opacity:0.4;} }
    .pulse-dot { animation: pulse-dot 1.5s ease-in-out infinite; }

    /* Orb */
    .orb { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; z-index: 0; }
  </style>
</head>
<body>
  <div class="orb" style="width:500px;height:500px;background:rgba(50,237,255,0.04);top:-100px;right:-150px;"></div>
  <div class="orb" style="width:400px;height:400px;background:rgba(50,237,255,0.03);bottom:-80px;left:-100px;"></div>

  <!-- Header -->
  <header class="glass-header sticky top-0 z-50 px-4 sm:px-6">
    <div class="max-w-6xl mx-auto flex items-center gap-3 h-16">
      <a href="/orion/home" class="flex items-center justify-center w-9 h-9 rounded-xl flex-shrink-0"
         style="background:rgba(50,237,255,0.08);border:1px solid rgba(50,237,255,0.18);" aria-label="Back">
        <iconify-icon icon="solar:arrow-left-linear" style="color:#32EDFF;font-size:1.25rem;"></iconify-icon>
      </a>
      <div class="flex items-center gap-2.5 flex-1 min-w-0">
        <div class="flex items-center justify-center w-9 h-9 rounded-xl flex-shrink-0"
             style="background:rgba(50,237,255,0.1);border:1px solid rgba(50,237,255,0.2);">
          <iconify-icon icon="solar:chart-square-linear" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
        </div>
        <div>
          <h1 class="text-white font-bold text-lg leading-tight">Product Summary</h1>
          <p class="text-xs leading-tight" style="color:rgba(255,255,255,0.4);">Current inventory overview</p>
        </div>
      </div>
      <button class="btn-ghost flex-shrink-0" onclick="loadProducts()" id="refreshBtn">
        <iconify-icon icon="solar:refresh-linear" style="font-size:1rem;"></iconify-icon>
        <span class="hidden sm:inline">Refresh</span>
      </button>
    </div>
  </header>

  <main class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 py-8">

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-7" id="statsRow">
      <div class="stat-card flex items-center gap-4">
        <div class="flex items-center justify-center w-11 h-11 rounded-xl flex-shrink-0"
             style="background:rgba(50,237,255,0.1);border:1px solid rgba(50,237,255,0.2);">
          <iconify-icon icon="solar:box-linear" style="color:#32EDFF;font-size:1.4rem;"></iconify-icon>
        </div>
        <div>
          <p class="text-xs mb-0.5" style="color:rgba(255,255,255,0.45);">Total Products</p>
          <p class="text-2xl font-black text-white" id="statTotal">—</p>
        </div>
      </div>
      <div class="stat-card flex items-center gap-4">
        <div class="flex items-center justify-center w-11 h-11 rounded-xl flex-shrink-0"
             style="background:rgba(167,139,250,0.1);border:1px solid rgba(167,139,250,0.2);">
          <iconify-icon icon="solar:tag-linear" style="color:#a78bfa;font-size:1.4rem;"></iconify-icon>
        </div>
        <div>
          <p class="text-xs mb-0.5" style="color:rgba(255,255,255,0.45);">Total Categories</p>
          <p class="text-2xl font-black text-white" id="statCategories">—</p>
        </div>
      </div>
      <div class="stat-card flex items-center gap-4">
        <div class="flex items-center justify-center w-11 h-11 rounded-xl flex-shrink-0"
             style="background:rgba(251,191,36,0.1);border:1px solid rgba(251,191,36,0.2);">
          <iconify-icon icon="solar:danger-triangle-linear" style="color:#fbbf24;font-size:1.4rem;" class="pulse-dot"></iconify-icon>
        </div>
        <div>
          <p class="text-xs mb-0.5" style="color:rgba(255,255,255,0.45);">Low Stock Alerts</p>
          <p class="text-2xl font-black" id="statLowStock" style="color:#fbbf24;">—</p>
        </div>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="glass-card p-4 mb-5">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="relative">
          <iconify-icon icon="solar:magnifer-linear" style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:rgba(255,255,255,0.3);font-size:1rem;pointer-events:none;"></iconify-icon>
          <input type="search" id="searchInput" class="orion-input" placeholder="Search product name…"
                 style="padding-left:2.25rem;" oninput="applyFilters()">
        </div>
        <div>
          <select id="categoryFilter" class="orion-select" onchange="applyFilters()">
            <option value="">All Categories</option>
          </select>
        </div>
        <div>
          <select id="typeFilter" class="orion-select" onchange="applyFilters()">
            <option value="">All Types</option>
            <option value="sales">Sales</option>
            <option value="repairs">Repairs</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Product Table -->
    <div class="glass-card overflow-hidden">
      <div class="flex items-center justify-between p-4 border-b" style="border-color:rgba(50,237,255,0.1);">
        <h2 class="font-bold text-white text-base flex items-center gap-2">
          <iconify-icon icon="solar:box-linear" style="color:#32EDFF;font-size:1.1rem;"></iconify-icon>
          Products
          <span id="productCount" class="ml-1 text-xs px-2 py-0.5 rounded-full"
                style="background:rgba(50,237,255,0.12);color:#32EDFF;font-weight:700;">0</span>
        </h2>
        <div id="filterActive" class="hidden text-xs px-2.5 py-1 rounded-full"
             style="background:rgba(50,237,255,0.1);color:#32EDFF;border:1px solid rgba(50,237,255,0.2);">
          Filtered
        </div>
      </div>

      <!-- Loading -->
      <div id="tableLoading" class="flex items-center justify-center gap-3 py-14 text-sm" style="color:rgba(255,255,255,0.4);">
        <iconify-icon icon="solar:refresh-linear" class="animate-spin" style="font-size:1.25rem;"></iconify-icon>
        Loading products…
      </div>

      <!-- Empty -->
      <div id="tableEmpty" class="hidden flex flex-col items-center gap-3 py-14 text-center px-4">
        <iconify-icon icon="solar:box-linear" style="color:rgba(50,237,255,0.25);font-size:3rem;"></iconify-icon>
        <p class="font-semibold" style="color:rgba(255,255,255,0.4);">No products match your filters</p>
        <button class="btn-ghost" onclick="clearFilters()">Clear Filters</button>
      </div>

      <div class="table-scroll" id="tableWrapper" style="display:none;">
        <table class="data-table" id="productTable">
          <thead>
            <tr>
              <th onclick="sortBy('name')" data-col="name">
                Product Name <iconify-icon icon="solar:sort-vertical-linear" class="sort-icon"></iconify-icon>
              </th>
              <th onclick="sortBy('category')" data-col="category">
                Category <iconify-icon icon="solar:sort-vertical-linear" class="sort-icon"></iconify-icon>
              </th>
              <th onclick="sortBy('type')" data-col="type">
                Type <iconify-icon icon="solar:sort-vertical-linear" class="sort-icon"></iconify-icon>
              </th>
              <th onclick="sortBy('price')" data-col="price">
                Price (₦) <iconify-icon icon="solar:sort-vertical-linear" class="sort-icon"></iconify-icon>
              </th>
              <th onclick="sortBy('stock')" data-col="stock">
                Stock <iconify-icon icon="solar:sort-vertical-linear" class="sort-icon"></iconify-icon>
              </th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="productTableBody"></tbody>
        </table>
      </div>
    </div>

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

  let allProducts = [];
  let filteredProducts = [];
  let sortCol = 'name';
  let sortDir = 'asc';

  function parseNum(v) { const n = parseFloat(String(v).replace(/,/g,'')); return isNaN(n)?0:n; }
  function formatNum(n) { return Number(n).toLocaleString('en-NG',{minimumFractionDigits:2,maximumFractionDigits:2}); }
  function escHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  async function loadProducts() {
    const btn = document.getElementById('refreshBtn');
    btn.querySelector('iconify-icon').classList.add('animate-spin');
    document.getElementById('tableLoading').classList.remove('hidden');
    document.getElementById('tableWrapper').style.display = 'none';
    document.getElementById('tableEmpty').classList.add('hidden');

    try {
      const r = await fetch(`${BASE}/product-summary`);
      if (!r.ok) throw new Error('HTTP ' + r.status);
      const d = await r.json();
      allProducts = Array.isArray(d) ? d : (d.products || []);
      populateCategoryFilter();
      updateStats();
      applyFilters();
    } catch (err) {
      document.getElementById('tableLoading').innerHTML =
        `<iconify-icon icon="solar:close-circle-linear" style="color:#f87171;font-size:1.5rem;"></iconify-icon>
         <span style="color:#f87171;">Failed to load: ${err.message}</span>`;
    } finally {
      btn.querySelector('iconify-icon').classList.remove('animate-spin');
    }
  }

  function populateCategoryFilter() {
    const cats = [...new Set(allProducts.map(p => p.category || 'Uncategorised'))].sort();
    const sel  = document.getElementById('categoryFilter');
    const cur  = sel.value;
    sel.innerHTML = '<option value="">All Categories</option>';
    cats.forEach(c => {
      const opt = document.createElement('option');
      opt.value = c;
      opt.textContent = c;
      if (c === cur) opt.selected = true;
      sel.appendChild(opt);
    });
  }

  function updateStats() {
    const cats     = new Set(allProducts.map(p => p.category || 'Uncategorised'));
    const lowStock = allProducts.filter(p => parseInt(p.stock||p.current_stock||0) > 0 && parseInt(p.stock||p.current_stock||0) < 5).length;
    document.getElementById('statTotal').textContent      = allProducts.length;
    document.getElementById('statCategories').textContent = cats.size;
    document.getElementById('statLowStock').textContent   = lowStock;
  }

  function applyFilters() {
    const q    = (document.getElementById('searchInput').value || '').toLowerCase();
    const cat  = document.getElementById('categoryFilter').value;
    const type = document.getElementById('typeFilter').value.toLowerCase();

    filteredProducts = allProducts.filter(p => {
      const name = (p.name || '').toLowerCase();
      const pCat = (p.category || '').toLowerCase();
      const pType = (p.type || '').toLowerCase();
      return (!q || name.includes(q))
          && (!cat  || pCat === cat.toLowerCase())
          && (!type || pType === type);
    });

    const isFiltered = !!(q || cat || type);
    document.getElementById('filterActive').classList.toggle('hidden', !isFiltered);

    sortAndRender();
  }

  function sortBy(col) {
    if (sortCol === col) sortDir = sortDir === 'asc' ? 'desc' : 'asc';
    else { sortCol = col; sortDir = 'asc'; }
    document.querySelectorAll('.data-table th[data-col]').forEach(th => {
      th.classList.remove('sort-asc', 'sort-desc');
      if (th.dataset.col === col) th.classList.add('sort-' + sortDir);
    });
    sortAndRender();
  }

  function sortAndRender() {
    const sorted = [...filteredProducts].sort((a, b) => {
      let av, bv;
      if (sortCol === 'price' || sortCol === 'stock') {
        const key = sortCol === 'stock' ? (a.stock !== undefined ? 'stock' : 'current_stock') : 'price';
        av = parseNum(a[key] ?? a[sortCol === 'stock' ? 'current_stock' : 'price'] ?? 0);
        bv = parseNum(b[key] ?? b[sortCol === 'stock' ? 'current_stock' : 'price'] ?? 0);
        return sortDir === 'asc' ? av - bv : bv - av;
      } else {
        av = (a[sortCol] || '').toLowerCase();
        bv = (b[sortCol] || '').toLowerCase();
        return sortDir === 'asc' ? av.localeCompare(bv) : bv.localeCompare(av);
      }
    });
    renderTable(sorted);
  }

  function renderTable(products) {
    const tbody   = document.getElementById('productTableBody');
    const loading = document.getElementById('tableLoading');
    const empty   = document.getElementById('tableEmpty');
    const wrapper = document.getElementById('tableWrapper');

    loading.classList.add('hidden');
    document.getElementById('productCount').textContent = products.length;

    if (!products.length) {
      wrapper.style.display = 'none';
      empty.classList.remove('hidden');
      return;
    }
    empty.classList.add('hidden');
    wrapper.style.display = '';

    tbody.innerHTML = '';
    products.forEach(p => {
      const stock = parseInt(p.stock ?? p.current_stock ?? 0);
      const price = parseNum(p.price);
      const type  = (p.type || 'sales').toLowerCase();

      let statusBadge;
      if (stock === 0) {
        statusBadge = '<span class="badge badge-red"><iconify-icon icon="solar:close-circle-linear"></iconify-icon>Out of Stock</span>';
      } else if (stock < 5) {
        statusBadge = '<span class="badge badge-yellow"><iconify-icon icon="solar:danger-triangle-linear"></iconify-icon>Low Stock</span>';
      } else {
        statusBadge = '<span class="badge badge-green"><iconify-icon icon="solar:check-circle-linear"></iconify-icon>In Stock</span>';
      }

      const typePill = type === 'repairs'
        ? `<span class="type-pill type-repairs"><iconify-icon icon="solar:settings-linear" style="vertical-align:-2px;font-size:.8rem;"></iconify-icon> Repairs</span>`
        : `<span class="type-pill type-sales"><iconify-icon icon="solar:cart-large-linear" style="vertical-align:-2px;font-size:.8rem;"></iconify-icon> Sales</span>`;

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="font-semibold text-white">${escHtml(p.name || '—')}</td>
        <td style="color:rgba(255,255,255,0.6);">${escHtml(p.category || '—')}</td>
        <td>${typePill}</td>
        <td class="font-bold" style="color:#32EDFF;">₦${formatNum(price)}</td>
        <td class="font-bold" style="color:${stock===0?'#f87171':stock<5?'#fbbf24':'rgba(255,255,255,0.85)'};">${stock}</td>
        <td>${statusBadge}</td>`;
      tbody.appendChild(tr);
    });
  }

  function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('categoryFilter').value = '';
    document.getElementById('typeFilter').value = '';
    applyFilters();
  }

  loadProducts();
  </script>
</body>
</html>
