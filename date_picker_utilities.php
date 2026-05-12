<?php
  session_start();

  $errors = [];
  $old = [];
    
  $selected_period = $_POST['selected-period'] ?? 'CURRENT_MONTH';
  $given_start_date = $_POST['start-date'] ?? '';
  $given_end_date = $_POST['end-date'] ?? '';

  $today = new DateTime();
  $today_zero = (clone $today)->setTime(0, 0, 0); // veriable to modify and compeare

  if ($selected_period === 'CUSTOM') {
    $old['start-date'] = $given_start_date;
    $old['end-date'] = $given_end_date;

    //Validate given custom start date
    $min_date = new DateTime('1970-01-01');
    $min_date_zero = (clone $min_date)->setTime(0, 0, 0);

    if($given_start_date === '') {
      $errors['start-date'] = "Date field is required";
    } else {
      $start_date = DateTime::createFromFormat('Y-m-d', $given_start_date); //Convert given start date to DATE type

      if(!$start_date || $start_date->format('Y-m-d') !== $given_start_date) {
        $errors['start-date'] = "Invalid date format";
      } else {
        $start_date_zero = (clone $start_date)->setTime(0, 0, 0);
        
        if($start_date_zero > $today_zero || $start_date_zero < $min_date_zero) {
          $errors['start-date'] = "Date out of range";
        }
      }

    }

    //Validate given custom end date
    if($given_end_date === '') {
      $errors['end-date'] = "Date field is required";
    } else {
      $end_date = DateTime::createFromFormat('Y-m-d', $given_end_date); //Convert given end date to DATE type

      if(!$end_date || $end_date->format('Y-m-d') !== $given_end_date) {
        $errors['end-date'] = "Invalid date format";
      } else {
         $end_date_zero = (clone $end_date)->setTime(0, 0, 0);
        
        if($end_date_zero > $today_zero || $end_date_zero < $min_date_zero) {
          $errors['end-date'] = "Date out of range";
        }
      }
    }

    if(!isset($errors['start-date']) && !isset($errors['end-date'])) {
      if($start_date > $end_date)
        $errors['start-date'] = "Start date cannot be later than end date";
    }

    if (!empty($errors)) {
      $_SESSION['selected-period'] = $selected_period;
      $_SESSION['errors'] = $errors;
      $_SESSION['old'] = $old;
      header('Location: overview.php');
      exit();
    }
  }

  switch($selected_period) {
    case "CURRENT_MONTH":
      $_SESSION['start-date'] = $today_zero->format('Y-m-01');      // First day of current month
      $_SESSION['end-date'] = $today_zero->format('Y-m-d');         // Today
      break;
    case "LAST_MONTH":
      $last_month = (clone $today_zero)->modify('-1 month');
      $_SESSION['start-date'] = $last_month->format('Y-m-01');      // First day of last month
      $_SESSION['end-date'] = $last_month->format('Y-m-t');         // Last day of last month
      break;
    case "CURRENT_YEAR":
      $_SESSION['start-date'] = $today_zero->format('Y-01-01');     // First day of current year
      $_SESSION['end-date'] = $today_zero->format('Y-m-d');         // Today
      break;
    case "CUSTOM":
      $_SESSION['start-date'] = $start_date->format('Y-m-d');       // First day of custom date
      $_SESSION['end-date'] = $end_date->format('Y-m-d');           // Last day of custom date
      break;
    default:
      // current month dates
      $_SESSION['start-date'] = $today_zero->format('Y-m-01');
      $_SESSION['end-date'] = $today_zero->format('Y-m-d');   
  }

  $_SESSION['selected-period'] = $selected_period;
  $_SESSION['old'] = $old;
  header('Location: overview.php');
  exit(); 
?>