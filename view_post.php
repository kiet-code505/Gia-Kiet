<?php
// Include your database connection
include 'database.php';

// Check if ID is set in the URL
if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);

    // Fetch post details along with module and image information
    $stmt = $pdo->prepare("
    SELECT c.*, u.username, p.title, p.content, p.image, m.name AS module_name, p.created_at
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.id
    LEFT JOIN posts p ON c.post_id = p.id
    LEFT JOIN modules m ON p.module_id = m.id
    WHERE c.post_id = :post_id
    ORDER BY c.created_at DESC
    ");

    // Execute the query with the correct parameter
    $stmt->execute(['post_id' => $post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo "<p>Post not found!</p>";
        exit;
    }
} else {
    echo "<p>Invalid post ID!</p>";
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title'] ?? 'No title available'); ?></title>
    <link href="style/manage.css" rel="stylesheet">
    <style>
body {
    font-family: 'Poppins', sans-serif;
    background-image: url('background.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    color: #FFFFFF; /* White text */
}

.container {
    background-color: rgba(44, 47, 51, 0.95); /* Dark gray with slight transparency */
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
    width: 100%;
    max-width: 800px;
    text-align: center;
    border: 2px solid #FFA500; /* Orange border */
}

h1 {
    color: #1E90FF; /* Dodger Blue for titles */
    font-weight: 700;
    margin-bottom: 20px;
}

p {
    color: #B0C4DE; /* Light Steel Blue for text */
    font-size: 16px;
    line-height: 1.6;
}

.post-image {
    max-width: 100%;
    height: auto;
    margin: 20px 0;
    border: 2px solid #FFA500; /* Orange border for images */
    border-radius: 8px;
}

.btn {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #1E90FF; /* Dodger Blue button */
    color: #FFFFFF; /* White text */
    border-radius: 4px;
    font-weight: 600;
    font-size: 15px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.btn:hover {
    background-color: #FFA500; /* Orange on hover */
}

.comment-form {
    margin-top: 30px;
    text-align: left;
}

.comment-form input[type="text"],
.comment-form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    background-color: #3B3F45; /* Slightly lighter gray for input fields */
    border: 1px solid #1E90FF; /* Dodger Blue border */
    color: #FFFFFF; /* White text */
    border-radius: 4px;
}

.comment-form button {
    padding: 10px 20px;
    background-color: #1E90FF; /* Dodger Blue button */
    border: none;
    color: #FFFFFF; /* White text */
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s;
}

.comment-form button:hover {
    background-color: #FFA500; /* Orange on hover */
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Post Title: <?php echo htmlspecialchars($post['title'] ?? 'No title available'); ?></h1>
        <p><strong>Module:</strong> <?php echo htmlspecialchars($post['module_name'] ?? 'No module available'); ?></p>

        <?php if (!empty($post['image'])): ?>
            <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post Image" style="max-width: 100%;">
        <?php else: ?>
            <p><em>No image available for this post.</em></p>
        <?php endif; ?>
        

        <p class="post-content"><?php echo nl2br(htmlspecialchars($post['content'] ?? 'No content available')); ?></p>
        <p><small>Posted on: <?php echo htmlspecialchars($post['created_at'] ?? 'Unknown date'); ?></small></p>
        <a href="manage_posts.php" class="btn">Back to Manage Posts</a>

        <!-- Comment Form -->
        <div class="comment-form">
            <h2>Leave a Comment</h2>
            <form action="submit_comment.php" method="POST">
                <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                <input type="text" name="username" placeholder="Your Name" required>
                <textarea name="comment" rows="4" placeholder="Your Comment" required></textarea>
                <button type="submit">Submit Comment</button>
            </form>
        </div>
    </div>
</body>
</html>


