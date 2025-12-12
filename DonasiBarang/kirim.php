<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$query = "UPDATE donasi SET status = 'dikirim' WHERE id = $id AND user_id = $user_id AND status = 'tersedia'";
db_query($conn, $query);

header('Location: dashboard.php?success=kirim');
exit();
?>
