<?php
  session_start();

  if(isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Budget Manager</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header id="header">
    <div id="header-content" class="container d-flex flex-column flex-sm-row gap-2 align-items-center justify-content-sm-between">
      <div>
        <a href="./index.php">
          <img src="./assets/images/LOGO.png" class="logo-size" alt="Budget Manager homepage">
        </a>
      </div>

      <div>
        <a href="./signup.php" class="btn btn-primary button-content-center text-nowrap gap-1">
          <img src="./assets/images/icons/person-plus-fill.svg" alt="" aria-hidden="true">
          Sign up
        </a>
      </div>
    </div>
  </header>

  <main id="main">
    <div class="container py-5 m-auto">
      <div class="px-4 pb-md-5 text-center">
        <h1 id="hero-title" class="display-4 mb-5 fw-bold">Take control of your money!</h1>
        <div class="lead col-lg-6 mx-auto">
          <p class="text-body-secondary">Welcome to Personal Budget Manager - an app for anyone who wants to start managing their finances consciously.</p>
          <p class="fw-semibold">Sign in to track your income, expenses, and savings, and make smarter financial decisions.</p>
        </div>
        <hr class="article-divider" role="presentation">
      </div>

      <form
        id="signin-form"
        class="mx-auto mt-md-5 py-5"
        action="signin_process.php"
        method="POST"
        aria-labelledby="form-title"
      >
        <h2 id="form-title" class="visually-hidden">Sign in</h2>
        <div class="signup-note">
          <p>Don't have an account?
            <a class="signup-link" href="./signup.php">Create an account here</a>
          </p>
        </div>
        <?php
          if(isset($_SESSION['success'])) {
            echo '<div
                    class="alert alert-success text-success text-center"
                    aria-live="polite"
                  >'
                    .$_SESSION['success']
                  .'</div>'
            ;
          }

          if(isset($_SESSION['errors']['general'])) {
            echo '<div
                    class="alert alert-danger text-danger text-center"
                    role="alert"
                  >'
                    .$_SESSION['errors']['general']
                  .'</div>'
            ;
          }

          if(isset($_SESSION['errors']['credentials'])) {
              echo '<div
                      id="credential-error"
                      class="alert alert-danger text-danger text-center"
                      role="alert"
                    >'
                      .$_SESSION['errors']['credentials']
                    .'</div>'
              ;
          } 

          if(isset($_SESSION['errors']['email'])) {
              echo '<div 
                      id="email-error"
                      class="alert alert-danger text-danger text-center"
                      role="alert"
                    >'
                      .$_SESSION['errors']['email']
                    .'</div>'
              ;
          }

          if(isset($_SESSION['errors']['password'])) {
              echo '<div
                      id="password-error"
                      class="alert alert-danger text-danger text-center"
                      role="alert"
                    >'
                      .$_SESSION['errors']['password']
                    .'</div>'
              ;
          }
        ?>
        <div class="form-floating">
          <input
            type="email"
            id="formLoginEmail"
            class="form-control <?= isset($_SESSION['errors']['email']) || isset($_SESSION['errors']['credentials']) ? 'is-invalid' : '' ?>"
            name="email"
            value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>"
            autocomplete="email"
            placeholder="name@example.com"
            required
            aria-describedby="
                                <?= (isset($_SESSION['errors']['email']) ? 'email-error' : '') ?>
                                <?= (isset($_SESSION['errors']['credentials']) ? 'credential-error' : '') ?>
                             "
            aria-invalid="<?= isset($_SESSION['errors']['email']) || isset($_SESSION['errors']['credentials']) ? 'true' : 'false' ?>"
          >
          <label for="formLoginEmail">Email address</label>
        </div>
        <div class="mt-1 form-floating">
          <input
            type="password"
            id="formLoginPassword"
            class="form-control <?= isset($_SESSION['errors']['password']) || isset($_SESSION['errors']['credentials']) ? 'is-invalid' : '' ?>"
            name="password"
            autocomplete="current-password"
            placeholder="Password"
            required
            aria-describedby="
                                <?= (isset($_SESSION['errors']['password']) ? 'password-error' : '') ?>
                                <?= (isset($_SESSION['errors']['credentials']) ? 'credential-error' : '') ?>
                             "
            aria-invalid="<?= isset($_SESSION['errors']['password']) || isset($_SESSION['errors']['credentials']) ? 'true' : 'false' ?>"
          >
          <label for="formLoginPassword">Password</label>
        </div>
        <div class="form-check text-start my-3">
          <input
            type="checkbox"
            id="rememberCheckbox"
            name="remember"
            class="form-check-input"
            value="1">
          <label class="form-check-label" for="rememberCheckbox">Remember me</label>
        </div>
        <div class="button-content-center">
          <button class="btn btn-success px-5 py-2" type="submit">Sign in</button>
        </div>
        <?php
          unset($_SESSION['errors'], $_SESSION['success'], $_SESSION['old']);
        ?>
      </form>
    </div>
  </main>

  <footer id="footer">
    <div class="contaner">
      <p class="my-0 py-3 text-center text-body-secondary">© 2025</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
  </script>
</body>

</html>