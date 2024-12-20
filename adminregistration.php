<?php
include 'database.php'; // Include your database connection

// Define a more secure admin key (this is just an example, use environment variables for better security)
$admin_key = "8929"; // Replace with a more secure key or mechanism

// Handle form submission
$message = '';  // For error or success messages
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $admin_key_input = trim($_POST['admin_key']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Debugging output
    echo "Admin Key Input: " . htmlspecialchars($admin_key_input); // Prevent XSS attacks

    // Check if the admin key matches the defined key
    if ($admin_key_input !== $admin_key) {
        $message = "Invalid admin key. Please try again.";
    } else {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Prepare the SQL statement to insert the new admin user
        try {
            $stmt = $pdo->prepare("INSERT INTO admins (username, email, password) VALUES (:username, :email, :password)");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $stmt->execute();

            $message = "Admin account created successfully!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Admin Account</title>
  <link rel="stylesheet" href="style.css"> <!-- Adjusted CSS file name -->
  <style>
    body {
        background-color: #2f3640;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        font-family: 'Arial', sans-serif;
    }

    .form-container {
        background-color: #353b48;
        border-radius: 12px;
        width: 380px;
        padding: 30px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .form-container h2 {
        text-align: center;
        color: #ff6347;
        margin-bottom: 20px;
    }

    .input-group {
        position: relative;
        margin-bottom: 20px;
    }

    .input-group input {
        width: 100%;
        padding: 12px;
        border: 2px solid #444;
        border-radius: 6px;
        background-color: #2f3640;
        color: white;
        font-size: 14px;
    }

    .input-group label {
        position: absolute;
        top: -18px;
        left: 10px;
        font-size: 14px;
        color: #ff6347;
    }

    .input-group input:focus {
        border-color: #ff6347;
        outline: none;
    }

    .input-group input:focus + label {
        color: #ff6347;
    }

    .button {
        width: 100%;
        padding: 12px;
        background-color: #ff6347;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .button:hover {
        background-color: #e55347;
    }

    .back-link {
        display: block;
        text-align: center;
        margin-top: 15px;
        color: #ccc;
        text-decoration: none;
    }

    .back-link:hover {
        color: #ff6347;
    }

    .message {
        text-align: center;
        color: white;
        margin-bottom: 20px;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <form method="POST">
      <h2>Create Admin Account</h2>

      <!-- Display message if any -->
      <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <div class="input-group">
        <input type="text" name="admin_key" required>
        <label>Admin Key</label>
      </div>

      <div class="input-group">
        <input type="text" name="username" required>
        <label>Username</label>
      </div>

      <div class="input-group">
        <input type="email" name="email" required>
        <label>Email</label>
      </div>

      <div class="input-group">
        <input type="password" name="password" required>
        <label>Password</label>
      </div>

      <button type="submit" class="button">Register</button>
      <a href="adminlogin.php" class="back-link">Back to Admin Login</a>
    </form>
  </div>

</body>
</html>

