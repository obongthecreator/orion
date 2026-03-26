<?php
if ( ! class_exists( 'Orion_Auth' ) ) { wp_redirect( '/orion/login' ); exit; }
Orion_Auth::require_login();
Orion_Auth::require_admin();
$current_user = Orion_Auth::get_current_user();
$user_name    = $current_user['display_name'] ?? $current_user['full_name'] ?? $current_user['username'] ?? 'Admin';
$user_role    = $current_user['role'] ?? 'admin';
$initials     = strtoupper( implode( '', array_map( fn($w) => $w[0], array_slice( explode( ' ', trim($user_name) ), 0, 2 ) ) ) );
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analytics — Orion Brothers</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <link rel="stylesheet" href="/wp-content/plugins/orion-inventory/assets/css/main.css">
  <script>tailwind.config={theme:{extend:{colors:{navy:'#0B1B3C',cyan:'#32EDFF'},fontFamily:{inter:['Inter','sans-serif']}}}}</script>
  <style>
    body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0B1B3C 0%,#060f22 100%);min-height:100vh;}
    .glass-header{background:rgba(11,27,60,0.82);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border-bottom:1px solid rgba(50,237,255,0.14);box-shadow:0 2px 32px rgba(0,0,0,0.3);}
    .glass-card{background:rgba(11,27,60,0.65);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(50,237,255,0.15);border-radius:1.25rem;box-shadow:0 8px 32px rgba(0,0,0,0.4);}
    .glass-footer{background:rgba(11,27,60,0.82);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border-top:1px solid rgba(50,237,255,0.14);}
    .metric-card{background:rgba(11,27,60,0.65);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(50,237,255,0.15);border-radius:1rem;padding:1.5rem;transition:transform 0.2s,border-color 0.2s;}
    .metric-card:hover{transform:translateY(-2px);border-color:rgba(50,237,255,0.35);}
    .metric-value{font-size:1.75rem;font-weight:800;color:#32EDFF;font-family:'Inter',sans-serif;line-height:1;}
    .metric-label{font-size:0.8rem;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.07em;margin-top:0.35rem;}
    .metric-change{font-size:0.78rem;font-weight:600;margin-top:0.5rem;}
    .change-up{color:#4ade80;}.change-down{color:#f87171;}
    .period-btn{padding:0.45rem 1.1rem;border-radius:9999px;font-size:0.82rem;font-weight:600;cursor:pointer;transition:all 0.2s;color:rgba(255,255,255,0.5);background:transparent;border:1px solid rgba(255,255,255,0.12);}
    .period-btn.active{background:rgba(50,237,255,0.15);color:#32EDFF;border-color:rgba(50,237,255,0.35);}
    .period-btn:hover:not(.active){color:#fff;background:rgba(255,255,255,0.07);}
    .data-table{width:100%;border-collapse:collapse;}
    .data-table th{color:rgba(50,237,255,0.9);font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;padding:0.7rem 1rem;text-align:left;border-bottom:1px solid rgba(50,237,255,0.15);}
    .data-table td{color:#fff;font-size:0.88rem;padding:0.8rem 1rem;border-bottom:1px solid rgba(255,255,255,0.06);}
    .data-table tr:hover td{background:rgba(50,237,255,0.04);}
    .rank-badge{width:1.6rem;height:1.6rem;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-weight:800;font-size:0.72rem;}
    .rank-1{background:rgba(255,215,0,0.2);color:#ffd700;border:1px solid rgba(255,215,0,0.3);}
    .rank-2{background:rgba(192,192,192,0.2);color:#c0c0c0;border:1px solid rgba(192,192,192,0.3);}
    .rank-3{background:rgba(205,127,50,0.2);color:#cd7f32;border:1px solid rgba(205,127,50,0.3);}
    .rank-n{background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.5);border:1px solid rgba(255,255,255,0.1);}
    .suggestion-item{background:rgba(50,237,255,0.05);border:1px solid rgba(50,237,255,0.15);border-radius:0.85rem;padding:1rem 1.25rem;display:flex;gap:0.85rem;align-items:flex-start;}
    .suggestion-icon{width:2.2rem;height:2.2rem;border-radius:50%;background:rgba(50,237,255,0.12);border:1px solid rgba(50,237,255,0.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .btn-cyan{background:linear-gradient(135deg,#32EDFF,#1bc8d9);color:#0B1B3C;font-weight:700;border-radius:9999px;padding:0.55rem 1.5rem;border:none;cursor:pointer;transition:opacity 0.2s,transform 0.15s;font-size:0.85rem;}
    .btn-cyan:hover{opacity:0.9;transform:scale(1.02);}
    .skeleton{background:rgba(255,255,255,0.07);border-radius:0.5rem;animation:pulse 1.5s ease-in-out infinite;}
    @keyframes pulse{0%,100%{opacity:0.5}50%{opacity:1}}
    canvas{max-height:280px;}
  </style>
</head>
<body class="min-h-screen flex flex-col">

<header class="glass-header px-6 py-3 flex items-center justify-between sticky top-0 z-50">
  <div class="flex items-center gap-3">
    <a href="/orion/home" class="text-[#32EDFF] hover:text-white transition-colors">
      <iconify-icon icon="solar:arrow-left-linear" style="font-size:1.3rem"></iconify-icon>
    </a>
    <iconify-icon icon="solar:graph-up-bold" style="font-size:1.4rem;color:#32EDFF"></iconify-icon>
    <span class="text-white font-bold text-lg">Analytics</span>
  </div>
  <div class="flex items-center gap-4">
    <span class="text-white/70 text-xs hidden sm:block" id="realTimeClock"></span>
    <span class="text-white/50 text-xs hidden md:block" id="userLocation">Detecting...</span>
    <div class="flex items-center gap-2">
      <div style="width:2rem;height:2rem;border-radius:50%;background:linear-gradient(135deg,#32EDFF,#0099b8);color:#0B1B3C;font-weight:800;font-size:0.75rem;display:flex;align-items:center;justify-content:center;"><?php echo esc_html($initials); ?></div>
      <span class="text-[#32EDFF] font-semibold text-sm hidden sm:block"><?php echo esc_html($user_name); ?></span>
    </div>
  </div>
</header>

<main class="flex-1 px-4 sm:px-6 py-8 max-w-7xl mx-auto w-full">

  <!-- Period Filter -->
  <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
    <div>
      <h1 class="text-white font-black text-2xl sm:text-3xl">Business Analytics</h1>
      <p class="text-white/40 text-sm mt-0.5" id="periodLabel">Today's overview</p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <button class="period-btn active" onclick="setPeriod('today',this)">Today</button>
      <button class="period-btn" onclick="setPeriod('week',this)">This Week</button>
      <button class="period-btn" onclick="setPeriod('month',this)">This Month</button>
      <button class="period-btn" onclick="setPeriod('year',this)">This Year</button>
    </div>
  </div>

  <!-- Metric Cards -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8" id="metricsGrid">
    <?php
    $metrics = [
      ['icon'=>'solar:dollar-minimalistic-bold','label'=>'Total Revenue','id'=>'mRevenue'],
      ['icon'=>'solar:cart-large-bold','label'=>'Sales Orders','id'=>'mOrders'],
      ['icon'=>'solar:chart-square-bold','label'=>'Avg Order Value','id'=>'mAvg'],
      ['icon'=>'solar:settings-bold','label'=>'Repairs Revenue','id'=>'mRepairs'],
    ];
    foreach ($metrics as $m): ?>
    <div class="metric-card">
      <div class="flex items-center justify-between mb-3">
        <iconify-icon icon="<?php echo esc_attr($m['icon']); ?>" style="font-size:1.5rem;color:rgba(50,237,255,0.7)"></iconify-icon>
        <span class="metric-change" id="<?php echo esc_attr($m['id']).'_chg'; ?>"></span>
      </div>
      <div class="metric-value" id="<?php echo esc_attr($m['id']); ?>">—</div>
      <div class="metric-label"><?php echo esc_html($m['label']); ?></div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Charts Row -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    <!-- Revenue Trend (line) -->
    <div class="glass-card p-6 lg:col-span-2">
      <h3 class="text-white font-bold mb-4 flex items-center gap-2">
        <iconify-icon icon="solar:chart-2-linear" style="color:#32EDFF;font-size:1.2rem"></iconify-icon>
        Revenue Trend (Last 7 Days)
      </h3>
      <canvas id="chartTrend"></canvas>
    </div>

    <!-- Category Breakdown (doughnut) -->
    <div class="glass-card p-6">
      <h3 class="text-white font-bold mb-4 flex items-center gap-2">
        <iconify-icon icon="solar:pie-chart-linear" style="color:#32EDFF;font-size:1.2rem"></iconify-icon>
        Sales by Category
      </h3>
      <canvas id="chartCat"></canvas>
      <div id="catLegend" class="mt-4 space-y-1.5 text-sm"></div>
    </div>
  </div>

  <!-- Top & Least Products -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="glass-card p-6">
      <h3 class="text-white font-bold mb-4 flex items-center gap-2">
        <iconify-icon icon="solar:ranking-bold" style="color:#32EDFF;font-size:1.2rem"></iconify-icon>
        Top Selling Products
      </h3>
      <div class="overflow-x-auto">
        <table class="data-table">
          <thead><tr><th>#</th><th>Product</th><th>Qty</th><th>Revenue (₦)</th></tr></thead>
          <tbody id="topProductsBody"></tbody>
        </table>
      </div>
    </div>
    <div class="glass-card p-6">
      <h3 class="text-white font-bold mb-4 flex items-center gap-2">
        <iconify-icon icon="solar:sad-square-linear" style="color:#ff9632;font-size:1.2rem"></iconify-icon>
        Least Selling Products
      </h3>
      <div class="overflow-x-auto">
        <table class="data-table">
          <thead><tr><th>#</th><th>Product</th><th>Qty</th><th>Revenue (₦)</th></tr></thead>
          <tbody id="leastProductsBody"></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Period Comparison Bar Chart -->
  <div class="glass-card p-6 mb-8">
    <h3 class="text-white font-bold mb-4 flex items-center gap-2">
      <iconify-icon icon="solar:chart-square-linear" style="color:#32EDFF;font-size:1.2rem"></iconify-icon>
      Current vs Previous Period Revenue
    </h3>
    <canvas id="chartComparison" style="max-height:200px"></canvas>
  </div>

  <!-- Smart Recommendations -->
  <div class="glass-card p-6">
    <h3 class="text-white font-bold mb-5 flex items-center gap-2">
      <iconify-icon icon="solar:lightbulb-bold" style="color:#32EDFF;font-size:1.2rem"></iconify-icon>
      Smart Recommendations
    </h3>
    <div id="recommendationsList" class="space-y-3">
      <div class="skeleton h-14 rounded-xl"></div>
      <div class="skeleton h-14 rounded-xl"></div>
      <div class="skeleton h-14 rounded-xl"></div>
    </div>
  </div>

</main>

<footer class="glass-footer mt-auto px-6 py-4 flex justify-between items-center">
  <div><p class="text-white font-semibold">Orion Brothers</p><p class="text-white/50 text-xs">Technology &amp; Electronics Store</p></div>
  <button onclick="logoutUser()" class="btn-cyan px-5 py-2 text-sm flex items-center gap-2">
    <iconify-icon icon="solar:logout-2-linear"></iconify-icon>Logout
  </button>
</footer>

<script>
const TOKEN = localStorage.getItem('orion_token') || '';
const API   = '/wp-json/orion/v1';
const HDR   = { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + TOKEN };

let currentPeriod = 'today';
let chartTrend = null, chartCat = null, chartComp = null;

// ---- Period ----
const PERIOD_LABELS = { today:'Today\'s overview', week:'This week\'s overview', month:'This month\'s overview', year:'This year\'s overview' };

function setPeriod(period, btn) {
  currentPeriod = period;
  document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('periodLabel').textContent = PERIOD_LABELS[period] || '';
  loadAnalytics();
}

// ---- Load ----
async function loadAnalytics() {
  const r = await fetch(API + '/analytics?period=' + currentPeriod, { headers: HDR });
  const d = await r.json();
  if (!d.success) return;
  renderMetrics(d);
  renderTrendChart(d.daily_trend || []);
  renderCategoryChart(d.category_breakdown || []);
  renderComparisonChart(d);
  renderProductTables(d.top_products || [], d.least_products || []);
  renderRecommendations(d);
}

// ---- Metrics ----
function fmtNaira(v) { return '₦' + Number(v||0).toLocaleString('en-NG', {minimumFractionDigits:0}); }

function renderMetrics(d) {
  document.getElementById('mRevenue').textContent  = fmtNaira(d.revenue);
  document.getElementById('mOrders').textContent   = (d.orders || 0).toLocaleString();
  document.getElementById('mAvg').textContent      = fmtNaira(d.avg_order);
  document.getElementById('mRepairs').textContent  = fmtNaira(d.repairs_revenue);

  // Revenue change badge
  const chgEl = document.getElementById('mRevenue_chg');
  if (d.revenue_change_pct !== null && d.revenue_change_pct !== undefined) {
    const up = d.revenue_change_pct >= 0;
    chgEl.innerHTML = `<iconify-icon icon="solar:arrow-${up?'up':'down'}-linear"></iconify-icon>${Math.abs(d.revenue_change_pct)}%`;
    chgEl.className = 'metric-change ' + (up ? 'change-up' : 'change-down');
  } else {
    chgEl.textContent = '';
  }
}

// ---- Trend Chart ----
function renderTrendChart(daily) {
  const labels  = daily.map(r => r.date);
  const revenue = daily.map(r => parseFloat(r.revenue || 0));
  const ctx = document.getElementById('chartTrend').getContext('2d');
  if (chartTrend) chartTrend.destroy();
  chartTrend = new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Revenue (₦)',
        data: revenue,
        borderColor: '#32EDFF',
        backgroundColor: 'rgba(50,237,255,0.08)',
        borderWidth: 2.5,
        pointRadius: 4,
        pointBackgroundColor: '#32EDFF',
        fill: true,
        tension: 0.4,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => '₦' + Number(ctx.raw).toLocaleString('en-NG') } }
      },
      scales: {
        x: { ticks: { color: 'rgba(255,255,255,0.4)', font: { size: 11 } }, grid: { color: 'rgba(255,255,255,0.05)' } },
        y: { ticks: { color: 'rgba(255,255,255,0.4)', font: { size: 11 }, callback: v => '₦'+Number(v).toLocaleString('en-NG') }, grid: { color: 'rgba(255,255,255,0.05)' } }
      }
    }
  });
}

// ---- Category Doughnut ----
const PALETTE = ['#32EDFF','#0ea5e9','#a78bfa','#fb923c','#f472b6','#34d399','#fbbf24','#60a5fa'];

function renderCategoryChart(cats) {
  const ctx = document.getElementById('chartCat').getContext('2d');
  if (chartCat) chartCat.destroy();
  if (!cats.length) { ctx.canvas.parentElement.querySelector('#catLegend').innerHTML = '<p class="text-white/30 text-xs text-center">No data</p>'; return; }
  const labels  = cats.map(c => c.category_name || 'Other');
  const values  = cats.map(c => parseFloat(c.revenue || 0));
  const colors  = labels.map((_, i) => PALETTE[i % PALETTE.length]);
  chartCat = new Chart(ctx, {
    type: 'doughnut',
    data: { labels, datasets: [{ data: values, backgroundColor: colors, borderColor: 'rgba(11,27,60,0.9)', borderWidth: 2 }] },
    options: {
      responsive: true,
      cutout: '65%',
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ctx.label + ': ₦' + Number(ctx.raw).toLocaleString('en-NG') } }
      }
    }
  });
  // Custom legend
  const total = values.reduce((a,b)=>a+b,0);
  document.getElementById('catLegend').innerHTML = labels.map((l,i) => `
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-1.5">
        <span style="width:10px;height:10px;border-radius:2px;background:${colors[i]};display:inline-block;flex-shrink:0"></span>
        <span class="text-white/70 text-xs truncate" style="max-width:130px">${escHtml(l)}</span>
      </div>
      <span class="text-xs font-bold" style="color:${colors[i]}">${total>0?Math.round((values[i]/total)*100):0}%</span>
    </div>`).join('');
}

// ---- Comparison Bar ----
function renderComparisonChart(d) {
  const ctx = document.getElementById('chartComparison').getContext('2d');
  if (chartComp) chartComp.destroy();
  const periodMap = { today:'Yesterday', week:'Last Week', month:'Last Month', year:'Last Year' };
  chartComp = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Current Period', 'Previous Period'],
      datasets: [{
        data: [parseFloat(d.revenue||0), parseFloat(d.prev_revenue||0)],
        backgroundColor: ['rgba(50,237,255,0.7)','rgba(50,237,255,0.2)'],
        borderColor: ['#32EDFF','rgba(50,237,255,0.4)'],
        borderWidth: 1.5,
        borderRadius: 8,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => '₦' + Number(ctx.raw).toLocaleString('en-NG') } }
      },
      scales: {
        x: { ticks: { color: 'rgba(255,255,255,0.5)' }, grid: { display: false } },
        y: { ticks: { color: 'rgba(255,255,255,0.4)', callback: v => '₦'+Number(v).toLocaleString('en-NG') }, grid: { color: 'rgba(255,255,255,0.05)' } }
      }
    }
  });
}

