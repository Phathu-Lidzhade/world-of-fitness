<?php

declare(strict_types=1);

function fetchUser(object $pdo, string $email){
  $sql = "SELECT * FROM users WHERE email = :email;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(":email", $email);
  $stmt->execute();

  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

/**
 * Fetch an admin row by email from admin table (model).
 * Returns associative array or null.
 */
function fetchAdminByEmail(object $pdo, string $email): ?array {
    $sql = "SELECT * FROM admins WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row === false ? null : $row;
}
