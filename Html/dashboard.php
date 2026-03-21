

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
      <script src="https://unpkg.com/lucide@latest"></script>

      <script src="../Script/systemNav.js"></script>
      <script src="../Script/dashboard.js"></script>
  </body>
</html>