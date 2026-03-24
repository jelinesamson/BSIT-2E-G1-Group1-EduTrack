<!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Product Management</title>

            <link rel="stylesheet" href="../Css/systemNav.css" />
            <link rel="stylesheet" href="../Css/productManagement.css">

            <link
                href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap"
                rel="stylesheet"
                />

            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
            <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
        </head>

        <body>
            <div class="systemNav-container">

             <?php include("systemNav.php"); ?>

             <header class="topbar">
          <button class="hamburger" id="menuBtn">
            <i data-lucide="menu"></i>
          </button>

        </header>

            <div class="container">
            <h2>Product Management</h2>

            <div class="buttons">
                <button onclick="openModal()" class="add-btn">Add Product</button>
            </div>

            <div id="productModal" class="modal">
                <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>

                <h3>Product Form</h3>

                <input type="text" id="code" placeholder="Product Code">

                <select id="type" onchange="checkType(this)">
                    <option value="">Select Type</option>
                    <option>ID Lace</option>
                    <option>Book</option>
                    <option>Uniform</option>
                    <option>Merchandise</option>
                    <option value="Other">Other</option>
                </select>

                <input type="text" id="otherType" placeholder="Enter Type">

                <select id="size">
                    <option value="">Size</option>
                    <option>Small</option>
                    <option>Medium</option>
                    <option>Large</option>
                </select>

                <select id="dept">
                    <option value="">Department</option>
                    <option>CICT</option>
                    <option>CBEA</option>
                    <option>CAFA</option>
                    <option>CAL</option>
                    <option>COE</option>
                    <option>COED</option>
                    <option>CS</option>
                    <option>CIT</option>
                </select>

                <input type="text" id="price" placeholder="Price">

                <div class="buttons">
                    <button onclick="saveProduct()" class="add-btn">Save</button>
                </div>
                </div>
            </div>

            <table id="myTable">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Product Code</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Dept</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

        <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

         <script src="https://unpkg.com/lucide@latest"></script>

        <script src="../Script/systemNav.js"></script>
        <script src="../Script/productManagement.js"></script>

        </body>
    </html>
fonts.googleapis.com