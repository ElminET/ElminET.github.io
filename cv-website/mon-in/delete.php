<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$post_id = $_GET['post_id'] ?? 0;
$redirect = $_GET['redirect'] ?? 'index.php';

// Check of de post van deze gebruiker is
$stmt = $db->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if ($post && $post['user_id'] == $_SESSION['user_id']) {
    // Eerst likes verwijderen
    $stmt = $db->prepare("DELETE FROM likes WHERE post_id = ?");
    $stmt->execute([$post_id]);

    // Dan post verwijderen
    $stmt = $db->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);

    $_SESSION['message'] = 'Post verwijderd.';
}

header("Location: $redirect");
exit;