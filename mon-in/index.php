<?php
require_once 'db.php';
require_once 'functions.php';
// foutmelding variable
$postError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $content = trim($_POST['content']);

    if ($content === '') {
        $postError = 'Je bericht mag maximaal 500 karakters zijn.';
    } else {
        $stmt = $db->prepare("INSERT INTO posts (content, user_id) VALUES (?, ?)");
        $stmt->execute([$content, $_SESSION['user_id']]);

        $_SESSION['message'] = 'Post geplaatst.';
        header('Location: index.php');
        exit;
    }
}
// connection met posts s
$stmt = $db->query("
    SELECT posts.*, users.name, users.avatar, users.headline,
    (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count
    FROM posts
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.created_at DESC
");
// hier
$posts = $stmt->fetchAll();

$session_id = session_id();
$likedStmt = $db->prepare("SELECT post_id FROM likes WHERE session_id = ?");
$likedStmt->execute([$session_id]);
$likedPosts = $likedStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Mon-In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<nav class="navbar navbar-expand navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-linkedin"></i> Mon-In
        </a>

<!-- show profile and logout if logged in, otherwise show login and register -->
        <div class="navbar-nav ms-auto">
            <a class="nav-link active" href="index.php">
                <i class="bi bi-house-door"></i> Home
            </a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a class="nav-link" href="profile.php">
                    <i class="bi bi-person"></i> Profile
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            <?php else: ?>
                <a class="nav-link" href="login.php">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
                <a class="nav-link" href="register.php">
                    <i class="bi bi-person-plus"></i> Register
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container mt-4 main-container">
    <?php showMessages(); ?>
    <!-- aleen als ingelogd laat dit zien (isset checkt of variable bestaat en of het een waarde heeft) -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="card shadow-sm border-0 mb-4 p-3">
            <!-- checkt of er een fout komt -->
            <?php if ($postError !== ''): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($postError); ?></div>
            <?php endif; ?>
            <!-- Als op post klik stuurt data naar php -->
            <form method="POST">
                <!-- typ area -->
                <textarea id="content" name="content" class="form-control mb-2" rows="3" placeholder="What's on your mind?" maxlength="500" required></textarea>
                <!-- typ area -->
                <div class="d-flex justify-content-between align-items-center">
                    <!-- getal voor characters over. word aangepast met js -->
                    <span class="counter-text"><span id="charCount">500</span> karakters over</span>
                    <!-- submit button. form -> POST -> PHP -->
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Post
                    </button>
                </div>
            </form>
        </div>
    <!-- einde login check -->
    <?php endif; ?>

    <h4 class="mb-3">Recent Posts</h4>
<!-- pak elke post uit de lijst $posts had bij php code gedaan -->
    <?php foreach ($posts as $post): ?>
    <!-- ieder post krijgt mooie card -->
        <div class="card shadow-sm post-card mb-3 p-4">
            <div class="d-flex align-items-center mb-3">
                <!-- heeft deze user profiel foto? -->
                <?php if ($post['avatar'] !== ''): ?>
                <!-- ja -->
                    <img src="<?php echo htmlspecialchars($post['avatar']); ?>" class="avatar-sm me-3" alt="Avatar">
                <?php else: ?>
                <!-- nee -->
                    <i class="bi bi-person-circle me-3" style="font-size: 48px; color: #aaa;"></i>
                <!-- einde script -->
                <?php endif; ?>

                <div>
                    <strong>
                        <!-- naam clickbaar maken en gaat naar die persoon de profile -->
                        <a href="user.php?id=<?php echo $post['user_id']; ?>" class="text-decoration-none text-dark">
                            <!-- toont naam user -->
                            <?php echo htmlspecialchars($post['name']); ?>
                        </a>
                    </strong><br>
                    <!-- toont headline net als Product Manager bv in card -->
                    <small class="text-muted"><?php echo htmlspecialchars($post['headline']); ?></small><br>
                    <!-- maakt datum van ago in card-->
                    <small class="text-muted"><?php echo timeAgo($post['created_at']); ?></small>
                </div>
            </div>
            <!-- post inhoud tonen. nl2br = enters worden nieuwe regels. htmlspecialchars = voor security XSS attack -->
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

            <div class="d-flex align-items-center gap-3">
                <!-- like knop connected met like.php -->
                <a href="like.php?post_id=<?php echo $post['id']; ?>&redirect=index.php" class="text-decoration-none text-muted">
                    <!-- icon veranderen als geliket als like dan word blauw-->
                    <i class="bi bi-hand-thumbs-up<?php echo in_array($post['id'], $likedPosts) ? '-fill text-primary' : ''; ?>"></i>
                    <!-- laat zien hoeveel likes -->
                    <?php echo $post['like_count']; ?>
                </a>
                <!-- is dit gebruiker post? als ja kan delete als nee niet -->
                <?php if (isset($_SESSION['user_id']) && $post['user_id'] == $_SESSION['user_id']): ?>
                    <a href="delete.php?post_id=<?php echo $post['id']; ?>&redirect=index.php"
                       class="text-decoration-none text-muted"
                       onclick="return confirm('U gaat nu uw post verwijderen. Weet u het zeker?');">
                        <!-- confirm message hier boven-->
                        <i class="bi bi-trash"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- character counter -->
<script>
    const content = document.getElementById('content');
    const charCount = document.getElementById('charCount');

    if (content && charCount) {
        content.addEventListener('input', function () {
            charCount.textContent = 500 - content.value.length;
        });
    }
</script>


</body>
</html>