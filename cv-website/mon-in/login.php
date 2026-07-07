<?php
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid email or password';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - MonIn</title>
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
            <a class="nav-link active" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
            <a class="nav-link" href="register.php"><i class="bi bi-person-plus"></i> Register</a>
        </div>
    </div>
</nav>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 85vh;">
    <div class="card shadow-sm border-0 p-4" style="width: 100%; max-width: 480px;">
        <div class="text-center mb-3">
            <i class="bi bi-linkedin" style="font-size: 48px; color: #0a66c2;"></i>
            <h3 class="mt-2">Welcome Back</h3>
            <p class="text-muted">Sign in to your account</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Sign In</button>
        </form>

        <hr>
        <p class="text-center mb-1">Don't have an account? <a href="register.php">Register</a></p>
        <p class="text-center text-muted small mb-0">
            Demo accounts: sarah@example.com,<br>
            michael@example.com, etc.<br>
            Password for all: <span style="color: #dc3545;">password</span>
        </p>
    </div>
</div>
</body>
</html>
