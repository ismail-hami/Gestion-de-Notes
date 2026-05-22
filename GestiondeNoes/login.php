<?php

session_start();
$expiration = 60 * 60 * 24 * 365; // 1 year

if (isset($_COOKIE['visits'])) {
    $visits = $_COOKIE['visits'] + 1;
} else {
    $visits = 1;
    setcookie("entryDateTime", date("F j, Y, g:i a"), time() + $expiration, "/");
}

setcookie("visits", $visits, time() + $expiration, "/");

// Display
echo "Visits: " . $visits . "<br>";

if (isset($_COOKIE['entryDateTime'])) {
    echo "First visit: " . $_COOKIE['entryDateTime'];
}
require_once('inc/dbConnect.php'); // Make sure your DB connection is included
if(isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // --- Check admin ---
    if($email === "admin@admin.com" && $password === "admin") {
        $_SESSION['loggedIN'] = true;
        $_SESSION['role'] = "admin";
        mail("admin@admin.com", "Hello", "logIN", "From:emsi@emsi-edu.ma\r\n");
        header("Location: admin.php");
        exit();
    }

    // --- Check users table ---
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // If passwords are plain text (not recommended!)
        if($password === $user['password_hash']) {
            $_SESSION['loggedIN'] = true;
            $_SESSION['role'] = "user";
            $_SESSION['user_id'] = $user['id'];
            mail($email, "EMSI", "Welcome to EMSI", "From:emsi@emsi-edu.ma\r\n");
            header("Location: user.php");
            exit();
        } else {
            $errorDisplay = "<p class='error'>Wrong PASSWORD!</p>";
        }
    } else {
        $errorDisplay = "<p class='error'>Wrong EMAIL or PASSWORD!</p>";
    }
}
?>

   
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
       <div style>
        <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background-color: #ecf0f1;">
            <div style="background: white; padding: 40px; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 400px;">
                <h2 style="text-align: center; margin-bottom: 30px; color: #2c3e50;">Connexion</h2>
                <form action="login.php" method="post" >
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" placeholder="votreemail@exemple.com" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
    <?php
          if (isset($errorDisplay)) {
            echo $errorDisplay;
          }

          ?>
</body>
</html>