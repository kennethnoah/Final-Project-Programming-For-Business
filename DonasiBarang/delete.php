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

$query = "SELECT foto FROM donasi WHERE id = $id AND user_id = $user_id AND status = 'tersedia'";
$result = db_query($conn, $query);

if (db_num_rows($result) == 1) {
    $donasi = db_fetch($result);
    
    if ($donasi['foto'] && file_exists('uploads/' . $donasi['foto'])) {
        unlink('uploads/' . $donasi['foto']);
    }
    
    db_query($conn, "DELETE FROM donasi WHERE id = $id AND user_id = $user_id");
}

header('Location: dashboard.php?success=delete');
exit();
?>