// ---- Product Tables ----
function renderProductTables(top, least) {
  const rankClass = i => i===0?'rank-1':i===1?'rank-2':i===2?'rank-3':'rank-n';
  const rows = (list, emptyMsg) => list.length ? list.map((p,i)=>`
    <tr>
      <td><span class="rank-badge ${rankClass(i)}">${i+1}</span></td>
      <td class="font-medium">${escHtml(p.product_name||'-')}</td>
      <td class="font-inter font-bold text-white">${Number(p.qty_sold||0).toLocaleString()}</td>
      <td class="font-inter font-bold text-[#32EDFF]">₦${Number(p.revenue||0).toLocaleString('en-NG')}</td>
    </tr>`).join('')
    : `<tr><td colspan="4" class="text-center text-white/30 py-6">${emptyMsg}</td></tr>`;
  document.getElementById('topProductsBody').innerHTML   = rows(top, 'No sales data for this period');
  document.getElementById('leastProductsBody').innerHTML = rows(least, 'No sales data for this period');
}

// ---- Recommendations ----
function renderRecommendations(d) {
  const rec = [];
  const period = currentPeriod;
  const top = d.top_products || [];
  const least = d.least_products || [];
  const rev = parseFloat(d.revenue || 0);
  const prevRev = parseFloat(d.prev_revenue || 0);
  const chg = d.revenue_change_pct;

  if (top.length) {
    rec.push({
      icon: 'solar:star-bold',
      color: '#ffd700',
      title: `Stock up on "${top[0].product_name}"`,
      body: `Your best seller this period. Ensure adequate stock to avoid missing sales opportunities.`
    });
  }
  if (least.length && least[0].product_name !== (top[0] && top[0].product_name)) {
    rec.push({
      icon: 'solar:tag-price-bold',
      color: '#fb923c',
      title: `Consider discounting "${least[0].product_name}"`,
      body: `This product has low sales velocity. A promotional discount could help clear stock and attract new customers.`
    });
  }
  if (chg !== null && chg < 0) {
    rec.push({
      icon: 'solar:megaphone-bold',
      color: '#f472b6',
      title: 'Revenue is down compared to last period',
      body: `Sales dropped by ${Math.abs(chg)}%. Consider running promotions, social media ads, or loyalty offers to drive traffic in Nsukka.`
    });
  } else if (chg !== null && chg > 15) {
    rec.push({
      icon: 'solar:graph-up-bold',
      color: '#4ade80',
      title: 'Strong revenue growth!',
      body: `Revenue grew by ${chg}% vs last period. Keep the momentum — consider expanding popular product lines.`
    });
  }
  rec.push({
    icon: 'solar:map-point-bold',
    color: '#32EDFF',
    title: 'Maximise the Nsukka market',
    body: 'Offer student discounts for university communities nearby. Bundle popular accessories (screen guards + pouches) at a slight discount to increase basket size.'
  });
  rec.push({
    icon: 'solar:users-group-rounded-bold',
    color: '#a78bfa',
    title: 'Build customer loyalty',
    body: 'Track repeat customers in Credit Sales. Offer loyalty points or discounts after every 5th purchase to increase customer retention.'
  });
  if (parseFloat(d.repairs_revenue || 0) > 0) {
    rec.push({
      icon: 'solar:settings-bold',
      color: '#60a5fa',
      title: 'Repairs are generating income',
      body: 'Promote repair services on social media. Many customers prefer repair over replacement — advertise quick turnaround times.'
    });
  }

  document.getElementById('recommendationsList').innerHTML = rec.map(r => `
    <div class="suggestion-item">
      <div class="suggestion-icon"><iconify-icon icon="${r.icon}" style="color:${r.color};font-size:1.1rem"></iconify-icon></div>
      <div>
        <p class="text-white font-semibold text-sm">${escHtml(r.title)}</p>
        <p class="text-white/50 text-xs mt-0.5 leading-relaxed">${escHtml(r.body)}</p>
      </div>
    </div>`).join('');
}

function escHtml(s){ const d=document.createElement('div'); d.textContent=s; return d.innerHTML; }

async function logoutUser() {
  await fetch(API+'/logout', {method:'POST', headers:HDR});
  localStorage.removeItem('orion_token');
  window.location.href = '/orion/login';
}

function updateClock(){
  const el=document.getElementById('realTimeClock');
  if(el) el.textContent=new Date().toLocaleString('en-NG',{dateStyle:'medium',timeStyle:'short'});
}
setInterval(updateClock,1000); updateClock();

if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(async pos => {
    try {
      const r = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${pos.coords.latitude}&lon=${pos.coords.longitude}&format=json`);
      const d = await r.json();
      document.getElementById('userLocation').textContent = d.address?.city || d.address?.town || 'Nsukka, Enugu';
    } catch(e) { document.getElementById('userLocation').textContent = 'Nsukka, Enugu'; }
  }, () => { document.getElementById('userLocation').textContent = 'Nsukka, Enugu'; });
}

// Init
loadAnalytics();
</script>
</body>
</html>
