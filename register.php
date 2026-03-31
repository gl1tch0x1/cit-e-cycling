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
    
</body>
</html>