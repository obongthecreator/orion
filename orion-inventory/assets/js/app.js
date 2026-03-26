/**
 * Orion Brothers Inventory System — App JavaScript
 * Vanilla JS (no jQuery). Requires ES2017+ (async/await).
 */

/* ==========================================================================
   1. Namespace & Core State
   ========================================================================== */
const OrionApp = {
  baseUrl:     window.orionConfig?.baseUrl || '/wp-json/orion/v1',
  token:       localStorage.getItem('orion_token'),
  currentUser: null,
  isSubmitting: false,
};

/* ==========================================================================
   2. API Helper
   ========================================================================== */
OrionApp.api = {
  /**
   * Central fetch wrapper. Attaches Authorization header, parses JSON,
   * and redirects to login on 401.
   *
   * @param {string} method   - HTTP verb
   * @param {string} endpoint - Path relative to baseUrl (e.g. '/sales')
   * @param {object|null} data - Request body (JSON-serialised automatically)
   * @returns {Promise<any>}  Parsed JSON response
   */
  async request(method, endpoint, data = null) {
    const url = `${OrionApp.baseUrl}${endpoint}`;

    /** @type {RequestInit} */
    const options = {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Accept':        'application/json',
      },
    };

    if (OrionApp.token) {
      options.headers['Authorization'] = `Bearer ${OrionApp.token}`;
    }

    if (data !== null) {
      options.body = JSON.stringify(data);
    }

    const response = await fetch(url, options);

    // Redirect to login when session expires
    if (response.status === 401) {
      localStorage.removeItem('orion_token');
      window.location.href = '/orion/login';
      return null;
    }

    // For 204 No Content there is nothing to parse
    if (response.status === 204) return null;

    const json = await response.json();

    if (!response.ok) {
      const err = new Error(json?.message || `Request failed: ${response.status}`);
      err.status = response.status;
      err.data   = json;
      throw err;
    }

    return json;
  },

  get:    (endpoint)       => OrionApp.api.request('GET',    endpoint),
  post:   (endpoint, data) => OrionApp.api.request('POST',   endpoint, data),
  put:    (endpoint, data) => OrionApp.api.request('PUT',    endpoint, data),
  delete: (endpoint)       => OrionApp.api.request('DELETE', endpoint),
};

/* ==========================================================================
   3. Letter-by-Letter Text Reveal
   ========================================================================== */
/**
 * Finds every element with [data-letter-animate], splits its text content
 * into individual <span class="letter"> elements, and assigns a
 * --letter-index CSS custom property so the CSS stagger delay works.
 */
OrionApp.initLetterAnimation = function () {
  const targets = document.querySelectorAll('[data-letter-animate]');

  targets.forEach((el) => {
    const text = el.textContent;
    el.textContent = '';
    el.classList.add('letter-reveal');

    [...text].forEach((char, i) => {
      const span = document.createElement('span');
      span.className = 'letter';
      span.style.setProperty('--letter-index', i);
      span.textContent = char;
      el.appendChild(span);
    });
  });
};

/* ==========================================================================
   4. Page Transitions
   ========================================================================== */
/**
 * Plays a CSS exit animation on the current page then navigates to `url`.
 * On the destination page the page-enter animation is applied in
 * DOMContentLoaded (see bottom of file).
 *
 * @param {string} url
 */
OrionApp.navigateTo = function (url) {
  document.body.classList.add('page-exit', 'page-transitioning');

  setTimeout(() => {
    window.location.href = url;
  }, 100);
};

// Intercept all internal /orion/ anchor clicks
document.addEventListener('click', (e) => {
  const link = e.target.closest('a[href^="/orion/"]');
  if (!link) return;

  // Allow modifier keys to open in new tab/window as normal
  if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;

  e.preventDefault();
  OrionApp.navigateTo(link.href);
});

/* ==========================================================================
   5. Real-time Clock
   ========================================================================== */
/**
 * Populates #orion-clock with "Monday, 15 Jan 2024 | 14:30:25"
 * and #orion-date with just the date portion.
 * Updates every second.
 */
