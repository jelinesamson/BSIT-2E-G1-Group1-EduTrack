let products = [];
let cart     = [];
let rowId    = 1;

document.addEventListener('DOMContentLoaded', () => {
  lucide.createIcons();
  loadProducts();
  startClock();
});

function startClock() {
  const el = document.getElementById('liveClock');
  const tick = () => {
    const n = new Date();
    el.textContent = n.toLocaleDateString('en-PH', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' })
      + '  ' + n.toLocaleTimeString('en-PH');
  };
  tick();
  setInterval(tick, 1000);
}

async function loadProducts() {
  try {
    const res = await fetch('sales_management.php?api=products');
    products  = await res.json();
    const sel = document.getElementById('selProduct');
    products.forEach(p => {
      const opt         = document.createElement('option');
      opt.value         = p.id;
      opt.textContent   = p.name;
      opt.dataset.price = p.price;
      opt.dataset.stock = p.stock;
      sel.appendChild(opt);
    });
  } catch (e) {
    showToast('error', 'Failed to load products.');
  }
}

function onProductChange() {
  const sel = document.getElementById('selProduct');
  const opt = sel.options[sel.selectedIndex];
  if (!opt.value) {
    document.getElementById('inpPrice').value  = '';
    document.getElementById('stockInfo').textContent = '';
    return;
  }
  document.getElementById('inpPrice').value = parseFloat(opt.dataset.price).toFixed(2);
  const stock = parseInt(opt.dataset.stock);
  const info  = document.getElementById('stockInfo');
  info.textContent  = 'Available stock: ' + stock + ' unit(s)';
  info.style.color  = stock < 5 ? '#dc3545' : '#64748b';
}

function addToCart() {
  const sel   = document.getElementById('selProduct');
  const opt   = sel.options[sel.selectedIndex];
  const pid   = parseInt(opt.value || 0);
  const qty   = parseInt(document.getElementById('inpQty').value || 0);
  const price = parseFloat(document.getElementById('inpPrice').value || 0);

  if (!pid || qty < 1 || !price) {
    showToast('error', 'Please select a product and enter a valid quantity.');
    return;
  }

  const product  = products.find(p => p.id === pid);
  const existing = cart.find(i => i.pid === pid);

  if (existing) {
    const newQty = existing.qty + qty;
    if (newQty > product.stock) { showToast('error', 'Only ' + product.stock + ' unit(s) available.'); return; }
    existing.qty   = newQty;
    existing.total = existing.qty * existing.price;
  } else {
    if (qty > product.stock) { showToast('error', 'Only ' + product.stock + ' unit(s) in stock.'); return; }
    cart.push({ _id: rowId++, pid, name: product.name, qty, price, total: qty * price, stock: product.stock });
  }

  renderCart();
  sel.value = '';
  document.getElementById('inpPrice').value  = '';
  document.getElementById('inpQty').value    = 1;
  document.getElementById('stockInfo').textContent = '';
  showToast('success', product.name + ' added to cart.');
}

function changeQty(id, delta) {
  const item = cart.find(i => i._id === id);
  if (!item) return;
  const newQty = item.qty + delta;
  if (newQty < 1) { removeItem(id); return; }
  if (newQty > item.stock) { showToast('error', 'Max stock: ' + item.stock); return; }
  item.qty   = newQty;
  item.total = item.qty * item.price;
  renderCart();
}

function removeItem(id) {
  cart = cart.filter(i => i._id !== id);
  renderCart();
}

function clearCart() {
  if (!cart.length) return;
  cart = [];
  renderCart();
  showToast('success', 'Cart cleared.');
}

function renderCart() {
  const tbody = document.getElementById('cartBody');
  tbody.innerHTML = '';

  if (!cart.length) {
    tbody.innerHTML = '<tr><td colspan="6" class="empty-cell">Cart is empty. Add items above.</td></tr>';
    updateSummary();
    return;
  }

  cart.forEach((item, idx) => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="tbl-id">${String(idx + 1).padStart(3, '0')}</td>
      <td style="font-weight:600">${item.name}</td>
      <td>
        <div class="qty-ctrl">
          <button class="qty-btn" onclick="changeQty(${item._id}, -1)">−</button>
          <span style="min-width:22px;text-align:center;font-weight:600">${item.qty}</span>
          <button class="qty-btn" onclick="changeQty(${item._id}, 1)">+</button>
        </div>
      </td>
      <td>₱${item.price.toFixed(2)}</td>
      <td style="font-weight:700;color:var(--blue)">₱${item.total.toFixed(2)}</td>
      <td><button class="sm-btn danger sm" onclick="removeItem(${item._id})">✕</button></td>`;
    tbody.appendChild(tr);
  });

  updateSummary();
}

function updateSummary() {
  const total = cart.reduce((s, i) => s + i.total, 0);
  const units = cart.reduce((s, i) => s + i.qty,   0);
  document.getElementById('summaryTotal').textContent = '₱' + total.toFixed(2);
  document.getElementById('summaryItems').textContent = cart.length;
  document.getElementById('summaryUnits').textContent = units;
  document.getElementById('cartCount').textContent    = cart.length;
  computeChange();
}

function computeChange() {
  const total = cart.reduce((s, i) => s + i.total, 0);
  const cash  = parseFloat(document.getElementById('inpCash').value) || 0;
  const box   = document.getElementById('changeBox');
  if (cash > 0 && cash >= total) {
    document.getElementById('summaryChange').textContent = '₱' + (cash - total).toFixed(2);
    box.classList.add('show');
  } else {
    box.classList.remove('show');
  }
}

async function checkout() {
  if (!cart.length) { showToast('error', 'Cart is empty.'); return; }

  const cash  = parseFloat(document.getElementById('inpCash').value) || 0;
  const total = cart.reduce((s, i) => s + i.total, 0);

  if (cash <= 0)    { showToast('error', 'Please enter the cash amount.'); return; }
  if (cash < total) { showToast('error', 'Insufficient amount. Need ₱' + total.toFixed(2)); return; }

  const payload = {
    cart: cart.map(i => ({ product_id: i.pid, name: i.name, qty: i.qty, price: i.price })),
    paid: cash,
  };

  try {
    const res  = await fetch('sales_management.php?api=checkout', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const data = await res.json();

    if (data.error) { showToast('error', data.error); return; }

    showReceipt(data.receipt);
    cart = [];
    renderCart();
    document.getElementById('inpCash').value = '';
    document.getElementById('changeBox').classList.remove('show');

    const updated = await fetch('sales_management.php?api=products');
    products = await updated.json();

  } catch (e) {
    showToast('error', 'Checkout failed. Please try again.');
  }
}

function showReceipt(receipt) {
  document.getElementById('receiptTxnId').textContent  = receipt.id;
  document.getElementById('receiptDate').textContent   = new Date(receipt.date).toLocaleString('en-PH');
  document.getElementById('receiptTotal').textContent  = '₱' + parseFloat(receipt.total).toFixed(2);
  document.getElementById('receiptPaid').textContent   = '₱' + parseFloat(receipt.paid).toFixed(2);
  document.getElementById('receiptChange').textContent = '₱' + parseFloat(receipt.change).toFixed(2);

  const container = document.getElementById('receiptItems');
  container.innerHTML = '';
  receipt.items.forEach(item => {
    const div = document.createElement('div');
    div.className = 'r-item';
    div.innerHTML = `
      <span>${item.name}</span>
      <span>${item.qty}</span>
      <span>₱${parseFloat(item.price).toFixed(2)}</span>
      <span>₱${(item.qty * item.price).toFixed(2)}</span>`;
    container.appendChild(div);
  });

  document.getElementById('receiptModal').classList.add('open');
  lucide.createIcons();
}

function closeModal() {
  document.getElementById('receiptModal').classList.remove('open');
}

// ── Toast ──────────────────────────────────────────────────────────────────
function showToast(type, msg) {
  const container = document.getElementById('toastContainer');
  const div = document.createElement('div');
  div.className = 'toast ' + type;
  div.textContent = (type === 'success' ? '✓  ' : '✕  ') + msg;
  container.appendChild(div);
  setTimeout(() => div.remove(), 3500);
}