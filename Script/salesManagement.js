let currentProducts = [];
let cart = [];
let currentReceiptData = null;

$(document).ready(function () {
  loadProducts();
});

function loadProducts() {
  $.ajax({
    url: '../Api/salesManagement.php?api=products',
    type: 'GET',
    success: function (response) {
      currentProducts = response;
      renderProducts(currentProducts);
    },
    error: function () { console.error("Failed to fetch products."); }
  });
}

function renderProducts(products) {
  const grid = $('#productGrid');
  grid.empty();
  if (products.length === 0) { grid.html('<p>No products available.</p>'); return; }

  products.forEach(p => {
    let card = `
            <div class="product-card" onclick="addToCart(${p.id}, '${p.name}', ${p.price}, ${p.stock})">
                <h4>${p.name}</h4>
                <div class="price">₱${parseFloat(p.price).toFixed(2)}</div>
                <div class="stock">Stock: ${p.stock}</div>
            </div>`;
    grid.append(card);
  });
}

function filterProducts() {
  let term = $('#productSearch').val().toLowerCase();
  let filtered = currentProducts.filter(p => p.name.toLowerCase().includes(term));
  renderProducts(filtered);
}

function addToCart(id, name, price, maxStock) {
  let existingItem = cart.find(item => item.product_id === id);
  if (existingItem) {
    if (existingItem.qty < maxStock) existingItem.qty++;
    else alert(`Only ${maxStock} in stock.`);
  } else {
    cart.push({ product_id: id, name: name, price: price, qty: 1, maxStock: maxStock });
  }
  renderCart();
}

function updateQty(id, newQty) {
  let item = cart.find(i => i.product_id === id);
  if (item) {
    let qty = parseInt(newQty);
    if (isNaN(qty) || qty <= 0) { removeFromCart(id); return; }
    if (qty > item.maxStock) {
      alert(`Max available is ${item.maxStock}.`);
      item.qty = item.maxStock;
    } else { item.qty = qty; }
    renderCart();
  }
}

function removeFromCart(id) {
  cart = cart.filter(item => item.product_id !== id);
  renderCart();
}

function renderCart() {
  const tbody = $('#cartBody');
  tbody.empty();
  let total = 0;

  cart.forEach(item => {
    let subtotal = item.price * item.qty;
    total += subtotal;
    let row = `
            <tr>
                <td>${item.name}<br><small>₱${parseFloat(item.price).toFixed(2)}</small></td>
                <td><input type="number" class="cart-qty-input" value="${item.qty}" onchange="updateQty(${item.product_id}, this.value)"></td>
                <td>₱${subtotal.toFixed(2)}</td>
                <td><button class="remove-btn" onclick="removeFromCart(${item.product_id})">X</button></td>
            </tr>`;
    tbody.append(row);
  });

  $('#cartTotal').text('₱' + total.toFixed(2));
  $('#cartTotal').data('raw-total', total);
}

function processCheckout() {
  if (cart.length === 0) { alert("Cart is empty."); return; }

  let rawTotal = parseFloat($('#cartTotal').data('raw-total'));
  let amountPaid = parseFloat($('#amountPaid').val());

  if (isNaN(amountPaid) || amountPaid < rawTotal) {
    alert(`Insufficient payment! Due: ₱${rawTotal.toFixed(2)}`);
    return;
  }

  $.ajax({
    url: '../Api/salesManagement.php?api=checkout',
    type: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({ cart: cart, paid: amountPaid }),
    success: function (response) {
      if (response.success) {
        showReceipt(response.receipt);
        cart = [];
        $('#amountPaid').val('');
        renderCart();
        loadProducts();
      } else { alert("Checkout Failed: " + response.error); }
    },
    error: function () { alert("Server error during checkout."); }
  });
}

// receipt and export logic
function showReceipt(receipt) {
  currentReceiptData = receipt;
  $('#rTxnId').text(receipt.id);
  $('#rDate').text(receipt.date);

  let itemsHtml = '';
  receipt.items.forEach(item => {
    let subtotal = item.price * item.qty;
    itemsHtml += `<tr><td>${item.name}<br><small>${item.qty} x ₱${parseFloat(item.price).toFixed(2)}</small></td><td style="text-align: right;">₱${subtotal.toFixed(2)}</td></tr>`;
  });

  $('#rItems').html(itemsHtml);
  $('#rTotal').text(parseFloat(receipt.total).toFixed(2));
  $('#rPaid').text(parseFloat(receipt.paid).toFixed(2));
  $('#rChange').text(parseFloat(receipt.change).toFixed(2));

  $('#receiptModal').css('display', 'block');
}

function closeReceipt() { $('#receiptModal').css('display', 'none'); }
function printReceipt() { window.print(); }
function exportReceiptCSV() {
  if (!currentReceiptData) return;
  let csv = "Transaction ID,Date,Item Name,Price,Qty,Subtotal\n";
  currentReceiptData.items.forEach(item => {
    let subtotal = item.price * item.qty;
    csv += `${currentReceiptData.id},${currentReceiptData.date},"${item.name}",${item.price},${item.qty},${subtotal}\n`;
  });
  csv += `\n,,,Total,,${currentReceiptData.total}\n,,,Cash,,${currentReceiptData.paid}\n,,,Change,,${currentReceiptData.change}\n`;

  let blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  let link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = `Receipt_${currentReceiptData.id}.csv`;
  link.style.display = 'none';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}