OrionApp.initClock = function () {
  const clockEl = document.getElementById('orion-clock');
  const dateEl  = document.getElementById('orion-date');
  if (!clockEl && !dateEl) return;

  const DAY_NAMES  = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
  const MON_NAMES  = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

  const pad = (n) => String(n).padStart(2, '0');

  function tick() {
    const now  = new Date();
    const day  = DAY_NAMES[now.getDay()];
    const date = now.getDate();
    const mon  = MON_NAMES[now.getMonth()];
    const year = now.getFullYear();
    const time = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
    const dateStr = `${day}, ${pad(date)} ${mon} ${year}`;

    if (clockEl) clockEl.textContent = `${dateStr} | ${time}`;
    if (dateEl)  dateEl.textContent  = dateStr;
  }

  tick();
  setInterval(tick, 1000);
};

/* ==========================================================================
   6. Loading State Helpers
   ========================================================================== */
/**
 * Replaces button text with animated loading dots and disables the button.
 * Stores original label in a data attribute for restore.
 *
 * @param {HTMLButtonElement} btn
 */
OrionApp.showLoading = function (btn) {
  if (!btn) return;
  btn.dataset.originalText = btn.innerHTML;
  btn.innerHTML = '<span class="loading-dots"><span></span><span></span><span></span></span>';
  btn.disabled  = true;
};

/**
 * Restores a button to its pre-loading state.
 *
 * @param {HTMLButtonElement} btn
 */
OrionApp.hideLoading = function (btn) {
  if (!btn) return;
  if (btn.dataset.originalText) {
    btn.innerHTML = btn.dataset.originalText;
    delete btn.dataset.originalText;
  }
  btn.disabled = false;
};

/* ==========================================================================
   7. Toast Notifications
   ========================================================================== */
/**
 * Displays a glassmorphism toast notification.
 *
 * @param {string} message
 * @param {'success'|'error'|'warning'|'info'} [type='success']
 * @param {number} [duration=3000] - Auto-dismiss delay in ms
 */
OrionApp.toast = function (message, type = 'success', duration = 3000) {
  let container = document.getElementById('orion-toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'orion-toast-container';
    document.body.appendChild(container);
  }

  const icons = { success: '✓', error: '✕', warning: '⚠', info: 'ℹ' };

  const toast = document.createElement('div');
  toast.className  = `orion-toast toast-${type}`;
  toast.innerHTML  = `<span>${icons[type] || icons.info}</span><span>${message}</span>`;
  container.appendChild(toast);

  // Auto-dismiss
  setTimeout(() => {
    toast.classList.add('is-leaving');
    toast.addEventListener('animationend', () => toast.remove(), { once: true });
  }, duration);
};

/* ==========================================================================
   8. Anti-duplicate Submission Guard
   ========================================================================== */
/**
 * Calls `submitFn` exactly once per 3-second window to prevent double-taps.
 *
 * @param {HTMLFormElement} formEl   - The form being submitted
 * @param {Function}        submitFn - The actual submit handler
 * @returns {boolean} Whether the submission was allowed
 */
OrionApp.submitOnce = function (formEl, submitFn) {
  if (OrionApp.isSubmitting) return false;
  OrionApp.isSubmitting = true;
  setTimeout(() => { OrionApp.isSubmitting = false; }, 3000);
  submitFn();
  return true;
};

/* ==========================================================================
   9. Form Validation
   ========================================================================== */
/**
 * Validates all required fields in a form.
 * Adds .is-invalid + a .field-error message on failure, clears them on pass.
 *
 * @param {HTMLFormElement} formEl
 * @returns {boolean} true when all required fields are filled
 */
OrionApp.validate = function (formEl) {
  if (!formEl) return false;

  // Clear previous errors
  formEl.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
  formEl.querySelectorAll('.field-error').forEach((el) => el.remove());

  let isValid = true;

  formEl.querySelectorAll('[required]').forEach((field) => {
    const value = field.value?.trim();
    if (!value) {
      isValid = false;
      field.classList.add('is-invalid');

      const msg = document.createElement('p');
      msg.className   = 'field-error';
      msg.textContent = field.dataset.errorMsg || 'This field is required.';
      field.insertAdjacentElement('afterend', msg);
    }
  });

  return isValid;
};

