<?php
// ================================================
// UNTUK XAMPP: Rename file ini menjadi db.php
// ================================================
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'donasi_db';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

define('DB_TYPE', 'mysql');

function db_query($conn, $query) {
    return mysqli_query($conn, $query);
}

function db_fetch($result) {
    return mysqli_fetch_assoc($result);
}

function db_num_rows($result) {
    return mysqli_num_rows($result);
}

function db_escape($conn, $string) {
    return mysqli_real_escape_string($conn, $string);
}
?>
