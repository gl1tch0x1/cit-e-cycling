<?php
// including connection variables
include 'dbconnect.php';

$page_title = 'Register Interest';
$is_admin = false;

// Validating the inputs
$errors = [];

if ($_SERVER['REQUEST METHOD'] !== 'POST') {
    header('Location: register.form.html');
    exit;
}

$firstname = trim(strip_tags($_POST['firstname'] ?? ''));
$surname = trim(strip_tags($_POST['surname'] ?? ''));
$email = trim(strtolower($_POST['email'] ?? ''));
$terms = isset($_POST['terms']) ? 1 : 0;

if (strlen($firstname) < 2 || strlen($firstname) > 50)
    $errors[] = 'Firstname should be about 2 - 50 characters.';
if (strlen($surname) < 2 || strlen($surname) > 50)
    $errors[] = 'Surname should be about 2 - 50 characters.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = 'Please enter a valid email address.';
if (!$terms)
    $errors[] = 'You must accept the terms and conditions.';

$success = false;
$already_reg = false;
$db_error = false;
if (empty($errors)) {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);  // building a new connection object
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $chk = $conn->prepare('SELECT id FROM interest WHERE email = :e LIMIT 1');
        $chk->execute([':e' => $email]);

        if ($chk->rowCount() > 0) {
            $already_reg = true;
        } else {
            $ins = $conn->prepare(
                'INSERT INTO interest (firstname, surname, email, terms) VALUES (:fn , :sn, :em, :tc)'
            );
            $ins->execute([':fn' => $firstname, ':sn' => $surname, ':em' => $email, ':tc' => $terms]);
            $success = true;
        }
    } catch (PDOException $e) {
        $db_error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="theme-color" content="#0b0c0f">
  <title>Register Interest | Cit-E Cycling</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800;900&family=Outfit:wght@400;500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
   <nav class="nav">
     <a href="index.html" class="nav-brand">
        <span class="nav-brand-dot" aria-hidden="true">
        </span>CIT-E Cycling</a>
        <div class="nav-spacer"></div>
        <a href="admin_login.html" class="btn btn-outline btn-sm">
            <i class="bi bi-lock"></i> Admin 
        </a>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
            <i class="bi bi-list" id="navToggleIcon"></i>
        </button>
    </nav>
<div class="nav-drawer" id="navDrawer">
    <a href="index.html"><i class="bi bi-house"></i> Home </a>
    <a href="register_form.html"><i class="bi bi-person-plus"></i> Register Interest</a>
    <a href="admin_login.html"><i class="bi bi-lock"></i> Admin Login</a>
</div>

<div class="page-top">
    <div class="page-top-inner">
        <div class="wrap">
            <a href="register_form.html" class="breadcrumb-link">
                <i class="bi bi-arrow-left"></i> Back to Form 
            </a>
            <div class="t-overline mb-a">Registeration</div>
            <h1 class="t-h1">
                <?php echo $success ? 'Registration<br>Complete!' : ($already_reg ? 'Already<br>Registered!' : 'Registration<br>Result'); ?>
            </h1>
        </div>
    </div>
    <div class"page-divider"></div>
</div>

<main class="section" id="main-content">
    <div class="wrap wrap-sm fade-in">
        <?php if(!empty($errors)): ?>
            <div class="alert alert-error-triangle-fill"></i>
            <div class="alert-content">
                <strong>Please fix the following: </strong>
                <ul style="margin: 5rem 0 0; padding-left:1.2rem">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                </ul>
            </div>
    </div>
    <a href="register_form.html" class="btn btn-outline">
        <i class="bi bi-arrow-left"></li> Go Back
    </a>

    <?php elseif ($db_error) : ?>
        <div class="alert alert-error md-4">
            <i class="bi bi-database-x"></i>
            <div class="alert-content"><strong>Database Error.</strong> 
            Please try again in a moment. </div>
        </div>
        <a href="register_form.html" class="btn btn-outline"><i class="bi bi-arrow-left"></i>
        Try Again! </a>

        <?php elseif ($already_reg): ?>
            <div class="card" style="text-alignment:center;padding:3rem 2rem">
                <div style="font-size:3.5rem;margin-bottom:1.25rem"></div>
                <h2 class="t-h2 mb-2">Already Registered! </h2>
                <p class="t-body" style="max-width:380px;margin:0 auto 2rem">
                The email <strong style="color:var(--text)"><?= htmlspecialchars($email) ?></strong>
                is already on our list. We'll be in touch when booking open! </p>
                <a href="index.html" class="btn btn-lime"><i class="bi bi-house-fill"><i>
                    Back to Home 
                </a>
            </div>






</body>
</html>