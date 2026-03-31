<?php
session_start();
include 'dbconnect.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Location: admin_login.html');
    exit;
}
$u = trim($_POST['username'] ?? '');
$p = trim($_POST['password'] ?? '');

if (empty($u) || empty($p)) {
    header('Location: admin_login.html?error=empty');
    exit;
}
try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);  // building a new connection object
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare('SELECT id, username FROM user WHERE username = :u AND PASSWORD = :P LIMIT 1');
    $stmt->execute([':u' => $u, ':p' => $p]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['login_time'] = time();
        header('Location:admin_menu.php');
    } else {
        header('Location:admin_login.html?error=invalid&user=' . urlencode($u));
    }
} catch (PDOException $e) {
    header('Location: admin_login.html?error=db');
}
?>