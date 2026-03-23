

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>EduTrack Dashboard</title>

    <link rel="stylesheet" href="../Css/systemNav.css" />
    <link rel="stylesheet" href="../Css/dashboard.css" />

    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap"
      rel="stylesheet"
    />


  </head>

  <body>
    <div class="systemNav-container">
      <!-- SIDEBAR -->

    <?php include("systemNav.php"); ?>

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

            <div class="card card-blue">
              <div class="dashLabel">Total Sales Today</div>
              <div class="dashValue" id="totalSales">₱600</div>
              <div class="dashSub neu" id="salesSub">today</div>
            </div>

            <div class="card card-green">
              <div class="dashLabel">Total Solds Today</div>
              <div class="dashValue" id="totalSolds">10</div>
              <div class="dashSub neu" id="soldsSub">today</div>
            </div>

            <div class="card card-orange">
              <div class="dashLabel">Total Products</div>
              <div class="dashValue" id="totalProducts"></div>
              <div class="dashSub neu" id="productsSub">in stock</div>
            </div>

            <div class="card card-teal">
              <div class="dashLabel">Transactions Today</div>
              <div class="dashValue" id="totalTransactions">2</div>
              <div class="dashSub neu" id="transactionsSub">today</div>
            </div>

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
      <script src="https://unpkg.com/lucide@latest"></script>

      <script src="../Script/systemNav.js"></script>
      <script src="../Script/dashboard.js"></script>
  </body>
</html>