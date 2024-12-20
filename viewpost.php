<?php
session_start();
include 'database.php'; // Ensure this connects to your database using PDO.

if (!isset($_GET['id'])) {
    die("Post ID not specified.");
}

$post_id = $_GET['id'];

// Fetch the post details from the database
try {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :post_id");
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        die("Post not found.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Fetch comments related to this post
try {
    $stmt = $pdo->prepare("
    SELECT c.*, u.full_name AS fullname 
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.id
    WHERE c.post_id = :post_id
    ORDER BY c.created_at DESC
");

    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Handle comment submission
$message = '';  // To display any error or success messages
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if (!empty($comment)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment) 
                                   VALUES (:post_id, :user_id, :comment)");
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->execute();
            header("Location: viewpost.php?id=" . $post_id); // Refresh the page to show the new comment
            exit();
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Comment cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #23242a;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #1c1c1c;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            width: 600px;
        }
        h1 {
            color: #45f3ff;
        }
        .post {
            background: #333;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.5);
        }
        .post h2 {
            color: #45f3ff;
        }
        .post-meta {
            font-size: 14px;
            color: #aaa;
        }
        .comment-section {
            margin-top: 30px;
        }
        .comment {
            background: #444;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
        .comment .comment-meta {
            font-size: 12px;
            color: #aaa;
        }
        .comment-form {
            margin-top: 20px;
        }
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            background-color: #333;
            color: #fff;
        }
        button {
            background-color: #45f3ff;
            color: #1c1c1c;
            border: none;
            padding: 10px 20px;
            margin-top: 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <div class="post">
            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
            <div class="post-meta">
                <p><strong>Posted by:</strong> <?php echo htmlspecialchars($post['title']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($post['created_at']); ?></p>
            </div>
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <?php if ($post['image']): ?>
                <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image" style="width:100%; height:auto; margin-top: 20px;">
            <?php endif; ?>
        </div>

        <div class="comment-section">
            <h3>Comments</h3>
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="comment-meta">
                            <strong><?php echo htmlspecialchars($comment['fullname']); ?></strong>
                            <span><?php echo htmlspecialchars($comment['created_at']); ?></span>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments yet.</p>
            <?php endif; ?>
        </div>

        <?php if (isset($_SESSION['username'])): ?>
            <div class="comment-form">
                <h4>Add a Comment:</h4>
                <?php if (!empty($message)): ?>
                    <p style="color: #45f3ff;"><?php echo htmlspecialchars($message); ?></p>
                <?php endif; ?>
                <form method="POST">
                    <textarea name="comment" required></textarea>
                    <button type="submit">Post Comment</button>
                </form>
            </div>
        <?php else: ?>
            <p>You must be logged in to comment.</p>
        <?php endif; ?>

        <a href="dashboard.php" style="color: #45f3ff;">Back to Dashboard</a>
    </div>
</body>
</html>
