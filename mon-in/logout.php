<?php
require_once 'db.php';

session_unset();
session_destroy();

session_start();
$_SESSION['message'] = 'Je bent uitgelogd.';

header('Location: index.php');
exit;