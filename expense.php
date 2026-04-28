<?php
  session_start();

  if(!isset($_SESSION['user_id'])) {
    $_SESSION['errors']['general'] = "Please sign in to access this page!";
    header('Location: index.php');
    exit();
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Transaction - Budget Manager</title>

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
            <a href="./expense.php" class="active" aria-current="page">
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
            <a href="./dashboard.php">Home</a>
          </li>
          <li>
            <a href="./income.php">Add Income</a>
          </li>
          <li>
            <a href="./expense.php" class="active" aria-current="page">Add Expense</a>
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

    <div class="transaction-section d-flex flex-column m-auto">
      <section class="transaction-header bg-dark py-3">
        <h1 class="transaction-title">Add Expense</h1>
      </section>
      <section class="transaction-form p-5">

        <form
          id="transaction-form"
          action="transaction.php"
          method="POST"
        >
          <h2 class="visually-hidden">Transaction Form</h2>

          <!-- hidden input to recognize transaction type -->
          <input type="hidden" name="transaction-type" value="EXPENSE">

          <div class="input-group mb-3">
            <div class="form-floating">
              <input
                type="number"
                id="amount"
                class="form-control <?= isset($_SESSION['errors']['amount']) ? 'is-invalid' : '' ?>"
                name="amount"
                value="<?= htmlspecialchars($_SESSION['old']['amount'] ?? '0.01') ?>"
                placeholder=" "
                min="0.01"
                max="999999.99"
                step="0.01"
                required
                aria-describedby="amount-help <?= isset($_SESSION['errors']['amount']) ? 'amount-error' : '' ?>"
                aria-invalid="<?= isset($_SESSION['errors']['amount']) ? 'true' : 'false' ?>"
              >
              <label for="amount">Amount</label>
            </div>
            <span class="input-group-text">PLN</span>
            <small id="amount-help" class="form-text text-muted w-100 ms-2">
              Enter amount between 0.01 and 999999.99 PLN
            </small>
          </div>

          <div class="form-floating mb-3">
            <input
              type="date"
              id="date"
              class="form-control <?= isset($_SESSION['errors']['date']) ? 'is-invalid' : '' ?>"
              name="transaction-date"
              value="<?= htmlspecialchars($_SESSION['old']['date'] ?? date('Y-m-d')) ?>"
              min="1970-01-01"
              max="<?= date('Y-m-d') ?>"
              required
              aria-describedby="<?= isset($_SESSION['errors']['date']) ? 'date-error' : '' ?>"
              aria-invalid="<?= isset($_SESSION['errors']['date']) ? 'true' : 'false' ?>"
            >
            <label for="date">Date</label>
          </div>

          <div class="form-floating mb-3">
            <select
              id="payment-type"
              class="form-select <?= isset($_SESSION['errors']['payment-type']) ? 'is-invalid' : '' ?>"
              name="payment-type"
              required
              aria-describedby="<?= isset($_SESSION['errors']['payment-type']) ? 'payment-type-error' : '' ?>"
              aria-invalid="<?= isset($_SESSION['errors']['payment-type']) ? 'true' : 'false' ?>"
            >
              <option value="" disabled selected>Choose payment method...</option>
              <?php
                require_once "connect.php";
                $stmt = $connection->prepare('
                  SELECT id, name
                  FROM payment_methods_assigned_to_users
                  WHERE user_id = :user_id
                  ORDER BY name ASC
                ');
                $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->execute();

                $payment_type = $stmt->fetchAll();
              ?>

              <?php foreach($payment_type as $type): ?>
                  <option value="<?= $type['id'] ?>">
                    <?= htmlspecialchars($type['name']) ?>
                  </option>
              <?php endforeach; ?>
            </select>
            <label for="payment-type">Payment Method</label>
          </div>

          <div class="form-floating mb-3">
            <select
              id="transaction-category"
              class="form-select <?= isset($_SESSION['errors']['category']) ? 'is-invalid' : '' ?>"
              name="category"
              required
              aria-describedby="<?= isset($_SESSION['errors']['category']) ? 'category-error' : '' ?>"
              aria-invalid="<?= isset($_SESSION['errors']['category']) ? 'true' : 'false' ?>"
            >
              <option value="" disabled selected>Choose category...</option>
              <?php
                require_once "connect.php";
                $stmt = $connection->prepare('
                  SELECT id, name
                  FROM expenses_category_assigned_to_users
                  WHERE user_id = :user_id
                  ORDER BY (name = "Other") ASC, name ASC
                ');
                $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->execute();

                $expense_cat = $stmt->fetchAll();
              ?>

              <?php foreach($expense_cat as $cat): ?>
                  <option value="<?= $cat['id'] ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                  </option>
              <?php endforeach; ?>
            </select>
            <label for="transaction-category">Transaction Category</label>
          </div>

          <div class="form-floating mb-3">
            <textarea
              id="comment-field"
              class="form-control"
              name="comment"
              placeholder="Leave a comment here">
              <?= htmlspecialchars($_SESSION['old']['comment'] ?? '', ENT_QUOTES) ?>
            </textarea>
            <label for="comment-field">Comments (optional)</label>
          </div>

          <div class="d-flex gap-3 justify-content-end">
            <a href="./dashboard.php" class="btn btn-outline-secondary fw-semibold">Back Home</a>
            <button type="submit" class="btn btn-primary fw-semibold">Add</button>
          </div>
          <?php
            unset($_SESSION['errors'], $_SESSION['success'], $_SESSION['old']);
          ?>
        </form>
      </section>
    </div>

  </main>

  <footer id="footer">
    <div class="contaner">
      <p class="my-0 py-3 text-center text-body-secondary">© 2025</p>
    </div>
  </footer>

  <script src="index.js" defer></script>
</body>
</html>