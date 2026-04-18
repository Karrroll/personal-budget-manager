<?php
  session_start();

  $errors = [];
  $old = [];

  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $remember = $_POST['remember'] ?? NULL; //optional field. Implementation in progress

  $old['email'] = $email;

  //validate inputs
  if($email === '') {
    $errors['email'] = "Email field is required";
  } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Invalid email address";
  }

  if($password === '') {
    $errors['password'] = "Password field is required";
  }

  //Back to signin page if any error
  if(!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $old;
    
    header('Location: index.php');
    exit();
  }

  require_once "connect.php";
  
  //verify login credentials
  try {
    $stmt = $connection->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->bindValue(':email', strtolower($email), PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch();
    
    if($user !== false AND password_verify($password, $user['password'])) {
      session_regenerate_id(true);  // Prevent session fixation attacks

      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_name'] = $user['username'];

      header('Location: dashboard.php');
      exit();
    } else {
      $errors['credentials'] = "Invalid email address or password";
      $_SESSION['errors'] = $errors;
      $_SESSION['old'] = $old;
      header('Location: index.php');
      exit();
    }
  } catch(PDOException $e) {
    error_log($e->getMessage());
    $errors['general'] = "An unexpected error occurred. Please try again later.";
    $_SESSION['errors'] = $errors;
    header('Location: index.php');
    exit();
  }
?>