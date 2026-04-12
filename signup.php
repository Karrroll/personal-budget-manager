<?php
  session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header id="header">
    <div id="header-content" class="container d-flex justify-content-center">
        <a href="./index.html" aria-label="Go to homepage">
          <img src="./assets/images/LOGO.png" class="logo-size" alt="Budget app logo">
        </a>
    </div>
  </header>

  <main id="main">
    <div class="container py-5 m-auto">
      <div class="px-4 pb-md-5 text-center">
        <h1 class="display-4 mb-4 fw-bold">Create account</h1>
        <div class="lead col-lg-6 mx-auto">
          <p class="text-body-secondary">Join now for free and start managing your personal finances easily.</p>
        </div>
        <hr class="article-divider" role="presentation">
      </div>

      <form
        id="signup-form"
        class="mx-auto py-5"
        action="register_process.php"
        method="POST"
      >
        <p class="required-note required-sing">Reqiured</p>
        <div class="signup-fields"> 
          <div class="form-floating">
              <input
                type="text"
                id="form-signup-name"
                class="form-control <?= isset($_SESSION['errors']['username']) ? 'is-invalid' : '' ?>"
                name="username"
                value="<?= htmlspecialchars($_SESSION['old']['username'] ?? '') ?>"
                autocomplete="username"
                placeholder=" "
              >
              <label for="form-signup-name">Username</label>
              <?php
                if(isset($_SESSION['errors']['username'])) {
                  echo '<div class="error-note">' .$_SESSION['errors']['username'] .'</div>';
                }
              ?>
            </div>

            <div class="form-floating">
              <input
                type="email"
                id="form-signup-email"
                class="form-control <?= isset($_SESSION['errors']['email']) ? 'is-invalid' : '' ?>"
                name="email"
                value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>"
                autocomplete="email"
                placeholder=" "
                required 
              >
              <label for="form-signup-email" class="required-sing">Email Address</label>
              <?php
                if(isset($_SESSION['errors']['email'])) {
                  echo '<div class="error-note">' .$_SESSION['errors']['email'] .'</div>';
                }
              ?>
            </div>

            <div class="form-floating">
              <input
                type="password"
                id="form-signup-password"
                class="form-control <?= isset($_SESSION['errors']['password']) ? 'is-invalid' : '' ?>"
                name="password"
                autocomplete="new-password"
                placeholder="Password"
                required 
              >
              <label for="form-signup-password" class="required-sing">Password</label>
              <?php
                if(isset($_SESSION['errors']['password'])) {
                  echo '<div class="error-note">' .$_SESSION['errors']['password'] .'</div>';
                }
              ?>
            </div>

            <div class="form-floating">
              <input
                type="password"
                id="form-signup-confirm-password"
                class="form-control <?= isset($_SESSION['errors']['password']) ? 'is-invalid' : '' ?>"
                name="confirm_password"
                autocomplete="new-password"
                placeholder="Confirm Password"
                required 
              >
              <label for="form-signup-confirm-password" class="required-sing">Confirm Password</label>
            </div>
        </div>

        <div class="terms-note form-check">
          <input
            type="checkbox"
            id="signupTerms"
            class="form-check-input"
            name="terms"
            value="remember-me"
          >
          <label class="form-check-label required-sing" for="signupTerms">
            I have read and agree
            <a href="" class="text-nowrap terms-link">Terms & Conditions</a>
          </label>

          <?php
            if(isset($_SESSION['errors']['terms'])) {
              echo '<div class="error-note">' .$_SESSION['errors']['terms'] .'</div>';
            }
          ?>
        </div>

        <div class="button-content-center">
          <button type="submit" class="btn btn-primary fw-semibold py-2">
            Create account
          </button>
        </div>

        <div class="signin-note">
          <p>
            Already have an account?
            <a class="signin-link" href="./index.html">Signin</a>
          </p>
        </div>
        
        <?php
          unset($_SESSION['errors'], $_SESSION['old']);
        ?>
      </form>

    </div>
  </main>

  <footer id="footer">
    <div class="contaner">
      <p class="my-0 py-3 text-center text-body-secondary">© 2025</p>
    </div>
  </footer>

</body>
</html>