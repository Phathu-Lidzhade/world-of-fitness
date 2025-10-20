<?php

declare(strict_types=1);

function fetchUser(object $pdo, string $email){
  $sql = "SELECT email FROM users WHERE email = :email;";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(":email", $email);
  $stmt->execute();

  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  return $result;
}

function set_user(object $pdo, string $firstName, string $surname, string $numberPhone, string $idNumber, string $pwd, string $email) {

  $sql = "INSERT INTO users (firstName, surname, numberPhone, idNumber, email, pwd) VALUES (:firstName, :surname, :numberPhone, :idNumber, :email, :pwd)";
  $stmt = $pdo->prepare($sql);

  $options = [
    'cost' => 12
  ];
  $hashedPwd = password_hash($pwd, PASSWORD_BCRYPT, $options);

  $stmt->bindParam(":firstName", $firstName);
  $stmt->bindParam(":surname", $surname);
  $stmt->bindParam(":numberPhone", $numberPhone);
  $stmt->bindParam(":idNumber", $idNumber);
  $stmt->bindParam(":email", $email);
  $stmt->bindParam(":pwd", $hashedPwd);
  
  $stmt->execute();
}