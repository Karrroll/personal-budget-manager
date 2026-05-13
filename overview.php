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

  // initialize with default period values
  $selected_period = $_SESSION['selected-period'] ?? 'CURRENT';
  $start_date = $_SESSION['start-date'] ?? date('Y-m-01');
  $end_date = $_SESSION['end-date'] ?? date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Overview Balance - Budget Manager</title>

  <!-- Bootstrap’s CSS plugin -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  
  <link rel="stylesheet" href="style.css"> <!-- link with .css -->
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
              <span class="avatar-name" ><?= htmlspecialchars($_SESSION['user_name']); ?></span>
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
            <a href="./dashboard.php">
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
            <a href="./overview.php" class="active" aria-current="page">
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
            <a href="./dashboard.php">Home</a>
          </li>
          <li>
            <a href="./income.php">Add Income</a>
          </li>
          <li>
            <a href="./expense.php">Add Expense</a>
          </li>
          <li>
            <a href="./overview.php" class="active" aria-current="page">View Balance</a>
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

  <main id="main" class="py-5 px-2 px-sm-5 justify-content-center">
    <div id="overview-section" class="container d-flex flex-column m-0">

  <!-- Select period -->
      <section id="select-date-section"
        class="align-self-center align-self-md-end d-flex flex-column gap-2"
        aria-labelledby="select-period-heading"
      >
      <form
        id="data-picker-form"
        method="POST"
        action="date_picker_utilities.php"
      >
        <h2 id="select-period-heading" class="visually-hidden">Select financial period</h2>
        <div class="d-flex flex-column">
          <label id="select-period-label" for="select-period" class="fw-bold">Select period:</label>
          <select
            id="select-period"
            class="form-select"
            name="selected-period"
            aria-controls="select-custom-dates"
            aria-expanded="false"
            aria-label="Select financial period"
            aria-invalid=""
          >
            <option value="CURRENT_MONTH" <?= $selected_period === 'CURRENT_MONTH' ? 'selected' : '' ?> >This Month</option>
            <option value="LAST_MONTH" <?= $selected_period === 'LAST_MONTH' ? 'selected' : '' ?> >Previous Month</option>
            <option value="CURRENT_YEAR" <?= $selected_period === 'CURRENT_YEAR' ? 'selected' : '' ?> >This Year</option>
            <option value="CUSTOM" <?= $selected_period === 'CUSTOM' ? 'selected' : '' ?> >Custom Date</option>
          </select>
        </div>

        <div id="select-custom-dates" <?= $selected_period === 'CUSTOM' ? '' : 'hidden' ?> >
          <?php
            if(isset($_SESSION['errors']['start-date'])) {
                echo '<div
                        id="start-date-error"
                        class="alert alert-danger text-danger text-center"
                        role="alert"
                      >'
                        .$_SESSION['errors']['start-date']
                      .'</div>'
                ;
            }

            if(isset($_SESSION['errors']['end-date'])) {
                echo '<div
                        id="end-date-error"
                        class="alert alert-danger text-danger text-center"
                        role="alert"
                      >'
                        .$_SESSION['errors']['end-date']
                      .'</div>'
                ;
            }
          ?>
          <div class="form-floating">
            <input
              type="date"
              id="start-date"
              class="form-control <?= isset($_SESSION['errors']['start-date']) ? 'is-invalid' : '' ?>"
              name="start-date"
              value="<?= htmlspecialchars($_SESSION['old']['start-date'] ?? date('Y-m-d')) ?>"
              min="1970-01-01"
              max="<?= date('Y-m-d') ?>"
              required
              aria-describedby="<?= isset($_SESSION['errors']['start-date']) ? 'start-date-error' : '' ?>"
              aria-invalid="<?= isset($_SESSION['errors']['start-date']) ? 'true' : 'false' ?>"
            >
            <label for="start-date">From</label>
          </div>

          <div class="form-floating">
            <input
              type="date"
              id="end-date"
              class="form-control <?= isset($_SESSION['errors']['end-date']) ? 'is-invalid' : '' ?>"
              name="end-date"
              value="<?= htmlspecialchars($_SESSION['old']['end-date'] ?? date('Y-m-d')) ?>"
              min="1970-01-01"
              max="<?= date('Y-m-d') ?>"
              required
              aria-describedby="<?= isset($_SESSION['errors']['end-date']) ? 'end-date-error' : '' ?>"
              aria-invalid="<?= isset($_SESSION['errors']['end-date']) ? 'true' : 'false' ?>"
            >
            <label for="end-date">To</label>
          </div>

          <input type="submit" id="select-date-button" class="align-self-end btn btn-primary mt-2 fw-bold" value="APPLY" aria-label="Apply custom date range">
        
        </div>
        <?php  unset($_SESSION['errors'], $_SESSION['old']); ?>
      </form>
      </section>

      <section id="financial-summary"
        class="align-self-center d-flex flex-column mt-5"
        aria-labelledby="financial-summary-heading"
      >
        <h2 id="financial-summary-heading">Your Financial Summary</h2>
