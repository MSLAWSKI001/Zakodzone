<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Warzywniak</title>
</head>
<body>
    <link rel="stylesheet" href="styl2.css">
    <header class = "hlewy">
    <h1>Internetowy sklep z eko-warzywami</h1>
    </header>
    <header class = "hprawy">
    <ol>
        <li>warzywa</li>
        <li>owoce</li>
        <li><a href="https://terapiasokami.pl/">soki</a></li>
    </ol>
    </header>
    <main>
    <?php
    $con = mysqli_connect("localhost","root","","dane2");

    $q = "SELECT `nazwa`,`ilosc`,`opis`,`cena`,`zdjecie` FROM `produkty` WHERE `Rodzaje_id` IN (1,2);";
    $res = mysqli_query($con,$q);
    while ($row = mysqli_fetch_array  ($res))
    {
        echo "<div class='produkt'>
		<img src='$row[4]' alt='warzywniak' />
		<h5>$row[0]</h5>
		<p>opis: $row[2]</p>
		<p>na stanie: $row[1]</p>
		<h2>$row[3] zł</h2>
		</div>";
    }
    
    ?>
    </main>
    <footer>
        <form action="sklep.php" method="post">
            Nazwa: 
            <input type="text" name="nazwa">
            Cena: 
            <input type="text" name="cena">
            <button type="submit">Dodaj produkt</button>
        </form>
    <?php
    $nazwa = $_POST['nazwa'];
    $cena = $_POST['cena'];

    $q2 = "INSERT INTO `produkty` (`Rodzaje_id`, `Producenci_id`, `nazwa`, `ilosc`, `opis`, `cena`, `zdjecie`) VALUES ('1', '4', '$nazwa', '10', 'puste pole', '$cena', 'owoce.jpg');";
    mysqli_query($con,$q2);
    mysqli_close($con); 
    ?>    
        <p>Strone wykonalo 8257</p>
    </footer>
</body>
</html>