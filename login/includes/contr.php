<?php

declare(strict_types=1);

function is_input_empty(string $pwd, string $email): array {
  $errors = [];

  if (empty($email)) {
      $errors['email'] = "Email is required.";
  }
  if (empty($pwd)) {
      $errors['password'] = "Password is required.";
  }

  return $errors;
}

function is_email_invalid(string $email){
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return true;
  }
  else {
    return false;
  }
}

function is_email_wrong(bool|array $result){
  if (!$result) {
    return true;
  }
  else {
    return false;
  }
}

function is_password_wrong(string $pwd, string $hashedPwd){
  if (!password_verify($pwd, $hashedPwd)) {
    return true;
  }
  else {
    return false;
  }
}