<!-- Progress ring balance -->

        <?php
        //zmienne do obliczeń dynamicznych
        $selected_period_income = getIncome($connection, $user_id, $start_date, $end_date);
        $selected_period_expense = getExpense($connection, $user_id, $start_date, $end_date);
        
        if($selected_period_income === NULL || $selected_period_expense === NULL) {
          header('Location: dashboard.php?error=general');
          exit();
        }
        
        $selected_period_total = $selected_period_income + $selected_period_expense;
        
        $income_share = shareOfTotal($selected_period_total, $selected_period_income, 0); 
        $expense_share = shareOfTotal($selected_period_total, $selected_period_expense, 0);

        if($income_share === NULL || $expense_share === NULL) {
          header('Location: dashboard.php?error=general');
          exit();
        }

        ?>
        <div class="d-flex flex-column align-items-center">
          <div class="semi-ring-wrapper"
            aria-label="Income vs expense balance"
            aria-valuemin="0"
            aria-valuemax="100"
            role="progressbar"
          >
            <svg viewBox="0 0 300 300">
              <circle class="ring bg" cx="150" cy="150" r="125"></circle> <!-- bg ring -->
              <circle class="ring green" cx="150" cy="150" r="125"></circle> <!-- green ring -->
              <circle class="ring red" cx="150" cy="150" r="125"></circle> <!-- red ring -->
            </svg>
            <div class="ring-score" aria-label="Income <?= (int) $income_share ?>%, Expense <?= (int) $expense_share ?>%" role="status">  
              <span class="expense" aria-hidden="true"><?= (int) $expense_share ?></span>
              <span class="divider" aria-hidden="true">/</span>
              <span class="income" aria-hidden="true"><?= (int) $income_share ?></span>
            </div>
          </div>
          <div class="financial-feedback fs-5 mt-3" aria-live="polite" role="status">
            <p class="positive">
              <span>Great job!</span> Your finances are in good shape!
            </p>
            <p class="neutral">
              Your finances are balanced - keep an eye on your spending!
            </p>
            <p class="negative">
              <span>Warning!</span> You are spending more than you earn!
            </p>
            <p class="no-transactions">
              No transactions for the selected period.
            </p>
          </div>
        </div>

