<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    // Redirect to login page if the admin is not logged in
    header('Location: login.php');
    exit();
}

include 'database.php';

// Handle adding a new module
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_name = trim($_POST['module_name']);

    if (!empty($module_name)) {
        $stmt = $pdo->prepare("INSERT INTO modules (name) VALUES (?)");
        $stmt->execute([$module_name]);
        $success_message = "Module added successfully!";
    } else {
        $error_message = "Module name cannot be empty!";
    }
}

// Fetch all modules
$stmt = $pdo->query("SELECT * FROM modules");
$modules = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Modules</title>
    <link href="style/manage.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1d; /* Dark theme background */
            color: #f0f0f0; /* Light text */
            font-family: 'Roboto', sans-serif;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #ff4081; /* Bright accent color */
            margin-bottom: 30px;
        }

        .form-container {
            width: 50%;
            margin: 0 auto;
            padding: 25px;
            background-color: #2c3e50; /* Darker blue background */
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            margin-bottom: 50px;
        }

        .form-container h2 {
            text-align: center;
            color: #ff4081;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: none;
            outline: none;
            background-color: #34495e; /* Slate grey for inputs */
            color: white;
            border-radius: 5px;
        }

        .form-control:focus {
            background-color: #1abc9c; /* Light turquoise on focus */
            box-shadow: 0 0 5px rgba(26, 188, 156, 0.5);
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #e74c3c; /* Red accent */
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
        }

        button:hover {
            background-color: #c0392b; /* Darker red on hover */
        }

        table {
            width: 90%;
            margin: 0 auto;
            margin-top: 40px;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 15px;
            border: 1px solid #666;
            text-align: left;
        }

        table th {
            background-color: #2c3e50;
            color: white;
        }

        table td {
            background-color: #34495e;
            color: white;
        }

        table tr:hover {
            background-color: #1abc9c; /* Hover effect with light turquoise */
        }

        .success, .error {
            text-align: center;
            padding: 12px;
            border-radius: 6px;
            margin: 15px auto;
            width: 80%;
        }

        .success {
            background-color: #a5d6a7; /* Light green success message */
            color: #388e3c;
        }

        .error {
            background-color: #f8bbd0; /* Light pink error message */
            color: #d32f2f;
        }

        .BD {
            display: block;
            width: 220px;
            margin: 20px auto;
            text-align: center;
            padding: 12px;
            background-color: #9b59b6; /* Purple background */
            color: #fff;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .BD:hover {
            transform: translateY(-4px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        }

    </style>
</head>
<body>

<!-- Display success or error messages -->
<?php if (isset($success_message)): ?>
    <div class="success"><?php echo $success_message; ?></div>
<?php elseif (isset($error_message)): ?>
    <div class="error"><?php echo $error_message; ?></div>
<?php endif; ?>

<!-- Add New Module Form -->
<div class="form-container">
    <h2>Add New Module</h2>
    <form method="POST">
        <div class="form-group">
            <label for="module_name">Module Name:</label>
            <input type="text" id="module_name" name="module_name" class="form-control" required>
        </div>
        <button type="submit">Add Module</button>
    </form>
</div>

<!-- Display Modules Table -->
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Module Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($modules as $module): ?>
        <tr>
            <td><?php echo htmlspecialchars($module['id']); ?></td>
            <td><?php echo htmlspecialchars($module['name']); ?></td>
            <td>
                <a href="edit_module.php?id=<?php echo $module['id']; ?>" style="color: #3498db;">Edit</a>
                <a href="delete_module.php?id=<?php echo $module['id']; ?>" onclick="return confirm('Are you sure you want to delete this module?')" style="color: #e74c3c; margin-left: 10px;">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="admin_dashboard.php" class="BD">Back to Dashboard</a>

</body>
</html>

