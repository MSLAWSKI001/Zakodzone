<?php
$servername = "localhost";
$username = "kox";
$password = "haslo";
$dbname = "szkoła";
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Nie udało się połączyć: " . mysqli_connect_error());
}

$sql = "SELECT * FROM `szkoła`";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<table><tr><th>ID</th><th>Imię</th><th>Nazwisko</th><th>Wiek</th></tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>".$row["id"]."</td><td>".$row["imie"]."</td><td>".$row["nazwisko"]."</td><td>".$row["wiek"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "Brak wyników.";
}

mysqli_close($conn);
?>
