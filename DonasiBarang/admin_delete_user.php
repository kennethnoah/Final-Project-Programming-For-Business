<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: admin_users.php');
    exit();
}

$id = (int)$_GET['id'];

$donasi_result = db_query($conn, "SELECT foto, bukti_penyaluran FROM donasi WHERE user_id = $id");
while ($donasi = db_fetch($donasi_result)) {
    if ($donasi['foto'] && file_exists('uploads/' . $donasi['foto'])) {
        unlink('uploads/' . $donasi['foto']);
    }
}

db_query($conn, "DELETE FROM donasi WHERE user_id = $id");
db_query($conn, "DELETE FROM users WHERE id = $id AND role = 'user'");

header('Location: admin_users.php?success=delete');
exit();
?>
