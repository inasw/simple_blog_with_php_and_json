<?php
session_start();

function readPosts() {
    $data = file_get_contents('posts.json');
    return json_decode($data, true);
}

$posts = readPosts();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create a new post
    if (isset($_POST['submit'])) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $imagePath = '';

        // Check if the 'image' key exists in the $_FILES array
        if (isset($_FILES['image']['name'])) {
            $imagePath = 'uploads/' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        }

        $newPost = array(
            'title' => $title,
            'content' => $content,
            'image' => $imagePath
        );

        $userPosts = $_SESSION['user_posts'];
        $userPosts[] = $newPost;
        $_SESSION['user_posts'] = $userPosts;
       
        $posts['posts'][] = $newPost;
        file_put_contents('posts.json', json_encode($posts, JSON_PRETTY_PRINT));
    }

    // Delete a post
    if (isset($_POST['delete'])) {
        $postId = $_POST['delete'];
        unset($posts['posts'][$postId]);
        file_put_contents('posts.json', json_encode($posts, JSON_PRETTY_PRINT));
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Blog</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1 class="main-heading">Simple Blog</h1>

    <!-- Display existing posts -->
    <div class="posts-list">
    <?php foreach ($posts['posts'] as $postId => $post): ?>
        <article class="post">
            <!-- Display the image -->
            <div class="image-div">
                <?php if (!empty($post['image'])): ?>
                <img src="<?php echo $post['image']; ?>" alt="Post Image">
                <?php endif; ?>
           </div>

           <div class="contents">
                <h2 class="post-title"><?php echo $post['title']; ?></h2>
                <p class="post-content"><?php echo $post['content']; ?></p>
          

            <div class="post-actions">
            <a href="edit.php?id=<?php echo $postId; ?>">Edit</a>

            <!-- Add a form for deleting a post -->
            <form method="post" action="index.php"  class="delete-form">
                <input type="hidden" name="delete" value="<?php echo $postId; ?>">
                <button type="submit" class="delete-button">Delete</button>
            </form>
        </div>
        </div>
        </article>
    <?php endforeach; ?>
    </div>

    <!-- Create a new post form -->

    <div class="create-post-form">
    <h2>Create a New Post</h2>
    <form method="post" action="index.php" enctype="multipart/form-data" class="new-post-form">
        <label for="image" class="image-label">Image:</label>
        <input type="file" name="image" accept="image/*" class="image-input"><br>

        <label for="title" class="title-label">Title:</label>
        <input type="text" name="title" required class="title-input"><br>

        <label for="content" class="content-label">Content:</label><br>
        <textarea name="content" rows="4" cols="50" required class="content-input"></textarea><br>


        <input type="submit" name="submit" value="Create" class="submit-button">
    </form>
</body>
</html>
