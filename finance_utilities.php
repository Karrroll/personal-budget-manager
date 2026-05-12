<?php
  //calculate income for user
  function getIncome(PDO $connection, int $user_id, string $start, string $end) {
    try {
      $stmt = $connection->prepare('
        SELECT SUM(amount) FROM incomes WHERE user_id = :user_id AND date_of_income BETWEEN :start_date AND :end_date
      ');
      $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->bindValue(':start_date', $start, PDO::PARAM_STR);
      $stmt->bindValue(':end_date', $end, PDO::PARAM_STR);
      $stmt->execute();
 
      return (float) $stmt->fetchColumn();
    } catch(PDOException $e) {
      error_log($e->getMessage());
      return NULL;
    }
  }

  //calculate expense for user
  function getExpense(PDO $connection, int $user_id, string $start, string $end) {
    try {
      $stmt = $connection->prepare('
        SELECT SUM(amount) FROM expenses WHERE user_id = :user_id AND date_of_expense BETWEEN :start_date AND :end_date
      ');
      $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
      $stmt->bindValue(':start_date', $start, PDO::PARAM_STR);
      $stmt->bindValue(':end_date', $end, PDO::PARAM_STR);
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

  function shareOfTotal(float $total, float $amount, int $precision) {
    if($total != 0)
      return ($amount != 0) ? round(($amount / $total) * 100, $precision) : 0;
    else
      return "#";
  }
?>