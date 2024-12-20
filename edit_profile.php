<?php
include 'database.php'; // Ensure the connection to the database

// Fetch the user details to display in the form
$stmt = $pdo->prepare("SELECT * FROM users LIMIT 1");
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Initialize the message variable
$message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Safely retrieve POST variables with default empty strings if not set
    $fullname = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $current_password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    // Validate inputs
    if (empty($fullname) || empty($email)) {
        $message = "Please fill in all fields.";
    } else {
        try {
            // Handle password change
            if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
                // Check if the current password matches
                if (password_verify($current_password, $user['password'])) {
                    if ($new_password === $confirm_password) {
                        // Hash the new password
                        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE users SET fullname = :fullname, email = :email, password = :new_password WHERE id = :user_id");
                        $stmt->bindParam(':new_password', $new_hashed_password, PDO::PARAM_STR);
                    } else {
                        $message = "New passwords do not match.";
                    }
                } else {
                    $message = "Current password is incorrect.";
                }
            } else {
                // Update profile without changing the password
                $stmt = $pdo->prepare("UPDATE users SET fullname = :fullname, email = :email WHERE id = :user_id");
            }

            // Execute the update query if no error message exists
            if (empty($message)) {
                $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
                $stmt->execute();

                $message = "Profile updated successfully!";
            }
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
    <title>Edit Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
    body {
    font-family: "Poppins", sans-serif;
    background: linear-gradient(135deg, #1e3c72, #2a5298); /* Blue gradient background */
    color: #333;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    background: #fff; /* White background for the container */
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    width: 500px;
    text-align: center;
}

h1 {
    color: #2a5298; /* Dark blue for the header */
}

form {
    display: flex;
    flex-direction: column;
}

label {
    margin-top: 10px;
    font-size: 14px;
    color: #555; /* Dark gray for labels */
}

input[type="text"], 
input[type="email"], 
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    background-color: #f0f0f0; /* Light gray for input fields */
    color: #333; /* Dark gray for text */
    border: 1px solid #ccc; /* Light gray border */
}

button {
    background-color: #2a5298; /* Dark blue for the button */
    color: #fff;
    border: none;
    padding: 10px 20px;
    margin-top: 10px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

button:hover {
    background-color: #1e3c72; /* Darker blue on hover */
}

.back-link {
    margin-top: 20px;
    display: inline-block;
    color: #2a5298; /* Dark blue for back link */
    text-decoration: none;
    font-size: 16px;
}

.message {
    margin-top: 10px;
    color: #2a5298; /* Dark blue for messages */
}
    </style>
</head>
<body>

    <div class="container">
        <h1>Edit Profile</h1>
        
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" value="<?php echo isset($user['fullname']) ? htmlspecialchars($user['fullname']) : ''; ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="password">Current Password:</label>
            <input type="password" name="password">

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password">

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password">

            <button type="submit" name="update">Update Profile</button>
        </form>

        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>

</body>
</html>

