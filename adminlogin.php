<?php
session_start();
include 'database.php'; // Include your database connection

$message = '';  // Initialize the message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the entered credentials
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Check if the username is valid
    if (!empty($username) && !empty($password)) {
        // Prepare the SQL statement to fetch the admin record based on the username
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        // Fetch the admin record
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($password, $admin['password'])) {
            // If password matches, store the session data and redirect to the admin dashboard
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: admin_dashboard.php');  // Redirect to the admin dashboard
            exit();
        } else {
            $message = 'Invalid username or password.';
        }
    } else {
        $message = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Color Mixing Tool</title>
        <link rel="stylesheet" href="style/login.css">
       <style>
         @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap");

            * {
            margin: 0;
            box-sizing: border-box;
            }

            body {
    margin: 0;
    padding: 0;
    display: grid;
    place-content: center;
    height: 100vh;
    width: 100vw;
    background: linear-gradient(135deg, #ff8a65, #ff4081); /* Gradient from orange to pink */
    overflow: hidden;
    color: white;
    font-family: "Poppins", sans-serif;
    position: relative;
}


body::before {
    content: ''; /* Create an empty content */
    position: absolute; /* Position it absolutely within the body */
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5); /* Semi-transparent color overlay */
    z-index: 1; /* Ensure it's behind the content */
}

.box {
    position: relative;
    width: 380px;
    height: 500px;
    background: rgba(255, 255, 255, 0.1); /* Transparent white frame */
    border-radius: 12px;
    backdrop-filter: blur(10px); /* Frosted glass effect */
    overflow: hidden;
    font-family: "Poppins", sans-serif;
    --color: #ff8a65; /* Orange-pink for highlights */
    box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.3); /* Soft shadow for depth */
    z-index: 2; /* Ensure the box is above the color layer */
}

.form {
    position: absolute;
    background: rgba(0, 0, 0, 0.5); /* Semi-transparent black for inner form */
    z-index: 10;
    inset: 2px;
    border-radius: 12px;
    padding: 20px 38px;
    display: flex;
    flex-direction: column;
    height: 100%;
    box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.5); /* Inner shadow for depth */
}
.form h2 {
    color: var(--color);
    font-weight: 600;
    text-align: center;
    letter-spacing: 0.1em;
}

.inputbox input {
    position: relative;
    width: 100%;
    padding: 10px 10px;
    background: transparent;
    border: none;
    outline: none;
    font-size: 1em;
    letter-spacing: 0.05em;
    color: #fff; /* White text for better contrast */
    z-index: 2;
}

.inputbox span {
    position: absolute;
    color: #c3a6e6; /* Light purple text */
    left: 0;
    padding: 20px 0 10px 0;
    font-size: 1em;
    pointer-events: none;
    letter-spacing: 0.05em;
    transform: translateY(-10px);
    transition: 0.5s;
}

.inputbox input:valid ~ span,
.inputbox input:focus ~ span {
    color: var(--color);
    transform: translateY(-40px);
    font-size: 0.75em;
}

.inputbox i {
    position: absolute;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 2px;
    background: var(--color);
    transition: 0.5s;
    border-radius: 4px;
    pointer-events: none;
}

.inputbox input:valid ~ i,
.inputbox input:focus ~ i {
    height: 40px;
}

input[type="submit"] {
    width: 300px;
    background: var(--color);
    border: none;
    outline: none;
    padding: 11px 25px;
    margin-top: 20px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    color: #fff;
}

.admin-btn {
    width: 300px;
    background: transparent;
    border: 2px solid var(--color);
    outline: none;
    padding: 11px 25px;
    margin-top: 15px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 15px;
    text-align: center;
    color: var(--color);
    text-decoration: none;
    display: block;
    cursor: pointer;
    transition: background 0.3s, color 0.3s;
}

.admin-btn:hover {
    background: var(--color);
    color: #fff;
}

       </style>
    </head>
    <body>
    <div class="box">
        <div class="form">
            <h2>Admin Login</h2>
            <form method="POST">
                <div class="inputbox">
                    <input type="text" name="username" required="required">
                    <span>Username</span>
                    <i></i>
                </div>
                <div class="inputbox">
                    <input type="password" name="password" required="required">
                    <span>Password</span>
                    <i></i>
                </div>
                <?php if ($message): ?>
                    <div class="error"><?php echo $message; ?></div>
                <?php endif; ?>
                <input type="submit" value="Login" class="login-btn">
                <a href="adminregistration.php" class="admin-btn">Admin Register</a>
                <h2>OR</h2>
                <a href="login.php" class="admin-btn">Back to User Login</a>
            </form>
        </div>
    </div>
</body>
</html>

