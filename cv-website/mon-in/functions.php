<?php

function timeAgo($datetime) {
    $now = new DateTime();
    $postTime = new DateTime($datetime);
    $diff = $now->diff($postTime);

    if ($diff->y > 0) return $diff->y . ' jaar geleden';
    if ($diff->m > 0) return $diff->m . ' maand' . ($diff->m > 1 ? 'en' : '') . ' geleden';
    if ($diff->d > 0) return $diff->d . ' dag' . ($diff->d > 1 ? 'en' : '') . ' geleden';
    if ($diff->h > 0) return $diff->h . ' uur geleden';
    if ($diff->i > 0) return $diff->i . ' minuut' . ($diff->i > 1 ? 'en' : '') . ' geleden';

    return 'zojuist';
}

function showMessages() {
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['message']) . '</div>';
        unset($_SESSION['message']);
    }

    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
        unset($_SESSION['error']);
    }
}