/* ==========================================================================
   10. Sales Form
   ========================================================================== */
OrionApp.SalesForm = {
  /** Reference to the items <tbody> */
  tbody: null,

  init() {
    const form = document.getElementById('orion-sales-form');
    if (!form) return;

    this.tbody = form.querySelector('#sales-items-tbody');

    // Add first row if empty
    if (this.tbody && this.tbody.rows.length === 0) {
      this.addItemRow();
    }

    // Add-row button
    form.querySelector('#btn-add-item')?.addEventListener('click', () => {
      this.addItemRow();
    });

    // Payment method toggle
    form.querySelectorAll('[name="payment_method"]').forEach((radio) => {
      radio.addEventListener('change', () => this.handlePaymentMethod());
    });
    this.handlePaymentMethod();

    // Submit
    form.addEventListener('submit', (e) => this.submitSale(e));
  },

  /** Appends a new item row to the sales table. */
  addItemRow() {
    if (!this.tbody) return;

    const rowIndex = this.tbody.rows.length;
    const tr = document.createElement('tr');
    tr.dataset.row = rowIndex;
    tr.innerHTML = `
      <td>
        <select class="orion-select item-product" name="items[${rowIndex}][product_id]" required>
          <option value="">— Select product —</option>
        </select>
      </td>
      <td><input class="orion-input item-qty"   type="number" name="items[${rowIndex}][qty]"   value="1" min="1" step="1"></td>
      <td><input class="orion-input item-price" type="number" name="items[${rowIndex}][price]" value="0" min="0" step="0.01" readonly></td>
      <td><input class="orion-input item-total" type="number" name="items[${rowIndex}][total]" value="0" readonly tabindex="-1"></td>
      <td>
        <button type="button" class="orion-btn-danger orion-btn-sm orion-btn-icon btn-remove-row" title="Remove">✕</button>
      </td>`;

    this.tbody.appendChild(tr);

    // Load product options into this row's select
    this._populateProductSelect(tr.querySelector('.item-product'));

    // Event wiring
    tr.querySelector('.item-product').addEventListener('change', (e) => {
      this.onProductSelect(e.target);
    });

    ['change', 'input'].forEach((evt) => {
      tr.querySelector('.item-qty').addEventListener(evt, () => this.updateItemRow(tr));
    });

    tr.querySelector('.btn-remove-row').addEventListener('click', (e) => {
      this.removeItemRow(e.currentTarget);
    });
  },

  /** Removes an item row and recalculates totals. */
  removeItemRow(btn) {
    const row = btn.closest('tr');
    if (!row) return;
    // Keep at least one row
    if (this.tbody.rows.length <= 1) {
      OrionApp.toast('At least one item is required.', 'warning');
      return;
    }
    row.remove();
    this.calculateGrandTotal();
  },

  /**
   * Recalculates the row total (qty × price) and triggers grand-total update.
   * @param {HTMLTableRowElement} row
   */
  updateItemRow(row) {
    const qty   = parseFloat(row.querySelector('.item-qty')?.value)   || 0;
    const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
    const total = qty * price;
    const totalField = row.querySelector('.item-total');
    if (totalField) totalField.value = total.toFixed(2);
    this.calculateGrandTotal();
  },

  /** Sums all row totals and updates the grand-total display. */
  calculateGrandTotal() {
    let grand = 0;
    this.tbody?.querySelectorAll('.item-total').forEach((el) => {
      grand += parseFloat(el.value) || 0;
    });

    const display = document.getElementById('sales-grand-total');
    if (display) display.textContent = OrionApp._formatCurrency(grand);

    const hidden = document.getElementById('sales-grand-total-input');
    if (hidden) hidden.value = grand.toFixed(2);
  },

  /**
   * Called when the product select changes; fetches product details from API
   * and auto-fills the price field.
   *
   * @param {HTMLSelectElement} select
   */
  async onProductSelect(select) {
    const productId = select.value;
    if (!productId) return;

    const row = select.closest('tr');
    try {
      const product = await OrionApp.api.get(`/products/${productId}`);
      if (!product) return;

      const priceField = row.querySelector('.item-price');
      if (priceField) {
        priceField.value = product.price ?? 0;
      }

      this.updateItemRow(row);
    } catch (err) {
      console.error('Failed to fetch product:', err);
    }
  },

  /**
   * Populates a product <select> with all available products.
   * @param {HTMLSelectElement} selectEl
   */
  async _populateProductSelect(selectEl) {
    try {
      const products = await OrionApp.api.get('/products');
      if (!Array.isArray(products)) return;

      products.forEach((p) => {
        const opt = document.createElement('option');
        opt.value       = p.id;
        opt.textContent = p.name;
        opt.dataset.price = p.price ?? 0;
        selectEl.appendChild(opt);
      });
    } catch (err) {
      console.error('Could not load products:', err);
    }
  },

  /**
   * Shows/hides payment-method-specific fields and computes change/balance.
   */
  handlePaymentMethod() {
    const selected = document.querySelector('[name="payment_method"]:checked')?.value;

    const cashFields     = document.getElementById('cash-fields');
    const transferFields = document.getElementById('transfer-fields');

    if (cashFields)     cashFields.style.display     = selected === 'cash'     ? '' : 'none';
    if (transferFields) transferFields.style.display = selected === 'transfer' ? '' : 'none';

    // Recalculate change if cash
    if (selected === 'cash') {
      const amountPaid = parseFloat(document.getElementById('amount-paid')?.value) || 0;
      const grand      = parseFloat(document.getElementById('sales-grand-total-input')?.value) || 0;
      const change     = amountPaid - grand;
      const changeEl   = document.getElementById('change-display');
      if (changeEl) changeEl.textContent = OrionApp._formatCurrency(Math.max(change, 0));
    }
  },

  /**
   * Collects form data and POSTs to /wp-json/orion/v1/sales.
   * @param {SubmitEvent} e
   */
  async submitSale(e) {
    e.preventDefault();
    const form = e.currentTarget;

    if (!OrionApp.validate(form)) {
      OrionApp.toast('Please fill in all required fields.', 'error');
      return;
    }

    const submitBtn = form.querySelector('[type="submit"]');

    OrionApp.submitOnce(form, async () => {
      OrionApp.showLoading(submitBtn);

      try {
        const payload = Object.fromEntries(new FormData(form));

        // Collect item rows into an array
        payload.items = [];
        this.tbody?.querySelectorAll('tr').forEach((row) => {
          payload.items.push({
            product_id: row.querySelector('.item-product')?.value,
            qty:        row.querySelector('.item-qty')?.value,
            price:      row.querySelector('.item-price')?.value,
            total:      row.querySelector('.item-total')?.value,
          });
        });

        const result = await OrionApp.api.post('/sales', payload);

        OrionApp.toast('Sale recorded successfully!', 'success');

        // Navigate to receipt if returned
        if (result?.receipt_url) {
          OrionApp.navigateTo(result.receipt_url);
        } else {
          form.reset();
          this.tbody.innerHTML = '';
          this.addItemRow();
        }
      } catch (err) {
        OrionApp.toast(err.message || 'Failed to record sale.', 'error');
      } finally {
        OrionApp.hideLoading(submitBtn);
        OrionApp.isSubmitting = false;
      }
    });
  },
};

