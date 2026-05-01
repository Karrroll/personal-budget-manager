<?php
  //calculate total monthly transaction for user
  function getMonthlyTransactions(PDO $connection, int $user_id, DateTime $date, string $type) {
    try {
      $current_month = $date->format('Y-m');

      if($type === "INCOME") {
        $stmt = $connection->prepare('
          SELECT SUM(amount) FROM incomes WHERE user_id = :user_id AND date_of_income LIKE :current_month
        ');
      } else if($type === "EXPENSE") {
        $stmt = $connection->prepare('
          SELECT SUM(amount) FROM expenses WHERE user_id = :user_id AND date_of_expense LIKE :current_month
        ');
      } else {
        error_log($e->getMessage());
        header('Location: dashboard.php?error=general');
        exit();
      }

      $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->bindValue(':current_month', $current_month .'%', PDO::PARAM_STR);
      $stmt->execute();
 
      return (float) $stmt->fetchColumn();
    } catch(PDOException $e) {
      error_log($e->getMessage());
      return NULL;
    }
  }

  function calculateBalance(float $inc, float $exp) {
    return $inc - $exp;
  }
?>