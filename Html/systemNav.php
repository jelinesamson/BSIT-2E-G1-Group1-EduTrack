        <nav class="sidebar" id="sidebar">
        <div class="profile">
            <img src="https://imgcdn.stablediffusionweb.com/2024/9/27/bc8687ab-dd73-432d-b99c-956f74fd0f9a.jpg"/>
            <h3>EduTrack</h3>
            <p>Admin</p>
        </div>

        <ul class="nav-links">
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                <a href="../Html/dashboard.php">
                    <i data-lucide="layout-dashboard"></i>
                    Dashboard
                </a>
            </li>

            <li class="<?= basename($_SERVER['PHP_SELF']) == 'productManagement.php' ? 'active' : '' ?>">
                <a href="../Html/productManagement.php">
                    <i data-lucide="package"></i>
                    Product Management
                </a>
            </li>

            <li class="<?= basename($_SERVER['PHP_SELF']) == 'inventoryManagement.php' ? 'active' : '' ?>">
                <a href="../Html/inventoryManagement.php">
                    <i data-lucide="warehouse"></i>
                    Inventory Management
                    </li>
                </a>
            <li class="<?= basename($_SERVER['PHP_SELF']) == 'salesManagement.php' ? 'active' : '' ?>">
            <i data-lucide="shopping-cart"></i>
            Sales Management
            </li>

            <li class="<?= basename($_SERVER['PHP_SELF']) == 'salesReport.php' ? 'active' : '' ?>">
            <i data-lucide="bar-chart-3"></i>
            Sales Report
            </li>
        </ul>

        <div class="logout">
            <i data-lucide="log-out"></i>
            Logout
        </div>
        </nav>