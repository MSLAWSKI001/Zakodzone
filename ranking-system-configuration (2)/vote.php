<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$elementId = isset($_POST['element_id']) ? (int)$_POST['element_id'] : 0;
$captchaInput = isset($_POST['captcha']) ? strtoupper(trim($_POST['captcha'])) : '';
$userIP = $_SERVER['REMOTE_ADDR'];

// Sprawdzenie czy captcha została wypełniona (walidacja jest po stronie JS)
if (empty($captchaInput) || strlen($captchaInput) !== 5) {
    $_SESSION['vote_message'] = ' Nieprawidłowy kod CAPTCHA. Spróbuj ponownie.';
    $_SESSION['vote_type'] = 'error';
    header('Location: index.php');
    exit;
}

// Sprawdzenie GLOBALNEGO cooldownu IP (jeden głos na DOWOLNY element)
$voteCooldown = (int)getConfig('vote_cooldown');
$cooldownTime = date('Y-m-d H:i:s', strtotime("-$voteCooldown minutes"));

$userIPEscaped = mysqli_real_escape_string($conn, $userIP);

// Sprawdź ostatni głos użytkownika na DOWOLNY element
$voteCheck = mysqli_query($conn, "SELECT voted_at FROM votes WHERE ip_address = '$userIPEscaped' AND voted_at > '$cooldownTime' ORDER BY voted_at DESC LIMIT 1");

if (mysqli_num_rows($voteCheck) > 0) {
    $row = mysqli_fetch_assoc($voteCheck);
    $votedTime = strtotime($row['voted_at']);
    $unlockTime = $votedTime + ($voteCooldown * 60);
    $remaining = $unlockTime - time();
    $mins = floor($remaining / 60);
    $secs = $remaining % 60;
    
    $_SESSION['vote_message'] = " Możesz oddać tylko jeden głos co {$voteCooldown} minut. Kolejny głos za {$mins} min {$secs} sek.";
    $_SESSION['vote_type'] = 'error';
    header('Location: index.php');
    exit;
}

// Sprawdzenie czy element istnieje i jest aktywny
$elementCheck = mysqli_query($conn, "SELECT id FROM elements WHERE id = $elementId AND active = 1");

if (mysqli_num_rows($elementCheck) == 0) {
    $_SESSION['vote_message'] = ' Element nie istnieje lub nie jest aktywny.';
    $_SESSION['vote_type'] = 'error';
    header('Location: index.php');
    exit;
}

// Dodanie głosu
mysqli_query($conn, "INSERT INTO votes (element_id, ip_address) VALUES ($elementId, '$userIPEscaped')");
mysqli_query($conn, "UPDATE elements SET votes = votes + 1 WHERE id = $elementId");

$_SESSION['vote_message'] = ' Dziękujemy za oddanie głosu! Możesz zagłosować ponownie za ' . $voteCooldown . ' minut.';
$_SESSION['vote_type'] = 'success';

header('Location: index.php');
exit;
?>
