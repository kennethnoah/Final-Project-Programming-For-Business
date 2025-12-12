<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: admin_donasi.php');
    exit();
}

$id = (int)$_GET['id'];

$query = "SELECT foto, bukti_penyaluran FROM donasi WHERE id = $id";
$result = db_query($conn, $query);

if (db_num_rows($result) == 1) {
    $donasi = db_fetch($result);
    
    if ($donasi['foto'] && file_exists('uploads/' . $donasi['foto'])) {
        unlink('uploads/' . $donasi['foto']);
    }
    
    if ($donasi['bukti_penyaluran'] && file_exists('uploads/bukti/' . $donasi['bukti_penyaluran'])) {
        unlink('uploads/bukti/' . $donasi['bukti_penyaluran']);
    }
    
    db_query($conn, "DELETE FROM donasi WHERE id = $id");
}

header('Location: admin_donasi.php?success=delete');
exit();
?>
