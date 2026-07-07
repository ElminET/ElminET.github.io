<?php
// database verbinding laden (en session startten)
require_once 'db.php';

// haal post id uit URL (Welke post wordt liked?)
$post_id = $_GET['post_id'] ?? 0;
// bepaal waar we terug gaan na dat we like hebben gedaan
$redirect = $_GET['redirect'] ?? 'index.php';
// uniek ID van deze user (via session)
$session_id = session_id();

// bestaat de like al?
$stmt = $db->prepare("SELECT id FROM likes WHERE session_id = ? AND post_id = ?");
$stmt->execute([$session_id, $post_id]);
$like = $stmt->fetch();

// toggle like aan/uit
if ($like) {
    // like bestaat al -> verwijderen on click
    $stmt = $db->prepare("DELETE FROM likes WHERE session_id = ? AND post_id = ?");
    $stmt->execute([$session_id, $post_id]);
} else {
    // like bestaat niet -> voeg like toe
    $stmt = $db->prepare("INSERT INTO likes (session_id, post_id) VALUES (?, ?)");
    $stmt->execute([$session_id, $post_id]);
}

header("Location: $redirect");
exit;