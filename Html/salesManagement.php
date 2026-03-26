<?php include("../Api/salesManagement.php"); ?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>EduTrack - Sales Management</title>

    <link rel="stylesheet" href="../Css/systemNav.css"/>
    <link rel="stylesheet" href="../Css/salesManagement.css"/>

    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap"
      rel="stylesheet"
    />

  </head>
  <body>
    <div class="systemNav-container">

    <?php include("systemNav.php"); ?>

      <main class="main">

      <header class="topbar">
          <button class="hamburger" id="menuBtn"><i data-lucide="menu"></i></button>
          <span id="liveClock" style="font-size:0.85rem;color:#64748b"></span>
        </header>
        
          <h2>Sales Management</h2>
          

        <div class="sales-grid">

          <div>

            <div class="sm-card">
              <div class="sm-card-header">
                <h3><i data-lucide="plus-circle" style="width:16px;height:16px"></i> Add Item to Cart</h3>
              </div>
              <div class="sm-card-body">
                <div class="form-row">
                  <div class="form-group">
                    <label>Product</label>
                    <select id="selProduct" onchange="onProductChange()">
                      <option value="">— Select Product —</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Unit Price (₱)</label>
                    <input type="number" id="inpPrice" placeholder="0.00" readonly />
                  </div>
                  <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" id="inpQty" value="1" min="1" />
                  </div>
                  <div class="form-group" style="align-self:flex-end">
                    <button class="sm-btn primary full" onclick="addToCart()">
                      <i data-lucide="plus" style="width:15px;height:15px"></i> ADD
                    </button>
                  </div>
                </div>
                <div id="stockInfo" class="stock-info"></div>
              </div>
            </div>

            <div class="sm-card">
              <div class="sm-card-header">
                <h3>
                  <i data-lucide="shopping-bag" style="width:16px;height:16px"></i>
                  Cart &nbsp;<span class="cart-badge" id="cartCount">0</span>
                </h3>
                <button class="sm-btn outline sm" onclick="clearCart()">
                  <i data-lucide="trash-2" style="width:13px;height:13px"></i> Clear
                </button>
              </div>
              <div class="table-wrap">
                <table class="sm-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Product</th>
                      <th>Qty</th>
                      <th>Unit Price</th>
                      <th>Subtotal</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody id="cartBody">
                    <tr>
                      <td colspan="6" class="empty-cell">Cart is empty. Add items above.</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>

          <div class="checkout-sticky">
            <div class="sm-card">
              <div class="sm-card-header">
                <h3><i data-lucide="receipt" style="width:16px;height:16px"></i> Checkout</h3>
              </div>
              <div class="sm-card-body">

                <div class="summary-row total-row">
                  <span class="s-label">TOTAL AMOUNT</span>
                  <span class="s-value total-value" id="summaryTotal">₱0.00</span>
                </div>
                <hr class="sm-divider" />

                <div class="summary-row">
                  <span class="s-label">Items</span>
                  <span class="s-value" id="summaryItems">0</span>
                </div>
                <div class="summary-row">
                  <span class="s-label">Units</span>
                  <span class="s-value" id="summaryUnits">0</span>
                </div>

                <hr class="sm-divider" />

                <div class="form-group" style="margin-bottom:1rem">
                  <label>Cash Tendered (₱)</label>
                  <div class="money-wrap">
                    <span class="money-prefix">₱</span>
                    <input type="number" id="inpCash" placeholder="0.00" min="0" step="0.01" oninput="computeChange()" />
                  </div>
                </div>

                <div class="change-box" id="changeBox">
                  <span class="s-label" style="color:#198754">CHANGE</span>
                  <span class="change-value" id="summaryChange">₱0.00</span>
                </div>

                <button class="sm-btn success full pay-btn" onclick="checkout()">
                  <i data-lucide="check-circle" style="width:17px;height:17px"></i> PAY NOW
                </button>

              </div>
            </div>
          </div>

        </div>
      </main>
    </div>

    <div class="modal-overlay" id="receiptModal">
      <div class="receipt-card">
        <div class="receipt-header">
          <h3>Payment Successful!</h3>
          <p id="receiptTxnId"></p>
        </div>
        <div class="receipt-body">
          <div class="r-row"><span style="color:#64748b">Date</span><span id="receiptDate"></span></div>
          <hr class="r-divider" />
          <div class="r-items-header">
            <span>Item</span><span>Qty</span><span>Price</span><span>Sub</span>
          </div>
          <div id="receiptItems"></div>
          <div class="r-totals">
            <div class="r-row bold"><span>TOTAL</span><span id="receiptTotal"></span></div>
            <div class="r-row"><span style="color:#64748b">Cash</span><span id="receiptPaid"></span></div>
            <div class="r-row change"><span>CHANGE</span><span id="receiptChange"></span></div>
          </div>
          <p class="r-thanks">Thank you for your purchase! — EduTrack</p>
        </div>
        <div class="receipt-footer">
          <button class="sm-btn outline full" onclick="window.print()">
            <i data-lucide="printer" style="width:15px;height:15px"></i> Print
          </button>
          <button class="sm-btn primary full" onclick="closeModal()">
            <i data-lucide="check" style="width:15px;height:15px"></i> Done
          </button>
        </div>
      </div>
    </div>

    <div id="toastContainer"></div>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script src="../Script/systemNav.js"></script>
    <script src="../Script/salesManagement.js"></script>
  </body>
</html>

