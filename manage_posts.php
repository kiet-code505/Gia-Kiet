<?php
// Include the database connection
include 'database.php';

session_start();

// Check if the admin is logged in by verifying if 'admin_id' is set
if (!isset($_SESSION['admin_id'])) {
    // If not logged in, redirect to login page
    header("Location: admin_login.php");
    exit();
}

// Fetch posts and associated module names
$stmt = $pdo->query("SELECT p.id, p.title, p.content, p.module_id, m.name AS module_name, p.created_at
                     FROM posts p
                     LEFT JOIN modules m ON p.module_id = m.id"); 
$posts = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Posts</title>
    <link href="style/manage.css" rel="stylesheet">
    <style>
body {
    font-family: 'Poppins', sans-serif;
    background-color: #1a1f36; /* Dark blue background */
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    min-height: 100vh;
    color: white;
}

.container {
    background-color: #262b44; /* Slightly lighter dark blue container */
    border: 2px solid #ffffff; /* White frame around the container */
    border-radius: 8px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    width: 90%;
    max-width: 1200px;
    text-align: center;
    margin-top: 20px;
}

h1 {
    color: #6ac5fe; /* Light blue title */
    font-weight: 600;
    margin-bottom: 20px;
}

.posts-list {
    margin-top: 20px;
}

.post-item {
    background-color: #2e3451; /* Medium dark blue for post items */
    border: 2px solid #ffffff; /* White frame around each post item */
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    text-align: left;
}

.post-item h2 {
    color: #6ac5fe; /* Light blue for post titles */
    font-size: 24px;
    margin-bottom: 10px;
}

.post-item p {
    color: #e0e0e0; /* Light grey for text content */
    font-size: 16px;
    line-height: 1.6;
}

.btn {
    display: inline-block;
    margin-top: 10px;
    padding: 10px 20px;
    background-color: #4caf50; /* Green color for the buttons */
    color: white;
    border-radius: 4px;
    font-weight: 600;
    font-size: 15px;
    text-decoration: none;
    margin-right: 5px;
}

.btn:hover {
    background-color: #45a045; /* Slightly darker green for hover effect */
}


    </style>
</head>
<body>
<div class="container">
    <h1>Manage Posts</h1>
    <a href="admin_newposts.php" class="btn add-post-btn">Add New Post</a>

    <div class="posts-list">
        <?php
        // Loop through the posts and display them with their associated module names
        if ($posts) {
            foreach ($posts as $post) {
                echo '<div class="post-item">';
                echo '<h2>Post By: <a href="view_post.php?id=' . $post['id'] . '">' . htmlspecialchars($post['title']) . '</a></h2>';
                echo '<p>Post Content: ' . nl2br(htmlspecialchars($post['content'])) . '</p>';
                echo '<p><small>Posted on: ' . htmlspecialchars($post['created_at']) . '</small></p>';
                echo '<p><strong>Module: </strong>' . htmlspecialchars($post['module_name']) . '</p>';
                echo '<a href="edit_post.php?id=' . $post['id'] . '" class="btn">Edit</a>';
                echo ' <a href="delete_post.php?id=' . $post['id'] . '" class="btn" onclick="return confirm(\'Are you sure you want to delete this post?\');">Delete</a>';
                echo '</div>';
            }
        } else {
            echo '<p>No posts found.</p>';
        }
        ?>
    </div>

    <br>
    <a href="admin_dashboard.php" class="btn">Back to Admin Dashboard</a>
</div>
</body>
</html>
