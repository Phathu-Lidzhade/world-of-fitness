<?php

declare(strict_types=1);

function is_input_empty(string $firstName, string $surname, string $numberPhone, string $idNumber, string $pwd, string $email): array {
  $errors = [];

  if (empty($firstName)) {
      $errors['firstName'] = "First name is required.";
  }
  if (empty($surname)) {
      $errors['surname'] = "Surname is required.";
  }
  if (empty($numberPhone)) {
      $errors['numberPhone'] = "Phone number is required.";
  }
  if (empty($idNumber)) {
      $errors['idNumber'] = "ID number is required.";
  }
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

function is_email_taken(object $pdo, string $email){
  if (fetchUser($pdo, $email)) {
    return true;
  }
  else {
    return false;
  }
}

function is_idNumber_invalid(string $idNumber){
  if (!validateSAID($idNumber)) {
    return true;
  }
  else {
    return false;
  }
}

// ID number validation function

function validateSAID($id) {
  // Must be 13 digits
  if (!preg_match('/^\d{13}$/', $id)) {
      return false;
  }

  // Extract date of birth part
  $dob = substr($id, 0, 6);
  $year = substr($dob, 0, 2);
  $month = substr($dob, 2, 2);
  $day = substr($dob, 4, 2);

  // Convert YY to YYYY (assume >= 00 and <= current year)
  $fullYear = ($year <= date('y')) ? '20' . $year : '19' . $year;

  // Check if date is valid
  if (!checkdate((int)$month, (int)$day, (int)$fullYear)) {
      return false;
  }

  // Validate using Luhn algorithm
  return luhnCheck($id);
}

function luhnCheck($number) {
  $sum = 0;
  $alt = false;
  for ($i = strlen($number) - 1; $i >= 0; $i--) {
      $n = (int)$number[$i];
      if ($alt) {
          $n *= 2;
          if ($n > 9) {
              $n -= 9;
          }
      }
      $sum += $n;
      $alt = !$alt;
  }
  return ($sum % 10 === 0);
}

function is_sa_phone_simple(string $numberPhone): bool {
  // Trim just in case
  $phone = trim($numberPhone);

  // Check exact pattern: starts with 0, followed by 9 digits (total 10 digits)
  return preg_match('/^0\d{9}$/', $phone) === 1;
}

function create_user(object $pdo, string $firstName, string $surname, string $numberPhone, string $idNumber, string $pwd, string $email) {
  set_user($pdo, $firstName, $surname, $numberPhone, $idNumber, $pwd, $email);
}


/*
function is_input_empty(string $firstName, string $surname, string $numberPhone, string $idNumber, string $pwd, string $email){
  if (empty($firstName) || empty($surname) || empty($numberPhone) || empty($idNumber) || empty($pwd) || empty($email)) {
    return true;
  }
  else {
    return false;
  }
}*/