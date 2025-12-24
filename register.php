<?php
// Show errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection info
$host = "localhost";
$db = "bob_db";   // updated database name
$user = "root";
$pass = "";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST['firstname']);
    $lastname  = trim($_POST['lastname']);
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm'];

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO register (firstname, lastname, email, password, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $firstname, $lastname, $email, $hashed_password);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Create Account - ZRC</title>
  <link rel="stylesheet" href="register.css" />
</head>
<body>
  <div class="form-wrapper">
    <form class="register-form" action="register.php" method="POST" onsubmit="return validateForm()">
      <h2>Create Your Account</h2>

      <?php if (!empty($errors)) : ?>
        <div style="color: red; margin-bottom: 15px;">
          <?php foreach ($errors as $error) : ?>
            <p><?php echo htmlspecialchars($error); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="form-group">
        <label for="firstname">First Name</label>
        <input type="text" id="firstname" name="firstname" placeholder="Zuhura" value="<?php echo isset($firstname) ? htmlspecialchars($firstname) : ''; ?>" required />
      </div>

      <div class="form-group">
        <label for="lastname">Last Name</label>
        <input type="text" id="lastname" name="lastname" placeholder="Ziad" value="<?php echo isset($lastname) ? htmlspecialchars($lastname) : ''; ?>" required />
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="you@example.com" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="At least 6 characters" required />
      </div>

      <div class="form-group">
        <label for="confirm">Confirm Password</label>
        <input type="password" id="confirm" name="confirm" placeholder="Re-enter password" required />
      </div>

      <button type="submit">Register</button>

      <p class="login-link">
        Already have an account? <a href="login.php">Sign In</a>
      </p>
    </form>
  </div>

  <script>
    function validateForm() {
      const pass = document.getElementById("password").value;
      const confirm = document.getElementById("confirm").value;
      if (pass !== confirm) {
        alert("Passwords do not match!");
        return false;
      }
      return true;
    }
  </script>
</body>
</html>
