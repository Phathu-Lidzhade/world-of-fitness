<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST["email"] ?? '';
  $pwd = $_POST["password"] ?? '';

  try {
    
    require_once "../../api/dbh.php";
    require_once "model.php";
    require_once "view.php";
    require_once "contr.php";

    //Error handlers
    $errors = [];

    // Empty field checks
    if (empty($email)) {
      $errors['email'] = "Email is required.";
    }
    if (empty($pwd)) {
      $errors['password'] = "Password is required.";
    }

    // Format and business rules
    if (!empty($email) && is_email_invalid($email)) {
      $errors['email'] = "Please enter a valid email address.";
    }

        // Try normal user first
    $result = fetchUser($pdo, $email);
    $isAdmin = false; // flag if we later match an admin account

    if ($result && !is_password_wrong($pwd, (string)$result["pwd"])) {
        // user exists and password matches, proceed (no error)
    } else {
        // either no user or password wrong — try admin via model
        $admin = fetchAdminByEmail($pdo, $email);

        if ($admin) {
            // support hashed admin passwords or plain-text fallback
            $adminPasswordMatches = false;
            if (!empty($admin['pwd']) && password_verify($pwd, $admin['pwd'])) {
                $adminPasswordMatches = true;
            } elseif ($pwd === $admin['pwd']) {
                $adminPasswordMatches = true;
            }

            if ($adminPasswordMatches) {
                // map admin PK into idusers so existing session code can use $result["idusers"]
                $adminId = $admin['idadmin'] ?? ($admin['id'] ?? null);
                $admin['idusers'] = $adminId;
                $result = $admin;
                $isAdmin = true;
            } else {
                // admin exists but wrong password
                $errors['password'] = "Password incorrect.";
            }
        } else {
            // no admin found — decide appropriate error for user path
            if (!$result) {
                $errors['email'] = "User does not exist.";
            } else {
                $errors['password'] = "Password incorrect.";
            }
        }
    }

    require_once "../../api/config_session.php";



    if (!empty($errors)) {
        $_SESSION['errors_login'] = $errors;
        
        header("Location: ../login.php");
        die();
    }

    $newSessionId = session_create_id();
    $sessionId = $newSessionId . "_" . $result["idusers"];
    session_id($sessionId);

    $_SESSION["user_idusers"] = $result["idusers"];
    $_SESSION["last_regeneration"] = time();
    //to show the users email
    $_SESSION["user_email"] = htmlspecialchars($result["email"]);

        // set role in session
    $_SESSION["role"] = !empty($isAdmin) ? 'admin' : 'user';

    // redirect based on role
    if (!empty($isAdmin)) {
        header("Location: ../../admin/admin.php"); // change to your admin page path
    } else {
        header("Location: ../../users page/dashboard.php");
    }


    $pdo = null;
    $stmt = null;

    die();

  } catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
  }
  

}else {
  header("Location: ../login.php");
  die();
}
