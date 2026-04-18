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

  //validate passwords
  if(($password === '') || ($confirm_password === '')) {
    $errors['password_both'] = "Password fields are required";
  } else if(mb_strlen($password) < 6) {
    $errors['password'] = "Password must contain at least 6 characters";
  } else if(
    !preg_match('/\p{Lu}/u', $password) ||  //if no uppercase letter
    !preg_match('/\p{Ll}/u', $password) ||  //if no lowercase letter
    !preg_match('/[0-9]/', $password)       //if no number
  ) {
    $errors['password'] = "Password must contain uppercase, lowercase, and number";
  } else if($password !== $confirm_password) {
    $errors['password_both'] = "Passwords do not match";
  }

  //validate username
  if ($username === '') {
    $errors['username'] = "Username field is required";
  } else if (mb_strlen($username) < 3) {
    $errors['username'] = "Username must contain at least 3 characters";
  } else if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $errors['username'] = "Username can only contain letters, numbers, and underscores";
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
  
  //check if email or username exist in database
  try {
    $stmt = $connection->prepare('SELECT `username`, `email` FROM users WHERE email = :email OR username = :username');
    $stmt->bindValue(':email', strtolower($email), PDO::PARAM_STR);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    $existing_users = $stmt->fetchAll();

    if($existing_users) {
      $exist_email = false;
      $exist_username = false;

      foreach ($existing_users as $row) {
        if (!$exist_email && strtolower($row['email']) === strtolower($email)) {
            $errors['email'] = "Address email already exists";
            $exist_email = true;
        }
        
        if (!$exist_username && strtolower($row['username']) === strtolower($username)) {
            $errors['username'] = "Username already exists";
            $exist_username = true;
        }
      }

      if(!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $old;

        header('Location: signup.php');
        exit();
      }
    }
  } catch(PDOException $e) {
    error_log($e->getMessage());
    $errors['general'] = "Cannot create account right now. Please try again later.";
    $_SESSION['errors'] = $errors;
    header('Location: index.php');
    exit();
  }

  $email = strtolower($email);  //convert email to lowercase befor add user to DB
  $password_hash = password_hash($password, PASSWORD_BCRYPT);
  
  //add user to database
  try {
    $stmt = $connection->prepare('INSERT INTO users(`username`, `password`, `email`) VALUES(:username, :password_hash, :email)');
    
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    
    $stmt->execute();
  } catch(PDOException $e) {
    error_log($e->getMessage());
    $errors['general'] = "Cannot create account right now. Please try again later.";
    $_SESSION['errors'] = $errors;
    header('Location: index.php');
    exit();
  }

  //SUCCESS
  $_SESSION['success'] = "Account created! Please sign in.";
  header('Location: index.php');
  exit();
?>