<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Pętle</title>
</head>
<body>
    <?php
    for ($i=1; $i < 11; $i++) { 
        echo $i;
        echo "  ";
    }
    echo"<br>";
    for ($q=1; $q <=10 ; $q++) { 
        $w;
        $w=$q*2;
        echo $w;
        echo "  ";
        
    }
    echo"<br>"; 
    $liczba = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $liczba = (int)$_POST["liczba"];

    if ($liczba <= 10) {
        echo "Podana liczba jest mniejsza lub równa 10. Spróbuj ponownie.\n";
    }
}

if ($liczba > 10) {
    echo "Podano poprawną liczbę większą niż 10: $liczba\n";
}

echo"<br>";
for ($e=1; $e <=10 ; $e++) { 
    $r=$e*$e;
    echo $r;
    echo"  ";
}
$a = 0;
$liczba1 = (int)$_POST["liczba1"];
$liczba2 = (int)$_POST["liczba2"];
    $t = $liczba1 + $liczba2;
   if ($t>100) {
    echo"za mało";
   }
   if ($t<100) {
    echo"za dużo";
   }
   $haslo = (string)$_POST["haslo"];
if ($haslo = "haslo") {
    echo"zgadłeś";
}
else
{
    echo"nie zgadłeś";
}

?>

<form method="post">
    <label for="liczba">Podaj liczbę większą niż 10:</label>
    <input type="number" name="liczba" id="liczba">
    <br>
    <input type="number" name="liczba1" id="liczba1">
    <br>
    <input type="number" name="liczba2" id="liczba2">
    <br>
    <input type="password" name="haslo" id="haslo">
    <button type="submit">Sprawdź</button>
</form>



</body>
</html>