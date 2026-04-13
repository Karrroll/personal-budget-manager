<?php
  session_start();

  $errors = [];
  $old = [];

  $username = trim($_POST['username'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';
  $terms = $_POST['terms'] ?? NULL;

  $old['username'] = $username;
  $old['email'] = $email;

// ------------------ VALIDATE INPUTS ------------------ 
  //validate email
  if($email === '') {
    $errors['email'] = "Email field is required";
  } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Invalid email address";
  }

  //validate password
  if(($password === '') || ($confirm_password === '')) {
    $errors['password'] = "Password field is required";
  } else if(mb_strlen($password) < 6) {
    $errors['password'] = "Password must contain at least 6 characters";
  } else if(
    !preg_match('/\p{Lu}/u', $password) ||  //if no uppercase letter
    !preg_match('/\p{Ll}/u', $password) ||  //if no lowercase letter
    !preg_match('/[0-9]/', $password)       //if no number
  ) {
    $errors['password'] = "Password must contain uppercase, lowercase, and number";
  } else if($password !== $confirm_password) {
    $errors['password'] = "Passwords do not match";
  }

  //validate username (optional field)
  if ($username !== '') {
    
    if (mb_strlen($username) < 3) {
      $errors['username'] = "Username must contain at least 3 characters";
    } else if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
      $errors['username'] = "Username can only contain letters, numbers, and underscores";
    }
  } else {
    $username = NULL; //convert to NULL for daatbase(needed?)
  }
 
  //validate terms checkbox
  if ($terms === NULL) {
    $errors['terms'] = "You must accept Terms & Conditions";
  }
// ------------------------------------------------------

  //Back to form page if any error
  if(!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $old;
    
    header('Location: signup.php');
    exit();
  }

  require_once "connect.php";
  
  //check if email exist in database
  try {
    $stmt = $connection->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->bindValue(':email', strtolower($email), PDO::PARAM_STR);
    $stmt->execute();

    if($stmt->fetch()) {
      $errors['email'] = "Address email already exist";
      $_SESSION['errors'] = $errors;
      header('Location: signup.php');
      exit();
    }
  } catch(PDOException $e) {
    echo "Error occurred, please try again later";
  }

  $email = strtolower($email);
  $password_hash = password_hash($password, PASSWORD_BCRYPT);

    //SUCCESS
    header('Location: signup.php');
    exit();
?>