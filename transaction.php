<?php
  session_start();

  $errors = [];
  $old = [];

  $type = $_POST['transaction-type'] ?? '';
  $amount = trim($_POST['amount'] ?? '');
  $given_date = trim($_POST['transaction-date'] ?? '');
  $payment_method = $_POST['payment-type'] ?? ''; // ONLY FOR EXPENSE TRANSACTION
  $category = $_POST['category'] ?? '';
  $comment = trim($_POST['comment'] ?? '');

  $old['amount'] = $amount;
  $old['date'] = $given_date;
  $old['comment'] = $comment;

  if($type === "INCOME") {
    $redirect = 'income.php';
  } else if ($type === "EXPENSE") {
    $redirect = 'expense.php';
  } else {
    header('Location: dashboard.php?error=general');
    exit();
  }

  //validate amount
  $min_amount = 0.01;
  $max_amount = 999999.99;

  if($amount === '') {
    $errors['amount'] = "Amount field is required";
  } else if (!preg_match('/^(?:0|[1-9]\d*)([.,]\d{1,2})?$/', $amount)) {
      $errors['amount'] = "Invalid amount format";
  } else {
    $amount_float = (float) str_replace(',', '.', $amount); //Convert amount to FLOAT
    
    if($amount_float < $min_amount || $amount_float > $max_amount) {
      $errors['amount'] = "Amount out of range";
    }
  }

  //validate date
  if($given_date === '') {
    $errors['date'] = "Date field is required";
  } else {
    $date = DateTime::createFromFormat('Y-m-d', $given_date); //Convert date to DATE type
    $today = new DateTime('today');
    $min_date = new DateTime('1970-01-01');

    if (!$date || $date->format('Y-m-d') !== $given_date) {
      $errors['date'] = "Invalid date format";
    } else if ($date->setTime(0, 0, 0) > $today->setTime(0, 0, 0) || $date < $min_date) {
      $errors['date'] = "Date out of range";
    }
  } 

  //validate category
  if($category === '')
    $errors['category'] = "Category field is required";
  else if (!ctype_digit($category))
    $errors['category'] = "Invalid category format";
  else {
    try {
      require_once "connect.php";
      $category_id = (int) $category;

      if($type === "INCOME") {
        $stmt = $connection->prepare('
          SELECT 1
          FROM incomes_category_assigned_to_users
          WHERE user_id = :logged_user AND id = :category_id
        ');
      } else if($type === "EXPENSE") {
        $stmt = $connection->prepare('
          SELECT 1
          FROM expenses_category_assigned_to_users
          WHERE user_id = :logged_user AND id = :category_id
        ');
      }

      $stmt->bindValue(':logged_user', $_SESSION['user_id'], PDO::PARAM_INT);
      $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
      $stmt->execute();

      if(!$stmt->fetchColumn())
        $errors['category'] = "Selected category not found";
    
    } catch(PDOException $e) {
      error_log($e->getMessage());
      header('Location: dashboard.php?error=general');
      exit();
    }
  }

  //validate payment method - ONLY FOR EXPENSE TRANSACTION
  if($type === "EXPENSE") {
    if($payment_method === '')
      $errors['payment-type'] = "Payment method field is required";
    else if (!ctype_digit($payment_method))
      $errors['payment-type'] = "Invalid payment method format";
    else {
      try {
        require_once "connect.php";
        $payment_method_id = (int) $payment_method;

        $stmt = $connection->prepare('
          SELECT 1
          FROM payment_methods_assigned_to_users
          WHERE user_id = :logged_user AND id = :payment_method_id
        ');
        $stmt->bindValue(':logged_user', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':payment_method_id', $payment_method_id, PDO::PARAM_INT);
        $stmt->execute();

        if(!$stmt->fetchColumn())
          $errors['payment-type'] = "Selected payment method not found";
      
      } catch(PDOException $e) {
        error_log($e->getMessage());
        header('Location: dashboard.php?error=general');
        exit();
      }
    }
  }

  //check validation result
  if(!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $old;
    header("Location: $redirect");
    exit();  
  }

  //SUCCESS - Add transaction to DB
  try {
    $connection->beginTransaction();

    if($type === "INCOME") {
      $stmt = $connection->prepare('
        INSERT INTO incomes(`user_id`, `income_category_assigned_to_user_id`, `amount`, `date_of_income`, `income_comment`)
        VALUES(:logged_user, :category_id, :amount, :date, :comment)
      ');
    } else if ($type === "EXPENSE") {
      $stmt = $connection->prepare('
        INSERT INTO expenses(`user_id`, `expense_category_assigned_to_user_id`, `payment_method_assigned_to_user_id`, `amount`, `date_of_expense`, `expense_comment`)
        VALUES(:logged_user, :category_id, :payment_method, :amount, :date, :comment)
      ');
    }

    if($type === "EXPENSE") $stmt->bindValue(':payment_method', $payment_method_id, PDO::PARAM_INT);  // ONLY FOR EXPENSE TRANSACTION
    $stmt->bindValue(':logged_user', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    $stmt->bindValue(':amount', $amount_float, PDO::PARAM_STR);
    $stmt->bindValue(':date', $date->format('Y-m-d'), PDO::PARAM_STR);
    $stmt->bindValue(':comment', $comment === '' ? null : $comment);

    $stmt->execute();
    $connection->commit();

  } catch(PDOException $e) {
    error_log($e->getMessage());
    $errors['general'] = "Cannot add transaction right now. Please try again later.";
    $_SESSION['errors'] = $errors;
    $connection->rollBack();
    header("Location: $redirect");
    exit();
  }

  $_SESSION['success'] = "Transaction added successfully";
  header("Location: $redirect");
  exit();
?>