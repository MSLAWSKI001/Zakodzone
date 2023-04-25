<?php
$servername = "localhost";
$username = "kox";
$password = "haslo";
$dbname = "formularz";
$conn = mysqli_connect($servername,$username,$password,$dbname);
if (!$conn) {
  die("Nie udało się połączyć: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $login = $_POST["login"];
  $haslo = $_POST["haslo"];
  $email = $_POST["email"];
  $data = $_POST["data"];

  $sql = "INSERT INTO formularz (login, hasło, email, data) VALUES ('$login', '$haslo', '$email', '$data')";
  if (mysqli_query($conn, $sql)) {
    echo "Zapisane w bazie danych";
  } else {
    echo "Coś poszło nie tak: " . mysqli_error($conn);
  }    
  mysqli_close($conn);
}
?>