/* ==========================================================================
   11. Financial Summary Calculations
   ========================================================================== */
OrionApp.FinancialForm = {
  init() {
    const form = document.getElementById('orion-financial-form');
    if (!form) return;

    // Recalculate whenever any numeric input changes
    const fields = [
      'total_sales', 'transfer_card_sales', 'debtors_cash',
      'expense', 'old_cash',
    ];
    fields.forEach((name) => {
      form.querySelector(`[name="${name}"]`)?.addEventListener('input', () => {
        this.calculate(form);
      });
    });

    this.calculate(form);
  },

  /**
   * cash_left = total_sales - transfer_card_sales + debtors_cash - expense + old_cash
   * @param {HTMLFormElement} form
   */
  calculate(form) {
    const val = (name) => parseFloat(form.querySelector(`[name="${name}"]`)?.value) || 0;

    const cashLeft =
      val('total_sales')
      - val('transfer_card_sales')
      + val('debtors_cash')
      - val('expense')
      + val('old_cash');

    const cashLeftField = form.querySelector('[name="cash_left"]');
    if (cashLeftField) {
      cashLeftField.value = cashLeft.toFixed(2);
    }

    const display = document.getElementById('cash-left-display');
    if (display) display.textContent = OrionApp._formatCurrency(cashLeft);
  },
};

