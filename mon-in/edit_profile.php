<?php
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $headline = trim($_POST['headline']);
    $about = trim($_POST['about']);
    $skills = trim($_POST['skills']);
    $interests = trim($_POST['interests']);
    $avatar = trim($_POST['avatar']);

    if ($name === '') {
        $error = 'Naam mag niet leeg zijn.';
    } elseif (strlen($headline) > 100) {
        $error = 'Headline mag maximaal 100 karakters zijn.';
    } else {
        $stmt = $db->prepare("
            UPDATE users
            SET name = ?, headline = ?, about = ?, skills = ?, interests = ?, avatar = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, $headline, $about, $skills, $interests, $avatar, $_SESSION['user_id']]);

        $_SESSION['message'] = 'Profiel bijgewerkt.';
        header('Location: profile.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Mon-In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0ebe3; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold bi-linkedin" href="index.php">Mon-In</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php">Home</a>
            <a class="nav-link active" href="profile.php">Profile</a>
            <a class="nav-link" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4" style="max-width: 700px;">
    <div class="card shadow-sm border-0 p-4">
        <h3 class="mb-4">Edit Profile</h3>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Naam</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Headline</label>
                <input type="text" name="headline" class="form-control" value="<?php echo htmlspecialchars($user['headline']); ?>" maxlength="100">
            </div>

            <div class="mb-3">
                <label class="form-label">About</label>
                <textarea name="about" class="form-control" rows="4"><?php echo htmlspecialchars($user['about']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Skills</label>
                <input type="text" name="skills" class="form-control" value="<?php echo htmlspecialchars($user['skills']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Interests</label>
                <input type="text" name="interests" class="form-control" value="<?php echo htmlspecialchars($user['interests']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Avatar pad</label>
                <input type="text" name="avatar" class="form-control" value="<?php echo htmlspecialchars($user['avatar']); ?>">
            </div>

            <button type="submit" class="btn btn-primary">Opslaan</button>
        </form>

    </div>
</div>

</body>
</html>