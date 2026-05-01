<?php
  session_start();

  if(!isset($_SESSION['user_id'])) {
    $_SESSION['errors']['general'] = "Please sign in to access this page!";
    header('Location: index.php');
    exit();
  } else {
    $user_id = $_SESSION['user_id'];
  }

  require_once "connect.php";
  include_once "finance_utilities.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home Page - Budget Manager</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header id="header" class="d-flex flex-column">
    <div class="container">

      <div id="header-main">
        <a href="./dashboard.php">
          <img class="logo-size" src="./assets/images/LOGO.png" alt="Budget Manager homepage">
        </a>

        <div class="dropdown">
          <a
            id="user-dropdown"
            class="dropdown-a text-decoration-none"
            href="#"
            data-bs-toggle="dropdown"
            aria-label="User menu"
            aria-expanded="false"
          >
            <div id="user-avatar" class="d-flex flex-column">
              <img class="avatar-logo" src="./assets/images/icons/person-circle.svg" alt="User profile">
              <span class="avatar-name" ><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            </div>
          </a>

          <ul
            class="dropdown-menu dropdown-menu-end text-small shadow"
            aria-labelledby="user-dropdown"
          >
            <li>  <!-- My Profile item in progress -->
              <a class="dropdown-item" href="#">
                My Profile
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item text-nowrap gap-1" href="signout_process.php">
                <img src="./assets/images/icons/box-arrow-right.svg" alt="" aria-hidden="true">
                Sign out
              </a>
            </li>
          </ul>
        </div>
      </div>

      <nav id="nav">
        <ul class="sidebar-nav-menu">
          <li onclick=hideSidebar()>
            <a href="#" class="sidebar-menu-icon">
              <img src="./assets/images/icons/x-lg.svg" alt="">
            </a>
          </li>
          <li>
            <a href="./dashboard.php" class="active" aria-current="page">
              <img src="./assets/images/icons/house-door.svg" alt="" aria-hidden="true">
              Home
            </a>
          </li>
          <li>
            <a href="./income.php">
              <img src="./assets/images/icons/cash-coin.svg" alt="" aria-hidden="true">
              Add Income
            </a>
          </li>
          <li>
            <a href="./expense.php">
              <img src="./assets/images/icons/credit-card.svg" alt="" aria-hidden="true">
              Add Expense
            </a>
          </li>
          <li>
            <a href="./overview.php">
              <img src="./assets/images/icons/clipboard-data.svg" alt="" aria-hidden="true">
              View Balance
            </a>
          </li>
          <li>
            <a href="#">
              <img src="./assets/images/icons/gear.svg" alt="" aria-hidden="true">
              Settings
            </a>
          </li>
        </ul>

        <ul class="desktop-nav-menu">
          <li>
            <a href="./dashboard.php" class="active" aria-current="page">Home</a>
          </li>
          <li>
            <a href="./income.php">Add Income</a>
          </li>
          <li>
            <a href="./expense.php">Add Expense</a>
          </li>
          <li>
            <a href="./overview.php">View Balance</a>
          </li>
          <li>
            <a href="#">Settings</a>
          </li>
          <li onclick=showSidebar()>
            <a href="#" class="sidebar-menu-icon">
              <img src="./assets/images/icons/list.svg" alt="">
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </header>

  <main id="main" class="py-5 px-2 px-sm-5">

<?php if (isset($_GET['error']) && $_GET['error'] === 'general'): ?>
  <div id="error-modal" class="modal fade" data-bs-backdrop="static" tabindex="-1" aria-labelledby="errorModalTitle">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

      <div class="modal-header">
          <h1 class="modal-title fs-5" id="errorModalTitle">
            Error
          </h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body text-center">
          An unexpected error occurred. Please try again later.
        </div>

        <div class="modal-footer justify-content-center">
          <a href="dashboard.php" class="btn btn-primary">
            OK
          </a>
        </div>

      </div>
    </div>
  </div>
<?php endif; ?>

    <div class="dashboard-section d-flex flex-column flex-md-row m-auto">
      <h1 class="visually-hidden">Financial Dashboard</h1>
      <section class="d-flex flex-column">
        <h2 id="monthly-overview-title">Your Monthly Overview</h2>

        <?php //GET MONTHLY OVERVIEW NUMBERS
          $income = getMonthlyTransactions($connection, $user_id, new DateTime(), "INCOME") ?? "###";
          $expense = getMonthlyTransactions($connection, $user_id, new DateTime(), "EXPENSE") ?? "###";
          is_numeric($income) && is_numeric($expense) ? $balance = calculateBalance($income, $expense) : $balance = "###";
        ?>
        <div class="monthly-summary-items">
          <div class="item-label income-item">
            <span>
              <?= htmlspecialchars($income) ?>
            </span>
            <span>Income</span>
          </div>

          <div class="item-label expense-item">
            <span>
              <?= htmlspecialchars($expense) ?>
            </span>
            <span>Expense</span>
          </div>

          <div class="item-label balance-item">
            <span>
              <?= htmlspecialchars($balance) ?>
            </span>
            <span>Balance</span>
          </div>
        </div>

        <a class="details-link" href="./overview.php" aria-label="Go to full overview of incomes and expenses">Show details ></a>
      </section>

      <div class="divider-wrapper">
        <hr class="summary-divider" role="presentation">
      </div>

      <section class="quick-actions-wrapper">
        <h2 class="visually-hidden">Quick Actions</h2>
        <div class="quick-action-label income-action">
          <a class="action-content action-content-income" href="./income.php">
            <img src="./assets/images/icons/plus-lg.svg" alt="" aria-hidden="true">
            <span>Add Income</span>
          </a>
        </div>
        <div class="quick-action-label expense-action">
          <a class="action-content action-content-expense" href="./expense.php">
            <img src="./assets/images/icons/dash.svg" alt="" aria-hidden="true">
            <span>Add Expense</span>
          </a>
        </div>
      </section>

    </div>
  </main>

  <footer id="footer">
    <div class="contaner">
      <p class="my-0 py-3 text-center text-body-secondary">© 2025</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="index.js" defer></script>
</body>

</html>