/* ==========================================================================
   12. Stock Calculations
   ========================================================================== */
OrionApp.StockForm = {
  init() {
    const form = document.getElementById('orion-stock-form');
    if (!form) return;

    // Listen on all quantity inputs inside stock rows
    form.addEventListener('input', (e) => {
      const row = e.target.closest('[data-stock-row]');
      if (row) this.calculateRow(row);
    });
  },

  /**
   * closing_stock = opening_stock + import_qty - sold_stock
   * @param {HTMLElement} row
   */
  calculateRow(row) {
    const val = (cls) => parseFloat(row.querySelector(cls)?.value) || 0;

    const closing = val('.stock-opening') + val('.stock-import') - val('.stock-sold');

    const closingField = row.querySelector('.stock-closing');
    if (closingField) closingField.value = closing >= 0 ? closing : 0;
  },
};

/* ==========================================================================
   13. Import Form — Real-time Auto-save
   ========================================================================== */
OrionApp.ImportForm = {
  init() {
    const form = document.getElementById('orion-import-form');
    if (!form) return;

    form.addEventListener('change', (e) => {
      const row = e.target.closest('[data-import-row]');
      if (row) this.saveImportRow(row);
    });

    // Also save on blur for text inputs
    form.addEventListener('focusout', (e) => {
      if (e.target.matches('input,select')) {
        const row = e.target.closest('[data-import-row]');
        if (row) this.saveImportRow(row);
      }
    });
  },

  /**
   * Auto-saves a single import row to the API.
   * Shows a temporary "Saved ✓" indicator on the row.
   *
   * @param {HTMLElement} row
   */
  async saveImportRow(row) {
    const rowId = row.dataset.importRow;

    // Gather row data
    const data = {};
    row.querySelectorAll('[name]').forEach((field) => {
      data[field.name] = field.value;
    });

    // Optimistic saved indicator
    let indicator = row.querySelector('.save-indicator');
    if (!indicator) {
      indicator = document.createElement('span');
      indicator.className = 'save-indicator text-muted label-text';
      indicator.style.marginLeft = '8px';
      row.querySelector('td:last-child')?.appendChild(indicator);
    }
    indicator.textContent = '⏳ Saving…';

    try {
      if (rowId && rowId !== 'new') {
        await OrionApp.api.put(`/imports/${rowId}`, data);
      } else {
        const result = await OrionApp.api.post('/imports', data);
        if (result?.id) row.dataset.importRow = result.id;
      }
      indicator.textContent = '✓ Saved';
      setTimeout(() => { indicator.textContent = ''; }, 2000);
    } catch (err) {
      indicator.textContent = '✗ Error';
      indicator.style.color  = 'var(--status-danger)';
      console.error('Import save failed:', err);
    }
  },
};

/* ==========================================================================
   14. Profile Picture Preview
   ========================================================================== */
/**
 * Reads a selected image file and updates a preview <img> element.
 */
OrionApp.initProfilePicture = function () {
  const input = document.getElementById('profile_pic_input');
  if (!input) return;

  input.addEventListener('change', (e) => {
    const file = e.target.files?.[0];
    if (!file) return;

    const preview = document.getElementById('profile_pic_preview');
    if (!preview) return;

    const reader = new FileReader();
    reader.onload = (readerEvent) => {
      preview.src = readerEvent.target.result;
    };
    reader.readAsDataURL(file);
  });
};

