<?php
if ( ! class_exists( 'Orion_Auth' ) ) { wp_redirect( '/orion/login' ); exit; }
Orion_Auth::require_login();
$current_user = Orion_Auth::get_current_user();
if ( ! in_array( $current_user['role'] ?? '', [ 'admin', 'super_admin' ], true ) ) {
    wp_redirect( '/orion/home' ); exit;
}
$user_name  = $current_user['display_name'] ?? $current_user['username'] ?? 'Admin';
$user_role  = $current_user['role'] ?? 'admin';
$is_super   = $user_role === 'super_admin';
$initials   = strtoupper( implode( '', array_map( fn($w) => $w[0], array_slice( explode( ' ', trim($user_name) ), 0, 2 ) ) ) );
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel — Orion Brothers</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
  <link rel="stylesheet" href="/wp-content/plugins/orion-inventory/assets/css/main.css">
  <script>tailwind.config={theme:{extend:{colors:{navy:'#0B1B3C',cyan:'#32EDFF'},fontFamily:{inter:['Inter','sans-serif']}}}}</script>
  <style>
    body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0B1B3C 0%,#060f22 100%);min-height:100vh;}
    .glass-header{background:rgba(11,27,60,0.82);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border-bottom:1px solid rgba(50,237,255,0.14);box-shadow:0 2px 32px rgba(0,0,0,0.3);}
    .glass-card{background:rgba(11,27,60,0.65);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(50,237,255,0.15);border-radius:1.25rem;box-shadow:0 8px 32px rgba(0,0,0,0.4);}
    .glass-footer{background:rgba(11,27,60,0.82);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border-top:1px solid rgba(50,237,255,0.14);}
    .tab-btn{padding:0.5rem 1.25rem;border-radius:9999px;font-size:0.85rem;font-weight:600;cursor:pointer;transition:all 0.2s;color:rgba(255,255,255,0.5);background:transparent;border:1px solid transparent;}
    .tab-btn.active{background:rgba(50,237,255,0.15);color:#32EDFF;border-color:rgba(50,237,255,0.3);}
    .tab-btn:hover:not(.active){color:#fff;background:rgba(255,255,255,0.07);}
    .tab-content{display:none;}.tab-content.active{display:block;}
    .form-input{background:rgba(255,255,255,0.07);border:1px solid rgba(50,237,255,0.2);border-radius:0.75rem;color:#fff;padding:0.65rem 1rem;width:100%;font-family:'Inter',sans-serif;transition:border-color 0.2s;}
    .form-input:focus{outline:none;border-color:#32EDFF;background:rgba(50,237,255,0.07);}
    .form-input::placeholder{color:rgba(255,255,255,0.3);}
    .form-select{background:rgba(11,27,60,0.9);border:1px solid rgba(50,237,255,0.2);border-radius:0.75rem;color:#fff;padding:0.65rem 1rem;width:100%;font-family:'Inter',sans-serif;cursor:pointer;}
    .form-select:focus{outline:none;border-color:#32EDFF;}
    .form-select option{background:#0B1B3C;}
    .btn-cyan{background:linear-gradient(135deg,#32EDFF,#1bc8d9);color:#0B1B3C;font-weight:700;border-radius:9999px;padding:0.55rem 1.5rem;border:none;cursor:pointer;transition:opacity 0.2s,transform 0.15s;font-size:0.85rem;white-space:nowrap;}
    .btn-cyan:hover{opacity:0.9;transform:scale(1.02);}
    .btn-danger{background:rgba(255,77,77,0.15);color:#ff4d4d;font-weight:600;border-radius:9999px;padding:0.4rem 1rem;border:1px solid rgba(255,77,77,0.3);cursor:pointer;font-size:0.8rem;transition:all 0.2s;}
    .btn-danger:hover{background:rgba(255,77,77,0.3);}
    .btn-edit{background:rgba(50,237,255,0.12);color:#32EDFF;font-weight:600;border-radius:9999px;padding:0.4rem 1rem;border:1px solid rgba(50,237,255,0.25);cursor:pointer;font-size:0.8rem;transition:all 0.2s;}
    .btn-edit:hover{background:rgba(50,237,255,0.25);}
    .data-table{width:100%;border-collapse:collapse;}
    .data-table th{color:rgba(50,237,255,0.9);font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;padding:0.75rem 1rem;text-align:left;border-bottom:1px solid rgba(50,237,255,0.15);}
    .data-table td{color:#fff;font-size:0.9rem;padding:0.85rem 1rem;border-bottom:1px solid rgba(255,255,255,0.06);}
    .data-table tr:hover td{background:rgba(50,237,255,0.04);}
    .badge{font-size:0.7rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:9999px;}
    .badge-admin{background:rgba(255,200,50,0.15);color:#ffc832;border:1px solid rgba(255,200,50,0.3);}
    .badge-staff{background:rgba(50,237,255,0.12);color:#32EDFF;border:1px solid rgba(50,237,255,0.3);}
    .badge-super{background:rgba(255,100,200,0.15);color:#ff64c8;border:1px solid rgba(255,100,200,0.3);}
    .badge-sales{background:rgba(50,237,255,0.12);color:#32EDFF;border:1px solid rgba(50,237,255,0.3);}
    .badge-repairs{background:rgba(255,150,50,0.15);color:#ff9632;border:1px solid rgba(255,150,50,0.3);}
    .toast{position:fixed;bottom:1.5rem;right:1.5rem;background:rgba(11,27,60,0.95);border:1px solid #32EDFF;color:#fff;padding:0.85rem 1.5rem;border-radius:0.85rem;display:none;z-index:9999;font-size:0.9rem;}
    .toast.show{display:block;animation:fadeInUp 0.3s ease;}
    @keyframes fadeInUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
    .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);z-index:1000;display:none;align-items:center;justify-content:center;}
    .modal-overlay.show{display:flex;}
    .modal-box{background:rgba(11,27,60,0.95);border:1px solid rgba(50,237,255,0.25);border-radius:1.25rem;padding:2rem;max-width:480px;width:100%;box-shadow:0 24px 64px rgba(0,0,0,0.6);}
  </style>
</head>
<body class="min-h-screen flex flex-col">

<header class="glass-header px-6 py-3 flex items-center justify-between sticky top-0 z-50">
  <div class="flex items-center gap-3">
    <a href="/orion/home" class="text-[#32EDFF] hover:text-white transition-colors"><iconify-icon icon="solar:arrow-left-linear" style="font-size:1.3rem"></iconify-icon></a>
    <iconify-icon icon="solar:settings-bold" style="font-size:1.4rem;color:#32EDFF"></iconify-icon>
    <span class="text-white font-bold text-lg">Admin Panel</span>
  </div>
  <div class="flex items-center gap-4">
    <span class="text-white/70 text-xs hidden sm:block" id="realTimeClock"></span>
    <span class="text-white/50 text-xs hidden md:block" id="userLocation">Detecting...</span>
    <span class="text-[#32EDFF] font-semibold text-sm"><?php echo esc_html($user_name); ?></span>
  </div>
</header>

<main class="flex-1 px-4 sm:px-6 py-8 max-w-6xl mx-auto w-full">

  <!-- Tabs -->
  <div class="flex flex-wrap gap-2 mb-8">
    <button class="tab-btn active" onclick="switchTab('categories',this)">
      <iconify-icon icon="solar:tag-linear" class="mr-1"></iconify-icon>Categories
    </button>
    <button class="tab-btn" onclick="switchTab('products',this)">
      <iconify-icon icon="solar:box-linear" class="mr-1"></iconify-icon>Products
    </button>
    <button class="tab-btn" onclick="switchTab('users',this)">
      <iconify-icon icon="solar:users-group-rounded-linear" class="mr-1"></iconify-icon>Users
    </button>
    <?php if ($is_super): ?>
    <button class="tab-btn" onclick="switchTab('settings',this)">
      <iconify-icon icon="solar:tuning-linear" class="mr-1"></iconify-icon>Settings
    </button>
    <?php endif; ?>
  </div>

  <!-- Categories Tab -->
  <div id="tab-categories" class="tab-content active">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Sales Categories -->
      <div class="glass-card p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-white font-bold flex items-center gap-2">
            <iconify-icon icon="solar:cart-linear" style="color:#32EDFF"></iconify-icon>Sales Categories
          </h3>
        </div>
        <div class="flex gap-2 mb-4">
          <input type="text" id="newSalesCat" class="form-input" placeholder="New sales category name">
          <button class="btn-cyan" onclick="addCategory('sales')">Add</button>
        </div>
        <div id="salesCatList" class="space-y-2"></div>
      </div>
      <!-- Repairs Categories -->
      <div class="glass-card p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-white font-bold flex items-center gap-2">
            <iconify-icon icon="solar:wrench-linear" style="color:#32EDFF"></iconify-icon>Repairs Categories
          </h3>
        </div>
        <div class="flex gap-2 mb-4">
          <input type="text" id="newRepairsCat" class="form-input" placeholder="New repairs category name">
          <button class="btn-cyan" onclick="addCategory('repairs')">Add</button>
        </div>
        <div id="repairsCatList" class="space-y-2"></div>
      </div>
    </div>
  </div>

  <!-- Products Tab -->
  <div id="tab-products" class="tab-content">
    <div class="glass-card p-6 mb-6">
      <h3 class="text-white font-bold mb-4">Add New Product</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <input type="text" id="newProdName" class="form-input" placeholder="Product name">
        <select id="newProdCategory" class="form-select"><option value="">Select Category</option></select>
        <input type="number" id="newProdPrice" class="form-input font-inter font-bold" placeholder="Price (₦)">
        <select id="newProdType" class="form-select">
          <option value="sales">Sales</option>
          <option value="repairs">Repairs</option>
        </select>
      </div>
      <button class="btn-cyan mt-4" onclick="addProduct()">
        <iconify-icon icon="solar:add-circle-linear" class="mr-1"></iconify-icon>Add Product
      </button>
    </div>
    <div class="glass-card p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-white font-bold">All Products</h3>
        <div class="flex gap-2">
          <select id="filterType" class="form-select" style="width:auto" onchange="loadProducts()">
            <option value="">All Types</option>
            <option value="sales">Sales</option>
            <option value="repairs">Repairs</option>
          </select>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="data-table">
          <thead><tr>
            <th>Product</th><th>Category</th><th>Price (₦)</th><th>Type</th><th>Actions</th>
          </tr></thead>
          <tbody id="productTableBody"><tr><td colspan="5" class="text-center text-white/40 py-8">Loading...</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Users Tab -->
  <div id="tab-users" class="tab-content">
    <div class="glass-card p-6 mb-6">
      <h3 class="text-white font-bold mb-4">Add New User</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <input type="text" id="newUserName" class="form-input" placeholder="Full Name">
        <input type="text" id="newUserUsername" class="form-input" placeholder="Username">
        <input type="password" id="newUserPassword" class="form-input" placeholder="Password">
        <select id="newUserRole" class="form-select">
          <option value="staff">Staff</option>
          <option value="admin">Admin</option>
          <?php if ($is_super): ?><option value="super_admin">Super Admin</option><?php endif; ?>
        </select>
        <input type="email" id="newUserEmail" class="form-input" placeholder="Email (optional)">
        <input type="tel" id="newUserPhone" class="form-input" placeholder="Phone (optional)">
      </div>
      <button class="btn-cyan mt-4" onclick="addUser()">
        <iconify-icon icon="solar:user-plus-linear" class="mr-1"></iconify-icon>Add User
      </button>
    </div>
    <div class="glass-card p-6">
      <h3 class="text-white font-bold mb-4">Staff &amp; Admins</h3>
      <div class="overflow-x-auto">
        <table class="data-table">
          <thead><tr><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Created</th><th>Actions</th></tr></thead>
          <tbody id="usersTableBody"><tr><td colspan="5" class="text-center text-white/40 py-8">Loading...</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>

  <?php if ($is_super): ?>
  <!-- Settings Tab -->
  <div id="tab-settings" class="tab-content">
    <div class="glass-card p-6">
      <h3 class="text-white font-bold mb-6">System Settings</h3>
      <div class="grid gap-5 max-w-lg">
        <div>
          <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Company Name</label>
          <input type="text" class="form-input" value="Orion Brothers" id="settingCompany">
        </div>
        <div>
          <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Company Tagline</label>
          <input type="text" class="form-input" value="Technology &amp; Electronics Store" id="settingTagline">
        </div>
        <div>
          <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Receipt Footer Text</label>
          <input type="text" class="form-input" value="Thank you for shopping with us!" id="settingReceipt">
        </div>
        <button class="btn-cyan" onclick="saveSettings()">Save Settings</button>
      </div>
    </div>
  </div>
  <?php endif; ?>

</main>

<!-- Edit Product Modal -->
<div class="modal-overlay" id="editProductModal">
  <div class="modal-box">
    <h3 class="text-white font-bold text-lg mb-4">Edit Product</h3>
    <input type="hidden" id="editProdId">
    <div class="grid gap-4">
      <div>
        <label class="text-white/60 text-xs mb-1 block">Product Name</label>
        <input type="text" id="editProdName" class="form-input">
      </div>
      <div>
        <label class="text-white/60 text-xs mb-1 block">Price (₦)</label>
        <input type="number" id="editProdPrice" class="form-input font-bold">
      </div>
    </div>
    <div class="flex gap-3 mt-6">
      <button class="btn-cyan flex-1" onclick="saveEditProduct()">Save Changes</button>
      <button class="btn-danger" onclick="closeModal('editProductModal')">Cancel</button>
    </div>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal-overlay" id="editUserModal">
  <div class="modal-box" style="max-width:520px">
    <h3 class="text-white font-bold text-lg mb-5 flex items-center gap-2">
      <iconify-icon icon="solar:pen-bold" style="color:#32EDFF"></iconify-icon>Edit User
    </h3>
    <input type="hidden" id="editUserId">
    <div class="grid gap-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Full Name</label>
          <input type="text" id="editUserFullName" class="form-input" placeholder="Full Name">
        </div>
        <div>
          <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Username</label>
          <input type="text" id="editUserUsername" class="form-input opacity-50" readonly>
        </div>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Email</label>
          <input type="email" id="editUserEmail" class="form-input" placeholder="Email">
        </div>
        <div>
          <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Phone</label>
          <input type="tel" id="editUserPhone" class="form-input" placeholder="Phone">
        </div>
      </div>
      <div>
        <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">Role</label>
        <select id="editUserRole" class="form-select">
          <option value="staff">Staff</option>
          <option value="admin">Admin</option>
          <?php if ($is_super): ?><option value="super_admin">Super Admin</option><?php endif; ?>
        </select>
      </div>
      <div>
        <label class="text-white/60 text-xs uppercase tracking-wider mb-1 block">New Password <span class="text-white/30 normal-case">(leave blank to keep current)</span></label>
        <div class="relative">
          <input type="password" id="editUserPassword" class="form-input pr-12" placeholder="New password">
          <button type="button" style="background:none;border:none;color:rgba(255,255,255,0.5);cursor:pointer;position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);" onclick="toggleEditPw(this)">
            <iconify-icon icon="solar:eye-linear" style="font-size:1.1rem"></iconify-icon>
          </button>
        </div>
      </div>
    </div>
    <div class="flex gap-3 mt-6">
      <button class="btn-cyan flex-1" onclick="saveEditUser()">
        <iconify-icon icon="solar:diskette-linear" class="mr-1"></iconify-icon>Save Changes
      </button>
      <button class="btn-danger" onclick="closeModal('editUserModal')">Cancel</button>
    </div>
  </div>
</div>

<footer class="glass-footer mt-auto px-6 py-4 flex justify-between items-center">
  <div><p class="text-white font-semibold">Orion Brothers</p><p class="text-white/50 text-xs">Technology &amp; Electronics Store</p></div>
  <button onclick="logoutUser()" class="btn-cyan px-5 py-2 text-sm flex items-center gap-2">
    <iconify-icon icon="solar:logout-2-linear"></iconify-icon>Logout
  </button>
</footer>
<div class="toast" id="toast"></div>

<script>
const TOKEN = localStorage.getItem('orion_token') || '';
const API = '/wp-json/orion/v1';
const headers = {'Content-Type':'application/json','Authorization':'Bearer '+TOKEN};

function showToast(msg, ok=true) {
  const t=document.getElementById('toast');
  t.textContent=msg; t.style.borderColor=ok?'#32EDFF':'#ff4d4d';
  t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),3000);
}

function switchTab(name, btn) {
  document.querySelectorAll('.tab-content').forEach(el=>el.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  document.getElementById('tab-'+name).classList.add('active');
  btn.classList.add('active');
  if(name==='products') loadProducts();
  if(name==='users') loadUsers();
  if(name==='categories') loadCategories();
}

function closeModal(id){document.getElementById(id).classList.remove('show');}

// ---- CATEGORIES ----
async function loadCategories() {
  const r = await fetch(API+'/categories', {headers});
  const d = await r.json();
  if (!d.success) return;
  const sales = d.data.filter(c=>c.type==='sales');
  const repairs = d.data.filter(c=>c.type==='repairs');
  renderCategoryList('salesCatList', sales);
  renderCategoryList('repairsCatList', repairs);
  populateCategoryDropdown(d.data);
}

function renderCategoryList(containerId, cats) {
  const el = document.getElementById(containerId);
  if (!cats.length) { el.innerHTML='<p class="text-white/30 text-sm text-center py-2">No categories yet</p>'; return; }
  el.innerHTML = cats.map(c=>`
    <div class="flex items-center justify-between bg-white/5 rounded-lg px-3 py-2">
      <span class="text-white text-sm">${escHtml(c.name)}</span>
      <button class="btn-danger py-1 px-3 text-xs" onclick="deleteCategory(${c.id})">Delete</button>
    </div>`).join('');
}

function populateCategoryDropdown(cats) {
  const sel = document.getElementById('newProdCategory');
  const type = document.getElementById('newProdType').value;
  sel.innerHTML = '<option value="">Select Category</option>' +
    cats.filter(c=>c.type===type||type==='').map(c=>`<option value="${c.id}" data-type="${c.type}">${escHtml(c.name)} (${c.type})</option>`).join('');
}

document.getElementById('newProdType')?.addEventListener('change', () => loadCategories());

async function addCategory(type) {
  const inp = document.getElementById(type==='sales'?'newSalesCat':'newRepairsCat');
  const name = inp.value.trim();
  if(!name) return showToast('Enter category name', false);
  const r = await fetch(API+'/categories', {method:'POST', headers, body:JSON.stringify({name, type})});
  const d = await r.json();
  if(d.success){ inp.value=''; loadCategories(); showToast('Category added!'); }
  else showToast(d.message||'Error', false);
}

async function deleteCategory(id) {
  if(!confirm('Delete this category? Products under it may be affected.')) return;
  const r = await fetch(API+'/categories/'+id, {method:'DELETE', headers});
  const d = await r.json();
  if(d.success){ loadCategories(); showToast('Deleted!'); }
  else showToast(d.message||'Error', false);
}

// ---- PRODUCTS ----
async function loadProducts() {
  const type = document.getElementById('filterType').value;
  const url = type ? API+'/products?type='+type : API+'/products';
  const r = await fetch(url, {headers});
  const d = await r.json();
  const tbody = document.getElementById('productTableBody');
  if(!d.success||!d.data.length){ tbody.innerHTML='<tr><td colspan="5" class="text-center text-white/40 py-8">No products found</td></tr>'; return; }
  tbody.innerHTML = d.data.map(p=>`
    <tr>
      <td class="font-medium">${escHtml(p.name)}</td>
      <td>${escHtml(p.category_name||p.category||'-')}</td>
      <td class="font-inter font-bold text-[#32EDFF]">₦${Number(p.price).toLocaleString('en-NG')}</td>
      <td><span class="badge badge-${p.type}">${p.type}</span></td>
      <td class="flex gap-2">
        <button class="btn-edit" onclick="openEditProduct(${p.id},'${escAttr(p.name)}',${p.price})">Edit</button>
        <button class="btn-danger" onclick="deleteProduct(${p.id})">Delete</button>
      </td>
    </tr>`).join('');
}

async function addProduct() {
  const name = document.getElementById('newProdName').value.trim();
  const cat = document.getElementById('newProdCategory').value;
  const price = document.getElementById('newProdPrice').value;
  const type = document.getElementById('newProdType').value;
  if(!name||!price) return showToast('Fill name and price', false);
  const r = await fetch(API+'/products', {method:'POST', headers, body:JSON.stringify({name, category_id:cat||null, price:parseFloat(price), type})});
  const d = await r.json();
  if(d.success){ document.getElementById('newProdName').value=''; document.getElementById('newProdPrice').value=''; loadProducts(); showToast('Product added!'); }
  else showToast(d.message||'Error', false);
}

function openEditProduct(id, name, price) {
  document.getElementById('editProdId').value=id;
  document.getElementById('editProdName').value=name;
  document.getElementById('editProdPrice').value=price;
  document.getElementById('editProductModal').classList.add('show');
}

async function saveEditProduct() {
  const id = document.getElementById('editProdId').value;
  const name = document.getElementById('editProdName').value.trim();
  const price = parseFloat(document.getElementById('editProdPrice').value);
  if(!name||isNaN(price)) return showToast('Invalid input', false);
  const r = await fetch(API+'/products/'+id, {method:'PUT', headers, body:JSON.stringify({name, price})});
  const d = await r.json();
  if(d.success){ closeModal('editProductModal'); loadProducts(); showToast('Product updated!'); }
  else showToast(d.message||'Error', false);
}

async function deleteProduct(id) {
  if(!confirm('Delete this product?')) return;
  const r = await fetch(API+'/products/'+id, {method:'DELETE', headers});
  const d = await r.json();
  if(d.success){ loadProducts(); showToast('Deleted!'); }
  else showToast(d.message||'Error', false);
}

// ---- USERS ----
async function loadUsers() {
  const r = await fetch(API+'/users', {headers});
  const d = await r.json();
  const tbody = document.getElementById('usersTableBody');
  if(!d.success||!d.data||!d.data.length){
    tbody.innerHTML='<tr><td colspan="6" class="text-center text-white/40 py-8">No users found</td></tr>'; return;
  }
  tbody.innerHTML = d.data.map(u=>`
    <tr>
      <td class="font-semibold">${escHtml(u.full_name||u.username)}</td>
      <td class="text-white/60">${escHtml(u.username)}</td>
      <td class="text-white/50 text-xs">${escHtml(u.email||'-')}</td>
      <td><span class="badge badge-${u.role==='super_admin'?'super':u.role}">${escHtml(u.role.replace(/_/g,' '))}</span></td>
      <td class="text-white/50 text-xs">${u.created_at?String(u.created_at).split('T')[0]:'-'}</td>
      <td class="flex gap-2">
        <button class="btn-edit" onclick="openEditUser(${u.id},'${escAttr(u.full_name||'')}','${escAttr(u.username||'')}','${escAttr(u.email||'')}','${escAttr(u.phone||'')}','${escAttr(u.role||'staff')}')">
          <iconify-icon icon="solar:pen-linear"></iconify-icon> Edit
        </button>
        <button class="btn-danger" onclick="deleteUser(${u.id})">Delete</button>
      </td>
    </tr>`).join('');
}

async function addUser() {
  const full_name=document.getElementById('newUserName').value.trim();
  const username=document.getElementById('newUserUsername').value.trim();
  const password=document.getElementById('newUserPassword').value;
  const role=document.getElementById('newUserRole').value;
  const email=document.getElementById('newUserEmail').value.trim();
  const phone=document.getElementById('newUserPhone').value.trim();
  if(!full_name||!username||!password) return showToast('Name, username and password required', false);
  const r = await fetch(API+'/users', {method:'POST', headers, body:JSON.stringify({full_name, username, password, role, email, phone})});
  const d = await r.json();
  if(d.success){
    ['newUserName','newUserUsername','newUserPassword','newUserEmail','newUserPhone'].forEach(id=>document.getElementById(id).value='');
    loadUsers(); showToast('User created!');
  } else showToast(d.message||'Error', false);
}

function openEditUser(id, fullName, username, email, phone, role) {
  document.getElementById('editUserId').value = id;
  document.getElementById('editUserFullName').value = fullName;
  document.getElementById('editUserUsername').value = username;
  document.getElementById('editUserEmail').value = email;
  document.getElementById('editUserPhone').value = phone;
  document.getElementById('editUserRole').value = role;
  document.getElementById('editUserPassword').value = '';
  document.getElementById('editUserModal').classList.add('show');
}

async function saveEditUser() {
  const id       = document.getElementById('editUserId').value;
  const full_name= document.getElementById('editUserFullName').value.trim();
  const email    = document.getElementById('editUserEmail').value.trim();
  const phone    = document.getElementById('editUserPhone').value.trim();
  const role     = document.getElementById('editUserRole').value;
  const password = document.getElementById('editUserPassword').value;
  if(!full_name) return showToast('Full name is required', false);
  const body = {full_name, email, phone, role};
  if(password) body.password = password;
  const r = await fetch(API+'/users/'+id, {method:'PUT', headers, body:JSON.stringify(body)});
  const d = await r.json();
  if(d.success){ closeModal('editUserModal'); loadUsers(); showToast('User updated!'); }
  else showToast(d.message||'Error', false);
}

function toggleEditPw(btn) {
  const inp = document.getElementById('editUserPassword');
  const show = inp.type==='password';
  inp.type = show ? 'text' : 'password';
  btn.innerHTML = show
    ? '<iconify-icon icon="solar:eye-closed-linear" style="font-size:1.1rem"></iconify-icon>'
    : '<iconify-icon icon="solar:eye-linear" style="font-size:1.1rem"></iconify-icon>';
}

async function deleteUser(id) {
  if(!confirm('Delete this user? This cannot be undone.')) return;
  const r = await fetch(API+'/users/'+id, {method:'DELETE', headers});
  const d = await r.json();
  if(d.success){ loadUsers(); showToast('User deleted!'); }
  else showToast(d.message||'Error', false);
}

function saveSettings() { showToast('Settings saved!'); }

// Utilities
function escHtml(s){ const d=document.createElement('div'); d.textContent=s; return d.innerHTML; }
function escAttr(s){ return String(s).replace(/'/g,"\\'"); }

async function logoutUser() {
  await fetch(API+'/logout', {method:'POST', headers});
  localStorage.removeItem('orion_token');
  window.location.href = '/orion/login';
}

function updateClock(){
  const el=document.getElementById('realTimeClock');
  if(el) el.textContent=new Date().toLocaleString('en-NG',{dateStyle:'medium',timeStyle:'short'});
}
setInterval(updateClock,1000); updateClock();

if(navigator.geolocation){
  navigator.geolocation.getCurrentPosition(async pos=>{
    try{
      const r=await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${pos.coords.latitude}&lon=${pos.coords.longitude}&format=json`);
      const d=await r.json();
      document.getElementById('userLocation').textContent=d.address?.city||d.address?.town||'Nsukka, Enugu';
    }catch(e){document.getElementById('userLocation').textContent='Nsukka, Enugu';}
  },()=>{document.getElementById('userLocation').textContent='Nsukka, Enugu';});
}

// Init
loadCategories();
</script>
</body>
</html>
