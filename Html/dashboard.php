<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>EduTrack Dashboard</title>

    <link rel="stylesheet" href="../Css/dashboard.css" />

    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap"
      rel="stylesheet"
    />

    <script src="https://unpkg.com/lucide@latest"></script>
  </head>

  <body>
    <div class="dashboard-container">
      <!-- SIDEBAR -->

      <nav class="sidebar" id="sidebar">
        <div class="profile">
          <img
            src="https://imgcdn.stablediffusionweb.com/2024/9/27/bc8687ab-dd73-432d-b99c-956f74fd0f9a.jpg"
          />

          <h3>EduTrack</h3>
          <p>Admin</p>
        </div>

        <ul class="nav-links">
          <li class="active">
            <!-- <a href="#"></a> -->
            <i data-lucide="layout-dashboard"></i>
            Dashboard
          </li>

          <li>
            <!-- <a href="#"></a> -->
            <i data-lucide="package"></i>
            Product Management
          </li>

          <li>
            <!-- <a href="#"></a> -->
            <i data-lucide="warehouse"></i>
            Inventory Management
          </li>

          <li>
            <!-- <a href="#"></a> -->
            <i data-lucide="shopping-cart"></i>
            Sales Management
          </li>

          <li>
            <!-- <a href="#"></a> -->
            <i data-lucide="bar-chart-3"></i>
            Sales Report
          </li>
        </ul>

        <div class="logout">
          <!-- <a href="#"></a> -->
          <i data-lucide="log-out"></i>
          Logout
        </div>
      </nav>

      <!-- MAIN -->

      <main class="main">
        <header class="topbar">
          <button class="hamburger" id="menuBtn">
            <i data-lucide="menu"></i>
          </button>

          <h2>Welcome to EduTrack!</h2>

          
        </header>

        <section class="dashboard-body">
          <h3>Overview</h3>

          <div class="stats-grid">
            <div class="card"></div>
            <div class="card"></div>
            <div class="card"></div>
            <div class="card"></div>
          </div>

          <div class="empty">
            <p>
              Dashboard is currently empty. Start adding data to see your
              metrics.
            </p>
          </div>
        </section>
      </main>
    </div>

    <script src="../Script/dashboard.js"></script>
  </body>
</html>