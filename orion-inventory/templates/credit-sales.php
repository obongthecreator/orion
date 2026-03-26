<?php
if ( ! class_exists( 'Orion_Auth' ) ) {
    wp_redirect( '/orion/login' );
    exit;
}
Orion_Auth::require_login();
$current_user = Orion_Auth::get_current_user();
$user_name    = $current_user['display_name'] ?? $current_user['username'] ?? 'User';
$user_role    = $current_user['role'] ?? 'staff';
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Credit Sales — Orion Brothers</title>
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
    .orion-input, .orion-select, .orion-textarea {
      width: 100%;
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(50,237,255,0.18);
      border-radius: 0.625rem;
      color: #fff;
      padding: 0.625rem 0.875rem;
      font-size: 0.875rem;
      outline: none;
      font-family: 'Inter', sans-serif;
      transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    }
    .orion-input::placeholder, .orion-textarea::placeholder { color: rgba(255,255,255,0.3); }
    .orion-input:focus, .orion-select:focus, .orion-textarea:focus {
      border-color: rgba(50,237,255,0.55);
      background: rgba(50,237,255,0.06);
      box-shadow: 0 0 0 3px rgba(50,237,255,0.1);
    }
    .orion-select option { background: #0B1B3C; color: #fff; }
    label { color: rgba(255,255,255,0.6); font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.375rem; }

    /* Tab toggle */
    .tab-btn {
      padding: 0.625rem 1.5rem;
      border-radius: 9999px;
      font-size: 0.875rem;
      font-weight: 600;
      cursor: pointer;
      border: 1px solid rgba(50,237,255,0.2);
      background: transparent;
      color: rgba(255,255,255,0.5);
      transition: all 0.2s;
    }
    .tab-btn.active {
      background: linear-gradient(135deg, #32EDFF 0%, #00b8d4 100%);
      color: #0B1B3C;
      border-color: transparent;
      box-shadow: 0 4px 16px rgba(50,237,255,0.3);
    }

    /* Items table */
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table th {
      padding: 0.625rem 0.625rem;
      text-align: left;
      font-size: 0.68rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: rgba(50,237,255,0.7);
      border-bottom: 1px solid rgba(50,237,255,0.12);
      white-space: nowrap;
    }
    .items-table td {
      padding: 0.5rem 0.5rem;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      vertical-align: middle;
    }
    .items-table tr:last-child td { border-bottom: none; }

    /* Qty stepper */
    .qty-btn {
      width: 1.75rem; height: 1.75rem;
      background: rgba(50,237,255,0.1);
      border: 1px solid rgba(50,237,255,0.2);
      border-radius: 0.375rem;
      color: #32EDFF;
      font-size: 1rem;
      cursor: pointer;
      display: inline-flex; align-items: center; justify-content: center;
      transition: background 0.15s;
    }
    .qty-btn:hover { background: rgba(50,237,255,0.2); }
    .qty-input { width: 3rem; text-align: center; background: transparent; border: none; color: #fff; font-size: 0.875rem; font-weight: 600; outline: none; }

    /* Radio pill */
    .radio-pill {
      display: inline-flex; align-items: center; gap: 0.5rem;
      padding: 0.5rem 1rem;
      border-radius: 9999px;
      border: 1px solid rgba(50,237,255,0.2);
      background: rgba(50,237,255,0.05);
      cursor: pointer;
      font-size: 0.875rem;
      font-weight: 500;
      color: rgba(255,255,255,0.6);
      transition: all 0.2s;
      user-select: none;
    }
    .radio-pill:has(input:checked), .radio-pill.selected {
      border-color: #32EDFF;
      background: rgba(50,237,255,0.15);
      color: #32EDFF;
    }

    /* Buttons */
    .btn-primary {
      display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
      padding: 0.75rem 2rem;
      border-radius: 9999px;
      background: linear-gradient(135deg, #32EDFF 0%, #00b8d4 100%);
      color: #0B1B3C;
      font-weight: 700;
      font-size: 0.9375rem;
      border: none;
      cursor: pointer;
      transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
      box-shadow: 0 4px 20px rgba(50,237,255,0.3);
    }
    .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
    .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

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

    .btn-sm {
      padding: 0.35rem 0.75rem;
      border-radius: 0.5rem;
      font-size: 0.75rem;
      font-weight: 600;
      cursor: pointer;
      border: 1px solid transparent;
      transition: all 0.2s;
    }
    .btn-danger { background: rgba(255,80,80,0.12); border-color: rgba(255,80,80,0.25); color: #ff9898; }
    .btn-danger:hover { background: rgba(255,80,80,0.22); }

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
    .data-table tbody tr:hover td { background: rgba(50,237,255,0.03); }

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

    /* Modal */
    .modal-overlay {
      position: fixed; inset: 0;
      background: rgba(6,15,34,0.85);
      backdrop-filter: blur(8px);
      z-index: 200;
      display: flex; align-items: center; justify-content: center;
      padding: 1rem;
      opacity: 0; pointer-events: none;
      transition: opacity 0.2s;
    }
    .modal-overlay.open { opacity: 1; pointer-events: all; }
    .modal-box {
      background: rgba(11,27,60,0.95);
      border: 1px solid rgba(50,237,255,0.2);
      border-radius: 1.25rem;
      width: 100%; max-width: 560px;
      max-height: 85vh;
      overflow-y: auto;
      padding: 1.75rem;
      transform: translateY(16px);
      transition: transform 0.25s cubic-bezier(.34,1.56,.64,1);
    }
    .modal-overlay.open .modal-box { transform: translateY(0); }

    /* Toast */
    .toast {
      position: fixed; bottom: 1.5rem; left: 50%; transform: translateX(-50%) translateY(60px);
      background: rgba(11,27,60,0.95);
      border: 1px solid rgba(50,237,255,0.25);
      border-radius: 9999px;
      padding: 0.75rem 1.5rem;
      font-size: 0.875rem;
      font-weight: 600;
      color: #fff;
      z-index: 999;
      transition: transform 0.35s cubic-bezier(.34,1.56,.64,1), opacity 0.3s;
      opacity: 0;
      white-space: nowrap;
    }
    .toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
    .toast.success { border-color: rgba(52,211,153,0.4); color: #34d399; }
    .toast.error   { border-color: rgba(248,113,113,0.4); color: #f87171; }

    /* Orb */
    .orb { position: fixed; border-radius: 50%; filter: blur(100px); pointer-events: none; z-index: 0; }

    /* Scrollable table wrapper */
    .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .table-scroll::-webkit-scrollbar { height: 4px; }
    .table-scroll::-webkit-scrollbar-track { background: transparent; }
    .table-scroll::-webkit-scrollbar-thumb { background: rgba(50,237,255,0.2); border-radius: 2px; }

    /* Highlight balance row */
    .balance-highlight {
      background: rgba(50,237,255,0.08);
      border: 1px solid rgba(50,237,255,0.2);
      border-radius: 0.75rem;
      padding: 0.875rem 1.25rem;
    }
  </style>
</head>
<body>
  <div class="orb" style="width:500px;height:500px;background:rgba(50,237,255,0.04);top:-100px;right:-150px;"></div>
  <div class="orb" style="width:400px;height:400px;background:rgba(50,237,255,0.03);bottom:-80px;left:-100px;"></div>

  <!-- Header -->
  <header class="glass-header sticky top-0 z-50 px-4 sm:px-6">
    <div class="max-w-5xl mx-auto flex items-center gap-3 h-16">
      <a href="/orion/home" class="flex items-center justify-center w-9 h-9 rounded-xl flex-shrink-0"
         style="background:rgba(50,237,255,0.08);border:1px solid rgba(50,237,255,0.18);"
         aria-label="Back to home">
        <iconify-icon icon="solar:arrow-left-linear" style="color:#32EDFF;font-size:1.25rem;"></iconify-icon>
      </a>
      <div class="flex items-center gap-2.5 flex-1 min-w-0">
        <div class="flex items-center justify-center w-9 h-9 rounded-xl flex-shrink-0"
             style="background:rgba(50,237,255,0.1);border:1px solid rgba(50,237,255,0.2);">
          <iconify-icon icon="solar:hand-money-linear" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
        </div>
        <div>
          <h1 class="text-white font-bold text-lg leading-tight">Credit Sales</h1>
          <p class="text-xs leading-tight" style="color:rgba(255,255,255,0.4);">Manage credit transactions</p>
        </div>
      </div>
      <span class="hidden sm:block text-sm font-medium" style="color:rgba(255,255,255,0.4);"><?php echo htmlspecialchars($user_name); ?></span>
    </div>
  </header>

  <main class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 py-8">

    <!-- Tab Toggle -->
    <div class="flex gap-2 mb-7">
      <button class="tab-btn active" id="tabNewBtn" onclick="switchTab('new')">
        <iconify-icon icon="solar:add-circle-linear" style="vertical-align:-3px;margin-right:4px;"></iconify-icon>
        New Credit Sale
      </button>
      <button class="tab-btn" id="tabListBtn" onclick="switchTab('list')">
        <iconify-icon icon="solar:list-linear" style="vertical-align:-3px;margin-right:4px;"></iconify-icon>
        View Credit Sales
      </button>
    </div>

    <!-- ══════════════════════════════════════
         SECTION A — New Credit Sale Form
    ══════════════════════════════════════ -->
    <div id="sectionNew">
      <form id="creditSaleForm" novalidate>

        <!-- Items Card -->
        <div class="glass-card p-5 mb-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-white text-base flex items-center gap-2">
              <iconify-icon icon="solar:cart-large-linear" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
              Sale Items
            </h2>
            <button type="button" class="btn-ghost" id="addItemBtn" onclick="addItemRow()">
              <iconify-icon icon="solar:add-circle-linear" style="font-size:1rem;"></iconify-icon>
              Add Item
            </button>
          </div>

          <div class="table-scroll">
            <table class="items-table" id="itemsTable">
              <thead>
                <tr>
                  <th style="min-width:160px;">Item</th>
                  <th style="min-width:110px;">Category</th>
                  <th style="min-width:100px;">Price (₦)</th>
                  <th style="min-width:110px;">Qty</th>
                  <th style="min-width:100px;">Discount (₦)</th>
                  <th style="min-width:100px;">Total (₦)</th>
                  <th style="width:40px;"></th>
                </tr>
              </thead>
              <tbody id="itemsBody">
                <!-- rows injected by JS -->
              </tbody>
            </table>
          </div>

          <!-- Grand Total -->
          <div class="mt-4 flex justify-end">
            <div class="balance-highlight flex items-center gap-4">
              <span class="text-sm font-semibold" style="color:rgba(255,255,255,0.6);">Grand Total</span>
              <span class="text-xl font-black" style="color:#32EDFF;">₦<span id="grandTotal">0.00</span></span>
            </div>
          </div>
        </div>

        <!-- Customer Info Card -->
        <div class="glass-card p-5 mb-5">
          <h2 class="font-bold text-white text-base flex items-center gap-2 mb-4">
            <iconify-icon icon="solar:user-linear" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
            Customer Information
          </h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="customerName">Customer Name <span style="color:#f87171;">*</span></label>
              <input type="text" id="customerName" name="customer_name" class="orion-input"
                     placeholder="Full name" required autocomplete="name">
            </div>
            <div>
              <label for="customerWhatsapp">WhatsApp Number <span style="color:#f87171;">*</span></label>
              <input type="tel" id="customerWhatsapp" name="customer_whatsapp" class="orion-input"
                     placeholder="e.g. 08012345678" required autocomplete="tel">
            </div>
          </div>
        </div>

        <!-- Payment Card -->
        <div class="glass-card p-5 mb-5">
          <h2 class="font-bold text-white text-base flex items-center gap-2 mb-4">
            <iconify-icon icon="solar:wallet-linear" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
            Payment Details
          </h2>

          <!-- Payment method -->
          <div class="mb-4">
            <label>Payment Method</label>
            <div class="flex flex-wrap gap-2 mt-1">
              <label class="radio-pill">
                <input type="radio" name="payment_method" value="transfer_card" checked class="sr-only" onchange="handlePaymentMethod(this)">
                <iconify-icon icon="solar:card-linear" style="font-size:1rem;"></iconify-icon>
                Transfer / Card
              </label>
              <label class="radio-pill">
                <input type="radio" name="payment_method" value="cash" class="sr-only" onchange="handlePaymentMethod(this)">
                <iconify-icon icon="solar:banknote-linear" style="font-size:1rem;"></iconify-icon>
                Cash
              </label>
              <label class="radio-pill">
                <input type="radio" name="payment_method" value="both" class="sr-only" onchange="handlePaymentMethod(this)">
                <iconify-icon icon="solar:hand-money-linear" style="font-size:1rem;"></iconify-icon>
                Both
              </label>
            </div>
          </div>

          <!-- Split amounts (shown when "both" selected) -->
          <div id="splitAmounts" class="hidden grid grid-cols-2 gap-4 mb-4">
            <div>
              <label for="transferAmount">Transfer / Card Amount (₦)</label>
              <input type="text" id="transferAmount" name="transfer_amount" class="orion-input"
                     placeholder="0.00" inputmode="decimal" oninput="formatMoneyInput(this); calcBalance();">
            </div>
            <div>
              <label for="cashAmount">Cash Amount (₦)</label>
              <input type="text" id="cashAmount" name="cash_amount" class="orion-input"
                     placeholder="0.00" inputmode="decimal" oninput="formatMoneyInput(this); calcBalance();">
            </div>
          </div>

          <!-- Amount paid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="amountPaid">Initial Amount Paid (₦)</label>
              <input type="text" id="amountPaid" name="amount_paid" class="orion-input"
                     placeholder="0.00" inputmode="decimal" oninput="formatMoneyInput(this); calcBalance();">
            </div>
            <div>
              <label>Balance Due (₦)</label>
              <div class="balance-highlight flex items-center">
                <span class="text-lg font-black" style="color:#32EDFF;">₦<span id="balanceDisplay">0.00</span></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Confirmation + Submit -->
        <div class="glass-card p-5 mb-6">
          <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" id="confirmCheck" class="mt-0.5 w-4 h-4 rounded accent-cyan-400" required>
            <span class="text-sm" style="color:rgba(255,255,255,0.7);">
              I confirm that the items and customer details above are correct.
            </span>
          </label>
        </div>

        <div class="flex justify-end">
          <button type="submit" class="btn-primary" id="submitCreditBtn" disabled>
            <iconify-icon icon="solar:check-circle-linear" style="font-size:1.1rem;"></iconify-icon>
            <span id="submitLabel">Save Credit Sale</span>
          </button>
        </div>
      </form>
    </div><!-- /sectionNew -->

    <!-- ══════════════════════════════════════
         SECTION B — Credit Sales List
    ══════════════════════════════════════ -->
    <div id="sectionList" class="hidden">

      <!-- Search bar -->
      <div class="glass-card p-4 mb-5 flex flex-col sm:flex-row gap-3">
        <div class="flex-1">
          <input type="search" id="creditSearch" class="orion-input" placeholder="Search by name or WhatsApp…"
                 oninput="filterCreditTable()">
        </div>
        <div>
          <select id="statusFilter" class="orion-select" onchange="filterCreditTable()" style="width:auto;min-width:140px;">
            <option value="">All Statuses</option>
            <option value="paid">Paid</option>
            <option value="partial">Partial</option>
            <option value="unpaid">Unpaid</option>
          </select>
        </div>
        <button class="btn-ghost flex-shrink-0" onclick="loadCreditSales()">
          <iconify-icon icon="solar:refresh-linear" style="font-size:1rem;"></iconify-icon>
          Refresh
        </button>
      </div>

      <div class="glass-card overflow-hidden">
        <div class="p-4 border-b" style="border-color:rgba(50,237,255,0.1);">
          <h2 class="font-bold text-white text-base flex items-center gap-2">
            <iconify-icon icon="solar:list-linear" style="color:#32EDFF;font-size:1.2rem;"></iconify-icon>
            Credit Sales
            <span id="creditCount" class="ml-2 text-xs px-2 py-0.5 rounded-full"
                  style="background:rgba(50,237,255,0.12);color:#32EDFF;font-weight:700;">0</span>
          </h2>
        </div>

        <!-- Loading state -->
        <div id="creditLoading" class="flex items-center justify-center gap-3 py-12 text-sm" style="color:rgba(255,255,255,0.4);">
          <iconify-icon icon="solar:refresh-linear" style="font-size:1.25rem;" class="animate-spin"></iconify-icon>
          Loading credit sales…
        </div>

        <!-- Empty state -->
        <div id="creditEmpty" class="hidden flex flex-col items-center gap-3 py-14 text-center px-4">
          <iconify-icon icon="solar:hand-money-linear" style="color:rgba(50,237,255,0.25);font-size:3rem;"></iconify-icon>
          <p class="font-semibold" style="color:rgba(255,255,255,0.4);">No credit sales found</p>
        </div>

        <div class="table-scroll">
          <table class="data-table" id="creditTable">
            <thead>
              <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>WhatsApp</th>
                <th>Total (₦)</th>
                <th>Paid (₦)</th>
                <th>Balance (₦)</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="creditTableBody">
            </tbody>
          </table>
        </div>
      </div>
    </div><!-- /sectionList -->

  </main>

  <!-- ══ Modal: Payment Update ══ -->
  <div class="modal-overlay" id="paymentModal" role="dialog" aria-modal="true" aria-label="Update Payment">
    <div class="modal-box">
      <div class="flex items-center justify-between mb-5">
        <h3 class="font-bold text-white text-lg flex items-center gap-2">
          <iconify-icon icon="solar:wallet-money-linear" style="color:#32EDFF;font-size:1.3rem;"></iconify-icon>
          Update Payment
        </h3>
        <button onclick="closeModal('paymentModal')" class="p-1.5 rounded-lg" style="color:rgba(255,255,255,0.5);">
          <iconify-icon icon="solar:close-circle-linear" style="font-size:1.4rem;"></iconify-icon>
        </button>
      </div>
      <div class="mb-3 p-3 rounded-xl" style="background:rgba(50,237,255,0.06);border:1px solid rgba(50,237,255,0.12);">
        <p class="text-sm" style="color:rgba(255,255,255,0.6);">Customer: <span id="pmCustomerName" class="text-white font-semibold"></span></p>
        <p class="text-sm mt-1" style="color:rgba(255,255,255,0.6);">Outstanding Balance: <span id="pmBalance" class="font-bold" style="color:#32EDFF;"></span></p>
      </div>
      <div class="mb-4">
        <label for="pmAmount">Payment Amount (₦) <span style="color:#f87171;">*</span></label>
        <input type="text" id="pmAmount" class="orion-input" placeholder="0.00"
               inputmode="decimal" oninput="formatMoneyInput(this)">
      </div>
      <div class="mb-5">
        <label for="pmMethod">Payment Method</label>
        <select id="pmMethod" class="orion-select">
          <option value="transfer_card">Transfer / Card</option>
          <option value="cash">Cash</option>
        </select>
      </div>
      <button class="btn-primary w-full" id="pmSubmitBtn" onclick="submitPaymentUpdate()">
        <iconify-icon icon="solar:check-circle-linear" style="font-size:1.1rem;"></iconify-icon>
        Confirm Payment
      </button>
    </div>
  </div>

  <!-- ══ Modal: View Items ══ -->
  <div class="modal-overlay" id="itemsModal" role="dialog" aria-modal="true" aria-label="View Items">
    <div class="modal-box">
      <div class="flex items-center justify-between mb-5">
        <h3 class="font-bold text-white text-lg flex items-center gap-2">
          <iconify-icon icon="solar:cart-large-linear" style="color:#32EDFF;font-size:1.3rem;"></iconify-icon>
          Sale Items
        </h3>
        <button onclick="closeModal('itemsModal')" class="p-1.5 rounded-lg" style="color:rgba(255,255,255,0.5);">
          <iconify-icon icon="solar:close-circle-linear" style="font-size:1.4rem;"></iconify-icon>
        </button>
      </div>
      <div id="itemsModalContent"></div>
    </div>
  </div>

  <!-- ══ Modal: View History ══ -->
  <div class="modal-overlay" id="historyModal" role="dialog" aria-modal="true" aria-label="Customer History">
    <div class="modal-box" style="max-width:680px;">
      <div class="flex items-center justify-between mb-5">
        <h3 class="font-bold text-white text-lg flex items-center gap-2">
          <iconify-icon icon="solar:history-linear" style="color:#32EDFF;font-size:1.3rem;"></iconify-icon>
          Credit History — <span id="historyCustomerName" class="ml-1" style="color:#32EDFF;"></span>
        </h3>
        <button onclick="closeModal('historyModal')" class="p-1.5 rounded-lg" style="color:rgba(255,255,255,0.5);">
          <iconify-icon icon="solar:close-circle-linear" style="font-size:1.4rem;"></iconify-icon>
        </button>
      </div>
      <div id="historyModalContent">
        <div class="flex items-center justify-center gap-2 py-8 text-sm" style="color:rgba(255,255,255,0.4);">
          <iconify-icon icon="solar:refresh-linear" class="animate-spin" style="font-size:1.2rem;"></iconify-icon>
          Loading history…
        </div>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div class="toast" id="toast" role="alert" aria-live="assertive"></div>

  <script>
  window.orionConfig = {
    baseUrl: '/wp-json/orion/v1',
    currentUser: <?php echo json_encode(Orion_Auth::get_current_user()); ?>,
  };
  </script>
  <script src="/wp-content/plugins/orion-inventory/assets/js/app.js"></script>
  <script>
  /* ─────────────────────────────────────────
     Utilities
  ───────────────────────────────────────── */
  const BASE = window.orionConfig.baseUrl;
  let allCreditSales = [];
  let currentPaymentId = null;
  let submitting = false;
  let products = [];

  function parseNum(val) {
    if (typeof val === 'string') val = val.replace(/,/g, '');
    const n = parseFloat(val);
    return isNaN(n) ? 0 : n;
  }

  function formatNum(n) {
    return Number(n).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function formatMoneyInput(el) {
    const raw = el.value.replace(/[^0-9.]/g, '');
    const parts = raw.split('.');
    let integer = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    el.value = parts.length > 1 ? integer + '.' + parts[1].slice(0, 2) : integer;
  }

  function showToast(msg, type = '') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast' + (type ? ' ' + type : '');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
  }

  function openModal(id) { document.getElementById(id).classList.add('open'); }
  function closeModal(id) { document.getElementById(id).classList.remove('open'); }

  // Close modals on overlay click
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => { if (e.target === overlay) overlay.classList.remove('open'); });
  });

  /* ─────────────────────────────────────────
     Tab switching
  ───────────────────────────────────────── */
  function switchTab(tab) {
    const isNew = tab === 'new';
    document.getElementById('sectionNew').classList.toggle('hidden', !isNew);
    document.getElementById('sectionList').classList.toggle('hidden', isNew);
    document.getElementById('tabNewBtn').classList.toggle('active', isNew);
    document.getElementById('tabListBtn').classList.toggle('active', !isNew);
    if (!isNew) loadCreditSales();
  }

  /* ─────────────────────────────────────────
     Load products for item dropdown
  ───────────────────────────────────────── */
  async function loadProducts() {
    try {
      const r = await fetch(`${BASE}/products`);
      const d = await r.json();
      products = Array.isArray(d) ? d : (d.data || d.products || []);
    } catch (_) { products = []; }
  }

  function buildProductOptions(selectedId) {
    let opts = '<option value="">— Select item —</option>';
    products.forEach(p => {
      const sel = p.id == selectedId ? 'selected' : '';
      opts += `<option value="${p.id}" data-price="${parseFloat(p.price)||0}" data-category="${escHtml(p.category||'')}" ${sel}>${escHtml(p.name)}</option>`;
    });
    return opts;
  }

  function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  /* ─────────────────────────────────────────
     Items table
  ───────────────────────────────────────── */
  let rowCount = 0;

  function addItemRow() {
    const id = rowCount++;
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.id = `row-${id}`;
    tr.innerHTML = `
      <td>
        <select class="orion-select item-select" data-row="${id}" onchange="onItemSelect(this)" style="min-width:150px;padding:0.5rem 0.625rem;">
          ${buildProductOptions('')}
        </select>
      </td>
      <td>
        <span class="text-sm row-category" data-row="${id}" style="color:rgba(255,255,255,0.5);">—</span>
      </td>
      <td>
        <input type="text" class="orion-input row-price" data-row="${id}" value="0"
               placeholder="0.00" inputmode="decimal" style="min-width:90px;"
               oninput="formatMoneyInput(this);calcRowTotal(${id});">
      </td>
      <td>
        <div class="flex items-center gap-1">
          <button type="button" class="qty-btn" onclick="changeQty(${id},-1)">−</button>
          <input type="number" class="qty-input row-qty" data-row="${id}" value="1" min="1"
                 oninput="calcRowTotal(${id})">
          <button type="button" class="qty-btn" onclick="changeQty(${id},1)">+</button>
        </div>
      </td>
      <td>
        <input type="text" class="orion-input row-discount" data-row="${id}" value="0"
               placeholder="0.00" inputmode="decimal" style="min-width:90px;"
               oninput="formatMoneyInput(this);calcRowTotal(${id});">
      </td>
      <td>
        <span class="row-total font-bold text-sm" data-row="${id}" style="color:#32EDFF;">₦0.00</span>
      </td>
      <td>
        <button type="button" class="btn-sm btn-danger" onclick="removeRow(${id})" aria-label="Remove row">
          <iconify-icon icon="solar:trash-bin-minimalistic-linear" style="font-size:0.9rem;"></iconify-icon>
        </button>
      </td>`;
    tbody.appendChild(tr);
    calcGrandTotal();
  }

  function removeRow(id) {
    const row = document.getElementById(`row-${id}`);
    if (row) row.remove();
    calcGrandTotal();
  }

  function onItemSelect(sel) {
    const id = sel.dataset.row;
    const opt = sel.options[sel.selectedIndex];
    const price = parseFloat(opt.dataset.price) || 0;
    const cat = opt.dataset.category || '—';
    document.querySelector(`.row-price[data-row="${id}"]`).value = price ? formatNum(price) : '0';
    document.querySelector(`.row-category[data-row="${id}"]`).textContent = cat;
    calcRowTotal(id);
  }

  function changeQty(id, delta) {
    const input = document.querySelector(`.row-qty[data-row="${id}"]`);
    const newVal = Math.max(1, parseInt(input.value || 1) + delta);
    input.value = newVal;
    calcRowTotal(id);
  }

  function calcRowTotal(id) {
    const price    = parseNum(document.querySelector(`.row-price[data-row="${id}"]`).value);
    const qty      = parseInt(document.querySelector(`.row-qty[data-row="${id}"]`).value) || 1;
    const discount = parseNum(document.querySelector(`.row-discount[data-row="${id}"]`).value);
    const total    = Math.max(0, price * qty - discount);
    document.querySelector(`.row-total[data-row="${id}"]`).textContent = '₦' + formatNum(total);
    calcGrandTotal();
  }

  function calcGrandTotal() {
    let sum = 0;
    document.querySelectorAll('.row-total').forEach(el => {
      sum += parseNum(el.textContent.replace('₦', ''));
    });
    document.getElementById('grandTotal').textContent = formatNum(sum);
    calcBalance();
  }

  /* ─────────────────────────────────────────
     Balance calculation
  ───────────────────────────────────────── */
  function calcBalance() {
    const total   = parseNum(document.getElementById('grandTotal').textContent);
    const paid    = parseNum(document.getElementById('amountPaid').value);
    const balance = Math.max(0, total - paid);
    document.getElementById('balanceDisplay').textContent = formatNum(balance);
  }

  /* ─────────────────────────────────────────
     Payment method handler
  ───────────────────────────────────────── */
  function handlePaymentMethod(radio) {
    document.querySelectorAll('.radio-pill').forEach(p => p.classList.remove('selected'));
    radio.closest('.radio-pill').classList.add('selected');
    const splitDiv = document.getElementById('splitAmounts');
    if (radio.value === 'both') {
      splitDiv.classList.remove('hidden');
      splitDiv.classList.add('grid');
    } else {
      splitDiv.classList.add('hidden');
      splitDiv.classList.remove('grid');
    }
  }

  /* ─────────────────────────────────────────
     Confirmation checkbox → enable submit
  ───────────────────────────────────────── */
  document.getElementById('confirmCheck').addEventListener('change', function() {
    document.getElementById('submitCreditBtn').disabled = !this.checked;
  });

  /* ─────────────────────────────────────────
     Form submit
  ───────────────────────────────────────── */
  document.getElementById('creditSaleForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (submitting) return;

    const name = document.getElementById('customerName').value.trim();
    const wa   = document.getElementById('customerWhatsapp').value.trim();
    if (!name || !wa) { showToast('Customer name and WhatsApp are required.', 'error'); return; }

    // Collect rows
    const rows = [];
    document.querySelectorAll('#itemsBody tr').forEach(tr => {
      const rowId = tr.id.replace('row-', '');
      const itemSel = tr.querySelector('.item-select');
      if (!itemSel || !itemSel.value) return;
      rows.push({
        product_id: itemSel.value,
        product_name: itemSel.options[itemSel.selectedIndex].text,
        price: parseNum(tr.querySelector('.row-price').value),
        qty: parseInt(tr.querySelector('.row-qty').value) || 1,
        discount: parseNum(tr.querySelector('.row-discount').value),
        total: parseNum(tr.querySelector('.row-total').textContent.replace('₦', '')),
      });
    });

    if (rows.length === 0) { showToast('Please add at least one item.', 'error'); return; }

    const total      = parseNum(document.getElementById('grandTotal').textContent);
    const amountPaid = parseNum(document.getElementById('amountPaid').value);
    const balance    = Math.max(0, total - amountPaid);
    const method     = document.querySelector('input[name="payment_method"]:checked').value;

    const payload = {
      customer_name: name,
      customer_whatsapp: wa,
      items: rows,
      total_amount: total,
      amount_paid: amountPaid,
      balance: balance,
      payment_method: method,
    };

    if (method === 'both') {
      payload.transfer_amount = parseNum(document.getElementById('transferAmount').value);
      payload.cash_amount     = parseNum(document.getElementById('cashAmount').value);
    }

    submitting = true;
    const btn   = document.getElementById('submitCreditBtn');
    const label = document.getElementById('submitLabel');
    btn.disabled = true;
    label.textContent = 'Saving…';

    try {
      const r = await fetch(`${BASE}/credit-sales`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const d = await r.json();
      if (!r.ok) throw new Error(d.message || 'Failed to save.');

      showToast('Credit sale saved!', 'success');
      this.reset();
      document.getElementById('itemsBody').innerHTML = '';
      document.getElementById('grandTotal').textContent = '0.00';
      document.getElementById('balanceDisplay').textContent = '0.00';
      document.getElementById('confirmCheck').checked = false;
      rowCount = 0;
      addItemRow();
    } catch (err) {
      showToast(err.message, 'error');
    } finally {
      submitting = false;
      btn.disabled = false;
      label.textContent = 'Save Credit Sale';
    }
  });

  /* ─────────────────────────────────────────
     Load credit sales list
  ───────────────────────────────────────── */
  async function loadCreditSales() {
    const loading = document.getElementById('creditLoading');
    const empty   = document.getElementById('creditEmpty');
    const tbody   = document.getElementById('creditTableBody');

    loading.classList.remove('hidden');
    empty.classList.add('hidden');
    tbody.innerHTML = '';

    try {
      const r = await fetch(`${BASE}/credit-sales`);
      const d = await r.json();
      allCreditSales = Array.isArray(d) ? d : (d.data || d.sales || []);
      renderCreditTable(allCreditSales);
    } catch (_) {
      showToast('Could not load credit sales.', 'error');
    } finally {
      loading.classList.add('hidden');
    }
  }

  function renderCreditTable(sales) {
    const tbody = document.getElementById('creditTableBody');
    const empty = document.getElementById('creditEmpty');
    document.getElementById('creditCount').textContent = sales.length;
    tbody.innerHTML = '';

    if (!sales.length) {
      empty.classList.remove('hidden');
      return;
    }
    empty.classList.add('hidden');

    sales.forEach(s => {
      const balance = parseFloat(s.balance) || 0;
      const paid    = parseFloat(s.amount_paid) || 0;
      const total   = parseFloat(s.total_amount) || 0;

      let statusBadge;
      if (balance <= 0) {
        statusBadge = '<span class="badge badge-green"><iconify-icon icon="solar:check-circle-linear"></iconify-icon>Paid</span>';
      } else if (paid > 0) {
        statusBadge = '<span class="badge badge-yellow"><iconify-icon icon="solar:clock-circle-linear"></iconify-icon>Partial</span>';
      } else {
        statusBadge = '<span class="badge badge-red"><iconify-icon icon="solar:close-circle-linear"></iconify-icon>Unpaid</span>';
      }

      const date = s.created_at ? new Date(s.created_at).toLocaleDateString('en-NG') : '—';
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td style="color:rgba(255,255,255,0.6);font-size:0.8rem;">${escHtml(date)}</td>
        <td class="font-semibold">${escHtml(s.customer_name || '—')}</td>
        <td style="color:rgba(255,255,255,0.6);">${escHtml(s.customer_whatsapp || '—')}</td>
        <td class="font-bold" style="color:#32EDFF;">₦${formatNum(total)}</td>
        <td style="color:rgba(52,211,153,0.9);">₦${formatNum(paid)}</td>
        <td style="color:${balance > 0 ? '#f87171' : '#34d399'};">₦${formatNum(balance)}</td>
        <td>${statusBadge}</td>
        <td>
          <div class="flex items-center gap-1.5 flex-wrap">
            <button class="btn-ghost btn-sm" style="padding:.3rem .65rem;font-size:.72rem;"
                    onclick="viewItems(${s.id})">
              <iconify-icon icon="solar:eye-linear" style="font-size:.85rem;"></iconify-icon>Items
            </button>
            ${balance > 0 ? `<button class="btn-ghost btn-sm" style="padding:.3rem .65rem;font-size:.72rem;"
                    onclick="openPaymentModal(${s.id}, '${escHtml(s.customer_name)}', ${balance})">
              <iconify-icon icon="solar:wallet-money-linear" style="font-size:.85rem;"></iconify-icon>Pay
            </button>` : ''}
            <button class="btn-ghost btn-sm" style="padding:.3rem .65rem;font-size:.72rem;"
                    onclick="viewHistory('${escHtml(s.customer_whatsapp)}', '${escHtml(s.customer_name)}')">
              <iconify-icon icon="solar:history-linear" style="font-size:.85rem;"></iconify-icon>History
            </button>
          </div>
        </td>`;
      tbody.appendChild(tr);
    });
  }

  function filterCreditTable() {
    const q      = document.getElementById('creditSearch').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;

    const filtered = allCreditSales.filter(s => {
      const matchQ = !q || (s.customer_name || '').toLowerCase().includes(q)
                        || (s.customer_whatsapp || '').toLowerCase().includes(q);
      const bal   = parseFloat(s.balance) || 0;
      const paid  = parseFloat(s.amount_paid) || 0;
      let sStatus = bal <= 0 ? 'paid' : (paid > 0 ? 'partial' : 'unpaid');
      const matchS = !status || sStatus === status;
      return matchQ && matchS;
    });
    renderCreditTable(filtered);
  }

  /* ─────────────────────────────────────────
     View Items modal
  ───────────────────────────────────────── */
  async function viewItems(saleId) {
    document.getElementById('itemsModalContent').innerHTML =
      '<div class="flex items-center justify-center gap-2 py-8 text-sm" style="color:rgba(255,255,255,0.4);"><iconify-icon icon="solar:refresh-linear" class="animate-spin" style="font-size:1.2rem;"></iconify-icon>Loading…</div>';
    openModal('itemsModal');
    try {
      const r = await fetch(`${BASE}/credit-sales/${saleId}`);
      const d = await r.json();
      const items = d.items || [];
      if (!items.length) {
        document.getElementById('itemsModalContent').innerHTML = '<p class="text-center py-6" style="color:rgba(255,255,255,0.4);">No items found.</p>';
        return;
      }
      let html = '<div class="table-scroll"><table class="items-table"><thead><tr>'
               + '<th>Item</th><th>Price</th><th>Qty</th><th>Discount</th><th>Total</th>'
               + '</tr></thead><tbody>';
      items.forEach(it => {
        html += `<tr>
          <td class="font-medium">${escHtml(it.product_name || it.name || '—')}</td>
          <td>₦${formatNum(it.price)}</td>
          <td>${it.qty}</td>
          <td>₦${formatNum(it.discount || 0)}</td>
          <td class="font-bold" style="color:#32EDFF;">₦${formatNum(it.total)}</td>
        </tr>`;
      });
      html += '</tbody></table></div>';
      document.getElementById('itemsModalContent').innerHTML = html;
    } catch (_) {
      document.getElementById('itemsModalContent').innerHTML = '<p class="text-center py-6" style="color:#f87171;">Failed to load items.</p>';
    }
  }

  /* ─────────────────────────────────────────
     Update Payment modal
  ───────────────────────────────────────── */
  function openPaymentModal(saleId, name, balance) {
    currentPaymentId = saleId;
    document.getElementById('pmCustomerName').textContent = name;
    document.getElementById('pmBalance').textContent = '₦' + formatNum(balance);
    document.getElementById('pmAmount').value = '';
    openModal('paymentModal');
  }

  async function submitPaymentUpdate() {
    const amountEl = document.getElementById('pmAmount');
    const amount   = parseNum(amountEl.value);
    if (!amount || amount <= 0) { showToast('Enter a valid payment amount.', 'error'); return; }

    const method = document.getElementById('pmMethod').value;
    const btn    = document.getElementById('pmSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<iconify-icon icon="solar:refresh-linear" class="animate-spin"></iconify-icon> Saving…';

    try {
      const r = await fetch(`${BASE}/credit-sales/${currentPaymentId}/payment`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ amount_paid: amount, payment_method: method }),
      });
      const d = await r.json();
      if (!r.ok) throw new Error(d.message || 'Failed.');
      showToast('Payment updated!', 'success');
      closeModal('paymentModal');
      loadCreditSales();
    } catch (err) {
      showToast(err.message, 'error');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<iconify-icon icon="solar:check-circle-linear" style="font-size:1.1rem;"></iconify-icon> Confirm Payment';
    }
  }

  /* ─────────────────────────────────────────
     View History modal
  ───────────────────────────────────────── */
  async function viewHistory(whatsapp, name) {
    document.getElementById('historyCustomerName').textContent = name;
    document.getElementById('historyModalContent').innerHTML =
      '<div class="flex items-center justify-center gap-2 py-8 text-sm" style="color:rgba(255,255,255,0.4);"><iconify-icon icon="solar:refresh-linear" class="animate-spin" style="font-size:1.2rem;"></iconify-icon>Loading history…</div>';
    openModal('historyModal');
    try {
      const r = await fetch(`${BASE}/credit-history?whatsapp=${encodeURIComponent(whatsapp)}`);
      const d = await r.json();
      const transactions = Array.isArray(d) ? d : (d.data || d.transactions || []);
      renderHistoryModal(transactions, name, whatsapp);
    } catch (_) {
      document.getElementById('historyModalContent').innerHTML = '<p class="text-center py-6" style="color:#f87171;">Failed to load history.</p>';
    }
  }

  function renderHistoryModal(transactions, name, whatsapp) {
    if (!transactions.length) {
      document.getElementById('historyModalContent').innerHTML = '<p class="text-center py-6" style="color:rgba(255,255,255,0.4);">No transactions found.</p>';
      return;
    }
    let totalOwed = 0; let totalPaid = 0;
    transactions.forEach(t => { totalOwed += parseFloat(t.total_amount)||0; totalPaid += parseFloat(t.amount_paid)||0; });
    const totalBalance = Math.max(0, totalOwed - totalPaid);

    let html = `<div class="grid grid-cols-3 gap-3 mb-4">
      <div class="text-center p-3 rounded-xl" style="background:rgba(50,237,255,0.06);border:1px solid rgba(50,237,255,0.12);">
        <p class="text-xs mb-1" style="color:rgba(255,255,255,0.5);">Total Owed</p>
        <p class="font-bold text-sm" style="color:#32EDFF;">₦${formatNum(totalOwed)}</p>
      </div>
      <div class="text-center p-3 rounded-xl" style="background:rgba(52,211,153,0.06);border:1px solid rgba(52,211,153,0.12);">
        <p class="text-xs mb-1" style="color:rgba(255,255,255,0.5);">Total Paid</p>
        <p class="font-bold text-sm" style="color:#34d399;">₦${formatNum(totalPaid)}</p>
      </div>
      <div class="text-center p-3 rounded-xl" style="background:rgba(248,113,113,0.06);border:1px solid rgba(248,113,113,0.12);">
        <p class="text-xs mb-1" style="color:rgba(255,255,255,0.5);">Balance</p>
        <p class="font-bold text-sm" style="color:#f87171;">₦${formatNum(totalBalance)}</p>
      </div>
    </div>
    <div class="table-scroll"><table class="data-table"><thead><tr>
      <th>Date</th><th>Total</th><th>Paid</th><th>Balance</th>
    </tr></thead><tbody>`;

    let running = 0;
    transactions.forEach(t => {
      const bal = parseFloat(t.balance) || 0;
      running += bal;
      const date = t.created_at ? new Date(t.created_at).toLocaleDateString('en-NG') : '—';
      html += `<tr>
        <td style="color:rgba(255,255,255,0.6);font-size:.8rem;">${escHtml(date)}</td>
        <td class="font-bold" style="color:#32EDFF;">₦${formatNum(t.total_amount)}</td>
        <td style="color:#34d399;">₦${formatNum(t.amount_paid)}</td>
        <td style="color:${bal>0?'#f87171':'#34d399'};">₦${formatNum(bal)}</td>
      </tr>`;
    });
    html += '</tbody></table></div>';
    html += `<div class="mt-4 flex justify-end">
      <a href="/orion/credit-history?whatsapp=${encodeURIComponent(whatsapp)}&name=${encodeURIComponent(name)}"
         class="btn-ghost" style="text-decoration:none;">
        <iconify-icon icon="solar:arrow-right-linear" style="font-size:1rem;"></iconify-icon>
        Full History Page
      </a>
    </div>`;
    document.getElementById('historyModalContent').innerHTML = html;
  }

  /* ─────────────────────────────────────────
     Init
  ───────────────────────────────────────── */
  loadProducts().then(() => addItemRow());
  </script>
</body>
</html>
