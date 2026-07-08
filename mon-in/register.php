<?php
require_once 'db.php';
// als gebruiker al ingelogd, terug naar home
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
// variable voor error
$error = '';

// aleen uitvoer als formulier is verstuurd
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // formulier gegevens ophalen
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password']; // confirm

    // validatie van invoer
    if (empty($name) || empty($email)) {
        $error = 'All fields are required';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
        // password zelfde bij register
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match';
    } else {
        // check of e-mailadres al bestaat
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'Email already exists';
        } else {
            // wachtwoord veilig oplsaan in hash
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            // nieuwe gebruiker toevoegen aan database
            $stmt = $db->prepare("INSERT INTO users (name, email, password, avatar, headline, about, skills, interests) VALUES (?, ?, ?, '', '', '', '', '')");
            $stmt->execute([$name, $email, $hashed]);
            // na registration naar login page
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Mon-In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f0ebe3; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-linkedin"></i> Mon-In</a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> Home</a>
            <a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
            <a class="nav-link active" href="register.php"><i class="bi bi-person-plus"></i> Register</a>
        </div>
    </div>
</nav>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 85vh;">
    <div class="card shadow-sm border-0 p-4" style="width: 100%; max-width: 480px;">
        <div class="text-center mb-3">
            <i class="bi bi-linkedin" style="font-size: 48px; color: #0a66c2;"></i>
            <h3 class="mt-2">Join Mon-In</h3>
            <p class="text-muted">Create your professional profile</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" minlength="6" required>
                <small class="text-muted">At least 6 characters</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Create Account</button>
        </form>

        <hr>
        <p class="text-center">Already have an account? <a href="login.php">Sign In</a></p>
    </div>
</div>
</body>
</html>
