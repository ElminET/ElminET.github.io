<?php
require_once 'db.php';
require_once 'functions.php';

if (!isset($_GET['id'])) {
    die('Geen gebruiker gekozen.');
}

$user_id = $_GET['id'];

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die('Gebruiker niet gevonden.');
}

$stmt = $db->prepare("
    SELECT posts.*,
    (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count
    FROM posts
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$user_id]);
$posts = $stmt->fetchAll();
// als skils niet leeg zijn split text met komma dan is netjes
$skills = $user['skills'] !== '' ? explode(',', $user['skills']) : [];
$interests = $user['interests'] !== '' ? explode(',', $user['interests']) : [];

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
    <title>Profiel - Mon-In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<nav class="navbar navbar-expand navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-linkedin"></i> Mon-In</a>

        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Home</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a class="nav-link" href="profile.php">Profile</a>
                <a class="nav-link" href="logout.php">Logout</a>
            <?php else: ?>
                <a class="nav-link" href="login.php">Login</a>
                <a class="nav-link" href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container mt-4 main-container">
    <div class="card shadow-sm border-0 mb-4">
        <div class="cover-banner"></div>
        <div class="p-4">
            <?php if ($user['avatar'] !== ''): ?>
                <img src="<?php echo htmlspecialchars($user['avatar']); ?>" class="profile-avatar" alt="Avatar">
            <?php else: ?>
                <div class="avatar-placeholder">
                    <i class="bi bi-person-fill" style="font-size: 50px; color: #999;"></i>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-start mt-3">
                <div>
                    <h3 class="mb-0"><?php echo htmlspecialchars($user['name']); ?></h3>
                    <p class="text-muted"><?php echo htmlspecialchars($user['headline']); ?></p>
                </div>
            </div>

            <h6>About</h6>
            <p><?php echo htmlspecialchars($user['about']); ?></p>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4 p-4">
        <h6>Skills</h6>
        <div class="d-flex flex-wrap gap-2 mt-2">
            <?php foreach ($skills as $skill): ?>
                <span class="badge rounded-pill text-primary border border-primary"><?php echo htmlspecialchars(trim($skill)); ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4 p-4">
        <h6>Interests</h6>
        <div class="d-flex flex-wrap gap-2 mt-2">
            <?php foreach ($interests as $interest): ?>
                <span class="badge rounded-pill text-dark border"><?php echo htmlspecialchars(trim($interest)); ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4 p-4">
        <h6>Posts</h6>

        <?php foreach ($posts as $post): ?>
            <hr>
            <div class="mb-3">
                <small class="text-muted"><?php echo timeAgo($post['created_at']); ?></small>
                <p class="mt-1 mb-2"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

                <div class="d-flex align-items-center gap-3">
                    <a href="like.php?post_id=<?php echo $post['id']; ?>&redirect=user.php?id=<?php echo $user['id']; ?>" class="text-decoration-none text-muted">
                        <i class="bi bi-hand-thumbs-up<?php echo in_array($post['id'], $likedPosts) ? '-fill text-primary' : ''; ?>"></i>
                        <?php echo $post['like_count']; ?>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
zxzxzxzxzaw