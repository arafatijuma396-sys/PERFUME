<?php
session_start();

            header("Location: home-page.html");


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "bob_db";

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $email = $_POST['email'];
    $inputPassword = $_POST['password'];

    // Look for the user by email
    $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Check password
        if (password_verify($inputPassword, $user["pasword"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["firstname"];

            // Redirect to catalog
            header("Location: catalog.html");
            exit();
        } else {
            $error = "❌ Incorrect password.";
        }
    } else {
        $error = "❌ Email not found.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ZRC Login</title>
  <link rel="stylesheet" href="login.css" />
</head>
<body>
  <div class="login-container">
    <form class="login-form" action="login.php" method="POST">
      <h2>Welcome Back</h2>

      <?php if (!empty($error)) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="you@example.com" required />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter your password" required />
      </div>

      <button type="submit">Login</button>
      <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
    </form>
  </div>
</body>
</html>
