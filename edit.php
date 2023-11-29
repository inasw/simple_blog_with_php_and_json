<?php
session_start();

function readPosts() {
    $data = file_get_contents('posts.json');
    return json_decode($data, true);
}

$posts = readPosts();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Edit a post
    if (isset($_POST['edit'])) {
        $postId = $_POST['edit'];
        $editedTitle = $_POST['edited_title'];
        $editedContent = $_POST['edited_content'];

        $posts['posts'][$postId]['title'] = $editedTitle;
        $posts['posts'][$postId]['content'] = $editedContent;

        file_put_contents('posts.json', json_encode($posts, JSON_PRETTY_PRINT));
    }
}

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $postId = $_GET['id'];
    $post = $posts['posts'][$postId];
} else {
    // Redirect to the main page if 'id' is not set
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
</head>
<body>
    <h1>Edit Post</h1>

    <!-- Edit post form -->
    <form method="post" action="edit.php">
        <input type="hidden" name="edit" value="<?php echo $postId; ?>">
        
        <label for="edited_title">Title:</label>
        <input type="text" name="edited_title" value="<?php echo $post['title']; ?>" required><br>

        <label for="edited_content">Content:</label><br>
        <textarea name="edited_content" rows="4" cols="50" required><?php echo $post['content']; ?></textarea><br>

        
        <label for="edited_image">Change Image:</label>
        <input type="file" name="edited_image" accept="image/*"><br>
        
        <input type="submit" value="Save Changes">
    </form>

    <p><a href="index.php">Back to Home</a></p>
</body>
</html>