/* ==========================================================================
   15. Bluetooth / ESC-POS Printer
   ========================================================================== */
OrionApp.Printer = {
  /** @type {BluetoothDevice|null} */
  device: null,

  /** @type {BluetoothRemoteGATTCharacteristic|null} */
  characteristic: null,

  // Standard ESC/POS GATT UUIDs used by many thermal printers
  SERVICE_UUID:        '000018f0-0000-1000-8000-00805f9b34fb',
  CHARACTERISTIC_UUID: '00002af1-0000-1000-8000-00805f9b34fb',

  /**
   * Prompts the user to pair a Bluetooth printer.
   * Stores device and characteristic references for subsequent prints.
   */
  async connect() {
    if (!navigator.bluetooth) {
      OrionApp.toast('Web Bluetooth is not supported in this browser.', 'warning');
      return false;
    }

    try {
      this.device = await navigator.bluetooth.requestDevice({
        filters:          [{ services: [this.SERVICE_UUID] }],
        optionalServices: [this.SERVICE_UUID],
      });

      const server         = await this.device.gatt.connect();
      const service        = await server.getPrimaryService(this.SERVICE_UUID);
      this.characteristic  = await service.getCharacteristic(this.CHARACTERISTIC_UUID);

      OrionApp.toast('Printer connected!', 'success');
      return true;
    } catch (err) {
      OrionApp.toast('Could not connect to printer.', 'error');
      console.error('Bluetooth connect error:', err);
      return false;
    }
  },

  /**
   * Sends a formatted receipt to the Bluetooth printer.
   * Falls back to window.print() when BT is unavailable.
   *
   * @param {object} receiptData - Structured receipt information
   */
  async printReceipt(receiptData) {
    const text = this.formatReceiptText(receiptData);

    if (this.characteristic) {
      try {
        const encoder = new TextEncoder();
        const ESC     = 0x1b;
        const GS      = 0x1d;

        // ESC/POS init + align-center + bold on
        const initBytes   = new Uint8Array([ESC, 0x40]);                // ESC @  (init)
        const alignCenter = new Uint8Array([ESC, 0x61, 0x01]);          // ESC a 1
        const boldOn      = new Uint8Array([ESC, 0x45, 0x01]);          // ESC E 1
        const boldOff     = new Uint8Array([ESC, 0x45, 0x00]);          // ESC E 0
        const alignLeft   = new Uint8Array([ESC, 0x61, 0x00]);          // ESC a 0
        const cutPaper    = new Uint8Array([GS,  0x56, 0x00]);          // GS V 0 (full cut)
        const feedLines   = new Uint8Array([ESC, 0x64, 0x04]);          // ESC d 4

        const body = encoder.encode(text);

        // Send in chunks (BLE MTU is typically 20 bytes)
        const CHUNK = 20;
        const fullPayload = new Uint8Array([
          ...initBytes, ...alignCenter, ...boldOn,
          ...encoder.encode(receiptData.shopName + '\n'),
          ...boldOff, ...alignLeft, ...body,
          ...feedLines, ...cutPaper,
        ]);

        for (let offset = 0; offset < fullPayload.length; offset += CHUNK) {
          await this.characteristic.writeValue(fullPayload.slice(offset, offset + CHUNK));
        }

        OrionApp.toast('Receipt printed!', 'success');
        return;
      } catch (err) {
        console.error('BT print failed, falling back to window.print():', err);
      }
    }

    // Fallback: browser print dialog
    window.print();
  },

  /**
   * Formats a receipt object into plain ESC/POS-compatible text.
   *
   * @param {object} data
   * @param {string}   data.shopName
   * @param {string}   data.address
   * @param {string}   data.receiptNo
   * @param {string}   data.cashier
   * @param {Array}    data.items      - [{name, qty, price, total}]
   * @param {number}   data.grandTotal
   * @param {number}   data.amountPaid
   * @param {number}   data.change
   * @param {string}   data.paymentMethod
   * @returns {string}
   */
  formatReceiptText(data) {
    const LINE  = '-'.repeat(32);
    const DLINE = '='.repeat(32);
    const pad   = (l, r, width = 32) => l + ' '.repeat(Math.max(1, width - l.length - r.length)) + r;
    const cur   = (n) => OrionApp._formatCurrency(n);

    const now = new Date();
    const dateStr = now.toLocaleString('en-GB', {
      day: '2-digit', month: 'short', year: 'numeric',
      hour: '2-digit', minute: '2-digit', second: '2-digit',
    });

    let lines = [];
    lines.push(data.shopName || 'Orion Brothers');
    lines.push(data.address  || '');
    lines.push(LINE);
    lines.push(`Receipt : ${data.receiptNo || 'N/A'}`);
    lines.push(`Date    : ${dateStr}`);
    lines.push(`Cashier : ${data.cashier || 'N/A'}`);
    lines.push(LINE);

    (data.items || []).forEach((item) => {
      lines.push(item.name.substring(0, 22));
      lines.push(pad(`  ${item.qty} x ${cur(item.price)}`, cur(item.total)));
    });

    lines.push(DLINE);
    lines.push(pad('TOTAL', cur(data.grandTotal)));
    lines.push(pad('Payment (' + (data.paymentMethod || 'cash') + ')', cur(data.amountPaid)));
    if ((data.change || 0) > 0) {
      lines.push(pad('Change', cur(data.change)));
    }
    lines.push(DLINE);
    lines.push('    Thank you for your purchase!    ');
    lines.push('');
    lines.push('');

    return lines.join('\n');
  },
};

