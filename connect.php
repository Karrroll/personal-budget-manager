<?php
  $host = "localhost";
  $db_username = "root";
  $db_password = "";
  $db_name = "personal_budget_db";
  $charset = "utf8mb4";

  try {
    $connection = new PDO(
                            "mysql:host=$host;dbname=$db_name;charset=$charset",
                            $db_username,
                            $db_password,
                            [
                              PDO::ATTR_EMULATE_PREPARES => false,              // use native prepared statements
                              PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,      // throw exceptions
                              PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC  // fetch results as associative array
                            ]
                        );
  } catch (PDOException $e) {
    echo "Connection failed.";
  }
?>