<!-- Income summary table -->
        <div id="tables-wrapper">
          <div id="income-summary" class="d-flex flex-column">
            <div class="summary-title bg-dark text-center py-2">
              <h2>Income</h2>
            </div>

            <table class="table table-bordered table-striped table-hover m-0"
              aria-label="Summary of income by category for selected period"
            >
              <thead >
                <tr>
                  <th scope="col" class="text-center">#</th>
                  <th scope="col" class="text-center">Category</th>
                  <th scope="col" class="text-center">Amount [PLN]</th>
                  <th scope="col" class="text-center">Share [%]</th>
                </tr>
              </thead>
              <tbody class="table-group-divider">            
              <?php
                $stmt = $connection->prepare('
                  SELECT
                    assigned_cat.id AS `id`,
                    assigned_cat.name AS `name`,
                    SUM(incomes.amount) AS `category_amount`
                  FROM incomes_category_assigned_to_users AS `assigned_cat`
                    INNER JOIN incomes
                      ON assigned_cat.user_id = incomes.user_id
                      AND assigned_cat.id = incomes.income_category_assigned_to_user_id
                      AND date_of_income BETWEEN :start_date AND :end_date
                  WHERE assigned_cat.user_id = :user_id
                  GROUP BY `name`
                  ORDER BY `category_amount` DESC
                ');
                $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
                $stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
                $stmt->execute();

                $income_cat = $stmt->fetchAll();

                $total_amount = array_sum(array_column($income_cat, 'category_amount'));
              ?>

              <?php if (!empty($income_cat)): ?>
              <?php
                include_once "finance_utilities.php";

                $row_counter = 1;
                foreach($income_cat as $cat):
                  $category_share = shareOfTotal($total_amount, $cat['category_amount'], 2) ?? 0;
              ?>
                <tr>
                  <th scope="row" class="text-center"><?= $row_counter++ ?></th>
                  <td>
                    <button
                      type="button"
                      class="btn-link-table"
                      data-bs-toggle="modal"
                      data-bs-target="#staticBackdrop"
                      data-category="<?= htmlspecialchars($cat['id']) ?>"
                      aria-label="View details for <?= htmlspecialchars($cat['name']) ?> transactions">
                      <?= htmlspecialchars($cat['name']) ?>
                  </button>
                  </td>
                  <td class="text-center"> <?= htmlspecialchars($cat['category_amount']) ?> </td>
                  <td class="text-center"> <?= $category_share ?> </td>
                </tr>
              <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td class="text-center" colspan="4">
                    <div class="fw-bold my-2">No incomes for selected period</div>
                  </td>
                </tr>
              <?php endif; ?>

              </tbody>
              <tfoot class="table-group-divider">
                <tr>
                  <th scope="row" colspan="4" class="text-end" aria-label="Total income: <?= number_format((float)$total_amount, 2, '.', ',') ?> PLN">
                    <span>Total income:</span>
                    <span class="total-amount"> <?= number_format((float)$total_amount, 2, '.', ',') ?> </span>
                    <span>PLN</span>
                  </th>
                </tr>
              </tfoot>
            </table>
          </div>

<!-- Expense summary table -->        
          <div id="expense-summary" class="d-flex flex-column">
            <div class="summary-title bg-dark text-center py-2">
              <h2>Expense</h2>
            </div>

            <table class="table table-bordered table-striped table-hover m-0"
              aria-label="Summary of expense by category for selected period"
            >
              <thead >
                <tr>
                  <th scope="col" class="text-center">#</th>
                  <th scope="col" class="text-center">Category</th>
                  <th scope="col" class="text-center">Amount [PLN]</th>
                  <th scope="col" class="text-center">Share [%]</th>
                </tr>
              </thead>
              <tbody class="table-group-divider">
              <?php
                $stmt = $connection->prepare('
                  SELECT
                    assigned_cat.id AS `id`,
                    assigned_cat.name AS `name`,
                    SUM(expenses.amount) AS `category_amount`
                  FROM expenses_category_assigned_to_users AS `assigned_cat`
                    INNER JOIN expenses
                      ON assigned_cat.user_id = expenses.user_id
                      AND assigned_cat.id = expenses.expense_category_assigned_to_user_id
                      AND date_of_expense BETWEEN :start_date AND :end_date
                  WHERE assigned_cat.user_id = :user_id
                  GROUP BY `name`
                  ORDER BY `category_amount` DESC
                ');
                $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
                $stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
                $stmt->execute();

                $expense_cat = $stmt->fetchAll();

                $total_amount = array_sum(array_column($expense_cat, 'category_amount'));
              ?>

              <?php if (!empty($expense_cat)): ?>
              <?php
                $row_counter = 1;
                foreach($expense_cat as $cat):
                  $category_share = shareOfTotal($total_amount, $cat['category_amount'], 2) ?? 0; 
              ?>
                <tr>
                  <th scope="row" class="text-center"><?= $row_counter++ ?></th>
                  <td>
                    <button
                      type="button"
                      class="btn-link-table"
                      data-bs-toggle="modal"
                      data-bs-target="#staticBackdrop"
                      data-category="<?= htmlspecialchars($cat['id']) ?>"
                      aria-label="View details for <?= htmlspecialchars($cat['name']) ?> transactions">
                      <?= htmlspecialchars($cat['name']) ?>
                  </button>
                  </td>
                  <td class="text-center"> <?= htmlspecialchars($cat['category_amount']) ?> </td>
                  <td class="text-center"> <?= $category_share ?> </td>
                </tr>
              <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td class="text-center" colspan="4">
                    <div class="fw-bold my-2">No expenses for selected period</div>
                  </td>
                </tr>
              <?php endif; ?>        
              </tbody>
              <tfoot class="table-group-divider">
                <tr>
                  <th scope="row" colspan="4" class="text-end" aria-label="Total expense: <?= number_format((float)$total_amount, 2, '.', ',') ?> PLN">
                    <span>Total expense:</span>
                    <span class="total-amount"> <?= number_format((float)$total_amount, 2, '.', ',') ?> </span>
                    <span>PLN</span>
                  </th>
                </tr>
              </tfoot>
            </table>          
          </div>
        </div>
      </section>
                 
<!-- Category details modal -->
      <section class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true"> <!-- aria-hidden JS !! -->
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h2 class="modal-title fs-5" id="transactionModalLabel">"category name" transactions</h2>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p>List of category transactions (table with date, name, amount etc.)..... </p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>
              <!-- add print/export button -->
            </div>
          </div>
        </div>
      </section>

    </div>
    <?php  unset($_SESSION['selected-period'], $_SESSION['start-date'], $_SESSION['end-date']); ?>
  </main>

  <footer id="footer">
    <div class="contaner">
      <p class="my-0 py-3 text-center text-body-secondary">© 2025</p>
    </div>
  </footer>

  <!-- Bootstrap’s JavaScript plugin -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
    crossorigin="anonymous">
  </script>

  <script src="index.js" defer></script> <!-- link with .js -->
</body>
</html>