/* ==========================================================================
   16. Currency Formatter (shared utility)
   ========================================================================== */
/**
 * Formats a number as a locale-aware currency string.
 * Falls back to plain number if Intl is unavailable.
 *
 * @param {number} amount
 * @returns {string}
 */
OrionApp._formatCurrency = function (amount) {
  try {
    const currency = window.orionConfig?.currency || 'NGN';
    return new Intl.NumberFormat('en-NG', {
      style: 'currency',
      currency,
      minimumFractionDigits: 2,
    }).format(amount);
  } catch {
    return amount.toFixed(2);
  }
};

/* ==========================================================================
   17. Modal Helpers
   ========================================================================== */
/**
 * Opens a modal by adding .is-open.
 * @param {string} modalId
 */
OrionApp.openModal = function (modalId) {
  const modal = document.getElementById(modalId);
  if (!modal) return;
  modal.classList.add('is-open');
  document.body.style.overflow = 'hidden';
};

/**
 * Closes a modal by removing .is-open.
 * @param {string|HTMLElement} modalOrId
 */
OrionApp.closeModal = function (modalOrId) {
  const modal = typeof modalOrId === 'string'
    ? document.getElementById(modalOrId)
    : modalOrId;
  if (!modal) return;
  modal.classList.remove('is-open');
  document.body.style.overflow = '';
};

// Close modal when clicking the backdrop
document.addEventListener('click', (e) => {
  if (e.target.classList.contains('orion-modal')) {
    OrionApp.closeModal(e.target);
  }
  if (e.target.closest('.orion-modal__close')) {
    const modal = e.target.closest('.orion-modal');
    if (modal) OrionApp.closeModal(modal);
  }
});

// Close modal with Escape key
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.orion-modal.is-open').forEach(OrionApp.closeModal);
  }
});

/* ==========================================================================
   18. Initialisation on DOMContentLoaded
   ========================================================================== */
document.addEventListener('DOMContentLoaded', () => {
  // Page-enter transition
  document.body.classList.add('page-enter');
  setTimeout(() => document.body.classList.remove('page-enter'), 150);

  // Core features
  OrionApp.initLetterAnimation();
  OrionApp.initClock();
  OrionApp.initProfilePicture();

  // Page-specific modules (each guards itself with an element check)
  OrionApp.SalesForm.init();
  OrionApp.FinancialForm.init();
  OrionApp.StockForm.init();
  OrionApp.ImportForm.init();
});
